<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
class Task extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'priority_id',
        'status_id',
        'created_by',
        'updated_by',
        'start_date',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress',
        'tags',
        'metadata',
        'is_archived',
        'is_recurring',
        'repeat_mode',
        'repeat_interval',
        'repeat_days',
        'repeat_until',
        'repeat_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'repeat_until' => 'date',
        'tags' => 'array',
        'metadata' => 'array',
        'repeat_days' => 'array',
        'is_archived' => 'boolean',
        'progress' => 'integer',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'deleted_at' => 'datetime',
        'is_recurring' => 'boolean'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class)->where('is_active', true);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments')
                    ->withPivot([
                        'assigned_by', 'role', 'assignment_notes', 'assigned_at',
                        'accepted_at', 'completed_at', 'estimated_hours', 'logged_hours',
                        'is_active', 'notifications_enabled'
                    ])
                    ->withTimestamps()
                    ->wherePivot('is_active', true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class)->orderBy('created_at', 'desc');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TaskTimeLog::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function dependentTasks(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereHas('status', fn($q) => $q->where('is_completed', true));
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereHas('status', fn($q) => $q->where('is_completed', false));
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                    ->whereHas('status', fn($q) => $q->where('is_completed', false));
    }

    public function scopeAssignedTo(Builder $query, $userId): Builder
    {
        return $query->whereHas('assignees', fn($q) => $q->where('users.id', $userId));
    }

    public function scopeByPriority(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->join('task_priorities', 'tasks.priority_id', '=', 'task_priorities.id')
                    ->orderBy('task_priorities.level', $direction);
    }

    // Accessors & Mutators
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               !$this->status->is_completed;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status->is_completed;
    }

    public function getTotalLoggedHoursAttribute(): int
    {
        return $this->timeLogs()->sum('duration_minutes') / 60;
    }

    // Helper methods
    public function assignUser(User $user, User $assignedBy, array $options = []): TaskAssignment
    {
        return $this->assignments()->create([
            'user_id' => $user->id,
            'assigned_by' => $assignedBy->id,
            'role' => $options['role'] ?? 'assignee',
            'assignment_notes' => $options['notes'] ?? null,
            'estimated_hours' => $options['estimated_hours'] ?? null,
            'assigned_at' => now(),
        ]);
    }

    public function logActivity(string $action, ?User $user = null, array $data = []): void
    {
        $this->activityLogs()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'field' => $data['field'] ?? null,
            'old_value' => $data['old_value'] ?? null,
            'new_value' => $data['new_value'] ?? null,
            'description' => $data['description'] ?? "Task {$action}",
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    public function recurringInstances(): HasMany
    {
        return $this->hasMany(RecurringTaskInstance::class, 'parent_task_id');
    }

}
