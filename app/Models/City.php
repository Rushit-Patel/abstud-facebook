<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'id',
        'state_id',
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the state that this city belongs to
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country through the state relationship
     */
    public function country(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }

    /**
     * Scope: Get only active cities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full city name with state and country
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->state->name . ', ' . $this->state->country->name;
    }
}
