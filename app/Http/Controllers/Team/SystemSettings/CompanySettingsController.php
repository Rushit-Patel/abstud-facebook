<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CompanySetting;
use App\Models\Country;
use App\Models\State;
use App\Models\WhatsappProvider;
use App\Mail\TestSmtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class CompanySettingsController extends Controller
{
    /**
     * Display company settings
     */
    public function index()
    {
        $company = CompanySetting::getSettings();
        return view('team.settings.company.index', compact('company'));
    }

    /**
     * Show the form for editing company settings
     */
    public function edit()
    {
        $company = CompanySetting::getSettings();
        $countries = Country::orderBy('name')->get();
        
        // Get states and cities based on existing company location
        $states = $company && $company->country_id 
            ? State::where('country_id', $company->country_id)->orderBy('name')->get()
            : collect();
            
        $cities = $company && $company->state_id 
            ? City::where('state_id', $company->state_id)->orderBy('name')->get()
            : collect();
        
        // Get WhatsApp providers with their configurations
        $whatsappProviders = WhatsappProvider::with('configs')->byPriority()->get();
        $activeWhatsappProvider = WhatsappProvider::where('is_active', true)->first();
        
        return view('team.settings.company.edit', compact('company', 'countries', 'states', 'cities', 'whatsappProviders', 'activeWhatsappProvider'));
    }

    /**
     * Update company settings
     */
    public function update(Request $request)
    {
        $company = CompanySetting::first() ?? new CompanySetting();
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:255',
            'company_address' => 'nullable|string|max:1000',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string|max:20',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
            'is_setup_completed' => 'nullable|boolean',
        ]);

        $data = $request->except(['company_logo', 'company_favicon']);
        $data['is_setup_completed'] = $request->has('is_setup_completed');

        // Get location names from IDs
        if ($request->country_id) {
            $country = Country::find($request->country_id);
            $data['country'] = $country->name;
        }
        
        if ($request->state_id) {
            $state = State::find($request->state_id);
            $data['state'] = $state->name;
        }
        
        if ($request->city_id) {
            $city = City::find($request->city_id);
            $data['city'] = $city->name;
        }

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($company->company_logo) {
                Storage::disk('public')->delete($company->company_logo);
            }
            // Store new logo
            $data['company_logo'] = $request->file('company_logo')->store('company/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('company_favicon')) {
            // Delete old favicon if exists
            if ($company->company_favicon) {
                Storage::disk('public')->delete($company->company_favicon);
            }
            // Store new favicon
            $data['company_favicon'] = $request->file('company_favicon')->store('company/favicons', 'public');
        }

        if ($company->exists) {
            $company->update($data);
        } else {
            $company->fill($data);
            $company->save();
        }
        return redirect()->route('team.settings.company.index')
            ->with('success', 'Company settings updated successfully.');
    }

    /**
     * Upload company logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $company = CompanySetting::first();

        // Delete old logo if exists
        if ($company->company_logo) {
            Storage::disk('public')->delete($company->company_logo);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('company', 'public');
        
        $company->update(['company_logo' => $logoPath]);

        return response()->json([
            'success' => true,
            'message' => 'Logo uploaded successfully.',
            'logo_url' => Storage::url($logoPath)
        ]);
    }

    /**
     * Remove company logo
     */
    public function removeLogo()
    {
        $company = CompanySetting::first();

        if ($company->company_logo) {
            Storage::disk('public')->delete($company->company_logo);
            $company->update(['company_logo' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logo removed successfully.'
        ]);
    }

    /**
     * Upload company favicon
     */
    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:ico,png|max:1024',
        ]);

        $company = CompanySetting::first();

        // Delete old favicon if exists
        if ($company->company_favicon) {
            Storage::disk('public')->delete($company->company_favicon);
        }

        // Store new favicon
        $faviconPath = $request->file('favicon')->store('company', 'public');
        
        $company->update(['company_favicon' => $faviconPath]);

        return response()->json([
            'success' => true,
            'message' => 'Favicon uploaded successfully.',
            'favicon_url' => Storage::url($faviconPath)
        ]);
    }

    /**
     * Remove company favicon
     */
    public function removeFavicon()
    {
        $company = CompanySetting::first();

        if ($company->company_favicon) {
            Storage::disk('public')->delete($company->company_favicon);
            $company->update(['company_favicon' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Favicon removed successfully.'
        ]);
    }

    /**
     * Get states by country ID for AJAX calls
     */
    public function getStatesByCountry($countryId)
    {
        $states = State::where('country_id', $countryId)
                      ->orderBy('name')
                      ->get(['id', 'name']);
        
        return response()->json($states);
    }

    /**
     * Get cities by state ID for AJAX calls
     */
    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
                     ->orderBy('name')
                     ->get(['id', 'name']);
        
        return response()->json($cities);
    }

    /**
     * Test SMTP configuration
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Check if basic SMTP settings are configured
            $requiredSettings = [
                'MAIL_HOST' => env('MAIL_HOST'),
                'MAIL_PORT' => env('MAIL_PORT'),
                'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
            ];

            $missingSettings = [];
            foreach ($requiredSettings as $setting => $value) {
                if (empty($value)) {
                    $missingSettings[] = $setting;
                }
            }

            if (!empty($missingSettings)) {
                return back()->withErrors([
                    'test_email' => 'Missing required SMTP settings: ' . implode(', ', $missingSettings) . '. Please configure these in your .env file.'
                ]);
            }

            // Send test email using the TestSmtpMail class
            Mail::to($request->test_email)
            ->send(new TestSmtpMail($request->test_email));

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email . '! Please check your inbox and spam folder.');

        } catch (\Exception $e) {
            \Log::error('SMTP Test Error: ' . $e->getMessage());
            
            return back()->withErrors([
                'test_email' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Preview the SMTP test email template
     */
    public function previewSmtpTestEmail()
    {
        $testMail = new TestSmtpMail('test@example.com');
        
        return view('emails.test-smtp', [
            'testData' => $testMail->testData
        ]);
    }

    public function whatsappUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'whatsapp_provider' => 'required|string|in:interakt,gupshup,gallabox',
                'interakt_secret_key' => 'nullable|string|required_if:whatsapp_provider,interakt',
                'gupshup_apikey' => 'nullable|string|required_if:whatsapp_provider,gupshup',
                'gupshup_channel' => 'nullable|string',
                'gallabox_api_key' => 'nullable|string|required_if:whatsapp_provider,gallabox',
                'gallabox_workspace_id' => 'nullable|string|required_if:whatsapp_provider,gallabox',
            ]);

            // Deactivate all providers first
            WhatsappProvider::query()->update(['is_active' => false]);
            
            // Get the selected provider
            $selectedProvider = WhatsappProvider::where('slug', $validated['whatsapp_provider'])->first();
        
            if (!$selectedProvider) {
                return back()->withErrors(['whatsapp_provider' => 'Selected WhatsApp provider not found.']);
            }

            // Activate the selected provider
            $selectedProvider->update(['is_active' => true]);

            // Update provider configurations based on selected provider
            switch ($validated['whatsapp_provider']) {
                case 'interakt':
                    if (!empty($validated['interakt_secret_key'])) {
                        $selectedProvider->setConfigValue('api_key', $validated['interakt_secret_key'], false);
                    }
                    break;
                    
                case 'gupshup':
                    if (!empty($validated['gupshup_apikey'])) {
                        $selectedProvider->setConfigValue('apikey', $validated['gupshup_apikey'], false);
                    }
                    if (!empty($validated['gupshup_channel'])) {
                        $selectedProvider->setConfigValue('channel', $validated['gupshup_channel'], false);
                    }
                    break;
                    
                case 'gallabox':
                    if (!empty($validated['gallabox_api_key'])) {
                        $selectedProvider->setConfigValue('api_key', $validated['gallabox_api_key'], false);
                    }
                    if (!empty($validated['gallabox_workspace_id'])) {
                        $selectedProvider->setConfigValue('workspace_id', $validated['gallabox_workspace_id'], false);
                    }
                    break;
            }

            return back()->with('success', 'WhatsApp integration settings updated successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('WhatsApp Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating WhatsApp settings.'])->withInput();
        }
    }
}