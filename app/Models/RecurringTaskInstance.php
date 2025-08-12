<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringTaskInstance extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'parent_task_id', 'instance_task_id', 'scheduled_date', 'instance_number'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'instance_number' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function instanceTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'instance_task_id');
    }
}
