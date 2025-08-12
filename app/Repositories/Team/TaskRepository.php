<?php

namespace App\Repositories\Team;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TaskRepository
{
    /**
     * Get tasks with permissions applied
     */
    public function getTasks(?User $user = null): Builder
    {
        $user = $user ?: auth()->user();
        
        $query = Task::query()
            ->with(['category', 'priority', 'status', 'creator', 'assignees'])
            ->active();
        
        $query = $this->applyPermissions($query, $user);
        
        $query = $query->orderBy('tasks.id', 'desc');

        return $query;
    }

    /**
     * Apply permission-based filtering to query
     */
    private function applyPermissions(Builder $query, User $user): Builder
    {
        // If user has show-all permission, return query as is
        if ($user->can('task:show-all')) {
            return $query;
        }

        // Default: show only tasks user created or is assigned to
        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('assignees', function ($subQ) use ($user) {
                  $subQ->where('users.id', $user->id);
              });
        });
    }
}
