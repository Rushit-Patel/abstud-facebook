<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoachingMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'coaching',
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
        return $query->where('status', '1');
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getCoaching()
    {
        return $this->belongsTo(Coaching::class,'coaching' ,'id');
    }

    public function TotalStocks()
    {
        return $this->hasMany(CoachingMaterialStock::class);
    }

    public function issuedMaterials()
    {
        return $this->hasMany(ClientCoachingMaterial::class, 'material_id');
    }


}
