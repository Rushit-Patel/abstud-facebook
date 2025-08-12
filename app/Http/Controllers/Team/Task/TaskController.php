<?php

namespace App\Http\Controllers\Team\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\TaskAssignment;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\TaskTimeLog;
use App\Models\User;
use App\Notifications\TaskCommentNotification;
use App\Services\TaskService;
use App\DataTables\Team\Task\TaskDataTable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks
     */
    public function index(TaskDataTable $dataTable)
    {
        // Get filter options
        $categories = TaskCategory::active()->get();
        $priorities = TaskPriority::active()->ordered()->get();
        $statuses = TaskStatus::active()->ordered()->get();
        $users = User::active()->get();
        $branches = \App\Models\Branch::active()->get();

        // Get task statistics
        $totalTasks = Task::active()->count();
        $inProgressTasks = Task::active()->whereHas('status', function ($q) {
            $q->where('is_completed', false);
        })->count();
        $completedTasks = Task::active()->whereHas('status', function ($q) {
            $q->where('is_completed', true);
        })->count();
        $overdueTasks = Task::active()
            ->where('due_date', '<', now())
            ->whereHas('status', function ($q) {
                $q->where('is_completed', false);
            })->count();

        return $dataTable->render('team.task.index', compact(
            'categories', 
            'priorities', 
            'statuses', 
            'users',
            'branches',
            'totalTasks',
            'inProgressTasks',
            'completedTasks',
            'overdueTasks'
        ));
    }

    /**
     * Show the form for creating a new task
     */
    public function create(): View
    {
        $categories = TaskCategory::active()->get();
        $priorities = TaskPriority::active()->ordered()->get();
        $statuses = TaskStatus::active()->ordered()->get();
        $users = User::active()->get();

        return view('team.task.create', compact(
            'categories',
            'priorities', 
            'statuses',
            'users'
        ));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:task_categories,id',
            'priority_id' => 'nullable|exists:task_priorities,id',
            'status_id' => 'required|exists:task_statuses,id',
            'start_date' => 'required',
            'due_date' => 'nullable|after_or_equal:start_date',
            'estimated_hours' => 'nullable|integer|min:1',
            
            // Recurring task fields
            'is_recurring' => 'nullable|boolean',
            'repeat_mode' => 'nullable|required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
            'repeat_interval' => 'nullable|required_if:is_recurring,1|integer|min:1',
            'repeat_until' => 'nullable',
            
            // Assignees and Watchers
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'watchers' => 'nullable|array', 
            'watchers.*' => 'exists:users,id',
            
            // Tags
            'tags' => 'nullable|string',
            
            // File attachments
            'attachment_paths' => 'nullable|array',
            'attachment_names' => 'nullable|array',
        ]);

        try {
            // Process tags if they exist
            $tagsArray = [];
            if (!empty($validated['tags'])) {
                // Convert tag string to array (Tagify sends as JSON string)
                $tagsData = json_decode($validated['tags'], true);
                if (is_array($tagsData)) {
                    $tagsArray = array_map(function($tag) {
                        return is_array($tag) ? $tag['value'] : $tag;
                    }, $tagsData);
                } else {
                    // If not JSON, split by comma
                    $tagsArray = array_map('trim', explode(',', $validated['tags']));
                }
            }
            
            // Prepare task data
            $taskData = $validated;
            $taskData['tags'] = $tagsArray;
            
            // Handle recurring task fields
            if (!empty($validated['is_recurring']) && $validated['is_recurring']) {
                $taskData['is_recurring'] = true;
                $taskData['repeat_mode'] = $validated['repeat_mode'];
                $taskData['repeat_interval'] = $validated['repeat_interval'] ?? 1;
                $taskData['repeat_until'] = $validated['repeat_until'] ?? null;
                // For recurring tasks, don't set due_date
                $taskData['due_date'] = null;
            } else {
                $taskData['is_recurring'] = false;
                $taskData['repeat_mode'] = null;
                $taskData['repeat_interval'] = null;
                $taskData['repeat_until'] = null;
            }
            
            // Prepare assignees in the format expected by TaskService
            $assigneesData = [];
            if (!empty($validated['assignees'])) {
                foreach ($validated['assignees'] as $userId) {
                    $assigneesData[] = [
                        'user_id' => $userId,
                        'role' => 'assignee',
                        'estimated_hours' => null
                    ];
                }
            }
            
            // Add watchers as observers
            if (!empty($validated['watchers'])) {
                foreach ($validated['watchers'] as $userId) {
                    $assigneesData[] = [
                        'user_id' => $userId,
                        'role' => 'observer',
                        'estimated_hours' => null
                    ];
                }
            }
            
            $taskData['assignees'] = $assigneesData;
            
            $task = $this->taskService->createTask($taskData, Auth::user());

            // Handle file attachments from temporary uploads
            if ($request->has('attachment_paths') && is_array($request->attachment_paths)) {
                $this->moveTemporaryFiles($task, $request->attachment_paths, $request->attachment_names ?? []);
            }

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task created successfully.',
                    'redirect' => route('team.task.show', $task)
                ]);
            }

            return redirect()->route('team.task.show', $task)
                ->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating task: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Error creating task: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task
     */
    public function show(Task $task): View
    {
        $task->load([
            'category', 
            'priority', 
            'status', 
            'creator', 
            'updater',
            'assignments.user',
            'comments.user',
            'attachments.uploadedBy',
            'timeLogs.user',
            'activityLogs.user'
        ]);

        return view('team.task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task
     */
    public function edit(Task $task): View
    {
        $categories = TaskCategory::active()->get();
        $priorities = TaskPriority::active()->ordered()->get();
        $statuses = TaskStatus::active()->ordered()->get();
        $users = User::active()->get();

        $task->load(['assignments.user']);

        return view('team.task.edit', compact(
            'task',
            'categories',
            'priorities', 
            'statuses',
            'users'
        ));
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:task_categories,id',
            'priority_id' => 'nullable|exists:task_priorities,id',
            'status_id' => 'required|exists:task_statuses,id',
            'start_date' => 'nullable',
            'due_date' => 'nullable|after_or_equal:start_date',
            'estimated_hours' => 'nullable|integer|min:1',
            'actual_hours' => 'nullable|integer|min:1',
            'progress' => 'nullable|integer|min:0|max:100',
            
            // Recurring task fields
            'is_recurring' => 'nullable|boolean',
            'repeat_mode' => 'nullable|required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
            'repeat_interval' => 'nullable|required_if:is_recurring,1|integer|min:1',
            'repeat_until' => 'nullable|date|after:start_date',
            
            // Assignees and Watchers
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'watchers' => 'nullable|array', 
            'watchers.*' => 'exists:users,id',
            
            // Tags
            'tags' => 'nullable|string',
            
            // File attachments
            'attachment_paths' => 'nullable|array',
            'attachment_names' => 'nullable|array',
        ]);

        try {
            \Log::info('Task update started', [
                'task_id' => $task->id,
                'attachment_paths' => $request->attachment_paths,
                'attachment_names' => $request->attachment_names
            ]);
            // Process tags if they exist
            $tagsArray = [];
            if (!empty($validated['tags'])) {
                // Convert tag string to array (Tagify sends as JSON string)
                $tagsData = json_decode($validated['tags'], true);
                if (is_array($tagsData)) {
                    $tagsArray = array_map(function($tag) {
                        return is_array($tag) ? $tag['value'] : $tag;
                    }, $tagsData);
                } else {
                    // If not JSON, split by comma
                    $tagsArray = array_map('trim', explode(',', $validated['tags']));
                }
            }
            
            // Prepare task data
            $taskData = $validated;
            $taskData['tags'] = $tagsArray;
            
            // Handle recurring task fields
            if (!empty($validated['is_recurring']) && $validated['is_recurring']) {
                $taskData['is_recurring'] = true;
                $taskData['repeat_mode'] = $validated['repeat_mode'];
                $taskData['repeat_interval'] = $validated['repeat_interval'] ?? 1;
                $taskData['repeat_until'] = $validated['repeat_until'] ?? null;
                // For recurring tasks, don't set due_date
                $taskData['due_date'] = null;
            } else {
                $taskData['is_recurring'] = false;
                $taskData['repeat_mode'] = null;
                $taskData['repeat_interval'] = null;
                $taskData['repeat_until'] = null;
            }
            
            // Prepare assignees in the format expected by TaskService
            $assigneesData = [];
            if (!empty($validated['assignees'])) {
                foreach ($validated['assignees'] as $userId) {
                    $assigneesData[] = [
                        'user_id' => $userId,
                        'role' => 'assignee',
                        'estimated_hours' => null
                    ];
                }
            }
            
            // Add watchers as observers
            if (!empty($validated['watchers'])) {
                foreach ($validated['watchers'] as $userId) {
                    $assigneesData[] = [
                        'user_id' => $userId,
                        'role' => 'observer',
                        'estimated_hours' => null
                    ];
                }
            }
            $taskData['assignees'] = $assigneesData;
            
            $task = $this->taskService->updateTask($task, $taskData, Auth::user());

            // Handle file attachments from temporary uploads
            if ($request->has('attachment_paths') && is_array($request->attachment_paths)) {
                \Log::info('Processing file attachments for update', [
                    'attachment_paths' => $request->attachment_paths,
                    'attachment_names' => $request->attachment_names
                ]);
                $this->moveTemporaryFiles($task, $request->attachment_paths, $request->attachment_names ?? []);
            } else {
                \Log::info('No file attachments to process for update');
            }

            return redirect()->route('team.task.show', $task)
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating task: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task
     */
    public function destroy(Task $task): RedirectResponse
    {
        try {
            $task->delete();

            return redirect()->route('team.task.index')
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting task: ' . $e->getMessage());
        }
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:task_statuses,id'
        ]);

        try {
            $task = $this->taskService->updateTask($task, $validated, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully.',
                'task' => $task->load(['status'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update task progress
     */
    public function updateProgress(Request $request, Task $task)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        try {
            $task = $this->taskService->updateProgress($task, $validated['progress'], Auth::user());

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task progress updated successfully.',
                    'task' => $task
                ]);
            }

            // For direct form submission, redirect back with success message
            return redirect()->route('team.task.show', $task)
                ->with('success', 'Task progress updated successfully.');
                
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating task progress: ' . $e->getMessage()
                ], 500);
            }

            // For direct form submission, redirect back with error message
            return redirect()->route('team.task.show', $task)
                ->with('error', 'Error updating task progress: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle task archive status
     */
    public function toggleArchive(Request $request, Task $task)
    {
        try {
            $task->update(['is_archived' => !$task->is_archived]);

            $status = $task->is_archived ? 'archived' : 'unarchived';

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Task {$status} successfully.",
                    'is_archived' => $task->is_archived
                ]);
            }

            // For direct form submission, redirect back with success message
            return redirect()->route('team.task.show', $task)
                ->with('success', "Task {$status} successfully.");
                
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating task archive status: ' . $e->getMessage()
                ], 500);
            }

            // For direct form submission, redirect back with error message
            return redirect()->route('team.task.show', $task)
                ->with('error', 'Error updating task archive status: ' . $e->getMessage());
        }
    }

    /**
     * Assign users to task
     */
    public function assignUsers(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'assignees' => 'required|array',
            'assignees.*.user_id' => 'required|exists:users,id',
            'assignees.*.role' => 'required|in:assignee,reviewer,observer',
            'assignees.*.estimated_hours' => 'nullable|integer|min:1',
        ]);

        try {
            $assignments = $this->taskService->assignMultipleUsers(
                $task, 
                $validated['assignees'], 
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Users assigned successfully.',
                'assignments' => $assignments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove assignment from task
     */
    public function removeAssignment(Task $task, TaskAssignment $assignment): JsonResponse
    {
        try {
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment removed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store task comment
     */
    public function storeComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:task_comments,id',
            'is_internal' => 'nullable|in:true,false,1,0,yes,no,on,off'
        ]);

        try {
            \Log::info('Creating task comment', [
                'task_id' => $task->id,
                'user_id' => Auth::user()->id,
                'content_length' => strlen($validated['content']),
                'validated_data' => $validated,
                'raw_request_data' => $request->all()
            ]);
            
            // Convert is_internal to proper boolean
            $isInternal = false;
            if (isset($validated['is_internal'])) {
                $isInternal = in_array(strtolower($validated['is_internal']), ['true', '1', 'yes', 'on']);
            }
            
            \Log::info('Processing is_internal field', [
                'raw_value' => $validated['is_internal'] ?? 'not_set',
                'converted_value' => $isInternal
            ]);
            
            $comment = $task->comments()->create([
                'user_id' => Auth::user()->id,
                'content' => $validated['content'],
                'parent_id' => $validated['parent_id'] ?? null,
                'is_internal' => $isInternal,
            ]);

            $comment->load('user');
            
            \Log::info('Task comment created successfully', [
                'comment_id' => $comment->id,
                'task_id' => $task->id
            ]);

            // Send comment notifications to assignees and task creator (except the commenter)
            try {
                $notifyUsers = collect();
                
                // Add task creator
                if ($task->creator && $task->creator->id !== Auth::user()->id) {
                    $notifyUsers->push($task->creator);
                }
                
                // Add assignees
                foreach ($task->assignees as $assignee) {
                    if ($assignee->id !== Auth::user()->id && !$notifyUsers->contains('id', $assignee->id)) {
                        $notifyUsers->push($assignee);
                    }
                }
                
                // Send notifications
                foreach ($notifyUsers as $user) {
                    $notification = new TaskCommentNotification($task, $comment, $user);
                    $notification->send();
                }
                
            } catch (\Exception $e) {
                \Log::error('Failed to send task comment notifications: ' . $e->getMessage(), [
                    'task_id' => $task->id,
                    'comment_id' => $comment->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment added successfully.',
                    'comment' => $comment
                ]);
            }

            // For direct form submission, redirect back with success message
            return redirect()->route('team.task.show', $task)
                ->with('success', 'Comment added successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Failed to create task comment', [
                'task_id' => $task->id,
                'user_id' => Auth::user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding comment: ' . $e->getMessage()
                ], 500);
            }

            // For direct form submission, redirect back with error message
            return redirect()->route('team.task.show', $task)
                ->with('error', 'Error adding comment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete task comment
     */
    public function destroyComment(TaskComment $comment): JsonResponse
    {
        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store task attachment
     */
    public function storeAttachment(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('task-attachments', 'public');

            $attachment = $task->attachments()->create([
                'uploaded_by' => Auth::user()->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            $attachment->load('uploadedBy');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'attachment' => $attachment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete task attachment
     */
    public function destroyAttachment(TaskAttachment $attachment): JsonResponse
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store time log
     */
    public function storeTimeLog(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after:started_at',
            'duration_minutes' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'is_billable' => 'boolean',
        ]);

        try {
            $timeLog = $task->timeLogs()->create([
                'user_id' => Auth::user()->id,
                'started_at' => $validated['started_at'],
                'ended_at' => $validated['ended_at'],
                'duration_minutes' => $validated['duration_minutes'],
                'description' => $validated['description'],
                'is_billable' => $validated['is_billable'] ?? false,
            ]);

            $timeLog->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Time log added successfully.',
                'timeLog' => $timeLog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding time log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete time log
     */
    public function destroyTimeLog(TaskTimeLog $timeLog): JsonResponse
    {
        try {
            $timeLog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Time log deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting time log: ' . $e->getMessage()
            ], 500);
        }
    }

    // API Methods for AJAX calls

    /**
     * Get categories for API
     */
    public function getCategories(): JsonResponse
    {
        $categories = TaskCategory::active()->get();
        return response()->json($categories);
    }

    /**
     * Get priorities for API
     */
    public function getPriorities(): JsonResponse
    {
        $priorities = TaskPriority::active()->ordered()->get();
        return response()->json($priorities);
    }

    /**
     * Get statuses for API
     */
    public function getStatuses(): JsonResponse
    {
        $statuses = TaskStatus::active()->ordered()->get();
        return response()->json($statuses);
    }

    /**
     * Get users for API
     */
    public function getUsers(): JsonResponse
    {
        $users = User::active()->select('id', 'name', 'email')->get();
        return response()->json($users);
    }

    /**
     * Get task activity for API
     */
    public function getActivity(Task $task): JsonResponse
    {
        $activity = $task->activityLogs()
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json($activity);
    }

    public function storeFile()
    {
        // This method can be used for individual task file uploads
        // Currently handled by storeTempFile for form uploads
    }

    /**
     * Store temporary file for Dropzone uploads
     */
    public function storeTempFile(Request $request)
    {
        try {
            \Log::info('storeTempFile called', [
                'files' => $request->allFiles(),
                'has_file' => $request->hasFile('file'),
                'all_input' => $request->all()
            ]);

            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
            ]);

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            
            \Log::info('Processing file upload', [
                'original_name' => $originalName,
                'file_name' => $fileName,
                'size' => $file->getSize()
            ]);
            
            // Store in temporary uploads directory
            $path = $file->storeAs('temp/task-attachments', $fileName, 'public');
            
            $response = [
                'success' => true,
                'path' => $path,
                'name' => $originalName,
                'size' => $file->getSize(),
            ];
            
            \Log::info('File upload successful', $response);
            
            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary file
     */
    public function deleteTempFile(Request $request)
    {
        try {
            $path = $request->input('path');
            
            if ($path && \Storage::disk('public')->exists($path)) {
                \Storage::disk('public')->delete($path);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move temporary files to permanent location and create attachments
     */
    private function moveTemporaryFiles($task, $tempPaths, $originalNames)
    {
        \Log::info('moveTemporaryFiles called', [
            'task_id' => $task->id,
            'temp_paths' => $tempPaths,
            'original_names' => $originalNames
        ]);
        
        foreach ($tempPaths as $index => $tempPath) {
            try {
                \Log::info("Processing file {$index}", [
                    'temp_path' => $tempPath,
                    'exists' => \Storage::disk('public')->exists($tempPath)
                ]);
                
                if (!\Storage::disk('public')->exists($tempPath)) {
                    \Log::warning("Temporary file does not exist: {$tempPath}");
                    continue;
                }

                $originalName = $originalNames[$index] ?? 'unknown';
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $permanentPath = 'task-attachments/' . $fileName;

                \Log::info("Moving file", [
                    'from' => $tempPath,
                    'to' => $permanentPath,
                    'original_name' => $originalName
                ]);

                // Move file from temp to permanent location
                \Storage::disk('public')->move($tempPath, $permanentPath);

                $uploadedBy = Auth::user()->id;
                \Log::info("Creating attachment with uploaded_by", [
                    'uploaded_by' => $uploadedBy,
                    'uploaded_by_type' => gettype($uploadedBy)
                ]);

                // Create attachment record
                $attachment = $task->attachments()->create([
                    'original_name' => $originalName,
                    'file_path' => $permanentPath,
                    'file_size' => \Storage::disk('public')->size($permanentPath),
                    'mime_type' => mime_content_type(storage_path('app/public/' . $permanentPath)) ?? 'application/octet-stream',
                    'uploaded_by' => $uploadedBy,
                ]);

                \Log::info("Attachment created successfully", [
                    'attachment_id' => $attachment->id,
                    'original_name' => $originalName,
                    'permanent_path' => $permanentPath
                ]);

            } catch (\Exception $e) {
                \Log::error('Failed to move temporary file', [
                    'temp_path' => $tempPath,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        \Log::info('moveTemporaryFiles completed', [
            'task_id' => $task->id,
            'processed_files' => count($tempPaths)
        ]);
    }
}