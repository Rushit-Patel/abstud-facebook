<?php

namespace App\Services;

use App\Models\ClientLead;
use App\Models\EmailCampaign;
use App\Models\EmailAutomationLog;
use App\Jobs\SendAutomationEmailJob;
use Carbon\Carbon;

class EmailAutomationService
{
    /**
     * Schedule an email for a lead based on campaign
     */
    public function scheduleEmail(ClientLead $lead, EmailCampaign $campaign): ?EmailAutomationLog
    {
        // Check if this email was already scheduled for this lead+campaign
        $existingLog = EmailAutomationLog::where('client_lead_id', $lead->id)
            ->where('campaign_id', $campaign->id)
            ->where('status', '!=', 'failed')
            ->first();

        if ($existingLog) {
            return null; // Already scheduled
        }

        // Get recipient email
        $recipientEmail = $lead->client->email_id ?? null;
        if (!$recipientEmail) {
            return null; // No email address
        }

        // Calculate scheduled time
        $scheduledAt = now()->addMinutes($campaign->delay_minutes);

        // Prepare email data
        $emailData = $this->prepareEmailVariables($lead);

        // Create email log entry
        $emailLog = EmailAutomationLog::create([
            'client_lead_id' => $lead->id,
            'campaign_id' => $campaign->id,
            'email_template_id' => $campaign->email_template_id,
            'recipient_email' => $recipientEmail,
            'subject' => $this->replaceVariables($campaign->emailTemplate->subject, $emailData),
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
            'email_data' => $emailData
        ]);

        // Schedule the job
        SendAutomationEmailJob::dispatch($emailLog)->delay($scheduledAt);

        return $emailLog;
    }

    /**
     * Process all pending time-based campaigns
     */
    public function processTimeBased(): void
    {
        $campaigns = EmailCampaign::getByTriggerType('time_based');
        
        foreach ($campaigns as $campaign) {
            $this->processTimeBaedCampaign($campaign);
        }
    }

    /**
     * Process follow-up due campaigns
     */
    public function processFollowUpDue(): void
    {
        $campaigns = EmailCampaign::getByTriggerType('follow_up_due');
        
        foreach ($campaigns as $campaign) {
            $this->processFollowUpDueCampaign($campaign);
        }
    }

    /**
     * Process a time-based campaign
     */
    private function processTimeBaedCampaign(EmailCampaign $campaign): void
    {
        $conditions = $campaign->trigger_conditions;
        $daysSinceCreated = $conditions['days_since_created'] ?? null;
        
        if (!$daysSinceCreated) {
            return;
        }

        // Find leads created exactly X days ago
        $targetDate = now()->subDays($daysSinceCreated)->startOfDay();
        $endDate = $targetDate->copy()->endOfDay();

        $leads = ClientLead::whereBetween('created_at', [$targetDate, $endDate])
            ->with('client')
            ->get();

        foreach ($leads as $lead) {
            if ($campaign->shouldTriggerForLead($lead)) {
                $this->scheduleEmail($lead, $campaign);
            }
        }
    }

    /**
     * Process follow-up due campaign
     */
    private function processFollowUpDueCampaign(EmailCampaign $campaign): void
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // Find leads with overdue follow-ups
        $leads = ClientLead::whereHas('getFollowUps', function($query) use ($today) {
            $query->where('status', '0')
                  ->where('followup_date', '<', $today);
        })->with(['client', 'getFollowUps'])->get();

        foreach ($leads as $lead) {
            if ($campaign->shouldTriggerForLead($lead)) {
                $this->scheduleEmail($lead, $campaign);
            }
        }
    }

    /**
     * Prepare email variables for a lead
     */
    private function prepareEmailVariables(ClientLead $lead): array
    {
        $client = $lead->client;
        
        return [
            'client_name' => $client->first_name . ' ' . $client->last_name,
            'client_first_name' => $client->first_name,
            'client_last_name' => $client->last_name,
            'client_email' => $client->email_id,
            'client_mobile' => $client->country_code . $client->mobile_no,
            'lead_status' => $lead->status,
            'lead_sub_status' => $lead->sub_status,
            'lead_purpose' => $lead->purpose,
            'lead_country' => $lead->country,
            'lead_coaching' => $lead->coaching,
            'lead_source' => $lead->source,
            'company_name' => config('app.name'),
            'current_date' => now()->format('F j, Y'),
            'lead_created_date' => $lead->created_at->format('F j, Y'),
        ];
    }

    /**
     * Replace variables in text
     */
    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        
        return $text;
    }
}
