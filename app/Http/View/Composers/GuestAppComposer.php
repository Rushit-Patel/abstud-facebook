<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuestAppComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'appData' => $this->getAppData(),
        ]);
    }

    /**
     * Get all header-specific data
     */
    private function getAppData(): array
    {
        return [
            'companyLogo' => $this->getCompanyLogo(),
            'companyFavicon' => $this->getCompanyFavicon(),
            'companySetting' => $this->getCompanySettings()
        ];
    }

    /**
     * Get company logo URL with fallback
     */
    private function getCompanyLogo(): string
    {
        $settings = CompanySetting::getSettings();
        
        if ($settings && $settings->company_logo && Storage::disk('public')->exists($settings->company_logo)) {
            return Storage::disk('public')->url($settings->company_logo);
        }

        return $this->getDefaultLogo();
    }

    private function getCompanyFavicon(): string
    {
        $settings = CompanySetting::getSettings();

        if ($settings && $settings->company_favicon && Storage::disk('public')->exists($settings->company_favicon)) {
            return Storage::disk('public')->url($settings->company_favicon);
        }

        return $this->getDefaultFavicon();
    }

    /**
     * Get company settings with fallback
     */
    private function getCompanySettings()
    {
        $settings = CompanySetting::getSettings();

        return $settings;
    }

    /**
     * Get default logo path
     */
    private function getDefaultLogo(): string
    {
        return asset('default/images/logo/logo.png');
    }
    private function getDefaultFavicon(): string
    {
        return asset('default/images/logo/fav.png');
    }
}
