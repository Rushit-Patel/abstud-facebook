<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAssignment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_by',
        'role',
        'assignment_notes',
        'assigned_at',
        'accepted_at',
        'completed_at',
        'estimated_hours',
        'logged_hours',
        'is_active',
        'notifications_enabled',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'integer',
        'logged_hours' => 'integer',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
        'notifications_enabled' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaskComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'parent_id');
    }
}
