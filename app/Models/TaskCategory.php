<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'category_id');
    }

    public function activeTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'category_id')->where('is_archived', false);
    }

    public function completedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'category_id')
                    ->whereHas('status', fn($q) => $q->where('is_completed', true));
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWithTaskCount(Builder $query): Builder
    {
        return $query->withCount(['tasks', 'activeTasks', 'completedTasks']);
    }

    // Accessors
    public function getTasksCountAttribute(): int
    {
        return $this->tasks()->count();
    }

    public function getActiveTasksCountAttribute(): int
    {
        return $this->activeTasks()->count();
    }

    public function getCompletedTasksCountAttribute(): int
    {
        return $this->completedTasks()->count();
    }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->tasks_count;
        return $total > 0 ? round(($this->completed_tasks_count / $total) * 100, 2) : 0;
    }
}
