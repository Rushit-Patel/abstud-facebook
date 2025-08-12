<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCourseValues extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key_id',
        'university_course_id',
        'value'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function getKey()
    {
        return $this->hasOne(UniversityCourseKeys::class, 'id', 'key_id');
    }

    public function getUniversityCourse()
    {
        return $this->hasOne(UniversityCourse::class, 'id', 'university_course_id');
    }

    /**
     * Scope: Get only active lead types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
