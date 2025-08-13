<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_business_account_id',
        'page_name',
        'facebook_page_id',
        'page_access_token',
        'page_category',
        'fan_count',
        'profile_picture_url',
        'is_published',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'fan_count' => 'integer',
    ];

    protected $hidden = [
        'page_access_token',
    ];

    // Relationships
    public function facebookBusinessAccount(): BelongsTo
    {
        return $this->belongsTo(FacebookBusinessAccount::class);
    }

    public function facebookLeadForms(): HasMany
    {
        return $this->hasMany(FacebookLeadForm::class);
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
}
