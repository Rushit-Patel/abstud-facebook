<?php

namespace App\Repositories\Team;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class TodoRepository
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get todo tasks (tasks due today + recurring tasks) with permissions
     */
    public function getTodoTasks(?User $user = null): Builder
    {
        $user = $user ?: auth()->user();
        $today = Carbon::today();

        // Get base tasks with permissions
        $query = $this->taskRepository->getTasks($user);

        // Filter for todo tasks
        $query->where(function ($q) use ($today) {
            // Tasks due today (not completed)
            $q->where(function ($subQ) use ($today) {
                $subQ->whereDate('due_date', $today)
                    ->whereHas('status', function ($statusQ) {
                        $statusQ->where('is_completed', false);
                    });
            })
            // OR recurring tasks that should appear today
            ->orWhere(function ($subQ) use ($today) {
                $subQ->where('is_recurring', true)
                    ->where(function ($recurringQ) use ($today) {
                        // Active recurring tasks
                        $recurringQ->where(function ($activeQ) use ($today) {
                            $activeQ->whereNull('repeat_until')
                                ->orWhere('repeat_until', '>=', $today);
                        })
                        // Check if today matches the recurring pattern
                        ->where(function ($patternQ) use ($today) {
                            $this->applyRecurringLogic($patternQ, $today);
                        });
                    });
            });
        });

        return $query;
    }

    /**
     * Get all tasks with permissions (for task view option)
     */
    public function getAllTasks(?User $user = null): Builder
    {
        $user = $user ?: auth()->user();
        
        // Get base tasks with permissions - no date filtering
        return $this->taskRepository->getTasks($user);
    }

    /**
     * Apply recurring logic to determine if task should appear today
     */
    private function applyRecurringLogic(Builder $query, Carbon $today): void
    {
        $query->where(function ($q) use ($today) {
            // Daily recurring tasks
            $q->where('repeat_mode', 'daily')
                ->where(function ($dailyQ) use ($today) {
                    $dailyQ->whereRaw('DATE(created_at) <= ?', [$today->toDateString()])
                        ->whereRaw('(DATEDIFF(?, DATE(created_at)) % repeat_interval) = 0', [$today->toDateString()]);
                });
        })
        ->orWhere(function ($q) use ($today) {
            // Weekly recurring tasks
            $q->where('repeat_mode', 'weekly')
                ->where(function ($weeklyQ) use ($today) {
                    $weeklyDayNum = $today->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                    $weeklyQ->whereJsonContains('repeat_days', $weeklyDayNum)
                        ->whereRaw('DATE(created_at) <= ?', [$today->toDateString()])
                        ->whereRaw('(WEEK(?) - WEEK(DATE(created_at))) % repeat_interval = 0', [$today->toDateString()]);
                });
        })
        ->orWhere(function ($q) use ($today) {
            // Monthly recurring tasks
            $q->where('repeat_mode', 'monthly')
                ->where(function ($monthlyQ) use ($today) {
                    $monthlyQ->whereRaw('DATE(created_at) <= ?', [$today->toDateString()])
                        ->where(function ($dayQ) use ($today) {
                            // Same day of month
                            $dayQ->whereRaw('DAY(created_at) = ?', [$today->day])
                                ->whereRaw('(PERIOD_DIFF(DATE_FORMAT(?, "%Y%m"), DATE_FORMAT(DATE(created_at), "%Y%m")) % repeat_interval) = 0', [$today->toDateString()]);
                        });
                });
        })
        ->orWhere(function ($q) use ($today) {
            // Yearly recurring tasks
            $q->where('repeat_mode', 'yearly')
                ->where(function ($yearlyQ) use ($today) {
                    $yearlyQ->whereRaw('DATE(created_at) <= ?', [$today->toDateString()])
                        ->whereRaw('MONTH(created_at) = ? AND DAY(created_at) = ?', [$today->month, $today->day])
                        ->whereRaw('(YEAR(?) - YEAR(DATE(created_at))) % repeat_interval = 0', [$today->toDateString()]);
                });
        });
    }

    /**
     * Get todo tasks grouped by status for kanban board
     */
    public function getTodoTasksGrouped(?User $user = null): array
    {
        $tasks = $this->getTodoTasks($user)->get();

        $grouped = [
            'pending' => [],
            'in_progress' => [],
            'done' => []
        ];

        foreach ($tasks as $task) {
            $statusKey = $this->getStatusKey($task->status);
            if (isset($grouped[$statusKey])) {
                $grouped[$statusKey][] = $task;
            }
        }

        return $grouped;
    }

    /**
     * Get counts for each status
     */
    public function getTodoTasksCounts(?User $user = null): array
    {
        $tasks = $this->getTodoTasks($user)->get();

        $counts = [
            'total' => $tasks->count(),
            'pending' => 0,
            'in_progress' => 0,
            'done' => 0
        ];

        foreach ($tasks as $task) {
            $statusKey = $this->getStatusKey($task->status);
            if (isset($counts[$statusKey])) {
                $counts[$statusKey]++;
            }
        }

        return $counts;
    }

    /**
     * Convert task status to kanban status key
     */
    private function getStatusKey($status): string
    {
        if (!$status) return 'pending';

        // Map status names to kanban columns
        $statusName = strtolower($status->name);
        
        if (in_array($statusName, ['completed', 'done', 'finished', 'closed'])) {
            return 'done';
        } elseif (in_array($statusName, ['in progress', 'working', 'active', 'started'])) {
            return 'in_progress';
        } else {
            return 'pending';
        }
    }

    /**
     * Create next recurring task instance
     */
    public function createNextRecurringInstance(Task $task): ?Task
    {
        if (!$task->is_recurring) {
            return null;
        }

        $nextDueDate = $this->calculateNextDueDate($task);
        
        if (!$nextDueDate) {
            return null;
        }

        // Create new task instance
        $newTask = $task->replicate();
        $newTask->due_date = $nextDueDate;
        $newTask->progress = 0;
        $newTask->completed_at = null;
        $newTask->actual_hours = 0;
        
        // Reset to initial status
        $initialStatus = $task->category->taskStatuses()->where('is_initial', true)->first();
        if ($initialStatus) {
            $newTask->status_id = $initialStatus->id;
        }

        $newTask->save();

        // Copy assignees
        if ($task->assignees()->count() > 0) {
            $newTask->assignees()->sync($task->assignees->pluck('id'));
        }

        return $newTask;
    }

    /**
     * Calculate next due date for recurring task
     */
    private function calculateNextDueDate(Task $task): ?Carbon
    {
        if (!$task->due_date || !$task->is_recurring) {
            return null;
        }

        $currentDue = Carbon::parse($task->due_date);
        $interval = $task->repeat_interval ?? 1;

        switch ($task->repeat_mode) {
            case 'daily':
                return $currentDue->addDays($interval);
                
            case 'weekly':
                return $currentDue->addWeeks($interval);
                
            case 'monthly':
                return $currentDue->addMonths($interval);
                
            case 'yearly':
                return $currentDue->addYears($interval);
                
            default:
                return null;
        }
    }
}
