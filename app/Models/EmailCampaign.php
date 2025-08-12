<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'trigger_type',
        'execution_type',
        'scheduled_at',
        'schedule_frequency',
        'schedule_config',
        'next_run_at',
        'last_run_at',
        'lead_filters',
        'apply_to_new_leads',
        'trigger_conditions',
        'email_template_id',
        'delay_minutes',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'schedule_config' => 'array',
        'lead_filters' => 'array',
        'scheduled_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
        'apply_to_new_leads' => 'boolean'
    ];

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id', 'id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(EmailAutomationRule::class, 'campaign_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmailAutomationLog::class, 'campaign_id');
    }

    /**
     * Check if campaign conditions are met for a lead
     */
    public function shouldTriggerForLead(ClientLead $lead): bool
    {
        if (!$this->is_active) {
            return false;
        }

        foreach ($this->rules as $rule) {
            if (!$rule->evaluateForLead($lead)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get campaigns by trigger type
     */
    public static function getByTriggerType(string $triggerType)
    {
        return self::where('trigger_type', $triggerType)
                   ->where('is_active', true)
                   ->with(['rules', 'emailTemplate'])
                   ->get();
    }
}
