<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WhatsappCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'campaign_type',
        'trigger_type',
        'trigger_conditions',
        'message_type',
        'message_content',
        'template_name',
        'template_variables',
        'provider_id',
        'delay_minutes',
        'is_active',
        'priority',
        'execution_type',
        'scheduled_at',
        'schedule_frequency',
        'schedule_config',
        'next_run_at',
        'last_run_at',
        'lead_filters',
        'apply_to_new_leads',
        'total_recipients',
        'messages_sent',
        'messages_delivered',
        'messages_failed',
        'created_by',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'template_variables' => 'array',
        'schedule_config' => 'array',
        'lead_filters' => 'array',
        'is_active' => 'boolean',
        'apply_to_new_leads' => 'boolean',
        'scheduled_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name) . '-' . time();
            }
        });
    }

    /**
     * Get the provider for this campaign
     */
    public function provider()
    {
        return $this->belongsTo(WhatsappProvider::class, 'provider_id');
    }

    /**
     * Get the user who created this campaign
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the campaign rules
     */
    public function rules()
    {
        return $this->hasMany(WhatsappCampaignRule::class, 'campaign_id');
    }


    /**
     * Scope: Get only active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get campaigns by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('campaign_type', $type);
    }

    /**
     * Scope: Get campaigns ready to run
     */
    public function scopeReadyToRun($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('execution_type', 'automation')
                  ->orWhere(function ($subQ) {
                      $subQ->where('execution_type', 'one_time')
                           ->where('scheduled_at', '<=', now())
                           ->whereNull('last_run_at');
                  });
            });
    }

    /**
     * Check if campaign can be executed
     */
    public function canExecute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->execution_type === 'one_time') {
            return $this->scheduled_at <= now() && is_null($this->last_run_at);
        }

        return true; // Automation campaigns can always execute based on triggers
    }

    /**
     * Get campaign statistics
     */
    public function getStats(): array
    {
        return [
            'total_recipients' => $this->total_recipients,
            'messages_sent' => $this->messages_sent,
            'messages_delivered' => $this->messages_delivered,
            'messages_failed' => $this->messages_failed,
            'delivery_rate' => $this->messages_sent > 0 ? 
                round(($this->messages_delivered / $this->messages_sent) * 100, 2) : 0,
            'success_rate' => $this->total_recipients > 0 ? 
                round(($this->messages_sent / $this->total_recipients) * 100, 2) : 0,
        ];
    }

    /**
     * Update campaign statistics
     */
    public function updateStats(): void
    {
        $recipients = $this->recipients();
        
        $this->update([
            'total_recipients' => $recipients->count(),
            'messages_sent' => $recipients->whereIn('status', ['sent', 'delivered', 'read'])->count(),
            'messages_delivered' => $recipients->whereIn('status', ['delivered', 'read'])->count(),
            'messages_failed' => $recipients->where('status', 'failed')->count(),
        ]);
    }

    /**
     * Pause the campaign
     */
    public function pause(): void
    {
        $this->update(['is_active' => false]);
        
        $this->logs()->create([
            'event_type' => 'campaign_paused',
            'event_description' => 'Campaign was paused by user',
            'event_data' => ['paused_at' => now()],
        ]);
    }

    /**
     * Resume the campaign
     */
    public function resume(): void
    {
        $this->update(['is_active' => true]);
        
        $this->logs()->create([
            'event_type' => 'campaign_resumed',
            'event_description' => 'Campaign was resumed by user',
            'event_data' => ['resumed_at' => now()],
        ]);
    }

    /**
     * Get formatted schedule description
     */
    public function getScheduleDescription(): string
    {
        if ($this->execution_type === 'one_time') {
            return 'One-time execution' . ($this->scheduled_at ? ' on ' . $this->scheduled_at->format('M j, Y H:i') : '');
        }

        if ($this->schedule_frequency) {
            return 'Runs ' . $this->schedule_frequency;
        }

        return 'Automation trigger based';
    }
}
