<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoachingMaterialStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'coaching_material_id',
        'branch_id',
        'stock',
        'stock_date',
        'added_by',
        'remarks',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope: Get only active lead types
     */
    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getBranch()
    {
        return $this->belongsTo(Branch::class,'branch_id' ,'id');
    }

    public function getAddedBy()
    {
        return $this->belongsTo(User::class,'added_by' ,'id');
    }
}
