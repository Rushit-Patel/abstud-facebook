<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'phone_code',
        'currency',
        'currency_symbol',
        'timezones',
        'icon',
        'is_active'
    ];
    protected $casts = [
        'timezones' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all states belonging to this country
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /**
     * Scope: Get only active countries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted phone code for display
     */
    public function getFormattedPhoneCodeAttribute(): string
    {
        return $this->phone_code ? '+' . $this->phone_code : '';
    }


    public function getIconAttribute(): string
    {
        if (empty($this->name)) {
            return '';
        }
        $filename = strtolower(trim($this->name));
        $filename = preg_replace('/\s+/', '-', $filename); // Replace spaces with hyphens
        $filename = preg_replace('/-+/', '-', $filename); // Remove multiple consecutive hyphens
        $filename = trim($filename, '-');
        
        $flagPath = "/default/images/flags/{$filename}.svg";
        
        // Optional: Check if file exists and provide fallback
        if (file_exists(public_path($flagPath))) {
            return asset($flagPath);
        }
        return '';
    }
    

    public function getFullCurrencyAttribute(): string
    {
        $currency = $this->currency ?: '';
        $symbol = $this->currency_symbol ?: '';
        
        if ($currency && $symbol) {
            return "{$currency} ({$symbol})";
        }
        
        return $currency ?: $symbol;
    }
}
