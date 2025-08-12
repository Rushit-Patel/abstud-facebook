<?php

namespace App\Http\Controllers\Team\Todo;

use App\DataTables\Team\Lead\LeadDataTable;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Todo;
use App\Models\User;
use App\Repositories\Team\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TodoController extends Controller
{

    public function index()
    {
        try {
            $currentUser = auth()->user();
            $userPermissions = $currentUser->getAllPermissions()->pluck('name');

            // Get team members for filter dropdown
            if ($userPermissions->filter(fn($perm) => Str::is('*:show-all', $perm))->isNotEmpty()) {
                $teamMembers = User::active()->get();
            } else {
                $teamMembers = User::where('branch_id', $currentUser->branch_id)->active()->get(); 
            }

            // Return initial empty todos for AJAX loading
            $todos = collect();

            return view('team.todo.create', compact('todos','teamMembers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading todos: ' . $e->getMessage());
        }
    }

    public function getTodos(Request $request)
    {
        try {
            $currentUser = auth()->user();
            $userPermissions = $currentUser->getAllPermissions()->pluck('name');

            // Build base query
            $query = Todo::with(['assignedUser', 'addedByUser']);

            $query->where(function($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id)
                    ->orWhere('added_by', $currentUser->id);
            });

            // Apply filters from request
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('assignment')) {
                $assignment = $request->assignment;
                switch($assignment) {
                    case 'my-todos':
                        $query->where(function($q) use ($currentUser) {
                            $q->where('user_id', $currentUser->id)
                              ->where('added_by', $currentUser->id);
                        });
                        break;
                    case 'assigned-to-me':
                        $query->where('user_id', $currentUser->id);
                        break;
                    case 'created-by-me':
                        $query->where('added_by', $currentUser->id);
                        break;
                    default:
                        if (str_starts_with($assignment, 'user-')) {
                            $userId = str_replace('user-', '', $assignment);
                            $query->where('user_id', $userId);
                        }
                }
            }

            if ($request->filled('date_range')) {
                $dateRange = explode(' to ', $request->date_range);
                if (count($dateRange) == 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->format('Y-m-d');
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->format('Y-m-d');
                }else{
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->format('Y-m-d');
                    $endDate = $startDate;
                }
                $query->whereBetween('due_date', [$startDate, $endDate]);
            }

            $todos = $query->orderByDesc('updated_at')->get();

            // Group todos by status
            $groupedTodos = $todos->groupBy('status');

            // Get tasks with same filtering logic
            $taskRepository = new TaskRepository;
            $taskQuery = $taskRepository->getTasks();
            
            // Apply task filters
            $taskQuery->whereHas('assignees', function($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id);
            });
            
            // Apply search filter to tasks
            if ($request->filled('search')) {
                $search = $request->search;
                $taskQuery->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Apply assignment filter to tasks
            if ($request->filled('assignment')) {
                $assignment = $request->assignment;
                switch($assignment) {
                    case 'assigned-to-me':
                        // Already filtered above
                        break;
                    case 'created-by-me':
                        $taskQuery->where('created_by', $currentUser->id);
                        break;
                    default:
                        if (str_starts_with($assignment, 'user-')) {
                            $userId = str_replace('user-', '', $assignment);
                            $taskQuery->whereHas('assignees', function($q) use ($userId) {
                                $q->where('id', $userId);
                            });
                        }
                }
            }
            
            // Apply date range filter to tasks
            if ($request->filled('date_range')) {
                $dateRange = explode(' to ', $request->date_range);
                if (count($dateRange) == 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->format('Y-m-d');
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->format('Y-m-d');
                } else {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->format('Y-m-d');
                    $endDate = $startDate;
                }
                $taskQuery->whereBetween('due_date', [$startDate, $endDate]);
            }
            
            $tasks = $taskQuery->get();

            // Map task statuses to todo status format
            $mappedTasks = $tasks->map(function($task) {
                // Map task status to todo status format
                $taskStatus = $task->status->slug ?? 'pending';
                switch($taskStatus) {
                    case 'pending':
                    case 'to-do':
                        $task->mapped_status = 'pending';
                        break;
                    case 'in-progress':
                    case 'review':
                        $task->mapped_status = 'in_progress';
                        break;
                    case 'completed':
                    case 'cancelled':
                        $task->mapped_status = 'done';
                        break;
                    default:
                        $task->mapped_status = 'pending';
                }
                return $task;
            });

            $groupedTasks = $mappedTasks->groupBy('mapped_status');

            $todoHtml = [];
            $taskHtml = [];
            $statuses = ['pending', 'in_progress', 'done'];
            
            foreach ($statuses as $status) {
                $statusTodos = $groupedTodos->get($status, collect());
                $statusTasks = $groupedTasks->get($status, collect());

                $todoHtml[$status] = view('team.todo.partials.todo-cards', compact('statusTodos'))->render();
                $taskHtml[$status] = view('team.todo.partials.task-cards', compact('statusTasks'))->render();
            }

            return response()->json([
                'success' => true,
                'todos' => $todoHtml,
                'tasks' => $taskHtml ?? [],
                'counts' => [
                    'total' => $todos->count(),
                    'pending' => $groupedTodos->get('pending', collect())->count(),
                    'in_progress' => $groupedTodos->get('in_progress', collect())->count(),
                    'done' => $groupedTodos->get('done', collect())->count(),
                ],
                'taskCounts' => [
                    'total' => $mappedTasks->count(),
                    'pending' => $groupedTasks->get('pending', collect())->count(),
                    'in_progress' => $groupedTasks->get('in_progress', collect())->count(),
                    'done' => $groupedTasks->get('done', collect())->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading todos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $todo = Todo::findOrFail($id);
            $todo->status = $request->status_id;
            $todo->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully.', 'todo' => $todo]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    public function updateTaskStatus(Request $request, $id)
    {
        // try {
            $task = Task::findOrFail($id);
            $currentUser = auth()->user();
            
            // Store old status for activity logging
            $oldStatus = $task->status;
            
            // Get the appropriate task status based on the simple status
            $statusMapping = [
                'pending' => ['pending', 'to-do'],
                'in_progress' => ['in-progress', 'review'],
                'done' => ['completed']
            ];
            
            $targetStatus = $request->status_id;
            $taskStatusSlugs = $statusMapping[$targetStatus] ?? ['pending'];
            // Find the first available status
            $taskStatus = TaskStatus::whereIn('slug', $taskStatusSlugs)
                ->where('is_active', true)
                ->first();
           
            
            if (!$taskStatus) {
                // Fallback to default statuses
                $defaultStatusSlug = $targetStatus === 'done' ? 'completed' : 'pending';
                $taskStatus = TaskStatus::where('slug', $defaultStatusSlug)
                    ->where('is_active', true)
                    ->first();
            }
            if ($taskStatus) {
                // Update task status and metadata
                $task->status_id = $taskStatus->id;
                $task->updated_by = $currentUser->id;
                
                // Handle status-specific logic
                if ($targetStatus == 'done' && $taskStatus->is_completed) {
                    // Marking as completed
                    $task->completed_at = now();
                    $task->progress = 100;
                    
                    // Log completion activity
                    $task->logActivity('status_changed', $currentUser, [
                        'field' => 'status_id',
                        'old_value' => $oldStatus->name ?? 'Unknown',
                        'new_value' => $taskStatus->name,
                        'description' => "Task completed via drag and drop",
                        'metadata' => [
                            'completed_at' => now()->toISOString(),
                            'progress_updated' => '100%',
                            'method' => 'drag_drop'
                        ]
                    ]);
                    
                } elseif ($targetStatus == 'in_progress') {
                    // Moving to in progress
                    $task->completed_at = null;
                    
                    // Set progress if not already set
                    if ($task->progress <= 0) {
                        $task->progress = 25;
                    }
                    
                    // Log start activity
                    $task->logActivity('status_changed', $currentUser, [
                        'field' => 'status_id',
                        'old_value' => $oldStatus->name ?? 'Unknown',
                        'new_value' => $taskStatus->name,
                        'description' => "Task moved to in progress via drag and drop",
                        'metadata' => [
                            'started_at' => now()->toISOString(),
                            'progress_updated' => $task->progress . '%',
                            'method' => 'drag_drop'
                        ]
                    ]);
                    
                } else {
                    // Moving to pending or other status
                    $task->completed_at = null;
                    
                    // Log general status change
                    $task->logActivity('status_changed', $currentUser, [
                        'field' => 'status_id',
                        'old_value' => $oldStatus->name ?? 'Unknown',
                        'new_value' => $taskStatus->name,
                        'description' => "Task status changed via drag and drop",
                        'metadata' => [
                            'changed_at' => now()->toISOString(),
                            'method' => 'drag_drop',
                            'from_status' => $oldStatus->slug ?? 'unknown',
                            'to_status' => $taskStatus->slug
                        ]
                    ]);
                }
                
                // Save the task
                $task->save();
                
                // Load fresh data with relationships for response
                $task = $task->fresh(['status', 'priority', 'category', 'assignees', 'activityLogs.user']);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Task status updated successfully.', 
                    'task' => $task,
                    'timeline' => [
                        'action' => 'status_changed',
                        'old_status' => $oldStatus->name ?? 'Unknown',
                        'new_status' => $taskStatus->name,
                        'updated_by' => $currentUser->name,
                        'updated_at' => now()->toISOString(),
                        'progress' => $task->progress
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Could not find appropriate task status.'
                ]);
            }
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false, 
        //         'message' => 'Error updating task status: ' . $e->getMessage()
        //     ]);
        // }
    }


    public function store(Request $request)
    {
        try {
            if($request->schedule=='1'){
                $due_date = Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
            }else{
                $due_date = date('Y-m-d');
            }
            if($request->assign=='1'){
                $user_id = $request->user_id;
            }else{
                $user_id = auth()->user()->id;
            }
            $todo = Todo::create([
                'title' => $request->title,
                'user_id' => $user_id,
                'status' => 'pending',
                'due_date' => $due_date,
                'added_by' => auth()->user()->id,
                'description' => $request->description
            ]);

            return back()->with(['success' => 'Task created successfully.']);
        } catch (\Exception $e) {
            return back()->with(['error' => 'Error creating todo: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date_format:d/m/Y',
            'user_id' => 'nullable|exists:users,id',
        ]);

        try {
            $todo = Todo::findOrFail($id);

            // Handle due date
            $due_date = null;
            if($request->edit_schedule == '1' && $request->due_date) {
                $due_date = Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
            }

            // Handle user assignment
            $user_id = $todo->user_id; // Keep current assignment if not changed
            if($request->edit_assign == '1' && $request->user_id) {
                $user_id = $request->user_id;
            } elseif($request->edit_assign != '1') {
                // If assignment is unchecked, assign to current user
                $user_id = auth()->user()->id;
            }

            $todo->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $due_date,
                'user_id' => $user_id,
            ]);

            return back()->with([
                'success' => "Task '{$todo->title}' updated successfully."
            ]);
        } catch (\Exception $e) {
            return back()->with(['error' => 'Error updating todo: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $todo = Todo::findOrFail($id);
            $todo->delete();
            return back()->with(['success' => 'Task deleted successfully.']);
        } catch (\Exception $e) {
            return back()->with(['error' => 'Error deleting todo: ' . $e->getMessage()]);
        }

    }

}
