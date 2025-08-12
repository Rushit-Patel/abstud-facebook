<?php

namespace App\Notifications;

use App\Models\NotificationConfig;
use App\Models\EmailTemplate;
use App\Models\TeamNotificationType;
use App\Services\TeamNotificationService;
use App\Services\WhatsappService;
use App\Services\TemplateVariableService;
use App\Jobs\SendAutomationEmailJob;
use App\Models\WhatsappMessage;
use App\Models\EmailAutomationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewTaskNotification
{
    protected NotificationConfig $config;
    protected array $notificationData;
    protected array $variables;

    public function __construct(array $notificationData = [])
    {
        $this->notificationData = $notificationData;
        $this->variables = $this->prepareVariables($notificationData);
        
        // Get notification configuration
        $this->config = NotificationConfig::where('slug', 'new_task_notification')->first();
        
        if (!$this->config) {
            throw new \Exception('Notification configuration for "new_task_notification" not found');
        }
    }

    /**
     * Send notifications via all enabled channels
     */
    public function send($userId): array
    {
        $results = [
            'email' => null,
            'whatsapp' => null,
            'system' => null,
        ];

        try {
            // Send email notification if enabled
            if ($this->config->email_enabled && $this->config->emailTemplate) {
                $results['email'] = $this->sendEmailNotification($userId);
            }

            // Send WhatsApp notification if enabled
            if ($this->config->whatsapp_enabled && $this->config->whatsapp_template) {
                $results['whatsapp'] = $this->sendWhatsappNotification($userId);
            }

            // Send system notification if enabled
            if ($this->config->system_enabled && $this->config->teamNotificationType) {
                $results['system'] = $this->sendSystemNotification($userId);
            }

            Log::info('New task notification sent', [
                'user_id' => $userId,
                'config_slug' => $this->config->slug,
                'channels' => array_keys(array_filter($results)),
                'variables' => $this->variables
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send new task notification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'variables' => $this->variables
            ]);
            throw $e;
        }

        return $results;
    }

    /**
     * Send to multiple users
     */
    public function sendToUsers(array $userIds): array
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            try {
                $results[$userId] = $this->send($userId);
            } catch (\Exception $e) {
                $results[$userId] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification($userId): array
    {
        try {
            $user = \App\Models\User::find($userId);
            if (!$user || !$user->email) {
                throw new \Exception('User not found or email not available');
            }

            // Create email automation log entry
            $emailLog = EmailAutomationLog::create([
                'client_lead_id' => $this->variables['lead_id'] ?? null,
                'campaign_id' => null, // Not from campaign
                'email_template_id' => $this->config->email_template_id,
                'recipient_email' => $user->email,
                'subject' => $this->config->emailTemplate->subject ?? 'New Task Assigned',
                'status' => 'pending',
                'scheduled_at' => now(),
                'email_data' => [
                    'notification_type' => 'new_task_notification',
                    'user_id' => $userId,
                    'variables' => $this->variables
                ]
            ]);

            // Queue the email job
            SendAutomationEmailJob::dispatch($emailLog)->onQueue('emails');

            return [
                'success' => true,
                'message' => 'Email notification queued successfully',
                'email_log_id' => $emailLog->id
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'user_id' => $userId,
                'template_id' => $this->config->email_template_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp notification
     */
    protected function sendWhatsappNotification($userId): array
    {
        try {
            $user = \App\Models\User::find($userId);
            if (!$user || !$user->whatsapp_no) {
                throw new \Exception('User not found or WhatsApp number not available');
            }

            // Prepare WhatsApp message content
            $messageContent = $this->prepareWhatsappMessage();

            // Create WhatsApp message record
            $whatsappMessage = WhatsappMessage::create([
                'phone_number' => $user->whatsapp_no,
                'message_type' => 'template',
                'template_name' => $this->config->whatsapp_template,
                'template_variables' => $this->getWhatsappVariables(),
                'message_content' => json_encode([
                    'template' => $this->config->whatsapp_template,
                    'parameters' => $this->getWhatsappVariables(),
                    'language' => 'en_US'
                ]),
                'status' => 'pending',
                'is_test' => false,
                'created_by' => Auth::id() ?? null,
            ]);

            // Queue the WhatsApp job
            \App\Jobs\SendAutomationWhatsappJob::dispatch($whatsappMessage)->onQueue('whatsapp');

            return [
                'success' => true,
                'message' => 'WhatsApp notification queued successfully',
                'whatsapp_message_id' => $whatsappMessage->id
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification', [
                'user_id' => $userId,
                'template' => $this->config->whatsapp_template,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send system notification
     */
    protected function sendSystemNotification($userId): array
    {
        try {
            $notificationService = new TeamNotificationService();
            
            $notification = $notificationService->create(
                $this->config->teamNotificationType->type_key,
                $userId,
                $this->variables,
                $this->variables['task_link'] ?? null,
                Auth::id()
            );

            return [
                'success' => true,
                'message' => 'System notification created successfully',
                'notification_id' => $notification->id
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send system notification', [
                'user_id' => $userId,
                'notification_type' => $this->config->teamNotificationType->type_key ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare notification variables from provided data
     */
    protected function prepareVariables(array $data): array
    {
        $now = now();
        
        return array_merge([
            // Task-related variables
            'task_title' => $data['task_title'] ?? 'New Task',
            'task_description' => $data['task_description'] ?? '',
            'task_priority' => $data['task_priority'] ?? 'Medium',
            'task_status' => $data['task_status'] ?? 'Pending',
            'task_due_date' => $data['task_due_date'] ?? '',
            'task_category' => $data['task_category'] ?? '',
            'task_link' => $data['task_link'] ?? '#',
            'task_id' => $data['task_id'] ?? null,

            // User-related variables
            'assigned_user_name' => $data['assigned_user_name'] ?? 'User',
            'assigned_by_name' => $data['assigned_by_name'] ?? (Auth::user()->name ?? 'System'),
            'user_id' => $data['user_id'] ?? null,

            // Lead/Client related (if applicable)
            'lead_id' => $data['lead_id'] ?? null,
            'client_name' => $data['client_name'] ?? '',
            'client_phone' => $data['client_phone'] ?? '',

            // System variables
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'company_name' => config('app.name'),
            'current_date' => $now->format('Y-m-d'),
            'current_time' => $now->format('H:i:s'),
            'current_datetime' => $now->format('Y-m-d H:i:s'),
            'current_year' => $now->year,
            'current_month' => $now->format('F'),
            'current_day' => $now->format('d'),
        ], $data);
    }

    /**
     * Prepare WhatsApp message content
     */
    protected function prepareWhatsappMessage(): string
    {
        $template = "New task assigned to you:\n\n";
        $template .= "📋 Task: {task_title}\n";
        $template .= "📅 Due Date: {task_due_date}\n";
        $template .= "⚡ Priority: {task_priority}\n";
        $template .= "👤 Assigned by: {assigned_by_name}\n\n";
        $template .= "Please check your dashboard for more details.";

        return TemplateVariableService::replaceVariables($template, $this->variables);
    }

    /**
     * Get WhatsApp template variables in the format expected by WhatsApp API
     */
    protected function getWhatsappVariables(): array
    {
        // Map your variables to WhatsApp template parameter positions
        // This depends on your WhatsApp template structure
        return [
            '1' => $this->variables['assigned_user_name'],
            '2' => $this->variables['task_title'],
            '3' => $this->variables['task_due_date'],
            '4' => $this->variables['task_priority'],
            '5' => $this->variables['assigned_by_name'],
        ];
    }

    /**
     * Static method for easy usage
     */
    public static function notify($userId, array $taskData): array
    {
        $notification = new self($taskData);
        return $notification->send($userId);
    }

    /**
     * Static method for notifying multiple users
     */
    public static function notifyUsers(array $userIds, array $taskData): array
    {
        $notification = new self($taskData);
        return $notification->sendToUsers($userIds);
    }

    /**
     * Get notification configuration
     */
    public function getConfig(): NotificationConfig
    {
        return $this->config;
    }

    /**
     * Get prepared variables
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Check if specific channel is enabled
     */
    public function isChannelEnabled(string $channel): bool
    {
        return match($channel) {
            'email' => $this->config->email_enabled && $this->config->emailTemplate,
            'whatsapp' => $this->config->whatsapp_enabled && $this->config->whatsapp_template,
            'system' => $this->config->system_enabled && $this->config->teamNotificationType,
            default => false
        };
    }
}