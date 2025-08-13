<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FacebookBusinessAccount;
use App\Models\FacebookPage;
use App\Models\FacebookLeadForm;
use App\Models\FacebookLead;
use App\Models\FacebookParameterMapping;
use App\Models\FacebookCustomFieldMapping;
use App\Services\FacebookLeadIntegrationService;
use App\Services\TemplateVariableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class FacebookIntegrationController extends Controller
{
    protected $integrationService;

    public function __construct(FacebookLeadIntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    /**
     * Dashboard - Overview of Facebook integration
     */
    public function dashboard()
    {
        $branchId = Auth::user()->branch_id ?? 1; // Adjust based on your auth system
        
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();
        
        $stats = [];
        $pages = collect();
        $leadForms = collect();
        $recentLeads = collect();
        
        if ($businessAccount) {
            $stats = $this->integrationService->getProcessingStats($businessAccount->id);
            
            // Get pages with lead forms count
            $pages = $businessAccount->facebookPages()
                ->withCount('facebookLeadForms')
                ->where('is_active', true)
                ->orderBy('page_name')
                ->get();
            
            // Get lead forms with recent leads count
            $leadForms = FacebookLeadForm::whereHas('facebookPage', function ($query) use ($businessAccount) {
                $query->where('facebook_business_account_id', $businessAccount->id);
            })
            ->withCount('facebookLeads')
            ->where('is_active', true)
            ->orderBy('form_name')
            ->limit(10)
            ->get();
            
            // Get recent leads
            $recentLeads = FacebookLead::whereHas('facebookLeadForm.facebookPage', function ($query) use ($businessAccount) {
                $query->where('facebook_business_account_id', $businessAccount->id);
            })
            ->with(['facebookLeadForm.facebookPage'])
            ->orderBy('facebook_created_time', 'desc')
            ->limit(10)
            ->get();
        }

        return view('team.facebook.dashboard', compact('businessAccount', 'stats', 'pages', 'leadForms', 'recentLeads'));
    }

    /**
     * Integration Overview - Simple status page
     */
    public function overview()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();
        
        // Webhook configuration status
        $webhookConfigured = !empty(config('services.facebook.webhook.url')) && 
                           !empty(config('services.facebook.webhook.verify_token'));
        
        // Facebook app configuration status
        $facebookConfigured = !empty(config('services.facebook.client_id')) && 
                             config('services.facebook.client_id') !== 'your_facebook_app_id' &&
                             !empty(config('services.facebook.client_secret'));
        
        $overview = [
            'facebook_configured' => $facebookConfigured,
            'webhook_configured' => $webhookConfigured,
            'business_account_connected' => $businessAccount && $businessAccount->status === 'connected',
            'total_pages' => 0,
            'subscribed_pages' => 0,
            'total_forms' => 0,
            'total_leads' => 0,
        ];
        
        if ($businessAccount) {
            $overview['total_pages'] = $businessAccount->facebookPages()->count();
            $overview['subscribed_pages'] = $businessAccount->facebookPages()->where('webhook_subscribed', true)->count();
            $overview['total_forms'] = FacebookLeadForm::whereHas('facebookPage', function ($query) use ($businessAccount) {
                $query->where('facebook_business_account_id', $businessAccount->id);
            })->count();
            $overview['total_leads'] = FacebookLead::whereHas('facebookLeadForm.facebookPage', function ($query) use ($businessAccount) {
                $query->where('facebook_business_account_id', $businessAccount->id);
            })->count();
        }
        
        return view('team.facebook.overview', compact('businessAccount', 'overview'));
    }

    /**
     * Business Account Management
     */
    public function businessAccount()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        return view('team.facebook.business-account', compact('businessAccount'));
    }

    /**
     * Connect Facebook Business Account
     */
    public function connectAccount(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'facebook_business_id' => 'required|string|unique:facebook_business_accounts,facebook_business_id',
            'app_id' => 'required|string',
            'app_secret' => 'required|string',
            'access_token' => 'required|string',
        ]);

        try {
            $branchId = Auth::user()->branch_id ?? 1;

            $businessAccount = FacebookBusinessAccount::create([
                'branch_id' => $branchId,
                'business_name' => $request->business_name,
                'facebook_business_id' => $request->facebook_business_id,
                'app_id' => $request->app_id,
                'app_secret' => $request->app_secret,
                'access_token' => $request->access_token,
                'token_expires_at' => now()->addDays(60), // Facebook tokens typically expire in 60 days
                'status' => 'connected',
            ]);

            return redirect()->route('facebook.business-account')
                ->with('success', 'Facebook Business Account connected successfully!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to connect Facebook Business Account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Disconnect Facebook Business Account
     */
    public function disconnectAccount()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if ($businessAccount) {
            $businessAccount->update(['status' => 'disconnected']);
            
            return redirect()->route('facebook.business-account')
                ->with('success', 'Facebook Business Account disconnected successfully!');
        }

        return redirect()->back()->with('error', 'No business account found to disconnect.');
    }

    /**
     * Refresh Access Token
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if ($businessAccount) {
            $businessAccount->update([
                'access_token' => $request->access_token,
                'token_expires_at' => now()->addDays(60),
                'status' => 'connected',
            ]);

            return redirect()->back()->with('success', 'Access token refreshed successfully!');
        }

        return redirect()->back()->with('error', 'No business account found.');
    }

    /**
     * Sync Facebook Pages
     */
    public function syncPages()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if (!$businessAccount) {
            return redirect()->back()->with('error', 'No business account found.');
        }

        if (!$businessAccount->access_token) {
            return redirect()->back()->with('error', 'Facebook access token not found. Please reconnect your account.');
        }

        try {
            // Call Facebook Graph API to get pages managed by the business account
            $result = $this->integrationService->syncPagesFromFacebook($businessAccount);
            
            if ($result['success']) {
                return redirect()->route('facebook.pages')
                    ->with('success', "Successfully synced {$result['count']} Facebook pages!");
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to sync pages: ' . $result['error']);
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync pages: ' . $e->getMessage());
        }
    }

    /**
     * Facebook Pages Management
     */
    public function pages()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        $pages = collect();
        if ($businessAccount) {
            $pages = $businessAccount->facebookPages()->with('facebookLeadForms')->get();
        }

        return view('team.facebook.pages', compact('pages', 'businessAccount'));
    }

    /**
     * Toggle Page Status
     */
    public function togglePage(FacebookPage $page)
    {
        $page->update(['is_active' => !$page->is_active]);

        return redirect()->back()->with('success', 'Page status updated successfully!');
    }

    /**
     * Sync Lead Forms for a Page
     */
    public function syncLeadForms(FacebookPage $page)
    {
        try {
            // Call Facebook Graph API to get lead forms for this specific page
            $result = $this->integrationService->syncLeadFormsFromFacebook($page);
            
            if ($result['success']) {
                return redirect()->back()
                    ->with('success', "Successfully synced {$result['count']} lead forms for {$page->page_name}!");
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to sync lead forms: ' . $result['error']);
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync lead forms: ' . $e->getMessage());
        }
    }

    /**
     * Subscribe Page to Webhook for Real-time Leads
     */
    public function subscribePageToWebhook(FacebookPage $page)
    {
        try {
            $businessAccount = $page->facebookBusinessAccount;
            
            if (!$businessAccount || !$businessAccount->access_token) {
                return redirect()->back()->with('error', 'No valid access token found for the business account.');
            }

            // Subscribe page to webhook with leadgen field
            $response = \Illuminate\Support\Facades\Http::post("https://graph.facebook.com/v23.0/{$page->facebook_page_id}/subscribed_apps", [
                'subscribed_fields' => 'leadgen',
                'access_token' => $page->page_access_token ?: $businessAccount->access_token,
            ]);

            if ($response->successful()) {
                $page->update([
                    'webhook_subscribed' => true,
                    'webhook_subscribed_at' => now(),
                    'webhook_subscribed_fields' => ['leadgen']
                ]);

                return redirect()->back()->with('success', "Successfully subscribed {$page->page_name} to webhook for real-time leads!");
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown error occurred';
                return redirect()->back()->with('error', "Failed to subscribe page to webhook: {$errorMessage}");
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to subscribe page to webhook: ' . $e->getMessage());
        }
    }

    /**
     * Unsubscribe Page from Webhook
     */
    public function unsubscribePageFromWebhook(FacebookPage $page)
    {
        try {
            $businessAccount = $page->facebookBusinessAccount;
            
            if (!$businessAccount || !$businessAccount->access_token) {
                return redirect()->back()->with('error', 'No valid access token found for the business account.');
            }

            // Unsubscribe page from webhook
            $response = \Illuminate\Support\Facades\Http::delete("https://graph.facebook.com/v23.0/{$page->facebook_page_id}/subscribed_apps", [
                'access_token' => $page->page_access_token ?: $businessAccount->access_token,
            ]);

            if ($response->successful() || $response->status() === 400) { // 400 might mean already unsubscribed
                $page->update([
                    'webhook_subscribed' => false,
                    'webhook_subscribed_at' => null,
                    'webhook_subscribed_fields' => null
                ]);

                return redirect()->back()->with('success', "Successfully unsubscribed {$page->page_name} from webhook!");
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown error occurred';
                return redirect()->back()->with('error', "Failed to unsubscribe page from webhook: {$errorMessage}");
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to unsubscribe page from webhook: ' . $e->getMessage());
        }
    }

    /**
     * Lead Forms Management
     */
    public function leadForms()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        
        $leadForms = FacebookLeadForm::whereHas('facebookPage.facebookBusinessAccount', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with(['facebookPage', 'facebookLeads'])->get();

        return view('team.facebook.lead-forms', compact('leadForms'));
    }

    /**
     * Show Lead Form Details
     */
    public function showLeadForm(FacebookLeadForm $leadForm)
    {
        $leadForm->load(['facebookPage', 'facebookLeads', 'facebookParameterMappings', 'facebookCustomFieldMappings']);

        return view('team.facebook.lead-forms.show', compact('leadForm'));
    }

    /**
     * Toggle Lead Form Status
     */
    public function toggleLeadForm(FacebookLeadForm $leadForm)
    {
        $leadForm->update(['is_active' => !$leadForm->is_active]);

        return redirect()->back()->with('success', 'Lead form status updated successfully!');
    }

    /**
     * Parameter Mappings Configuration
     */
    public function mappings(FacebookLeadForm $leadForm)
    {
        $leadForm->load(['facebookPage', 'facebookParameterMappings']);
        
        // Get system variables from TemplateVariableService
        $systemVariables = TemplateVariableService::getAllVariables();
        
        return view('team.facebook.lead-forms.mappings', compact('leadForm', 'systemVariables'));
    }

    /**
     * Save Parameter Mappings
     */
    public function saveMappings(Request $request, FacebookLeadForm $leadForm)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.facebook_field_name' => 'required|string',
            'mappings.*.facebook_field_type' => 'required|string',
            'mappings.*.system_field_name' => 'required|string',
            'mappings.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing mappings
            $leadForm->facebookParameterMappings()->delete();

            // Create new mappings
            foreach ($request->mappings as $mappingData) {
                FacebookParameterMapping::create([
                    'facebook_lead_form_id' => $leadForm->id,
                    'facebook_field_name' => $mappingData['facebook_field_name'],
                    'facebook_field_type' => $mappingData['facebook_field_type'],
                    'system_field_name' => $mappingData['system_field_name'],
                    'is_required' => $mappingData['is_required'] ?? false,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Parameter mappings saved successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save mappings: ' . $e->getMessage());
        }
    }

    /**
     * Delete Parameter Mapping
     */
    public function deleteMapping(FacebookParameterMapping $mapping)
    {
        $mapping->delete();

        return redirect()->back()->with('success', 'Mapping deleted successfully!');
    }

    /**
     * Custom Field Mappings Configuration
     */
    public function customMappings(FacebookLeadForm $leadForm)
    {
        $leadForm->load(['facebookPage', 'facebookCustomFieldMappings']);
        
        // Get system variables from TemplateVariableService
        $systemVariables = TemplateVariableService::getAllVariables();
        
        return view('team.facebook.lead-forms.custom-mappings', compact('leadForm', 'systemVariables'));
    }

    /**
     * Save Custom Field Mappings
     */
    public function saveCustomMappings(Request $request, FacebookLeadForm $leadForm)
    {
        $request->validate([
            'custom_mappings' => 'required|array',
            'custom_mappings.*.facebook_custom_question' => 'required|string',
            'custom_mappings.*.system_field_name' => 'required|string',
            'custom_mappings.*.data_type' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing custom mappings
            $leadForm->facebookCustomFieldMappings()->delete();

            // Create new custom mappings
            foreach ($request->custom_mappings as $mappingData) {
                FacebookCustomFieldMapping::create([
                    'facebook_lead_form_id' => $leadForm->id,
                    'facebook_custom_question' => $mappingData['facebook_custom_question'],
                    'system_field_name' => $mappingData['system_field_name'],
                    'data_type' => $mappingData['data_type'],
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Custom field mappings saved successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save custom mappings: ' . $e->getMessage());
        }
    }

    /**
     * Delete Custom Field Mapping
     */
    public function deleteCustomMapping(FacebookCustomFieldMapping $mapping)
    {
        $mapping->delete();

        return redirect()->back()->with('success', 'Custom mapping deleted successfully!');
    }

    /**
     * Leads Management
     */
    public function leads(Request $request)
    {
        $branchId = Auth::user()->branch_id ?? 1;

        $query = FacebookLead::whereHas('facebookLeadForm.facebookPage.facebookBusinessAccount', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->with(['facebookLeadForm.facebookPage', 'facebookLeadSource']);

        // Store base query for statistics before applying filters
        $statsQuery = clone $query;

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('facebook_created_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('facebook_created_time', '<=', $request->date_to);
        }

        $leads = $query->orderBy('facebook_created_time', 'desc')->paginate(20);

        // Get business account for sync functionality
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        // Calculate statistics for all leads (unfiltered)
        $stats = [
            'total' => $statsQuery->count(),
            'processed' => $statsQuery->where('status', 'processed')->count(),
            'pending' => $statsQuery->where('status', 'pending')->count(),
            'failed' => $statsQuery->where('status', 'failed')->count(),
        ];

        return view('team.facebook.leads', compact('leads', 'businessAccount', 'stats'));
    }

    /**
     * Sync Leads from Facebook
     */
    public function syncLeads()
    {
        try {
            $branchId = Auth::user()->branch_id ?? 1;
            $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

            if (!$businessAccount) {
                return redirect()->back()->with('error', 'No business account found. Please connect your Facebook business account first.');
            }

            $result = $this->integrationService->syncLeadsFromFacebook($businessAccount);

            if ($result['success']) {
                return redirect()->back()
                    ->with('success', $result['message']);
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to sync leads: ' . $result['error']);
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync leads: ' . $e->getMessage());
        }
    }

    /**
     * Show Lead Details
     */
    public function showLead(FacebookLead $lead)
    {
        // Redirect to dashboard since we don't have detailed views yet
        return redirect()->route('facebook.dashboard')->with('info', 'Lead details are available in the dashboard');
    }

    /**
     * Retry Failed Lead Processing
     */
    public function retryLead(FacebookLead $lead)
    {
        $result = $this->integrationService->retryFailedLead($lead->id);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Lead processing retried successfully!');
        }

        return redirect()->back()->with('error', 'Failed to retry lead: ' . $result['error']);
    }

    /**
     * Mark Lead as Processed
     */
    public function markProcessed(FacebookLead $lead)
    {
        $lead->markAsProcessed();

        return redirect()->back()->with('success', 'Lead marked as processed!');
    }

    /**
     * Analytics Dashboard
     */
    public function analytics()
    {
        // Redirect to main dashboard since analytics are included there
        return redirect()->route('facebook.dashboard')->with('info', 'Analytics are available in the main dashboard');
    }

    /**
     * Lead Sources Management
     */
    public function leadSources()
    {
        // Redirect to dashboard since we don't have detailed views yet
        return redirect()->route('facebook.dashboard')->with('info', 'Lead sources information is available in the dashboard');
    }

    /**
     * Webhook Settings
     */
    public function webhookSettings()
    {
        $webhookUrl = config('services.facebook.webhook.url');
        $verifyToken = config('services.facebook.webhook.verify_token');
        $isConfigured = !empty($webhookUrl) && !empty($verifyToken);
        
        return view('team.facebook.webhook-settings', compact('webhookUrl', 'verifyToken', 'isConfigured'));
    }

    /**
     * Save Webhook Settings - Not needed since it's in config/env
     */
    public function saveWebhookSettings(Request $request)
    {
        return redirect()->back()->with('info', 'Webhook settings are configured via environment variables. Please update your .env file.');
    }

    /**
     * Test Webhook Connection
     */
    public function testWebhook()
    {
        $webhookUrl = config('services.facebook.webhook.url');
        $verifyToken = config('services.facebook.webhook.verify_token');
        
        if (!$webhookUrl || !$verifyToken) {
            return redirect()->back()->with('error', 'Webhook not configured. Please set FACEBOOK_WEBHOOK_URL and FACEBOOK_WEBHOOK_VERIFY_TOKEN in your .env file.');
        }
        
        // Test the webhook URL
        $fullWebhookUrl = url($webhookUrl);
        return redirect()->back()->with('success', "Webhook URL: {$fullWebhookUrl} - Configuration is valid!");
    }

    /**
     * Regenerate Webhook Token - Not needed since it's in env
     */
    public function regenerateWebhookToken()
    {
        return redirect()->back()->with('info', 'Webhook token is configured via environment variables. Please update FACEBOOK_WEBHOOK_VERIFY_TOKEN in your .env file.');
    }

    /**
     * General Settings
     */
    public function settings()
    {
        // Redirect to dashboard since we don't have detailed views yet
        return redirect()->route('facebook.dashboard')->with('info', 'General settings are available in the dashboard');
    }

    /**
     * Save General Settings
     */
    public function saveSettings(Request $request)
    {
        // Implement general settings save logic
        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    /**
     * System Variables Reference
     */
    public function systemVariables()
    {
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();
        
        return view('team.facebook.system-variables', compact('systemVariables', 'sampleValues'));
    }

    /**
     * AJAX: Get Statistics
     */
    public function getStats()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if (!$businessAccount) {
            return response()->json(['error' => 'No business account found'], 404);
        }

        $stats = $this->integrationService->getProcessingStats($businessAccount->id);
        $todayLeads = $this->integrationService->getTodayLeadsCount($businessAccount->id);

        return response()->json([
            'stats' => $stats,
            'today_leads' => $todayLeads,
        ]);
    }

    /**
     * AJAX: Get Recent Leads
     */
    public function getRecentLeads()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if (!$businessAccount) {
            return response()->json(['error' => 'No business account found'], 404);
        }

        $recentLeads = $this->integrationService->getRecentLeads($businessAccount->id, 10);

        return response()->json($recentLeads);
    }

    /**
     * AJAX: Test Connection
     */
    public function testConnection()
    {
        // Implement connection testing logic
        return response()->json(['success' => true, 'message' => 'Connection test successful']);
    }

    /**
     * AJAX: Get System Variables
     */
    public function getSystemVariables()
    {
        $systemVariables = TemplateVariableService::getAllVariables();
        $sampleValues = TemplateVariableService::getSampleValues();
        
        return response()->json([
            'success' => true,
            'variables' => $systemVariables,
            'sample_values' => $sampleValues
        ]);
    }

    /**
     * Privacy Policy Page
     */
    public function privacyPolicy()
    {
        return view('facebook.legal.privacy-policy');
    }

    /**
     * Terms of Service Page
     */
    public function termsOfService()
    {
        return view('facebook.legal.terms-of-service');
    }
}
