<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'field_name',
        'operator',
        'field_value'
    ];

    protected $casts = [
        'field_value' => 'array'
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    /**
     * Evaluate if this rule matches for the given lead
     */
    public function evaluateForLead(ClientLead $lead): bool
    {
        $fieldValue = $this->getLeadFieldValue($lead, $this->field_name);

        return match($this->operator) {
            'equals' => $fieldValue == $this->field_value[0],
            'not_equals' => $fieldValue != $this->field_value[0],
            'in' => in_array($fieldValue, $this->field_value),
            'not_in' => !in_array($fieldValue, $this->field_value),
            'contains' => str_contains(strtolower($fieldValue), strtolower($this->field_value[0])),
            'not_contains' => !str_contains(strtolower($fieldValue), strtolower($this->field_value[0])),
            'greater_than' => $fieldValue > $this->field_value[0],
            'less_than' => $fieldValue < $this->field_value[0],
            default => false
        };
    }

    /**
     * Get field value from lead
     */
    private function getLeadFieldValue(ClientLead $lead, string $fieldName)
    {
        return match($fieldName) {
            'status' => $lead->status,
            'sub_status' => $lead->sub_status,
            'lead_type' => $lead->lead_type,
            'purpose' => $lead->purpose,
            'country' => $lead->country,
            'coaching' => $lead->coaching,
            'branch' => $lead->branch,
            'source' => $lead->source,
            'tag' => $lead->tag,
            'assign_owner' => $lead->assign_owner,
            'client_email' => $lead->client->email_id ?? '',
            'client_mobile' => $lead->client->mobile_no ?? '',
            'days_since_created' => now()->diffInDays($lead->created_at),
            default => null
        };
    }
}
