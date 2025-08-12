<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'id',
        'f_country_id',
        'f_state_id',
        'f_city_id',
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the state that this city belongs to
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(ForeignCity::class, 'f_city_id');
    }

    /**
     * Get the state that the campus belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(ForeignState::class, 'f_state_id');
    }

    /**
     * Get the country that the campus belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(ForeignCountry::class, 'f_country_id');
    }

    /**
     * Scope: Get only active cities
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
