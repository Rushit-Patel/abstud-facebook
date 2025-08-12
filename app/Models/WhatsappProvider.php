<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'api_endpoint', 'is_active', 
        'priority', 'rate_limit_per_minute'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'rate_limit_per_minute' => 'integer',
    ];

    /**
     * Get the configurations for this provider
     */
    public function configs()
    {
        return $this->hasMany(WhatsappProviderConfig::class, 'provider_id');
    }

    /**
     * Scope: Get only active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get a specific config value by key
     */
    public function getConfig(string $key): ?string
    {
        $config = $this->configs()->where('config_key', $key)->first();
        return $config ? $config->config_value : null;
    }

    /**
     * Get a specific config value by key (alias for getConfig)
     */
    public function getConfigValue(string $key): ?string
    {
        return $this->getConfig($key);
    }

    /**
     * Set a config value
     */
    public function setConfigValue($key, $value, $isEncrypted = false)
    {
        return $this->configs()->updateOrCreate(
            ['config_key' => $key],
            [
                'config_value' => $value, // Remove encryption
                'is_encrypted' => false   // Always set to false
            ]
        );
    }
}
