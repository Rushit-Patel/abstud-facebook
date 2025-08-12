<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookLeadForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_page_id',
        'form_name',
        'facebook_form_id',
        'form_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function facebookPage(): BelongsTo
    {
        return $this->belongsTo(FacebookPage::class);
    }

    public function facebookLeads(): HasMany
    {
        return $this->hasMany(FacebookLead::class);
    }

    public function facebookParameterMappings(): HasMany
    {
        return $this->hasMany(FacebookParameterMapping::class);
    }

    public function facebookCustomFieldMappings(): HasMany
    {
        return $this->hasMany(FacebookCustomFieldMapping::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getTotalLeadsCount(): int
    {
        return $this->facebookLeads()->count();
    }

    public function getNewLeadsCount(): int
    {
        return $this->facebookLeads()->where('status', 'new')->count();
    }
}
