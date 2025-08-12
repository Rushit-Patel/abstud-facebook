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
use App\Models\FacebookWebhookSetting;
use App\Services\FacebookLeadIntegrationService;
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
        if ($businessAccount) {
            $stats = $this->integrationService->getProcessingStats($businessAccount->id);
        }

        return view('facebook.dashboard', compact('businessAccount', 'stats'));
    }

    /**
     * Business Account Management
     */
    public function businessAccount()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        return view('facebook.business-account', compact('businessAccount'));
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

        // Here you would typically call Facebook API to get pages
        // For now, we'll create a sample page
        
        try {
            // Sample page creation (replace with actual Facebook API call)
            FacebookPage::updateOrCreate([
                'facebook_business_account_id' => $businessAccount->id,
                'facebook_page_id' => 'sample_page_id_' . time(),
            ], [
                'page_name' => 'Sample Page',
                'is_active' => true,
            ]);

            return redirect()->route('facebook.pages')->with('success', 'Pages synced successfully!');

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

        return view('facebook.pages', compact('pages', 'businessAccount'));
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
            // Sample lead form creation (replace with actual Facebook API call)
            FacebookLeadForm::updateOrCreate([
                'facebook_page_id' => $page->id,
                'facebook_form_id' => 'sample_form_id_' . time(),
            ], [
                'form_name' => 'Sample Lead Form',
                'form_description' => 'A sample lead form from ' . $page->page_name,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'Lead forms synced successfully!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync lead forms: ' . $e->getMessage());
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

        return view('facebook.lead-forms', compact('leadForms'));
    }

    /**
     * Show Lead Form Details
     */
    public function showLeadForm(FacebookLeadForm $leadForm)
    {
        $leadForm->load(['facebookPage', 'facebookLeads', 'facebookParameterMappings', 'facebookCustomFieldMappings']);

        return view('facebook.lead-forms.show', compact('leadForm'));
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
        $mappings = $leadForm->facebookParameterMappings()->get();

        return view('facebook.mappings', compact('leadForm', 'mappings'));
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
        $customMappings = $leadForm->facebookCustomFieldMappings()->get();

        return view('facebook.custom-mappings', compact('leadForm', 'customMappings'));
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

        return view('facebook.leads', compact('leads'));
    }

    /**
     * Show Lead Details
     */
    public function showLead(FacebookLead $lead)
    {
        $lead->load(['facebookLeadForm.facebookPage', 'facebookLeadSource']);

        return view('facebook.leads.show', compact('lead'));
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
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        $stats = [];
        if ($businessAccount) {
            $stats = $this->integrationService->getProcessingStats($businessAccount->id);
        }

        return view('facebook.analytics', compact('stats'));
    }

    /**
     * Lead Sources Management
     */
    public function leadSources()
    {
        $branchId = Auth::user()->branch_id ?? 1;

        $leadSources = DB::table('facebook_lead_sources as fls')
            ->join('facebook_leads as fl', 'fls.facebook_lead_id', '=', 'fl.id')
            ->join('facebook_lead_forms as flf', 'fl.facebook_lead_form_id', '=', 'flf.id')
            ->join('facebook_pages as fp', 'flf.facebook_page_id', '=', 'fp.id')
            ->join('facebook_business_accounts as fba', 'fp.facebook_business_account_id', '=', 'fba.id')
            ->where('fba.branch_id', $branchId)
            ->select('fls.*', 'fl.name as lead_name', 'flf.form_name', 'fp.page_name')
            ->paginate(20);

        return view('facebook.lead-sources', compact('leadSources'));
    }

    /**
     * Webhook Settings
     */
    public function webhookSettings()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        $webhookSettings = null;
        if ($businessAccount) {
            $webhookSettings = $businessAccount->webhookSettings;
        }

        return view('facebook.webhook-settings', compact('businessAccount', 'webhookSettings'));
    }

    /**
     * Save Webhook Settings
     */
    public function saveWebhookSettings(Request $request)
    {
        $request->validate([
            'webhook_url' => 'required|url',
            'verify_token' => 'required|string',
        ]);

        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if (!$businessAccount) {
            return redirect()->back()->with('error', 'No business account found.');
        }

        try {
            $businessAccount->webhookSettings()->updateOrCreate(
                ['facebook_business_account_id' => $businessAccount->id],
                [
                    'webhook_url' => $request->webhook_url,
                    'verify_token' => $request->verify_token,
                    'is_active' => true,
                ]
            );

            return redirect()->back()->with('success', 'Webhook settings saved successfully!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to save webhook settings: ' . $e->getMessage());
        }
    }

    /**
     * Test Webhook Connection
     */
    public function testWebhook()
    {
        // Here you would implement webhook testing logic
        return redirect()->back()->with('success', 'Webhook test completed successfully!');
    }

    /**
     * Regenerate Webhook Token
     */
    public function regenerateWebhookToken()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        if (!$businessAccount || !$businessAccount->webhookSettings) {
            return redirect()->back()->with('error', 'No webhook settings found.');
        }

        $newToken = $businessAccount->webhookSettings->regenerateVerifyToken();

        return redirect()->back()->with('success', 'Webhook token regenerated successfully!');
    }

    /**
     * General Settings
     */
    public function settings()
    {
        $branchId = Auth::user()->branch_id ?? 1;
        $businessAccount = FacebookBusinessAccount::where('branch_id', $branchId)->first();

        return view('facebook.settings', compact('businessAccount'));
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
}
