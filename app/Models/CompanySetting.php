<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_logo',
        'company_favicon',
        'website_url',
        'company_address',
        'phone',
        'email',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'is_setup_completed',
    ];

    protected $casts = [
        'is_setup_completed' => 'boolean',
    ];

    /**
     * Get the company settings (singleton pattern)
     */
    public static function getSettings()
    {
        return self::first();
    }

    /**
     * Check if company setup is completed
     */
    public static function isSetupCompleted(): bool
    {
        $settings = self::getSettings();
        return $settings && $settings->is_setup_completed;
    }
}
