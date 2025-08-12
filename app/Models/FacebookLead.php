<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FacebookLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_lead_form_id',
        'facebook_lead_id',
        'name',
        'email',
        'phone',
        'additional_data',
        'facebook_created_time',
        'status',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'facebook_created_time' => 'datetime',
    ];

    // Relationships
    public function facebookLeadForm(): BelongsTo
    {
        return $this->belongsTo(FacebookLeadForm::class);
    }

    public function facebookLeadSource(): HasOne
    {
        return $this->hasOne(FacebookLeadSource::class);
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('facebook_created_time', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('facebook_created_time', today());
    }

    // Helper Methods
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsProcessed(): void
    {
        $this->update(['status' => 'processed']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function getFullName(): string
    {
        return $this->name ?? 'Unknown';
    }

    public function getContactInfo(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
