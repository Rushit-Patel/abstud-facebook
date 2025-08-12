<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskActivityLog extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

