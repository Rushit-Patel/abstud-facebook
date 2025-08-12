<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    //
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'id',
        'user_id',
        'added_by',
        'due_date',
        'title',
        'description',
        'status',
    ];
    protected $casts = [
        'deleted_at' => 'datetime',
        'due_date' => 'date',
    ];

    /**
     * Get the user who is assigned to this todo
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created this todo
     */
    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
