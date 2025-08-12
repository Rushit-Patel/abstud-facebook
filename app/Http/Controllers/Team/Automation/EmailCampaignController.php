<?php

namespace App\Http\Controllers\Team\Automation;

use App\Http\Controllers\Controller;
use App\Mail\EmailTemplateMail;
use App\Models\Branch;
use App\Models\Coaching;
use App\Models\EmailCampaign;
use App\Models\EmailAutomationRule;
use App\Models\EmailTemplate;
use App\Models\ForeignCountry;
use App\Models\LeadStatus;
use App\Models\LeadTag;
use App\Models\LeadType;
use App\Models\Source;
use App\DataTables\Team\Automation\EmailCampaignDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmailCampaignController extends Controller
{
    public function index(EmailCampaignDataTable $dataTable)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Email Campaigns', 'url' => null],
        ];

        return $dataTable->render('team.automation.email.campaigns.index', compact('breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Email Campaigns', 'url' => route('team.automation.email.campaigns.index')],
            ['title' => 'Create Campaign', 'url' => null],
        ];

        $templates = EmailTemplate::where('is_active', true)->pluck('subject', 'id');
        
        $executionTypes = [
            'one_time' => 'One Time',
            'automation' => 'Set in Automation'
        ];

        $scheduleFrequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly'
        ];

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

        return view('team.automation.email.campaigns.create', compact(
            'breadcrumbs', 'templates', 'executionTypes', 'scheduleFrequencies',
            'fieldOptions', 'operators', 'leadStatuses', 'leadTypes', 'sources', 
            'branches', 'countries', 'coachings'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'execution_type' => 'required|in:one_time,automation',
            'scheduled_at' => 'required_if:execution_type,one_time|nullable',
            'schedule_frequency' => 'required_if:execution_type,automation|nullable|in:daily,weekly,monthly,yearly',
            'schedule_config' => 'nullable|array',
            'lead_filters' => 'nullable|array',
            'email_template_id' => 'required|exists:email_templates,id',
            'delay_minutes' => 'required|integer|min:0',
            'priority' => 'required|integer|min:1|max:3',
            'rules' => 'nullable|array',
            'rules.*.field_name' => 'required_with:rules|string',
            'rules.*.operator' => 'required_with:rules|string',
            'rules.*.field_value' => 'required_with:rules'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['trigger_type'] = 'manual'; // Set default trigger type
        $validated['trigger_conditions'] = []; // Set default empty array for trigger conditions

        if(isset($validated['scheduled_at']) && $validated['execution_type'] === 'one_time') {
            // Ensure scheduled_at is a valid date
            $validated['scheduled_at'] = Carbon::createFromFormat('d/m/Y H:i', $validated['scheduled_at'])->format('Y-m-d H:i');
        } else {
            $validated['scheduled_at'] = null; 
        }
        // Calculate next run time for automation campaigns
        if ($validated['execution_type'] === 'automation' && $validated['schedule_frequency']) {
            $validated['next_run_at'] = $this->calculateNextRunTime($validated['schedule_frequency'], $validated['schedule_config'] ?? []);
        }

        $campaign = EmailCampaign::create($validated);

        // Create rules if provided
        if (isset($validated['rules']) && is_array($validated['rules'])) {
            foreach ($validated['rules'] as $rule) {
                // Handle field_value - convert to array if it's a string
                if (isset($rule['field_value'])) {
                    if (is_string($rule['field_value'])) {
                        // Convert comma-separated string to array
                        $rule['field_value'] = array_filter(array_map('trim', explode(',', $rule['field_value'])));
                    } elseif (is_array($rule['field_value'])) {
                        // If it's already an array, filter out empty values
                        $rule['field_value'] = array_filter($rule['field_value']);
                    }
                }
                $campaign->rules()->create($rule);
            }
        }

        return redirect()->route('team.automation.email.campaigns.index')
            ->with('success', 'Email campaign created successfully.');
    }

    public function show(EmailCampaign $campaign)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Email Campaigns', 'url' => route('team.automation.email.campaigns.index')],
            ['title' => $campaign->name, 'url' => null],
        ];

        $campaign->load(['emailTemplate', 'rules', 'logs.clientLead.client']);

        // Process lead filters to get names instead of IDs
        $processedLeadFilters = [];
        if ($campaign->lead_filters && is_array($campaign->lead_filters)) {
            foreach ($campaign->lead_filters as $filterType => $filterValues) {
                if (!empty($filterValues)) {
                    $displayNames = [];
                    $values = is_array($filterValues) ? $filterValues : [$filterValues];
                    
                    foreach ($values as $value) {
                        switch ($filterType) {
                            case 'status':
                                $model = \App\Models\LeadStatus::find($value);
                                $displayNames[] = $model ? $model->name : $value;
                                break;
                            case 'lead_type':
                                $model = \App\Models\LeadType::find($value);
                                $displayNames[] = $model ? $model->name : $value;
                                break;
                            case 'source':
                                $model = \App\Models\Source::find($value);
                                $displayNames[] = $model ? $model->name : $value;
                                break;
                            case 'branch':
                                $model = \App\Models\Branch::find($value);
                                $displayNames[] = $model ? $model->branch_name : $value;
                                break;
                            case 'country':
                                $model = \App\Models\ForeignCountry::find($value);
                                $displayNames[] = $model ? $model->name : $value;
                                break;
                            case 'coaching':
                                $model = \App\Models\Coaching::find($value);
                                $displayNames[] = $model ? $model->name : $value;
                                break;
                            default:
                                $displayNames[] = $value;
                        }
                    }
                    
                    $processedLeadFilters[$filterType] = implode(', ', $displayNames);
                }
            }
        }

        $campaign->processed_lead_filters = $processedLeadFilters;

        return view('team.automation.email.campaigns.show', compact('breadcrumbs', 'campaign'));
    }

    public function edit(EmailCampaign $campaign)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Email Campaigns', 'url' => route('team.automation.email.campaigns.index')],
            ['title' => 'Edit Campaign', 'url' => null],
        ];

        $templates = EmailTemplate::where('is_active', true)->pluck('subject', 'id');
        
        $executionTypes = [
            'one_time' => 'One Time',
            'automation' => 'Set in Automation'
        ];

        $scheduleFrequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly'
        ];

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

        return view('team.automation.email.campaigns.edit', compact(
            'breadcrumbs', 'campaign', 'templates', 'executionTypes', 'scheduleFrequencies',
            'fieldOptions', 'operators', 'leadStatuses', 'leadTypes', 'sources', 
            'branches', 'countries', 'coachings'
        ));
    }

    public function update(Request $request, EmailCampaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'execution_type' => 'required|in:one_time,automation',
            'scheduled_at' => 'required_if:execution_type,one_time|nullable|date|after:now',
            'schedule_frequency' => 'required_if:execution_type,automation|nullable|in:daily,weekly,monthly,yearly',
            'schedule_config' => 'nullable|array',
            'lead_filters' => 'nullable|array',
            'email_template_id' => 'required|exists:email_templates,id',
            'delay_minutes' => 'required|integer|min:0',
            'priority' => 'required|integer|min:1|max:3',
            'rules' => 'nullable|array',
            'rules.*.field_name' => 'required_with:rules|string',
            'rules.*.operator' => 'required_with:rules|string',
            'rules.*.field_value' => 'required_with:rules'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Calculate next run time for automation campaigns
        if ($validated['execution_type'] === 'automation' && $validated['schedule_frequency']) {
            $validated['next_run_at'] = $this->calculateNextRunTime($validated['schedule_frequency'], $validated['schedule_config'] ?? []);
        }

        $campaign->update($validated);

        // Update rules
        $campaign->rules()->delete();
        if (isset($validated['rules']) && is_array($validated['rules'])) {
            foreach ($validated['rules'] as $rule) {
                // Handle field_value - convert to array if it's a string
                if (isset($rule['field_value'])) {
                    if (is_string($rule['field_value'])) {
                        // Convert comma-separated string to array
                        $rule['field_value'] = array_filter(array_map('trim', explode(',', $rule['field_value'])));
                    } elseif (is_array($rule['field_value'])) {
                        // If it's already an array, filter out empty values
                        $rule['field_value'] = array_filter($rule['field_value']);
                    }
                }
                $campaign->rules()->create($rule);
            }
        }

        return redirect()->route('team.automation.email.campaigns.index')
            ->with('success', 'Email campaign updated successfully.');
    }

    public function destroy(EmailCampaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('team.automation.email.campaigns.index')
            ->with('success', 'Email campaign deleted successfully.');
    }

    public function toggle(EmailCampaign $campaign)
    {
        $campaign->update(['is_active' => !$campaign->is_active]);

        $status = $campaign->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Campaign has been {$status}.",
            'is_active' => $campaign->is_active
        ]);
    }

    public function test($campaignId, Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        // try {
            $campaign = EmailCampaign::findOrFail($campaignId);
            // Load the campaign with its email template
            $campaign->load('emailTemplate');
            
            if (!$campaign->emailTemplate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No email template associated with this campaign.'
                ]);
            }

            $testEmail = $request->input('test_email');

            // Validate template variables if the template requires them
            $emailTemplate = $campaign->emailTemplate;

            $templateVariables = EmailTemplateMail::getTemplateVariables();
            $validation = $emailTemplate->validateVariables(array_keys($templateVariables));
            
            if (!$validation['valid']) {
                Log::warning('Template missing variables for campaign test', [
                    'campaign_id' => $campaign->getAttribute('id'),
                    'missing_variables' => $validation['missing']
                ]);
            }

            // Send the test email using the TemplateMail class
            Mail::to($testEmail)->send(new EmailTemplateMail($campaign, $testEmail));

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $testEmail
            ]);

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Failed to send test email: ' . $e->getMessage()
        //     ]);
        // }
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
                
            case 'yearly':
                $month = $config['month'] ?? 1; // Default to January
                $dayOfMonth = $config['day_of_month'] ?? 1;
                $hour = $config['hour'] ?? 9;
                $minute = $config['minute'] ?? 0;
                $nextRun = $now->copy()->startOfYear()->addMonths($month - 1)->addDays($dayOfMonth - 1)->setTime($hour, $minute, 0);
                
                // If date has passed this year, schedule for next year
                if ($nextRun->isPast()) {
                    $nextRun->addYear();
                }
                return $nextRun;
                
            default:
                return $now->addHour(); // Default fallback
        }
    }
}
