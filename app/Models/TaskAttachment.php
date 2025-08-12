<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'file_path',
        'original_name',
        'file_size',
        'mime_type'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
