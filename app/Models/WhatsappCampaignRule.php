<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappCampaignRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'field_name',
        'operator',
        'field_value',
    ];

    protected $casts = [
        'field_value' => 'array',
    ];

    /**
     * Get the campaign this rule belongs to
     */
    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'campaign_id');
    }

    /**
     * Check if a lead matches this rule
     */
    public function matches($lead): bool
    {
        $fieldValue = data_get($lead, $this->field_name);
        
        return match ($this->operator) {
            'equals' => $fieldValue == $this->field_value[0],
            'not_equals' => $fieldValue != $this->field_value[0],
            'in' => in_array($fieldValue, $this->field_value),
            'not_in' => !in_array($fieldValue, $this->field_value),
            'greater_than' => $fieldValue > $this->field_value[0],
            'less_than' => $fieldValue < $this->field_value[0],
            'contains' => str_contains(strtolower($fieldValue), strtolower($this->field_value[0])),
            'not_contains' => !str_contains(strtolower($fieldValue), strtolower($this->field_value[0])),
            default => false,
        };
    }
}
