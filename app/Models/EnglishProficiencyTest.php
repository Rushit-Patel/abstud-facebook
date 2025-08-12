<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnglishProficiencyTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'result_days',
        'status',
        'priority',
        'coaching_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope: Get only active lead types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function moduals()
    {
        return $this->hasMany(EnglishProficiencyTestModual::class, 'english_proficiency_tests_id');
    }

}
