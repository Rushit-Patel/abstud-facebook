<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'university_id',
        'campus_id',
        'course_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function getUniversity()
    {
        return $this->hasOne(University::class, 'id', 'university_id');
    }

    public function getCampus()
    {
        return $this->hasOne(Campus::class, 'id', 'campus_id');
    }

    public function getCourse()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    /**
     * Scope: Get only active lead types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
