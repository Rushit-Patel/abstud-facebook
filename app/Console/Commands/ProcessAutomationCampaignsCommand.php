<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailCampaign;
use App\Models\WhatsappCampaign;
use App\Models\ClientLead;
use App\Models\EmailAutomationLog;
use App\Models\WhatsappMessage;
use App\Models\WhatsappTemplateVariableMapping;
use App\Jobs\SendAutomationEmailJob;
use App\Jobs\SendAutomationWhatsappJob;
use App\Services\TemplateVariableService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helpers;

class ProcessAutomationCampaignsCommand extends Command
{
    protected $signature = 'automation:process-campaigns 
                            {--type=all : Type of campaigns to process (email, whatsapp, all)}
                            {--campaign-id= : Process specific campaign ID}
                            {--dry-run : Show what would be processed without actually doing it}
                            {--batch-size=100 : Number of leads to process per campaign}';
    
    protected $description = 'Process scheduled automation campaigns for both email and WhatsApp';

    public function handle(): int
    {
        $type = $this->option('type');
        $campaignId = $this->option('campaign-id');
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');

        $this->info('🚀 Processing automation campaigns...');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No messages will be sent');
        }

        try {
            $totalProcessed = 0;

            // Process Email Campaigns
            if (in_array($type, ['email', 'all'])) {
                $emailProcessed = $this->processEmailCampaigns($campaignId, $dryRun, $batchSize);
                $totalProcessed += $emailProcessed;
            }

            // Process WhatsApp Campaigns
            if (in_array($type, ['whatsapp', 'all'])) {
                $whatsappProcessed = $this->processWhatsappCampaigns($campaignId, $dryRun, $batchSize);
                $totalProcessed += $whatsappProcessed;
            }

            $this->info("✅ Successfully processed {$totalProcessed} messages total");
            
            Log::info('Automation campaigns processed', [
                'type' => $type,
                'total_processed' => $totalProcessed,
                'dry_run' => $dryRun
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error processing automation campaigns: ' . $e->getMessage());
            Log::error('Automation campaigns processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }

    /**
     * Process email campaigns
     */
    private function processEmailCampaigns($campaignId, bool $dryRun, int $batchSize): int
    {
        $this->info('📧 Processing Email Campaigns...');
        
        $campaigns = $this->getScheduledEmailCampaigns($campaignId);
        
        if ($campaigns->isEmpty()) {
            $this->info('   ℹ️  No scheduled email campaigns to process');
            return 0;
        }

        $totalProcessed = 0;
        $this->info("   📊 Found {$campaigns->count()} email campaigns to process");

        foreach ($campaigns as $campaign) {
            $processed = $this->processEmailCampaign($campaign, $dryRun, $batchSize);
            $totalProcessed += $processed;
            
            if (!$dryRun && $processed > 0) {
                $this->updateEmailCampaignSchedule($campaign);
            }
        }

        return $totalProcessed;
    }

    /**
     * Process WhatsApp campaigns
     */
    private function processWhatsappCampaigns($campaignId, bool $dryRun, int $batchSize): int
    {
        $this->info('💬 Processing WhatsApp Campaigns...');
        
        $campaigns = $this->getScheduledWhatsappCampaigns($campaignId);
        
        if ($campaigns->isEmpty()) {
            $this->info('   ℹ️  No scheduled WhatsApp campaigns to process');
            return 0;
        }

        $totalProcessed = 0;
        $this->info("   📊 Found {$campaigns->count()} WhatsApp campaigns to process");

        foreach ($campaigns as $campaign) {
            $processed = $this->processWhatsappCampaign($campaign, $dryRun, $batchSize);
            $totalProcessed += $processed;
            
            if (!$dryRun && $processed > 0) {
                $this->updateWhatsappCampaignSchedule($campaign);
            }
        }

        return $totalProcessed;
    }

    /**
     * Get email campaigns that are due to run
     */
    private function getScheduledEmailCampaigns($campaignId = null)
    {
        $query = EmailCampaign::where('is_active', true)
            ->where('execution_type', 'automation')
            ->whereNotNull('schedule_frequency')
            ->where(function ($q) {
                $q->whereNull('next_run_at')
                  ->orWhere('next_run_at', '<=', now());
            });

        if ($campaignId) {
            $query->where('id', $campaignId);
        }

        return $query->with('emailTemplate')->get();
    }

    /**
     * Get WhatsApp campaigns that are due to run
     */
    private function getScheduledWhatsappCampaigns($campaignId = null)
    {
        $query = WhatsappCampaign::where('is_active', true)
            ->where('execution_type', 'automation')
            ->whereNotNull('schedule_frequency')
            ->where(function ($q) {
                $q->whereNull('next_run_at')
                  ->orWhere('next_run_at', '<=', now());
            });

        if ($campaignId) {
            $query->where('id', $campaignId);
        }

        return $query->with('rules')->get();
    }

    /**
     * Process a single email campaign
     */
    private function processEmailCampaign(EmailCampaign $campaign, bool $dryRun, int $batchSize): int
    {
        $campaignName = $campaign->name;
        $this->info("   🎯 Processing email campaign: {$campaignName}");
        
        // try {
            $leads = $this->getFilteredLeadsForEmail($campaign, $batchSize);
            
            if ($leads->isEmpty()) {
                $this->info("      ℹ️  No matching leads found");
                return 0;
            }

            $leadCount = $leads->count();
            $this->info("      📈 Found {$leadCount} matching leads");

            if ($dryRun) {
                $this->warn("      🔍 DRY RUN: Would send {$leadCount} emails");
                return $leadCount;
            }

            $processed = 0;
            $failed = 0;

            foreach ($leads as $lead) {
                try {
                    if ($this->isEmailAlreadySent($campaign, $lead)) {
                        continue;
                    }

                    $emailLog = $this->createEmailLog($campaign, $lead);
                    SendAutomationEmailJob::dispatch($emailLog)->onQueue('emails');
                    $processed++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    $this->logError('email', $campaign->id, $lead->id, $e->getMessage());
                }
            }

            $this->info("      ✅ Successfully queued {$processed} emails");
            if ($failed > 0) {
                $this->warn("      ⚠️  {$failed} emails failed to queue");
            }

            return $processed;

        // } catch (\Exception $e) {
        //     $this->error("      ❌ Error processing email campaign {$campaignName}: " . $e->getMessage());
        //     Log::error('Email campaign processing failed', [
        //         'campaign_id' => $campaign->id,
        //         'error' => $e->getMessage()
        //     ]);
            
        //     return 0;
        // }
    }

    /**
     * Process a single WhatsApp campaign
     */
    private function processWhatsappCampaign(WhatsappCampaign $campaign, bool $dryRun, int $batchSize): int
    {
        $campaignName = $campaign->name;
        $this->info("   🎯 Processing WhatsApp campaign: {$campaignName}");
        
        try {
            $leads = $this->getFilteredLeadsForWhatsapp($campaign, $batchSize);
            
            if ($leads->isEmpty()) {
                $this->info("      ℹ️  No matching leads found");
                return 0;
            }

            $leadCount = $leads->count();
            $this->info("      📈 Found {$leadCount} matching leads");

            if ($dryRun) {
                $this->warn("      🔍 DRY RUN: Would send {$leadCount} WhatsApp messages");
                return $leadCount;
            }

            $processed = 0;
            $failed = 0;

            foreach ($leads as $lead) {
                try {
                    if ($this->isWhatsappAlreadySent($campaign, $lead)) {
                        continue;
                    }

                    $whatsappMessage = $this->createWhatsappMessage($campaign, $lead);
                    SendAutomationWhatsappJob::dispatch($whatsappMessage)->onQueue('whatsapp');
                    $processed++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    $this->logError('whatsapp', $campaign->id, $lead->id, $e->getMessage());
                }
            }

            $this->info("      ✅ Successfully queued {$processed} WhatsApp messages");
            if ($failed > 0) {
                $this->warn("      ⚠️  {$failed} messages failed to queue");
            }

            return $processed;

        } catch (\Exception $e) {
            $this->error("      ❌ Error processing WhatsApp campaign {$campaignName}: " . $e->getMessage());
            Log::error('WhatsApp campaign processing failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * Get leads that match email campaign filters
     */
    private function getFilteredLeadsForEmail(EmailCampaign $campaign, int $batchSize)
    {
        $query = ClientLead::with(['client']);
        
        // Apply campaign-specific lead filters
        if ($campaign->lead_filters) {
            $this->applySimpleFilters($query, $campaign->lead_filters);
        }

        // Ensure lead has valid email
        $query->whereHas('client', function ($q) {
            $q->whereNotNull('email_id')
              ->where('email_id', '!=', '')
              ->where('email_id', 'REGEXP', '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');
        });
        return $query->limit($batchSize)->get();
    }

    /**
     * Get leads that match WhatsApp campaign filters
     */
    private function getFilteredLeadsForWhatsapp(WhatsappCampaign $campaign, int $batchSize)
    {
        $query = ClientLead::with(['client']);
        
        // Apply campaign-specific lead filters
        if ($campaign->lead_filters) {
            $this->applySimpleFilters($query, $campaign->lead_filters);
        }

        // Ensure lead has valid WhatsApp number
        $query->whereHas('client', function ($q) {
            $q->whereNotNull('whatsapp_no')
              ->where('whatsapp_no', '!=', '')
              ->where('whatsapp_no', 'REGEXP', '^[0-9+\-\s()]{10,15}$');
        });
        return $query->limit($batchSize)->get();
    }

    /**
     * Apply campaign filters to lead query
     */
    private function applySimpleFilters($query, $filters)
    {
        if (!is_array($filters)) {
            return;
        }

        foreach ($filters as $filterGroup) {
            if (!isset($filterGroup['rules']) || !is_array($filterGroup['rules'])) {
                continue;
            }

            $query->where(function ($subQuery) use ($filterGroup) {
                foreach ($filterGroup['rules'] as $rule) {
                    $this->applyFilterRule($subQuery, $rule, $filterGroup['condition'] ?? 'and');
                }
            });
        }
    }

    /**
     * Apply individual filter rule
     */
    private function applyFilterRule($query, $rule, $condition = 'and')
    {
        if (!isset($rule['field']) || !isset($rule['operator']) || !isset($rule['value'])) {
            return;
        }

        $field = $rule['field'];
        $operator = $rule['operator'];
        $value = $rule['value'];
        $method = $condition === 'or' ? 'orWhere' : 'where';

        // Handle different field types and relationships
        if (str_contains($field, '.')) {
            // Handle relationship fields (e.g., 'client.city_id')
            $parts = explode('.', $field);
            $relation = $parts[0];
            $relationField = $parts[1];

            $query->$method(function ($q) use ($relation, $relationField, $operator, $value) {
                $q->whereHas($relation, function ($subQ) use ($relationField, $operator, $value) {
                    $this->applyOperatorCondition($subQ, $relationField, $operator, $value);
                });
            });
        } else {
            // Handle direct model fields
            $query->$method(function ($q) use ($field, $operator, $value) {
                $this->applyOperatorCondition($q, $field, $operator, $value);
            });
        }
    }

    /**
     * Apply operator-based condition to query
     */
    private function applyOperatorCondition($query, $field, $operator, $value)
    {
        switch ($operator) {
            case 'equals':
            case '=':
                $query->where($field, $value);
                break;
            case 'not_equals':
            case '!=':
                $query->where($field, '!=', $value);
                break;
            case 'contains':
            case 'like':
                $query->where($field, 'LIKE', "%{$value}%");
                break;
            case 'not_contains':
            case 'not_like':
                $query->where($field, 'NOT LIKE', "%{$value}%");
                break;
            case 'starts_with':
                $query->where($field, 'LIKE', "{$value}%");
                break;
            case 'ends_with':
                $query->where($field, 'LIKE', "%{$value}");
                break;
            case 'in':
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->whereIn($field, explode(',', $value));
                }
                break;
            case 'not_in':
                if (is_array($value)) {
                    $query->whereNotIn($field, $value);
                } else {
                    $query->whereNotIn($field, explode(',', $value));
                }
                break;
            case 'greater_than':
            case '>':
                $query->where($field, '>', $value);
                break;
            case 'greater_than_equal':
            case '>=':
                $query->where($field, '>=', $value);
                break;
            case 'less_than':
            case '<':
                $query->where($field, '<', $value);
                break;
            case 'less_than_equal':
            case '<=':
                $query->where($field, '<=', $value);
                break;
            case 'between':
                if (is_array($value) && count($value) === 2) {
                    $query->whereBetween($field, $value);
                }
                break;
            case 'not_between':
                if (is_array($value) && count($value) === 2) {
                    $query->whereNotBetween($field, $value);
                }
                break;
            case 'is_null':
                $query->whereNull($field);
                break;
            case 'is_not_null':
                $query->whereNotNull($field);
                break;
            case 'is_empty':
                $query->where(function ($q) use ($field) {
                    $q->whereNull($field)->orWhere($field, '');
                });
                break;
            case 'is_not_empty':
                $query->where($field, '!=', '')->whereNotNull($field);
                break;
            default:
                $query->where($field, $value);
                break;
        }
    }

    /**
     * Check if email already sent to this lead for this campaign
     */
    private function isEmailAlreadySent(EmailCampaign $campaign, ClientLead $lead): bool
    {
        $frequency = $campaign->schedule_frequency;
        $checkDate = $this->getCheckDateForFrequency($frequency);
        
        return EmailAutomationLog::where('campaign_id', $campaign->id)
            ->where('client_lead_id', $lead->id)
            ->where('status', 'sent')
            ->where('sent_at', '>=', $checkDate)
            ->exists();
    }

    /**
     * Check if WhatsApp already sent to this lead for this campaign
     */
    private function isWhatsappAlreadySent(WhatsappCampaign $campaign, ClientLead $lead): bool
    {
        $frequency = $campaign->schedule_frequency;
        $checkDate = $this->getCheckDateForFrequency($frequency);
        
        return WhatsappMessage::where('campaign_id', $campaign->id)
            ->where('phone_number', $lead->whatsapp_no)
            ->where('status', 'sent')
            ->where('sent_at', '>=', $checkDate)
            ->exists();
    }

    /**
     * Get check date based on frequency to avoid duplicate sends
     */
    private function getCheckDateForFrequency(string $frequency): Carbon
    {
        return match ($frequency) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => now()->startOfDay(),
        };
    }

    /**
     * Create email log entry
     */
    private function createEmailLog(EmailCampaign $campaign, ClientLead $lead): EmailAutomationLog
    {
        $scheduledAt = now();
        
        if ($campaign->delay_minutes > 0) {
            $scheduledAt->addMinutes($campaign->delay_minutes);
        }

        $client = $lead->client;
        $emailTemplate = $campaign->emailTemplate;

        return EmailAutomationLog::create([
            'client_lead_id' => $lead->id,
            'campaign_id' => $campaign->id,
            'email_template_id' => $campaign->email_template_id,
            'recipient_email' => $client->email_id,
            'subject' => $emailTemplate->subject ?? 'No Subject',
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
            'email_data' => [
                'lead_id' => $lead->id,
                'client_name' => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                'campaign_name' => $campaign->name
            ]
        ]);
    }

    /**
     * Create WhatsApp message entry
     */
    private function createWhatsappMessage(WhatsappCampaign $campaign, ClientLead $lead): WhatsappMessage
    {
        $scheduledAt = now();
        
        if ($campaign->delay_minutes > 0) {
            $scheduledAt->addMinutes($campaign->delay_minutes);
        }

        // Get actual template variables with real values from the lead
        $actualTemplateVariables = [];
        
        if ($campaign->message_type === 'template' && $campaign->template_name) {
            // Get template variable mappings
            $templateMappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($campaign->template_name);
            
            // If no mappings, use campaign's stored template variables
            if (empty($templateMappings) && !empty($campaign->template_variables)) {
                $templateMappings = $campaign->template_variables;
            }
            
            if (!empty($templateMappings)) {
                // Get actual values from lead
                $templateService = new TemplateVariableService();
                $leadValues = $templateService->getLeadVariables($lead);
                
                // Map variables to actual values
                foreach ($templateMappings as $whatsappVar => $systemVar) {
                    $actualTemplateVariables[$whatsappVar] = $leadValues[$systemVar] ?? '';
                }
            }
        }

        // Replace variables in message content for text messages
        $messageContent = $campaign->message_content;
        if ($campaign->message_type === 'text' && $messageContent) {
            $templateService = new TemplateVariableService();
            $messageContent = $templateService->processTemplate($messageContent, $lead);
        }

        return WhatsappMessage::create([
            'campaign_id' => $campaign->id,
            'phone_number' => $lead->client->whatsapp_no,
            'message_type' => $campaign->message_type,
            'message_content' => $messageContent,
            'template_name' => $campaign->template_name,
            'template_variables' => $actualTemplateVariables,
            'status' => 'pending',
            'is_test' => false,
            'created_by' => null,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Update email campaign's next run schedule
     */
    private function updateEmailCampaignSchedule(EmailCampaign $campaign): void
    {
        $nextRun = $this->calculateNextRun($campaign->schedule_frequency, $campaign->schedule_config);
        
        $campaign->update([
            'last_run_at' => now(),
            'next_run_at' => $nextRun
        ]);

        $this->info("      📅 Next email run scheduled for: {$nextRun->format('Y-m-d H:i:s')}");
    }

    /**
     * Update WhatsApp campaign's next run schedule
     */
    private function updateWhatsappCampaignSchedule(WhatsappCampaign $campaign): void
    {
        $nextRun = $this->calculateNextRun($campaign->schedule_frequency, $campaign->schedule_config);
        
        $campaign->update([
            'last_run_at' => now(),
            'next_run_at' => $nextRun
        ]);

        $this->info("      📅 Next WhatsApp run scheduled for: {$nextRun->format('Y-m-d H:i:s')}");
    }

    /**
     * Calculate next run time based on schedule frequency
     */
    private function calculateNextRun(string $frequency, array $config = []): Carbon
    {
        $now = now();

        return match ($frequency) {
            'daily' => $now->addDay(),
            'weekly' => $now->addWeek(),
            'monthly' => $now->addMonth(),
            'yearly' => $now->addYear(),
            default => $now->addDay(),
        };
    }

    /**
     * Log errors consistently
     */
    private function logError(string $type, int $campaignId, int $leadId, string $message): void
    {
        $this->error("      ❌ Failed to queue {$type} for lead {$leadId}: {$message}");
        
        Log::error("Failed to queue scheduled {$type} campaign", [
            'campaign_id' => $campaignId,
            'lead_id' => $leadId,
            'error' => $message
        ]);
    }
}
