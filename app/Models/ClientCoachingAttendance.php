<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCoachingAttendance extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_coaching_id',
            'batch_id',
            'attendance_date',
            'added_by',
            'status',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
