<?php

namespace App\Http\Controllers\Team\Automation;

use App\DataTables\Team\Automation\WhatsappCampaignDataTable;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coaching;
use App\Models\ForeignCountry;
use App\Models\LeadType;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignRule;
use App\Models\WhatsappProvider;
use App\Models\WhatsappTemplateVariableMapping;
use App\Models\ClientLead;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use App\Models\Source;
use App\Jobs\ProcessWhatsappCampaign;
use App\Jobs\ProcessWhatsappMessage;
use App\Services\TemplateVariableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WhatsappCampaignController extends Controller
{
    /**
     * Display campaigns list
     */
    public function index(WhatsappCampaignDataTable $dataTable)
    {
        return $dataTable->render('team.automation.whatsapp.campaigns.index');
    }

    /**
     * Show campaign creation form
     */
    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('team.dashboard'), 'title' => 'Dashboard'],
            ['label' => 'Automation', 'url' => route('team.automation.index'), 'title' => 'Automation'],
            ['label' => 'WhatsApp Campaigns', 'url' => route('team.automation.whatsapp.campaigns.index'), 'title' => 'Campaigns'],
            ['label' => 'Create Campaign', 'url' => null, 'title' => 'Create Campaign'],
        ];

        $providers = WhatsappProvider::where('is_active', true)->orderBy('priority')->get();
        $templates = WhatsappTemplateVariableMapping::select('template_name')->distinct()->pluck('template_name');
        
        // Lead filter options
        $leadStatuses = LeadStatus::active()->pluck('name', 'id');
        $leadTypes = LeadType::active()->pluck('name', 'id');
        $sources = Source::active()->pluck('name', 'id');
        $branches = Branch::active()->pluck('branch_name', 'id');
        $countries = ForeignCountry::active()->pluck('name', 'id');
        $coachings = Coaching::active()->pluck('name', 'id');

        $fieldOptions = [
            'no_of_days' => 'No. of Days',
            'tag' => 'Tag',
        ];

        $operators = [
            'equals' => 'Equals',
            'not_equals' => 'Not Equals',
            'contains' => 'Contains',
            'not_contains' => 'Does Not Contain',
            'starts_with' => 'Starts With',
            'ends_with' => 'Ends With',
            'greater_than' => 'Greater Than',
            'less_than' => 'Less Than',
            'in' => 'In List',
            'not_in' => 'Not In List',
        ];

        return view('team.automation.whatsapp.campaigns.create', compact(
            'breadcrumbs',
            'providers',
            'templates',
            'leadStatuses',
            'sources',
            'leadTypes',
            'branches',
            'countries',
            'coachings',
            'fieldOptions',
            'operators'
        ));
    }

    /**
     * Store new campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'execution_type' => 'required|in:one_time,automation',
            'message_type' => 'required|in:text,template',
            'message_content' => 'required_if:message_type,text|nullable|string|max:1000',
            'template_name' => 'required_if:message_type,template|nullable|string',
            'template_language' => 'nullable|string',
            'delay_between_messages' => 'nullable|integer|min:1|max:60',
            'priority' => 'required|integer|min:1|max:3',
            'scheduled_at' => 'nullable|date|after:now',
            'schedule_frequency' => 'nullable|in:daily,weekly,monthly',
            'schedule_config' => 'nullable|array',
            'schedule_config.hour' => 'nullable|integer|min:0|max:23',
            'schedule_config.minute' => 'nullable|integer|min:0|max:59',
            'schedule_config.day_of_week' => 'nullable|integer|min:0|max:6',
            'schedule_config.day_of_month' => 'nullable|integer|min:1|max:31',
            'lead_filters' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'rules' => 'nullable|array',
            'rules.*.field_name' => 'required_with:rules|string',
            'rules.*.operator' => 'required_with:rules|string',
            'rules.*.field_value' => 'required_with:rules|string',
        ]);

        try {
            DB::beginTransaction();

            // Determine campaign and trigger types
            $campaignType = $request->execution_type === 'one_time' ? 'bulk' : 'automation';
            $triggerType = $request->execution_type === 'one_time' ? 'manual' : 'time_based';

            // Auto-get template variables from WhatsappTemplateVariableMapping if template is selected
            $templateVariables = [];
            if ($request->message_type === 'template' && $request->template_name) {
                $templateVariables = WhatsappTemplateVariableMapping::getMappingsForTemplate($request->template_name);
            }

            // Create campaign
            $campaign = WhatsappCampaign::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'description' => $request->description,
                'campaign_type' => $campaignType,
                'trigger_type' => $triggerType,
                'trigger_conditions' => [],
                'message_type' => $request->message_type,
                'message_content' => $request->message_content,
                'template_name' => $request->template_name,
                'template_language' => $request->template_language ?? 'en_US',
                'template_variables' => $templateVariables,
                'provider_id' => null, // Not needed anymore
                'delay_minutes' => ($request->delay_between_messages ?? 5) / 60, // Convert seconds to minutes
                'is_active' => (bool) $request->input('is_active', true),
                'priority' => $request->priority,
                'execution_type' => $request->execution_type,
                'scheduled_at' => $request->scheduled_at,
                'schedule_frequency' => $request->schedule_frequency,
                'schedule_config' => $request->schedule_config ?? [],
                'lead_filters' => $request->lead_filters ?? [],
                'apply_to_new_leads' => $request->execution_type === 'automation',
                'created_by' => Auth::user()->id,
            ]);

            // Calculate next run time for automation campaigns
            if ($request->execution_type === 'automation' && $request->schedule_frequency) {
                $nextRunAt = $this->calculateNextRunTime($request->schedule_frequency, $request->schedule_config ?? []);
                $campaign->update(['next_run_at' => $nextRunAt]);
            }

            // Create campaign rules if provided
            if ($request->rules) {
                foreach ($request->rules as $rule) {
                    if (!empty($rule['field_name']) && !empty($rule['operator']) && !empty($rule['field_value'])) {
                        WhatsappCampaignRule::create([
                            'campaign_id' => $campaign->id,
                            'field_name' => $rule['field_name'],
                            'operator' => $rule['operator'],
                            'field_value' => explode(',', $rule['field_value']),
                        ]);
                    }
                }
            }
            DB::commit();

            return redirect()->route('team.automation.whatsapp.campaigns.index')
            ->with('success', 'WhatsApp campaign created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('team.automation.whatsapp.campaigns.index')
            ->with('error', 'Failed to create WhatsApp campaign: ' . $e->getMessage());
        }
    }

    /**
     * Show campaign details
     */
    public function show(WhatsappCampaign $campaign)
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('team.dashboard'), 'title' => 'Dashboard'],
            ['label' => 'Automation', 'url' => route('team.automation.index'), 'title' => 'Automation'],
            ['label' => 'WhatsApp Campaigns', 'url' => route('team.automation.whatsapp.campaigns.index'), 'title' => 'Campaigns'],
            ['label' => $campaign->name, 'url' => null, 'title' => 'Campaign Details'],
        ];

        $campaign->load(['provider', 'creator', 'rules']);
        $stats = $campaign->getStats();
        
    
        return view('team.automation.whatsapp.campaigns.show', compact(
            'breadcrumbs',
            'campaign',
            'stats'
        ));
    }

    /**
     * Show campaign edit form
     */
    public function edit(WhatsappCampaign $campaign)
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('team.dashboard'), 'title' => 'Dashboard'],
            ['label' => 'Automation', 'url' => route('team.automation.index'), 'title' => 'Automation'],
            ['label' => 'WhatsApp Campaigns', 'url' => route('team.automation.whatsapp.campaigns.index'), 'title' => 'Campaigns'],
            ['label' => 'Edit Campaign', 'url' => null, 'title' => 'Edit Campaign'],
        ];

        $providers = WhatsappProvider::where('is_active', true)->orderBy('priority')->get();
        $templates = WhatsappTemplateVariableMapping::select('template_name')->distinct()->pluck('template_name');
        
        // Get unique lead statuses and sources from client_leads table
        $leadStatuses = LeadStatus::active()->pluck('name', 'id');
        $leadTypes = LeadType::active()->pluck('name', 'id');
        $sources = Source::active()->pluck('name', 'id');
        $branches = Branch::active()->pluck('branch_name', 'id');
        $countries = ForeignCountry::active()->pluck('name', 'id');
        $coachings = Coaching::active()->pluck('name', 'id');

        $fieldOptions = [
            'no_of_days' => 'No. of Days',
            'tag' => 'Tag',
        ];

        $operators = [
            'equals' => 'Equals',
            'not_equals' => 'Not Equals',
            'contains' => 'Contains',
            'not_contains' => 'Does Not Contain',
            'starts_with' => 'Starts With',
            'ends_with' => 'Ends With',
            'greater_than' => 'Greater Than',
            'less_than' => 'Less Than',
            'in' => 'In List',
            'not_in' => 'Not In List',
        ];

        $executionTypes = [
            'one_time' => 'One Time',
            'automation' => 'Set in Automation'
        ];

        $scheduleFrequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly'
        ];

        $messageTypes = [
            'text' => 'Text Message',
            'template' => 'Template Message',
            'media' => 'Media Message'
        ];

        $priorities = [
            1 => 'High',
            2 => 'Medium', 
            3 => 'Low'
        ];

        $campaign->load('rules');

        // Format rules for the repeater
        $formattedRules = $campaign->rules->map(function($rule) {
            return [
                'field_name' => $rule->field_name,
                'operator' => $rule->operator,
                'field_value' => is_array($rule->field_value) ? implode(', ', $rule->field_value) : $rule->field_value
            ];
        })->toArray();

        // Add formatted rules to campaign object for easier access in view
        $campaign->formatted_rules = $formattedRules;

        return view('team.automation.whatsapp.campaigns.edit', compact(
            'breadcrumbs', 'campaign', 'providers', 'templates', 'executionTypes', 
            'scheduleFrequencies', 'fieldOptions', 'operators', 'messageTypes', 
            'priorities', 'leadStatuses', 'leadTypes', 'sources','branches','countries','coachings'
        ));
    }

    /**
     * Update campaign
     */
    public function update(Request $request, WhatsappCampaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'message_type' => 'required|string|in:text,template,media',
            'message_content' => 'required_if:message_type,text|nullable|string',
            'template_name' => 'required_if:message_type,template|nullable|string',
            'template_language' => 'nullable|string',
            'execution_type' => 'required|in:one_time,automation',
            'scheduled_at' => 'required_if:execution_type,one_time|nullable|date|after:now',
            'schedule_frequency' => 'required_if:execution_type,automation|nullable|in:daily,weekly,monthly',
            'schedule_config' => 'nullable|array',
            'schedule_config.hour' => 'nullable|integer|min:0|max:23',
            'schedule_config.minute' => 'nullable|integer|min:0|max:59',
            'schedule_config.day_of_week' => 'nullable|integer|min:0|max:6',
            'schedule_config.day_of_month' => 'nullable|integer|min:1|max:31',
            'priority' => 'required|integer|in:1,2,3',
            'delay_minutes' => 'nullable|integer|min:0|max:1440',
            'retry_attempts' => 'nullable|integer|min:0|max:5',
            'is_active' => 'boolean',
            'rules' => 'nullable|array',
            'rules.*.field_name' => 'required_with:rules|string',
            'rules.*.operator' => 'required_with:rules|string',
            'rules.*.field_value' => 'required_with:rules',
        ]);

        try {
            DB::beginTransaction();

            // Handle checkbox - convert to boolean
            $validated['is_active'] = (bool) $request->input('is_active', false);

            // Generate slug from name if needed
            if ($campaign->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']);
                
                // Ensure slug is unique
                $originalSlug = $validated['slug'];
                $counter = 1;
                while (WhatsappCampaign::where('slug', $validated['slug'])->where('id', '!=', $campaign->id)->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Handle schedule configuration
            if ($validated['execution_type'] === 'automation' && !empty($validated['schedule_config'])) {
                // Calculate next run time for automation campaigns
                $nextRunAt = $this->calculateNextRunTime($validated['schedule_frequency'], $validated['schedule_config']);
                $validated['next_run_at'] = $nextRunAt;
            } else {
                $validated['schedule_config'] = null;
                $validated['next_run_at'] = null;
            }

            // Auto-get template variables from WhatsappTemplateVariableMapping if template is selected
            $templateVariables = [];
            if ($validated['message_type'] === 'template' && $validated['template_name']) {
                $templateVariables = WhatsappTemplateVariableMapping::getMappingsForTemplate($validated['template_name']);
            }
            $validated['template_variables'] = $templateVariables;

            // Update campaign
            $campaign->update($validated);

            // Update rules
            if (isset($validated['rules']) && is_array($validated['rules'])) {
                // Delete existing rules
                $campaign->rules()->delete();
                
                // Create new rules
                foreach ($validated['rules'] as $ruleData) {
                    if (!empty($ruleData['field_name']) && !empty($ruleData['operator']) && !empty($ruleData['field_value'])) {
                        $campaign->rules()->create([
                            'field_name' => $ruleData['field_name'],
                            'operator' => $ruleData['operator'],
                            'field_value' => is_string($ruleData['field_value']) 
                                ? explode(',', $ruleData['field_value']) 
                                : $ruleData['field_value'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('team.automation.whatsapp.campaigns.show', $campaign)
                ->with('success', 'WhatsApp campaign updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update WhatsApp campaign: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update WhatsApp campaign: ' . $e->getMessage());
        }
    }

    /**
     * Execute campaign
     */
    public function execute(WhatsappCampaign $campaign)
    {
        try {
            if (!$campaign->canExecute()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaign cannot be executed at this time'
                ], 400);
            }

            // Dispatch job to execute campaign
            ProcessWhatsappCampaign::dispatch($campaign);

            return response()->json([
                'success' => true,
                'message' => 'Campaign execution started successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to execute WhatsApp campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to execute campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle campaign status
     */
    public function toggle(WhatsappCampaign $campaign)
    {
        try {
            if ($campaign->is_active) {
                $campaign->pause();
                $message = 'Campaign paused successfully';
            } else {
                $campaign->resume();
                $message = 'Campaign resumed successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $campaign->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle WhatsApp campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete campaign
     */
    public function destroy(WhatsappCampaign $campaign)
    {
        try {
            $campaign->delete();

            return redirect()->route('team.automation.whatsapp.campaigns.index')
                ->with('success', 'Campaign deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete WhatsApp campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to delete campaign: ' . $e->getMessage()]);
        }
    }

    /**
     * Get campaign preview
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_filters' => 'nullable|array',
            'rules' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = ClientLead::query();

            // Apply lead filters
            if ($request->lead_filters) {
                foreach ($request->lead_filters as $filter => $value) {
                    if (!empty($value)) {
                        if (is_array($value)) {
                            $query->whereIn($filter, $value);
                        } else {
                            $query->where($filter, $value);
                        }
                    }
                }
            }

            // Apply rules
            if ($request->rules) {
                foreach ($request->rules as $rule) {
                    $this->applyRuleToQuery($query, $rule);
                }
            }

            $totalLeads = $query->count();
            $leadsWithPhone = $query->whereNotNull('whatsapp_no')->count();
            
            $sampleLeads = $query->whereNotNull('whatsapp_no')
                ->take(5)
                ->get(['id', 'first_name', 'last_name', 'whatsapp_no']);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_leads' => $totalLeads,
                    'leads_with_phone' => $leadsWithPhone,
                    'estimated_recipients' => $leadsWithPhone,
                    'sample_leads' => $sampleLeads
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply rule to query
     */
    private function applyRuleToQuery($query, $rule): void
    {
        $fieldName = $rule['field_name'];
        $operator = $rule['operator'];
        $fieldValue = $rule['field_value'];

        switch ($operator) {
            case 'equals':
                $query->where($fieldName, $fieldValue[0]);
                break;
            case 'not_equals':
                $query->where($fieldName, '!=', $fieldValue[0]);
                break;
            case 'in':
                $query->whereIn($fieldName, $fieldValue);
                break;
            case 'not_in':
                $query->whereNotIn($fieldName, $fieldValue);
                break;
            case 'greater_than':
                $query->where($fieldName, '>', $fieldValue[0]);
                break;
            case 'less_than':
                $query->where($fieldName, '<', $fieldValue[0]);
                break;
            case 'contains':
                $query->where($fieldName, 'like', '%' . $fieldValue[0] . '%');
                break;
            case 'not_contains':
                $query->where($fieldName, 'not like', '%' . $fieldValue[0] . '%');
                break;
        }
    }

    /**
     * Duplicate a campaign
     */
    public function duplicate(WhatsappCampaign $campaign)
    {
        try {
            DB::beginTransaction();

            // Create a copy of the campaign
            $newCampaign = $campaign->replicate();
            $newCampaign->name = $campaign->name . ' (Copy)';
            $newCampaign->is_active = false; // Start as inactive
            $newCampaign->created_by = Auth::id();
            $newCampaign->save();

            // Copy campaign rules
            foreach ($campaign->rules as $rule) {
                $newRule = $rule->replicate();
                $newRule->campaign_id = $newCampaign->id;
                $newRule->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Campaign duplicated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate WhatsApp campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test WhatsApp message
     */
    public function sendTest(Request $request, WhatsappCampaign $campaign)
    {
        $validationRules = [
            'test_phone' => 'required|string',
            'test_phone_country_code' => 'nullable|string',
            'test_name' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($validationRules);

        try {
            // Get the active WhatsApp provider
            $provider = WhatsappProvider::where('is_active', true)
                ->orderBy('priority')
                ->first();

            if (!$provider) {
                return redirect()->back()
                    ->with('error', 'No active WhatsApp provider found');
            }

            // Format phone number
            $phoneNumber = $this->formatPhoneNumber(
                $validated['test_phone'], 
                $validated['test_phone_country_code'] ?? null
            );

            // Create WhatsApp message record directly
            if ($campaign->message_type === 'text') {
                // Use TemplateVariableService to get sample values and replace variables
                $sampleVariables = TemplateVariableService::getSampleValues();
                // Override with test user data
                $sampleVariables['name'] = $validated['test_name'] ?? 'Test User';
                $sampleVariables['first_name'] = explode(' ', $validated['test_name'] ?? 'Test User')[0];
                $sampleVariables['phone'] = $phoneNumber;
                
                $messageContent = TemplateVariableService::replaceVariables($campaign->message_content, $sampleVariables);
                
                \App\Models\WhatsappMessage::create([
                    'campaign_id' => $campaign->id,
                    'phone_number' => $phoneNumber,
                    'message_type' => 'text',
                    'message_content' => $messageContent,
                    'template_variables' => $sampleVariables,
                    'status' => 'pending',
                    'is_test' => true,
                    'created_by' => Auth::user()->id,
                ]);
                
            } elseif ($campaign->message_type === 'template') {
                // Get template variable mappings
                $templateMappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($campaign->template_name);
                
                if (!empty($templateMappings)) {
                    // Use TemplateVariableService to get sample values
                    $sampleVariables = TemplateVariableService::getSampleValues();
                    // Override with test user data
                    $sampleVariables['name'] = $validated['test_name'] ?? 'Test User';
                    $sampleVariables['client_name'] = $validated['test_name'] ?? 'Test User';
                    $sampleVariables['first_name'] = explode(' ', $validated['test_name'] ?? 'Test User')[0];
                    $sampleVariables['phone'] = $phoneNumber;
                    
                    // Map system variables to WhatsApp template variables
                    $finalTemplateVariables = [];
                    foreach ($templateMappings as $whatsappVar => $systemVar) {
                        $finalTemplateVariables[$whatsappVar] = $sampleVariables[$systemVar] ?? "[{$systemVar}]";
                    }
                } else {
                    // No mappings found - use basic test values
                    $finalTemplateVariables = [
                        '1' => $validated['test_name'] ?? 'Test User',
                        '2' => $phoneNumber,
                        '3' => 'Sample Value 3'
                    ];
                }

                \App\Models\WhatsappMessage::create([
                    'campaign_id' => $campaign->id,
                    'phone_number' => $phoneNumber,
                    'message_type' => 'template',
                    'template_name' => $campaign->template_name,
                    'template_variables' => $finalTemplateVariables,
                    'message_content' => json_encode([
                        'template' => $campaign->template_name,
                        'parameters' => $finalTemplateVariables,
                        'language' => $campaign->template_language ?? 'en_US'
                    ]),
                    'status' => 'pending',
                    'is_test' => true,
                    'created_by' => Auth::user()->id,
                ]);
                
            } else {
                return redirect()->back()
                    ->with('error', 'Media messages are not supported for test messages');
            }

            // Log the test message creation
            Log::info('Test WhatsApp message queued in database', [
                'campaign_id' => $campaign->id,
                'phone' => $phoneNumber,
                'message_type' => $campaign->message_type,
                'provider' => $provider->slug,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()
                ->with('success', 'Test message has been queued in database. Use "php artisan whatsapp:send-pending" to send it.');

        } catch (\Exception $e) {
            Log::error('Failed to queue test WhatsApp message', [
                'campaign_id' => $campaign->id,
                'phone' => $validated['test_phone'] ?? null,
                'error' => $e->getMessage(),
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to queue test message: ' . $e->getMessage());
        }
    }

    /**
     * Replace variables in message content
     */
    private function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace(['{' . $key . '}', '{{' . $key . '}}'], $value, $content);
        }
        
        return $content;
    }

    /**
     * Format phone number with country code
     */
    private function formatPhoneNumber(string $phone, ?string $countryCode = null): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // If phone already starts with +, return as is
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        
        // Add country code if provided
        if ($countryCode) {
            $countryCode = preg_replace('/[^\d+]/', '', $countryCode);
            if (!str_starts_with($countryCode, '+')) {
                $countryCode = '+' . $countryCode;
            }
            return $countryCode . $phone;
        }
        
        // Default to +91 if no country code provided
        return '+91' . $phone;
    }

    /**
     * Calculate the next run time for automation campaigns
     */
    private function calculateNextRunTime(string $frequency, array $config = []): \Carbon\Carbon
    {
        $now = \Carbon\Carbon::now();
        
        switch ($frequency) {
            case 'daily':
                $hour = $config['hour'] ?? 9; // Default to 9 AM
                $minute = $config['minute'] ?? 0;
                $nextRun = $now->copy()->setTime($hour, $minute, 0);
                
                // If time has passed today, schedule for tomorrow
                if ($nextRun->isPast()) {
                    $nextRun->addDay();
                }
                return $nextRun;
                
            case 'weekly':
                $dayOfWeek = $config['day_of_week'] ?? 1; // Default to Monday
                $hour = $config['hour'] ?? 9;
                $minute = $config['minute'] ?? 0;
                $nextRun = $now->copy()->next($dayOfWeek)->setTime($hour, $minute, 0);
                return $nextRun;
                
            case 'monthly':
                $dayOfMonth = $config['day_of_month'] ?? 1; // Default to 1st of month
                $hour = $config['hour'] ?? 9;
                $minute = $config['minute'] ?? 0;
                $nextRun = $now->copy()->startOfMonth()->addDays($dayOfMonth - 1)->setTime($hour, $minute, 0);
                
                // If date has passed this month, schedule for next month
                if ($nextRun->isPast()) {
                    $nextRun->addMonth();
                }
                return $nextRun;
                
            default:
                return $now->addHour(); // Default fallback
        }
    }
}
