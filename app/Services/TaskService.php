<?php

// Service Class: TaskService.php
// File: app/Services/TaskService.php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskTimeLog;
use App\Models\User;
use App\Models\TaskAssignment;
use App\Models\TaskStatus;
use App\Notifications\AssignTaskNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function createTask(array $data, User $creator): Task
    {
        $data['start_date'] = Carbon::createFromFormat('d/m/Y H:i', $data['start_date'])->format('Y-m-d H:i');
        if(isset($data['due_date'])) {
            $data['due_date'] = Carbon::createFromFormat('d/m/Y H:i', $data['due_date'])->format('Y-m-d H:i');
        } else {
            $data['due_date'] = null;
        }
        if(isset($data['repeat_until'])) {
            $data['repeat_until'] = Carbon::createFromFormat('d/m/Y', $data['repeat_until'])->format('Y-m-d');
        } else {
            $data['repeat_until'] = null;
        }
        return DB::transaction(function () use ($data, $creator) {
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'priority_id' => $data['priority_id'] ?? null,
                'status_id' => $data['status_id'],
                'created_by' => $creator->id,
                'start_date' => $data['start_date'],
                'due_date' => $data['due_date'] ?? null,
                'estimated_hours' => $data['estimated_hours'] ?? null,
                'tags' => $data['tags'] ?? [],
                'metadata' => $data['metadata'] ?? [],
                'is_recurring' => $data['is_recurring'] ?? false,
                'repeat_mode' => $data['repeat_mode'] ?? null,
                'repeat_interval' => $data['repeat_interval'] ?? null,
                'repeat_until' => $data['repeat_until'] ?? null,
            ]);

            // Assign users if provided
            if (!empty($data['assignees'])) {
                $this->assignMultipleUsers($task, $data['assignees'], $creator);
            }

            // Log activity
            $task->logActivity('created', $creator, [
                'description' => "Task '{$task->title}' was created"
            ]);

            return $task->load(['assignees', 'category', 'priority', 'status', 'creator']);
        });
    }

    public function updateTask(Task $task, array $data, User $updater): Task
    {
        $data['start_date'] = Carbon::createFromFormat('d/m/Y H:i', $data['start_date'])->format('Y-m-d H:i');
        if(isset($data['due_date'])) {
            $data['due_date'] = Carbon::createFromFormat('d/m/Y H:i', $data['due_date'])->format('Y-m-d H:i');
        } else {
            $data['due_date'] = null;

        }
        return DB::transaction(function () use ($task, $data, $updater) {
            $originalData = $task->toArray();
            
            $task->update([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'category_id' => $data['category_id'] ?? $task->category_id,
                'priority_id' => $data['priority_id'] ?? $task->priority_id,
                'status_id' => $data['status_id'] ?? $task->status_id,
                'updated_by' => $updater->id,
                'start_date' => $data['start_date'] ?? $task->start_date,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'estimated_hours' => $data['estimated_hours'] ?? $task->estimated_hours,
                'actual_hours' => $data['actual_hours'] ?? $task->actual_hours,
                'progress' => $data['progress'] ?? $task->progress,
                'tags' => $data['tags'] ?? $task->tags,
                'metadata' => $data['metadata'] ?? $task->metadata,
                'is_recurring' => $data['is_recurring'] ?? $task->is_recurring,
                'repeat_mode' => $data['repeat_mode'] ?? $task->repeat_mode,
                'repeat_interval' => $data['repeat_interval'] ?? $task->repeat_interval,
                'repeat_until' => $data['repeat_until'] ?? $task->repeat_until,
            ]);

            // Handle status change
            if (isset($data['status_id']) && $data['status_id'] != $originalData['status_id']) {
                $this->handleStatusChange($task, $originalData['status_id'], $data['status_id'], $updater);
            }

            // Update assignees if provided
            if (isset($data['assignees'])) {
                $this->updateTaskAssignments($task, $data['assignees'], $updater);
            }

            // Log changes
            $this->logTaskChanges($task, $originalData, $updater);

            return $task->fresh(['assignees', 'category', 'priority', 'status', 'creator', 'updater']);
        });
    }

    public function assignMultipleUsers(Task $task, array $assignees, User $assignedBy): Collection
    {
        $assignments = new Collection();

        foreach ($assignees as $assigneeData) {
            $user = User::find(id: $assigneeData['user_id']);
            if (!$user) continue;

            // Check if already assigned
            $existingAssignment = $task->assignments()
                ->where('user_id', $user->id)
                ->where('role', $assigneeData['role'] ?? 'assignee')
                ->where('is_active', true)
                ->first();

            if (!$existingAssignment) {
                $assignment = $task->assignUser($user, $assignedBy, [
                    'role' => $assigneeData['role'] ?? 'assignee',
                    'notes' => $assigneeData['notes'] ?? null,
                    'estimated_hours' => $assigneeData['estimated_hours'] ?? null,
                ]);

                $assignments->push($assignment);

                // Log activity
                $task->logActivity('assigned', $assignedBy, [
                    'description' => "Task assigned to {$user->name} as {$assignment->role}",
                    'metadata' => ['assigned_user_id' => $user->id, 'role' => $assignment->role]
                ]);

                // Send assignment notification
                try {
                    $notification = new AssignTaskNotification($task, $user);
                    $notification->send();
                } catch (\Exception $e) {
                    \Log::error('Failed to send task assignment notification: ' . $e->getMessage(), [
                        'task_id' => $task->id,
                        'assigned_user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $assignments;
    }

    public function unassignUser(Task $task, User $user, User $unassignedBy, string $role = 'assignee'): bool
    {
        $assignment = $task->assignments()
            ->where('user_id', $user->id)
            ->where('role', $role)
            ->where('is_active', true)
            ->first();

        if ($assignment) {
            $assignment->update(['is_active' => false]);

            $task->logActivity('unassigned', $unassignedBy, [
                'description' => "Task unassigned from {$user->name}",
                'metadata' => ['unassigned_user_id' => $user->id, 'role' => $role]
            ]);

            return true;
        }

        return false;
    }

    public function changeTaskStatus(Task $task, int $statusId, User $user): Task
    {
        $oldStatus = $task->status;
        $newStatus = TaskStatus::find($statusId);

        if (!$newStatus) {
            throw new \InvalidArgumentException('Invalid status ID');
        }

        return DB::transaction(function () use ($task, $newStatus, $oldStatus, $user) {
            $task->update([
                'status_id' => $newStatus->id,
                'updated_by' => $user->id
            ]);

            $this->handleStatusChange($task, $oldStatus->id, $newStatus->id, $user);

            return $task->fresh(['status']);
        });
    }

    public function completeTask(Task $task, User $user, array $data = []): Task
    {
        $completedStatus = TaskStatus::where('is_completed', true)
            ->where('slug', 'completed')
            ->first();

        if (!$completedStatus) {
            throw new \Exception('Completed status not found');
        }

        return DB::transaction(function () use ($task, $completedStatus, $user, $data) {
            $task->update([
                'status_id' => $completedStatus->id,
                'completed_at' => now(),
                'progress' => 100,
                'actual_hours' => $data['actual_hours'] ?? $task->actual_hours,
                'updated_by' => $user->id
            ]);

            // Mark user assignments as completed
            $task->assignments()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['completed_at' => now()]);

            $task->logActivity('completed', $user, [
                'description' => "Task completed by {$user->name}"
            ]);

            // Send completion notification to task creator and assignees
            try {
                // Notify task creator if different from the completing user
                if ($task->creator && $task->creator->id !== $user->id) {
                    $notification = new \App\Notifications\TaskCompletedNotification([
                        'task' => $task,
                        'completed_by' => $user,
                        'recipient' => $task->creator
                    ]);
                    $notification->send($task->creator->id);
                }

                // Notify assignees (except the one who completed it)
                foreach ($task->assignees as $assignee) {
                    if ($assignee->id !== $user->id) {
                        $notification = new TaskCompletedNotification([
                            'task' => $task,
                            'completed_by' => $user,
                            'recipient' => $assignee
                        ]);
                        $notification->send($assignee->id);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send task completion notification: ' . $e->getMessage(), [
                    'task_id' => $task->id,
                    'completed_by' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            return $task->fresh();
        });
    }

    public function archiveTask(Task $task, User $user): Task
    {
        $task->update([
            'is_archived' => true,
            'updated_by' => $user->id
        ]);

        $task->logActivity('archived', $user, [
            'description' => "Task archived by {$user->name}"
        ]);

        return $task;
    }

    public function restoreTask(Task $task, User $user): Task
    {
        $task->update([
            'is_archived' => false,
            'updated_by' => $user->id
        ]);

        $task->logActivity('restored', $user, [
            'description' => "Task restored by {$user->name}"
        ]);

        return $task;
    }

    public function getUserTasks(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->assignedTasks()
            ->with(['category', 'priority', 'status', 'creator']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status_id', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority_id', $filters['priority']);
        }

        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->overdue();
        }

        if (isset($filters['completed'])) {
            if ($filters['completed']) {
                $query->completed();
            } else {
                $query->pending();
            }
        }

        if (isset($filters['archived'])) {
            if ($filters['archived']) {
                $query->archived();
            } else {
                $query->active();
            }
        }

        // Search
        if (isset($filters['search'])) {
            $query->whereFullText(['title', 'description'], $filters['search']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        if ($sortBy === 'priority') {
            $query->byPriority($sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getTaskStats(User $user = null): array
    {
        $query = Task::query();

        if ($user) {
            $query->assignedTo($user->id);
        }

        return [
            'total' => $query->count(),
            'active' => $query->active()->count(),
            'completed' => $query->completed()->count(),
            'overdue' => $query->overdue()->count(),
            'in_progress' => $query->whereHas('status', fn($q) => $q->where('slug', 'in-progress'))->count(),
            'pending' => $query->whereHas('status', fn($q) => $q->where('slug', 'pending'))->count(),
        ];
    }

    public function addTaskComment(Task $task, User $user, string $content, array $options = []): TaskComment
    {
        $comment = $task->comments()->create([
            'user_id' => $user->id,
            'parent_id' => $options['parent_id'] ?? null,
            'content' => $content,
            'mentions' => $options['mentions'] ?? [],
            'is_internal' => $options['is_internal'] ?? false,
        ]);

        $task->logActivity('commented', $user, [
            'description' => "Comment added by {$user->name}"
        ]);

        return $comment;
    }

    public function logTime(Task $task, User $user, array $data): TaskTimeLog
    {
        $timeLog = $task->timeLogs()->create([
            'user_id' => $user->id,
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'description' => $data['description'] ?? null,
            'is_billable' => $data['is_billable'] ?? false,
            'hourly_rate' => $data['hourly_rate'] ?? null,
        ]);

        // Update assignment logged hours
        $assignment = $task->assignments()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if ($assignment && $timeLog->duration_minutes) {
            $assignment->increment('logged_hours', round($timeLog->duration_minutes / 60, 2));
        }

        $task->logActivity('time_logged', $user, [
            'description' => "Time logged: {$timeLog->duration_hours} hours"
        ]);

        return $timeLog;
    }

    private function handleStatusChange(Task $task, int $oldStatusId, int $newStatusId, User $user): void
    {
        $oldStatus = TaskStatus::find($oldStatusId);
        $newStatus = TaskStatus::find($newStatusId);

        // If moving to completed status
        if ($newStatus->is_completed && !$oldStatus->is_completed) {
            $task->update(['completed_at' => now(), 'progress' => 100]);
            
            // Send completion notification to task creator and assignees
            try {
                // Notify task creator if different from the completing user
                if ($task->creator && $task->creator->id !== $user->id) {
                    $notification = new TaskCompletedNotification([
                        'task' => $task,
                        'completed_by' => $user,
                        'recipient' => $task->creator
                    ]);
                    $notification->send($task->creator->id);
                }

                // Notify assignees (except the one who completed it)
                foreach ($task->assignees as $assignee) {
                    if ($assignee->id !== $user->id) {
                        $notification = new TaskCompletedNotification([
                            'task' => $task,
                            'completed_by' => $user,
                            'recipient' => $assignee
                        ]);
                        $notification->send($assignee->id);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send task completion notification: ' . $e->getMessage(), [
                    'task_id' => $task->id,
                    'completed_by' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // If moving from completed to non-completed
        if (!$newStatus->is_completed && $oldStatus->is_completed) {
            $task->update(['completed_at' => null]);
        }

        $task->logActivity('status_changed', $user, [
            'field' => 'status_id',
            'old_value' => $oldStatus->name,
            'new_value' => $newStatus->name,
            'description' => "Status changed from '{$oldStatus->name}' to '{$newStatus->name}'"
        ]);
    }

    private function updateTaskAssignments(Task $task, array $assignees, User $updater): void
    {
        // Get current assignments
        $currentAssignments = $task->assignments()->where('is_active', true)->get();
        $newAssigneeIds = collect(value: $assignees)->pluck('user_id');

        // Deactivate removed assignments
        foreach ($currentAssignments as $assignment) {
            if (!$newAssigneeIds->contains($assignment->user_id)) {
                $this->unassignUser($task, $assignment->user, $updater, $assignment->role);
            }
        }

        // Add new assignments
        $this->assignMultipleUsers($task, $assignees, $updater);
    }

    private function logTaskChanges(Task $task, array $originalData, User $updater): void
    {
        $changes = $task->getChanges();
        unset($changes['updated_at'], $changes['updated_by']);

        foreach ($changes as $field => $newValue) {
            $oldValue = $originalData[$field] ?? null;

            if ($field === 'status_id') continue; // Already handled in handleStatusChange

            $task->logActivity('updated', $updater, [
                'field' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'description' => "Field '{$field}' updated"
            ]);
        }
    }

    /**
     * Update task progress only
     */
    public function updateProgress(Task $task, int $progress, User $updater): Task
    {
        return DB::transaction(function () use ($task, $progress, $updater) {
            $oldProgress = $task->progress;
            
            $task->update([
                'progress' => $progress,
                'updated_by' => $updater->id,
            ]);

            // Log the progress change
            if ($oldProgress !== $progress) {
                $task->logActivity('updated', $updater, [
                    'field' => 'progress',
                    'old_value' => $oldProgress,
                    'new_value' => $progress,
                    'description' => "Progress updated from {$oldProgress}% to {$progress}%"
                ]);
            }

            return $task->fresh();
        });
    }
}