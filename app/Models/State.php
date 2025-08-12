<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'id',
        'country_id',
        'name',
        'state_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the country that this state belongs to
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all cities belonging to this state
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Scope: Get only active states
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full state name with country
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->country->name;
    }
}
