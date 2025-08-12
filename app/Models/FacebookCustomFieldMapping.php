<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookCustomFieldMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_lead_form_id',
        'facebook_custom_question',
        'system_field_name',
        'data_type',
        'is_active',
    ];

    protected $casts = [
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

    public function scopeByDataType($query, $type)
    {
        return $query->where('data_type', $type);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isTextType(): bool
    {
        return $this->data_type === 'text';
    }

    public function isNumberType(): bool
    {
        return $this->data_type === 'number';
    }

    public function isDateType(): bool
    {
        return $this->data_type === 'date';
    }

    public function isBooleanType(): bool
    {
        return $this->data_type === 'boolean';
    }
}
