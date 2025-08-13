<?php

namespace App\Services;

use App\Models\FacebookLead;
use App\Models\FacebookLeadForm;
use App\Models\FacebookParameterMapping;
use App\Models\FacebookCustomFieldMapping;
use App\Models\FacebookLeadSource;
use App\Models\FacebookBusinessAccount;
use App\Models\ClientLead; // Your existing lead model
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class FacebookLeadIntegrationService
{
    /**
     * Process incoming Facebook lead data
     */
    public function processLead(array $leadData, string $formId): array
    {
        try {
            DB::beginTransaction();

            // Find the lead form
            $leadForm = FacebookLeadForm::where('facebook_form_id', $formId)->first();
            if (!$leadForm) {
                throw new Exception("Lead form not found: {$formId}");
            }

            // Extract basic lead information
            $fieldData = $leadData['field_data'] ?? [];
            $extractedData = $this->extractLeadData($fieldData);

            // Create Facebook lead record
            $facebookLead = FacebookLead::create([
                'facebook_lead_form_id' => $leadForm->id,
                'facebook_lead_id' => $leadData['id'],
                'name' => $extractedData['name'],
                'email' => $extractedData['email'],
                'phone' => $extractedData['phone'],
                'additional_data' => $extractedData['additional_data'],
                'facebook_created_time' => $leadData['created_time'],
                'status' => 'new'
            ]);

            // Process the lead data and create system lead
            $systemLead = $this->createSystemLead($facebookLead, $fieldData);

            // Update Facebook lead status
            $facebookLead->markAsProcessed();

            // Process lead source information
            $this->processLeadSource($facebookLead, $leadData);

            DB::commit();

            return [
                'success' => true,
                'facebook_lead_id' => $facebookLead->id,
                'system_lead_id' => $systemLead->id ?? null,
                'message' => 'Lead processed successfully'
            ];

        } catch (Exception $e) {
            DB::rollBack();

            // Update processing status if Facebook lead was created
            if (isset($facebookLead)) {
                $facebookLead->markAsFailed();
            }

            Log::error('Facebook lead processing failed', [
                'error' => $e->getMessage(),
                'lead_data' => $leadData ?? null,
                'form_id' => $formId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract lead data from Facebook field data
     */
    protected function extractLeadData(array $fieldData): array
    {
        $extracted = [
            'name' => null,
            'email' => null,
            'phone' => null,
            'additional_data' => []
        ];

        foreach ($fieldData as $field) {
            $fieldName = strtolower($field['name']);
            $fieldValue = $field['values'][0] ?? null;

            // Map common field names
            switch ($fieldName) {
                case 'full_name':
                case 'name':
                case 'first_name':
                    $extracted['name'] = $fieldValue;
                    break;
                case 'email':
                case 'email_address':
                    $extracted['email'] = $fieldValue;
                    break;
                case 'phone_number':
                case 'phone':
                case 'mobile':
                    $extracted['phone'] = $fieldValue;
                    break;
                default:
                    // Store other fields in additional_data
                    $extracted['additional_data'][$field['name']] = $fieldValue;
                    break;
            }
        }

        return $extracted;
    }

    /**
     * Create system lead from Facebook lead data
     */
    protected function createSystemLead(FacebookLead $facebookLead, array $fieldData): ?ClientLead
    {
        $leadForm = $facebookLead->facebookLeadForm;
        $mappings = $leadForm->facebookParameterMappings()->where('is_active', true)->get();
        $customMappings = $leadForm->facebookCustomFieldMappings()->where('is_active', true)->get();

        $systemLeadData = [];

        // Process standard field mappings
        foreach ($mappings as $mapping) {
            $facebookValue = $this->extractFieldValue($fieldData, $mapping->facebook_field_name);
            
            if ($facebookValue !== null) {
                $systemLeadData[$mapping->system_field_name] = $facebookValue;
            }
        }

        // Process custom field mappings
        foreach ($customMappings as $customMapping) {
            $facebookValue = $this->extractCustomFieldValue($fieldData, $customMapping->facebook_custom_question);
            
            if ($facebookValue !== null) {
                $transformedValue = $this->castDataType($facebookValue, $customMapping->data_type);
                $systemLeadData[$customMapping->system_field_name] = $transformedValue;
            }
        }

        // Add default system values
        $systemLeadData = array_merge($systemLeadData, [
            'source' => 'facebook_lead_ads',
            'source_form_id' => $leadForm->facebook_form_id,
            'source_page_id' => $leadForm->facebookPage->facebook_page_id,
            'branch_id' => $leadForm->facebookPage->facebookBusinessAccount->branch_id,
        ]);

        // If no mappings found, use basic data
        if (empty($systemLeadData) || !isset($systemLeadData['name'])) {
            $systemLeadData = array_merge($systemLeadData, [
                'name' => $facebookLead->name,
                'email' => $facebookLead->email,
                'phone' => $facebookLead->phone,
            ]);
        }

        // Check for duplicates
        $existingLead = $this->checkForDuplicates($systemLeadData);
        if ($existingLead) {
            return $this->handleDuplicate($existingLead, $systemLeadData);
        }

        // Create the system lead
        try {
            return ClientLead::create($systemLeadData);
        } catch (Exception $e) {
            Log::warning('Failed to create system lead', [
                'error' => $e->getMessage(),
                'data' => $systemLeadData
            ]);
            return null;
        }
    }

    /**
     * Extract field value from Facebook field data
     */
    protected function extractFieldValue(array $fieldData, string $fieldName): mixed
    {
        foreach ($fieldData as $field) {
            if ($field['name'] === $fieldName) {
                return $field['values'][0] ?? null;
            }
        }
        return null;
    }

    /**
     * Extract custom field value from Facebook field data
     */
    protected function extractCustomFieldValue(array $fieldData, string $customQuestion): mixed
    {
        foreach ($fieldData as $field) {
            // Match by question text
            if (stripos($field['name'], $customQuestion) !== false) {
                return $field['values'][0] ?? null;
            }
        }
        return null;
    }

    /**
     * Cast data type for custom fields
     */
    protected function castDataType(mixed $value, string $dataType): mixed
    {
        return match($dataType) {
            'number' => is_numeric($value) ? (float)$value : null,
            'integer' => is_numeric($value) ? (int)$value : null,
            'boolean' => in_array(strtolower($value), ['yes', 'true', '1', 'on']) ? true : false,
            'date' => date('Y-m-d', strtotime($value)),
            'datetime' => date('Y-m-d H:i:s', strtotime($value)),
            default => $value
        };
    }

    /**
     * Process lead source information (UTM, campaign data)
     */
    protected function processLeadSource(FacebookLead $facebookLead, array $leadData): void
    {
        $adData = $leadData['ad_data'] ?? [];
        
        FacebookLeadSource::create([
            'facebook_lead_id' => $facebookLead->id,
            'campaign_id' => $adData['campaign_id'] ?? null,
            'campaign_name' => $adData['campaign_name'] ?? null,
            'adset_id' => $adData['adset_id'] ?? null,
            'adset_name' => $adData['adset_name'] ?? null,
            'ad_id' => $adData['ad_id'] ?? null,
            'ad_name' => $adData['ad_name'] ?? null,
            'utm_source' => $leadData['utm_source'] ?? 'facebook',
            'utm_medium' => $leadData['utm_medium'] ?? 'social',
            'utm_campaign' => $adData['campaign_name'] ?? null,
        ]);
    }

    /**
     * Check for duplicate leads
     */
    protected function checkForDuplicates(array $leadData): ?ClientLead
    {
        // Check by email first
        if (!empty($leadData['email'])) {
            $existing = ClientLead::where('email', $leadData['email'])->first();
            if ($existing) return $existing;
        }

        // Check by phone
        if (!empty($leadData['phone'])) {
            $existing = ClientLead::where('phone', $leadData['phone'])->first();
            if ($existing) return $existing;
        }

        return null;
    }

    /**
     * Handle duplicate lead
     */
    protected function handleDuplicate(ClientLead $existingLead, array $newLeadData): ClientLead
    {
        // Update existing lead with new data
        $existingLead->update([
            'updated_at' => now(),
            // Add any additional fields you want to update
        ]);

        return $existingLead;
    }

    /**
     * Retry failed lead processing
     */
    public function retryFailedLead(int $facebookLeadId): array
    {
        $facebookLead = FacebookLead::find($facebookLeadId);
        
        if (!$facebookLead || !$facebookLead->isFailed()) {
            return ['success' => false, 'error' => 'Lead not found or not in failed status'];
        }

        // Create mock lead data from stored Facebook lead
        $mockLeadData = [
            'id' => $facebookLead->facebook_lead_id,
            'created_time' => $facebookLead->facebook_created_time,
            'field_data' => []
        ];

        // Reconstruct field data
        if ($facebookLead->name) {
            $mockLeadData['field_data'][] = ['name' => 'full_name', 'values' => [$facebookLead->name]];
        }
        if ($facebookLead->email) {
            $mockLeadData['field_data'][] = ['name' => 'email', 'values' => [$facebookLead->email]];
        }
        if ($facebookLead->phone) {
            $mockLeadData['field_data'][] = ['name' => 'phone_number', 'values' => [$facebookLead->phone]];
        }
        if ($facebookLead->additional_data) {
            foreach ($facebookLead->additional_data as $key => $value) {
                $mockLeadData['field_data'][] = ['name' => $key, 'values' => [$value]];
            }
        }

        return $this->processLead($mockLeadData, $facebookLead->facebookLeadForm->facebook_form_id);
    }

    /**
     * Get lead processing statistics
     */
    public function getProcessingStats(int $businessAccountId): array
    {
        $stats = DB::table('facebook_leads as fl')
            ->join('facebook_lead_forms as flf', 'fl.facebook_lead_form_id', '=', 'flf.id')
            ->join('facebook_pages as fp', 'flf.facebook_page_id', '=', 'fp.id')
            ->where('fp.facebook_business_account_id', $businessAccountId)
            ->selectRaw('
                COUNT(*) as total_leads,
                COUNT(CASE WHEN fl.status = "processed" THEN 1 END) as processed,
                COUNT(CASE WHEN fl.status = "failed" THEN 1 END) as failed,
                COUNT(CASE WHEN fl.status = "new" THEN 1 END) as new_leads
            ')
            ->first();

        return [
            'total_leads' => $stats->total_leads,
            'processed' => $stats->processed,
            'failed' => $stats->failed,
            'new_leads' => $stats->new_leads,
            'success_rate' => $stats->total_leads > 0 ? 
                round(($stats->processed / $stats->total_leads) * 100, 2) : 0
        ];
    }

    /**
     * Get recent leads for a business account
     */
    public function getRecentLeads(int $businessAccountId, int $limit = 10): array
    {
        return FacebookLead::join('facebook_lead_forms', 'facebook_leads.facebook_lead_form_id', '=', 'facebook_lead_forms.id')
            ->join('facebook_pages', 'facebook_lead_forms.facebook_page_id', '=', 'facebook_pages.id')
            ->where('facebook_pages.facebook_business_account_id', $businessAccountId)
            ->select('facebook_leads.*')
            ->with(['facebookLeadForm.facebookPage', 'facebookLeadSource'])
            ->recent()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get leads count for today
     */
    public function getTodayLeadsCount(int $businessAccountId): int
    {
        return FacebookLead::join('facebook_lead_forms', 'facebook_leads.facebook_lead_form_id', '=', 'facebook_lead_forms.id')
            ->join('facebook_pages', 'facebook_lead_forms.facebook_page_id', '=', 'facebook_pages.id')
            ->where('facebook_pages.facebook_business_account_id', $businessAccountId)
            ->today()
            ->count();
    }

    /**
     * Sync Facebook Pages from Facebook Graph API
     */
    public function syncPagesFromFacebook($businessAccount): array
    {
        try {
            $accessToken = $businessAccount->access_token;
            $businessId = $businessAccount->facebook_business_id;
            
            // First, let's check what permissions and info we have with this token
            $tokenInfo = $this->callFacebookApi("/me", $accessToken, [
                'fields' => 'id,name,permissions{permission,status}'
            ]);
            
            if ($tokenInfo['success']) {
                Log::info('Facebook Token Info', [
                    'user_id' => $tokenInfo['data']['id'] ?? 'unknown',
                    'user_name' => $tokenInfo['data']['name'] ?? 'unknown',
                    'permissions' => $tokenInfo['data']['permissions']['data'] ?? []
                ]);
            }
            
            // Try to get pages the user manages (most common case)
            $response = $this->callFacebookApi("/me/accounts", $accessToken, [
                'fields' => 'id,name,access_token,category,fan_count,picture{url}',
                'limit' => 100
            ]);

            // If that fails, try getting pages from business account (if business_id is provided)
            if (!$response['success'] && !empty($businessId)) {
                $response = $this->callFacebookApi("/{$businessId}/client_pages", $accessToken, [
                    'fields' => 'id,name,access_token,category,fan_count,picture{url}',
                    'limit' => 100
                ]);
            }

            // If still no success, try just getting user's basic page access
            if (!$response['success']) {
                $response = $this->callFacebookApi("/me", $accessToken, [
                    'fields' => 'accounts{id,name,access_token,category,fan_count,picture{url}}',
                ]);
                
                // Restructure the response to match expected format
                if ($response['success'] && isset($response['data']['accounts'])) {
                    $response['data'] = ['data' => $response['data']['accounts']['data'] ?? []];
                }
            }

            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => 'Unable to fetch pages. Please ensure you have the correct permissions and access token. ' . $response['error']
                ];
            }

            $pagesData = $response['data']['data'] ?? [];
            $syncedCount = 0;

            foreach ($pagesData as $pageData) {
                // Update or create Facebook page record
                $page = \App\Models\FacebookPage::updateOrCreate([
                    'facebook_business_account_id' => $businessAccount->id,
                    'facebook_page_id' => $pageData['id'],
                ], [
                    'page_name' => $pageData['name'],
                    'page_category' => $pageData['category'] ?? 'Page',
                    'fan_count' => $pageData['fan_count'] ?? 0,
                    'page_access_token' => $pageData['access_token'] ?? null,
                    'profile_picture_url' => $pageData['picture']['data']['url'] ?? null,
                    'is_published' => true, // Assume published if we can access it
                    'is_active' => true,
                ]);

                $syncedCount++;
            }

            return [
                'success' => true,
                'count' => $syncedCount,
                'message' => "Successfully synced {$syncedCount} pages"
            ];

        } catch (Exception $e) {
            Log::error('Facebook pages sync failed', [
                'error' => $e->getMessage(),
                'business_account_id' => $businessAccount->id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync Lead Forms from Facebook Graph API for a specific page
     */
    public function syncLeadFormsFromFacebook($page): array
    {
        try {
            $accessToken = $page->page_access_token ?: $page->facebookBusinessAccount->access_token;
            
            if (!$accessToken) {
                throw new Exception('No access token available for page');
            }

            // Call Facebook Graph API to get lead forms for this page
            $response = $this->callFacebookApi("/{$page->facebook_page_id}/leadgen_forms", $accessToken, [
                'fields' => 'id,name,status,leads_count,created_time,questions,privacy_policy_url,follow_up_action_url',
                'limit' => 100
            ]);

            if (!$response['success']) {
                return $response;
            }

            $formsData = $response['data']['data'] ?? [];
            $syncedCount = 0;

            foreach ($formsData as $formData) {
                // Update or create lead form record
                $leadForm = \App\Models\FacebookLeadForm::updateOrCreate([
                    'facebook_page_id' => $page->id,
                    'facebook_form_id' => $formData['id'],
                ], [
                    'form_name' => $formData['name'],
                    'form_description' => $formData['name'], // Facebook doesn't provide description
                    'status' => $formData['status'] ?? 'ACTIVE',
                    'leads_count' => $formData['leads_count'] ?? 0,
                    'questions' => json_encode($formData['questions'] ?? []),
                    'privacy_policy_url' => $formData['privacy_policy_url'] ?? null,
                    'follow_up_action_url' => $formData['follow_up_action_url'] ?? null,
                    'facebook_created_time' => $formData['created_time'] ?? now(),
                    'is_active' => ($formData['status'] ?? 'ACTIVE') === 'ACTIVE',
                ]);

                $syncedCount++;
            }

            return [
                'success' => true,
                'count' => $syncedCount,
                'message' => "Successfully synced {$syncedCount} lead forms"
            ];

        } catch (Exception $e) {
            Log::error('Facebook lead forms sync failed', [
                'error' => $e->getMessage(),
                'page_id' => $page->id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync Leads from Facebook Graph API for all active lead forms
     */
    public function syncLeadsFromFacebook(FacebookBusinessAccount $businessAccount): array
    {
        try {
            if (!$businessAccount->access_token) {
                throw new Exception('No access token available for business account');
            }

            $syncedCount = 0;
            $totalProcessed = 0;

            // Get all active lead forms for this business account
            $leadForms = FacebookLeadForm::whereHas('facebookPage', function ($query) use ($businessAccount) {
                $query->where('facebook_business_account_id', $businessAccount->id);
            })->where('is_active', true)->get();

            foreach ($leadForms as $leadForm) {
                try {
                    // Get leads for this lead form from Facebook
                    $response = Http::get("https://graph.facebook.com/v18.0/{$leadForm->facebook_lead_form_id}/leads", [
                        'access_token' => $businessAccount->access_token,
                        'fields' => 'id,created_time,field_data',
                        'limit' => 100, // Facebook's max limit
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $leads = $data['data'] ?? [];

                        foreach ($leads as $leadData) {
                            $totalProcessed++;
                            
                            // Check if lead already exists
                            $existingLead = FacebookLead::where('facebook_lead_id', $leadData['id'])->first();
                            
                            if (!$existingLead) {
                                // Create new lead
                                FacebookLead::create([
                                    'facebook_lead_form_id' => $leadForm->id,
                                    'facebook_lead_id' => $leadData['id'],
                                    'facebook_created_time' => $leadData['created_time'],
                                    'field_data' => $leadData['field_data'] ?? [],
                                    'raw_data' => $leadData,
                                    'status' => 'pending',
                                    'processing_attempts' => 0
                                ]);
                                
                                $syncedCount++;
                            }
                        }

                        // Handle pagination if there are more leads
                        while (isset($data['paging']['next'])) {
                            $nextResponse = Http::get($data['paging']['next']);
                            if ($nextResponse->successful()) {
                                $data = $nextResponse->json();
                                $leads = $data['data'] ?? [];

                                foreach ($leads as $leadData) {
                                    $totalProcessed++;
                                    
                                    $existingLead = FacebookLead::where('facebook_lead_id', $leadData['id'])->first();
                                    
                                    if (!$existingLead) {
                                        FacebookLead::create([
                                            'facebook_lead_form_id' => $leadForm->id,
                                            'facebook_lead_id' => $leadData['id'],
                                            'facebook_created_time' => $leadData['created_time'],
                                            'field_data' => $leadData['field_data'] ?? [],
                                            'raw_data' => $leadData,
                                            'status' => 'pending',
                                            'processing_attempts' => 0
                                        ]);
                                        
                                        $syncedCount++;
                                    }
                                }
                            } else {
                                break; // Stop if pagination request fails
                            }
                        }

                    } else {
                        Log::warning('Failed to sync leads for form', [
                            'form_id' => $leadForm->id,
                            'facebook_form_id' => $leadForm->facebook_lead_form_id,
                            'response' => $response->body()
                        ]);
                    }

                } catch (Exception $e) {
                    Log::error('Error syncing leads for form', [
                        'form_id' => $leadForm->id,
                        'error' => $e->getMessage()
                    ]);
                    continue; // Continue with next form
                }
            }

            return [
                'success' => true,
                'count' => $syncedCount,
                'total_processed' => $totalProcessed,
                'message' => "Successfully synced {$syncedCount} new leads (processed {$totalProcessed} total)"
            ];

        } catch (Exception $e) {
            Log::error('Facebook leads sync failed', [
                'error' => $e->getMessage(),
                'business_account_id' => $businessAccount->id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Call Facebook Graph API
     */
    private function callFacebookApi(string $endpoint, string $accessToken, array $params = []): array
    {
        try {
            $url = 'https://graph.facebook.com/v18.0' . $endpoint;
            $params['access_token'] = $accessToken;

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url . '?' . http_build_query($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new Exception("CURL Error: {$error}");
            }

            $responseData = json_decode($response, true);

            if ($httpCode !== 200) {
                $errorMessage = 'Unknown Facebook API error';
                $errorCode = 'unknown';
                
                if (isset($responseData['error'])) {
                    $errorMessage = $responseData['error']['message'] ?? $errorMessage;
                    $errorCode = $responseData['error']['code'] ?? $errorCode;
                }

                // Provide helpful error messages for common issues
                $helpfulMessage = $errorMessage;
                if ($errorCode == 100) {
                    $helpfulMessage .= "\n\nPossible solutions:\n";
                    $helpfulMessage .= "- Ensure your access token has 'pages_manage_metadata' permission\n";
                    $helpfulMessage .= "- Make sure you're using a User Access Token, not an App Access Token\n";
                    $helpfulMessage .= "- Check that the business ID is correct (if using business account)";
                }

                throw new Exception("Facebook API Error ({$httpCode}): {$helpfulMessage}");
            }

            return [
                'success' => true,
                'data' => $responseData,
                'http_code' => $httpCode
            ];

        } catch (Exception $e) {
            Log::error('Facebook API call failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'params' => array_keys($params) // Log param keys, not values for security
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
