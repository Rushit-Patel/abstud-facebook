<?php

namespace App\Jobs;

use App\Models\EmailAutomationLog;
use App\Models\EmailTemplate;
use App\Mail\EmailTemplateMail;
use App\Services\TemplateVariableService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAutomationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public EmailAutomationLog $emailLog
    ) {}

    public function handle(): void
    {
        try {
            // Get fresh email log with lock
            $emailLog = EmailAutomationLog::lockForUpdate()
                ->with(['clientLead.client', 'emailTemplate', 'campaign'])
                ->find($this->emailLog->id);
            
            if (!$emailLog || $emailLog->status !== 'pending') {
                return;
            }

            // Validate required data
            if (!$this->isValidEmailLog($emailLog)) {
                $emailLog->update(['status' => 'failed', 'error_message' => 'Invalid email data']);
                return;
            }

            // Process template variables
            $templateService = new TemplateVariableService();
            
            $subject = $templateService->processTemplate($emailLog->emailTemplate->subject, $emailLog->clientLead);
            $body = $templateService->processTemplate($emailLog->emailTemplate->html_template, $emailLog->clientLead);

            // Send email
            Mail::to($emailLog->recipient_email)->send(new EmailTemplateMail($subject, $body));

            // Mark as sent
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);
            
        } catch (\Exception $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            Log::error('Email send failed', [
                'email_log_id' => $this->emailLog->id,
                'error' => $e->getMessage()
            ]);
            
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    private function isValidEmailLog($emailLog): bool
    {
        return $emailLog->recipient_email && 
               filter_var($emailLog->recipient_email, FILTER_VALIDATE_EMAIL) &&
               $emailLog->emailTemplate &&
               $emailLog->clientLead;
    }

    public function failed(\Throwable $exception): void
    {
        $this->emailLog->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);
    }
}
