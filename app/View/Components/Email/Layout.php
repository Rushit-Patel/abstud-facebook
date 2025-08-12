<?php

namespace App\View\Components\Email;

use Illuminate\View\Component;
use App\Models\CompanySetting;

class Layout extends Component
{
    public $title;
    public $logo;
    public $companyData;

    /**
     * Create a new component instance.
     *
     * @param string|null $title
     * @param string|null $logo
     */
    public function __construct($title = null, $logo = null)
    {
        $this->title = $title ?? config('app.name');
        $this->logo = $logo;
        $this->companyData = $this->getCompanyData();
    }

    /**
     * Get company data from database or config
     */
    private function getCompanyData()
    {
        try {
            // Try to get company settings from database
            $companySettings = CompanySetting::first();
            
            if ($companySettings) {
                return [
                    'name' => $companySettings->company_name ?? config('app.name'),
                    'logo' => $companySettings->logo ?? null,
                    'email' => $companySettings->email ?? config('mail.from.address'),
                    'phone' => $companySettings->phone ?? null,
                    'address' => $companySettings->address ?? null,
                    'website' => $companySettings->website ?? config('app.url'),
                    'tagline' => $companySettings->tagline ?? null,
                ];
            }
        } catch (\Exception $e) {
            // If database is not available or model doesn't exist, use config
        }

        // Fallback to config values
        return [
            'name' => config('app.name'),
            'logo' => null,
            'email' => config('mail.from.address'),
            'phone' => null,
            'address' => null,
            'website' => config('app.url'),
            'tagline' => null,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.email.layout');
    }
}
