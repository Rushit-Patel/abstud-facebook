<?php

namespace App\Services;

use App\Models\FacebookLead;
use App\Models\FacebookLeadForm;
use App\Models\FacebookParameterMapping;
use App\Models\FacebookCustomFieldMapping;
use App\Models\FacebookLeadSource;
use App\Models\ClientLead; // Your existing lead model
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
                COUNT(CASE WHEN status = "processed" THEN 1 END) as processed,
                COUNT(CASE WHEN status = "failed" THEN 1 END) as failed,
                COUNT(CASE WHEN status = "new" THEN 1 END) as new_leads
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
}
