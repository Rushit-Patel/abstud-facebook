<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAutomationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_lead_id',
        'campaign_id',
        'email_template_id',
        'recipient_email',
        'subject',
        'status',
        'scheduled_at',
        'sent_at',
        'error_message',
        'email_data',
        'retry_count'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'email_data' => 'array'
    ];

    public function clientLead(): BelongsTo
    {
        return $this->belongsTo(ClientLead::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function performance()
    {
        return $this->hasOne(EmailPerformance::class, 'email_log_id');
    }

    /**
     * Get pending emails ready to be sent
     */
    public static function getPendingEmails()
    {
        return self::where('status', 'pending')
                   ->where('scheduled_at', '<=', now())
                   ->with(['clientLead.client', 'campaign', 'emailTemplate'])
                   ->get();
    }

    /**
     * Mark email as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1
        ]);
    }
}
