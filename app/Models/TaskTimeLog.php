<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTimeLog extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration_minutes',
        'description',
        'is_billable',
        'hourly_rate',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer',
        'is_billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
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

    public function getDurationHoursAttribute(): float
    {
        return $this->duration_minutes ? round($this->duration_minutes / 60, 2) : 0;
    }

    public function getTotalCostAttribute(): float
    {
        if (!$this->is_billable || !$this->hourly_rate || !$this->duration_minutes) {
            return 0;
        }

        return ($this->duration_minutes / 60) * $this->hourly_rate;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($timeLog) {
            if ($timeLog->started_at && $timeLog->ended_at) {
                $timeLog->duration_minutes = $timeLog->started_at->diffInMinutes($timeLog->ended_at);
            }
        });
    }
}
