<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status'
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
}
