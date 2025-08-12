<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookParameterMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_lead_form_id',
        'facebook_field_name',
        'facebook_field_type',
        'system_field_name',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function facebookLeadForm(): BelongsTo
    {
        return $this->belongsTo(FacebookLeadForm::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeByFieldType($query, $type)
    {
        return $query->where('facebook_field_type', $type);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isRequired(): bool
    {
        return $this->is_required;
    }
}
