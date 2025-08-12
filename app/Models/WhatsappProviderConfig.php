<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappProviderConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'config_key',
        'config_value',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the provider that owns this config
     */
    public function provider()
    {
        return $this->belongsTo(WhatsappProvider::class, 'provider_id');
    }

    /**
     * Get the decrypted value if encrypted
     */
    public function getDecryptedValueAttribute()
    {
        if ($this->is_encrypted && $this->config_value) {
            try {
                return decrypt($this->config_value);
            } catch (\Exception $e) {
                return $this->config_value;
            }
        }
        
        return $this->config_value;
    }

    /**
     * Set encrypted value
     */
    public function setEncryptedValue($value)
    {
        $this->config_value = encrypt($value);
        $this->is_encrypted = true;
        return $this;
    }

    /**
     * Set plain value
     */
    public function setPlainValue($value)
    {
        $this->config_value = $value;
        $this->is_encrypted = false;
        return $this;
    }
}
