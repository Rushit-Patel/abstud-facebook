<?php

namespace App\Jobs;

use App\Models\WhatsappMessage;
use App\Services\WhatsappService;
use App\Services\TemplateVariableService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAutomationWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds

    public function __construct(
        public WhatsappMessage $whatsappMessage
    ) {}

    public function handle(WhatsappService $whatsappService): void
    {
        try {
            // Refresh the model to get the latest status and use row locking
            $currentMessage = WhatsappMessage::lockForUpdate()->find($this->whatsappMessage->id);
            
            if (!$currentMessage || $currentMessage->status !== 'pending') {
                Log::info('WhatsApp message already processed or not found', [
                    'message_id' => $this->whatsappMessage->id,
                    'status' => $currentMessage?->status
                ]);
                return;
            }

            // Update the reference to the fresh model
            $this->whatsappMessage = $currentMessage;

            // Validate the message before processing
            if (!$this->validateMessage()) {
                $this->whatsappMessage->update([
                    'status' => 'failed',
                    'error_message' => 'Invalid message data'
                ]);
                return;
            }

            // Prepare message content with variable replacement
            $this->updateMessageContentWithVariables();

            // Send the message using WhatsappService (without creating new record)
            $result = $this->sendMessage($whatsappService);

            if ($result['success']) {
                $this->whatsappMessage->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'message_id' => $result['message_id'] ?? null,
                    'provider_response' => $result['response'] ?? null,
                ]);

                Log::info('WhatsApp automation message sent successfully', [
                    'message_id' => $this->whatsappMessage->id,
                    'phone' => $this->whatsappMessage->phone_number,
                    'provider_message_id' => $result['message_id'] ?? null
                ]);
            } else {
                $this->whatsappMessage->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error occurred'
                ]);
                throw new \Exception($result['error'] ?? 'Unknown error occurred');
            }
            
        } catch (\Exception $e) {
            $this->whatsappMessage->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => $this->whatsappMessage->retry_count + 1
            ]);
            
            Log::error('Failed to send WhatsApp automation message', [
                'message_id' => $this->whatsappMessage->id,
                'phone' => $this->whatsappMessage->phone_number,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);
            
            // If this is not the last attempt, re-throw to retry
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * Validate the message data
     */
    private function validateMessage(): bool
    {
        if (empty($this->whatsappMessage->phone_number)) {
            Log::warning('WhatsApp message has empty phone number', [
                'message_id' => $this->whatsappMessage->id
            ]);
            return false;
        }

        if (empty($this->whatsappMessage->message_content) && 
            empty($this->whatsappMessage->template_name)) {
            Log::warning('WhatsApp message has no content or template', [
                'message_id' => $this->whatsappMessage->id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get replacement variables from associated lead/client
     */
    private function getReplacementVariables(): array
    {
        // If message is associated with a campaign, get lead data by phone number
        if ($this->whatsappMessage->campaign_id) {
            $lead = \App\Models\ClientLead::whereHas('client', function($query) {
                $query->where('whatsapp_no', $this->whatsappMessage->phone_number);
            })->with('client')->first();
                
            if ($lead) {
                // Use TemplateVariableService to get all variables
                return TemplateVariableService::getValuesFromClientLead($lead);
            }
        }
        
        // Fallback to sample values if no lead data found
        $variables = TemplateVariableService::getSampleValues();
        $variables['phone'] = $this->whatsappMessage->phone_number;
        
        return $variables;
    }

    /**
     * Send the message using WhatsappService (without creating duplicate records)
     */
    private function sendMessage(WhatsappService $whatsappService): array
    {
        // Auto-select provider since we removed provider_id field
        $whatsappService->selectProvider();

        // Use the new method that doesn't create duplicate records
        return $whatsappService->sendExistingMessage($this->whatsappMessage);
    }

    /**
     * Update message content with variable replacement before sending
     */
    private function updateMessageContentWithVariables(): void
    {
        $variables = $this->getReplacementVariables();

        if ($this->whatsappMessage->message_type === 'text') {
            // Replace variables in text message content
            $content = TemplateVariableService::replaceVariables(
                $this->whatsappMessage->message_content, 
                $variables
            );
            $this->whatsappMessage->message_content = $content;
        } elseif ($this->whatsappMessage->message_type === 'template') {
            // Replace variables in template parameters
            $templateVariables = $this->whatsappMessage->template_variables ?? [];
            $processedVariables = [];
            
            foreach ($templateVariables as $key => $value) {
                $processedVariables[$key] = TemplateVariableService::replaceVariables($value, $variables);
            }
            
            $this->whatsappMessage->template_variables = $processedVariables;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        $this->whatsappMessage->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'retry_count' => $this->whatsappMessage->retry_count + 1
        ]);
        
        Log::error('SendAutomationWhatsappJob failed permanently', [
            'message_id' => $this->whatsappMessage->id,
            'phone' => $this->whatsappMessage->phone_number,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
