<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookLeadSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_lead_id',
        'campaign_id',
        'campaign_name',
        'adset_id',
        'adset_name',
        'ad_id',
        'ad_name',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    // Relationships
    public function facebookLead(): BelongsTo
    {
        return $this->belongsTo(FacebookLead::class);
    }

    // Scopes
    public function scopeByCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    public function scopeByAdset($query, $adsetId)
    {
        return $query->where('adset_id', $adsetId);
    }

    public function scopeByAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }

    // Helper Methods
    public function hasCampaignInfo(): bool
    {
        return !empty($this->campaign_id) && !empty($this->campaign_name);
    }

    public function hasAdsetInfo(): bool
    {
        return !empty($this->adset_id) && !empty($this->adset_name);
    }

    public function hasAdInfo(): bool
    {
        return !empty($this->ad_id) && !empty($this->ad_name);
    }

    public function hasUtmInfo(): bool
    {
        return !empty($this->utm_source) || !empty($this->utm_medium) || !empty($this->utm_campaign);
    }

    public function getSourcePath(): string
    {
        $path = [];
        
        if ($this->campaign_name) {
            $path[] = $this->campaign_name;
        }
        
        if ($this->adset_name) {
            $path[] = $this->adset_name;
        }
        
        if ($this->ad_name) {
            $path[] = $this->ad_name;
        }
        
        return implode(' > ', $path) ?: 'Unknown Source';
    }
}
