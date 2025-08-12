<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSubStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
        'lead_status_id',
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the country that this state belongs to
     */
    public function leadStatus(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }

    /**
     * Scope: Get only active states
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

}
