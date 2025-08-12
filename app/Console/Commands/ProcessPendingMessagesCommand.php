<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailAutomationLog;
use App\Models\WhatsappMessage;
use App\Jobs\SendAutomationEmailJob;
use App\Jobs\SendAutomationWhatsappJob;
use Illuminate\Support\Facades\Log;

class ProcessPendingMessagesCommand extends Command
{
    protected $signature = 'messages:process-pending 
                            {--type=all : Type of messages to process (email, whatsapp, all)}
                            {--limit=100 : Maximum number of messages to process}
                            {--dry-run : Show what would be processed without actually doing it}';
    
    protected $description = 'Process pending email and WhatsApp messages in the queue';

    public function handle(): int
    {
        $type = $this->option('type');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info('📤 Processing pending messages...');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No messages will be sent');
        }

        try {
            $totalProcessed = 0;

            // Process Email Messages
            if (in_array($type, ['email', 'all'])) {
                $emailProcessed = $this->processPendingEmails($limit, $dryRun);
                $totalProcessed += $emailProcessed;
            }

            // Process WhatsApp Messages
            if (in_array($type, ['whatsapp', 'all'])) {
                $whatsappProcessed = $this->processPendingWhatsapp($limit, $dryRun);
                $totalProcessed += $whatsappProcessed;
            }

            $this->info("✅ Successfully processed {$totalProcessed} pending messages");
            
            Log::info('Pending messages processed', [
                'type' => $type,
                'total_processed' => $totalProcessed,
                'dry_run' => $dryRun
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error processing pending messages: ' . $e->getMessage());
            Log::error('Pending messages processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }

    /**
     * Process pending email messages
     */
    private function processPendingEmails(int $limit, bool $dryRun): int
    {
        $this->info('📧 Processing pending emails...');
        
        $pendingEmails = EmailAutomationLog::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                      ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($pendingEmails->isEmpty()) {
            $this->info('   ℹ️  No pending emails to process');
            return 0;
        }

        $count = $pendingEmails->count();
        $this->info("   📊 Found {$count} pending emails");

        if ($dryRun) {
            $this->warn("   🔍 DRY RUN: Would process {$count} emails");
            return $count;
        }

        $processed = 0;
        $failed = 0;

        foreach ($pendingEmails as $emailLog) {
            try {
                // Validate email log before processing
                if (!$this->validateEmailLog($emailLog)) {
                    $emailLog->markAsFailed('Invalid email log data');
                    $failed++;
                    continue;
                }
                
                // Dispatch the job
                SendAutomationEmailJob::dispatch($emailLog)->onQueue('emails');
                $processed++;
                
            } catch (\Exception $e) {
                $emailLog->markAsFailed($e->getMessage());
                $failed++;
                $this->error("   ❌ Failed to process email {$emailLog->id}: " . $e->getMessage());
            }
        }

        $this->info("   ✅ Successfully processed {$processed} emails");
        if ($failed > 0) {
            $this->warn("   ⚠️  {$failed} emails failed to process");
        }

        return $processed;
    }

    /**
     * Process pending WhatsApp messages
     */
    private function processPendingWhatsapp(int $limit, bool $dryRun): int
    {
        $this->info('💬 Processing pending WhatsApp messages...');
        
        $pendingMessages = WhatsappMessage::readyToSend()
            ->orderBy('scheduled_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingMessages->isEmpty()) {
            $this->info('   ℹ️  No pending WhatsApp messages to process');
            return 0;
        }

        $count = $pendingMessages->count();
        $this->info("   📊 Found {$count} pending WhatsApp messages");

        if ($dryRun) {
            $this->warn("   🔍 DRY RUN: Would process {$count} WhatsApp messages");
            return $count;
        }

        $processed = 0;
        $failed = 0;

        foreach ($pendingMessages as $message) {
            try {
                // Validate message before processing
                if (!$this->validateWhatsappMessage($message)) {
                    $message->update([
                        'status' => 'failed',
                        'error_message' => 'Invalid message data'
                    ]);
                    $failed++;
                    continue;
                }
                
                // Dispatch the job
                SendAutomationWhatsappJob::dispatch($message)->onQueue('whatsapp');                
                $processed++;
                
            } catch (\Exception $e) {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                $failed++;
                $this->error("   ❌ Failed to process WhatsApp message {$message->id}: " . $e->getMessage());
            }
        }

        $this->info("   ✅ Successfully processed {$processed} WhatsApp messages");
        if ($failed > 0) {
            $this->warn("   ⚠️  {$failed} WhatsApp messages failed to process");
        }

        return $processed;
    }

    /**
     * Validate email log data
     */
    private function validateEmailLog(EmailAutomationLog $emailLog): bool
    {
        // Check if recipient email is valid
        if (!$emailLog->recipient_email || !filter_var($emailLog->recipient_email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Email log has invalid recipient email', [
                'email_log_id' => $emailLog->id,
                'recipient_email' => $emailLog->recipient_email
            ]);
            return false;
        }
        
        // Check if campaign exists and is active
        if (!$emailLog->campaign || !$emailLog->campaign->is_active) {
            Log::warning('Email log has inactive or missing campaign', [
                'email_log_id' => $emailLog->id,
                'campaign_id' => $emailLog->campaign_id
            ]);
            return false;
        }
        
        // Check if email template exists
        if (!$emailLog->emailTemplate) {
            Log::warning('Email log missing email template', [
                'email_log_id' => $emailLog->id,
                'template_id' => $emailLog->email_template_id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Validate WhatsApp message data
     */
    private function validateWhatsappMessage(WhatsappMessage $message): bool
    {
        if (empty($message->phone_number)) {
            Log::warning('WhatsApp message has empty phone number', [
                'message_id' => $message->id
            ]);
            return false;
        }

        if (empty($message->message_content) && empty($message->template_name)) {
            Log::warning('WhatsApp message has no content or template', [
                'message_id' => $message->id
            ]);
            return false;
        }

        return true;
    }
}
