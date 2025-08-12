<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'message_id',
        'phone_number',
        'message_type',
        'message_content',
        'template_name',
        'template_variables',
        'status',
        'provider_response',
        'error_message',
        'retry_count',
        'is_test',
        'created_by',
        'scheduled_at',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'template_variables' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_test' => 'boolean',
    ];

    /**
     * Get the campaign this message belongs to
     */
    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'campaign_id');
    }

    /**
     * Get the user who created this message
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get only pending messages
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get only sent messages
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope: Get only failed messages
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark message as sent
     */
    public function markAsSent($messageId = null, $providerResponse = null)
    {
        $this->update([
            'status' => 'sent',
            'message_id' => $messageId,
            'provider_response' => $providerResponse,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark message as failed
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Mark message as delivered
     */
    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mutator: Format phone number when setting
     */
    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = $this->formatPhoneNumber($value);
    }

    /**
     * Format phone number to proper WhatsApp format
     */
    public function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return $phone;
        }

        // Remove any non-numeric characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // If phone already starts with +, return as is
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }
        
        // If starts with 0, remove it and add country code
        if (str_starts_with($cleaned, '0')) {
            $cleaned = substr($cleaned, 1);
        }
        
        // Add default country code (+91 for India) if not present
        return '+91' . $cleaned;
    }

    /**
     * Scope: Get messages that are ready to be sent (scheduled_at <= now)
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending')
                     ->where(function($q) {
                         $q->whereNull('scheduled_at')
                           ->orWhere('scheduled_at', '<=', now());
                     });
    }

    /**
     * Scope: Get scheduled messages (not yet ready to send)
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'pending')
                     ->where('scheduled_at', '>', now());
    }
}
