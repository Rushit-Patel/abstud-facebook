<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookWebhookSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_business_account_id',
        'webhook_url',
        'verify_token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'verify_token',
    ];

    // Relationships
    public function facebookBusinessAccount(): BelongsTo
    {
        return $this->belongsTo(FacebookBusinessAccount::class);
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

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function regenerateVerifyToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['verify_token' => $token]);
        return $token;
    }
}
