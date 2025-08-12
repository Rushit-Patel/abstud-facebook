<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\NotificationConfig;
use App\Models\EmailTemplate;
use App\Mail\EmailTemplateMail;
use App\Models\TeamNotificationType;
use App\Models\TeamNotification;
use App\Services\WhatsappService;
use Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\TemplateVariableService;
use App\Models\WhatsappTemplateVariableMapping;

class TaskCommentNotification
{
    protected $taskData;
    protected $commentData;
    protected $recipientUser;
    
    public function __construct($task, $comment, $recipientUser)
    {
        $this->taskData = $task;
        $this->commentData = $comment;
        $this->recipientUser = $recipientUser;
    }

    public function send()
    {
        $config = NotificationConfig::where('slug', 'task_comment_notification')->first();

        if (!$config) {
            return;
        }
        
        if ($config->email_enabled) {
            $this->sendEmail($config);
        }
        
        if ($config->whatsapp_enabled) {
            $this->sendWhatsapp($config);
        }
        
        if ($config->system_enabled) {
            $this->sendSystemNotification($config);
        }
    }

    private function sendEmail($config)
    {
        try {
            $emailTemplate = EmailTemplate::find($config->email_template_id);
            if (!$emailTemplate) {
                return;
            }
            
            $emailTo = $this->recipientUser->email;
            
            if (!$emailTo) {
                return;
            }

            $subject = $this->processTemplate($emailTemplate->subject);
            $body = $this->processTemplate($emailTemplate->html_template);

            // Send email
            Mail::to($emailTo)->send(new EmailTemplateMail($subject, $body));
            
        } catch (\Exception $e) {
            Log::error('Task comment email sending failed: ' . $e->getMessage());
        }
    }

    private function sendWhatsapp($config)
    {
        try {
            $whatsappTemplate = $config->whatsapp_template;
            
            // Get WhatsApp number from recipient user
            $whatsappNumber = null;
            if ($this->recipientUser->whatsapp_no && $this->recipientUser->whatsapp_country_code) {
                $whatsappNumber = $this->recipientUser->whatsapp_country_code . $this->recipientUser->whatsapp_no;
            } elseif ($this->recipientUser->mobile_no && $this->recipientUser->country_code) {
                $whatsappNumber = $this->recipientUser->country_code . $this->recipientUser->mobile_no;
            }

            if (!$whatsappNumber) {
                Log::warning('No WhatsApp number found for user', ['user_id' => $this->recipientUser->id]);
                return;
            }

            // Get variable mappings for this template
            $mappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($whatsappTemplate);
            
            // Get all available variables from our task data
            $allVariables = $this->getTaskCommentVariables();
            
            // Build WhatsApp template parameters based on mappings
            $templateParams = [];
            foreach ($mappings as $whatsappVar => $systemVar) {
                $templateParams[$whatsappVar] = $allVariables[$systemVar] ?? '';
            }

            // Send WhatsApp message using the service
            $whatsappService = new WhatsappService();
            $result = $whatsappService->sendTemplate($whatsappNumber, $whatsappTemplate, $templateParams);
            
            if ($result['success']) {
                Log::info('WhatsApp task comment notification sent successfully', [
                    'task_id' => $this->taskData->id,
                    'comment_id' => $this->commentData->id,
                    'recipient_user_id' => $this->recipientUser->id,
                    'template' => $whatsappTemplate,
                    'number' => $whatsappNumber,
                    'message_id' => $result['message_id'] ?? null
                ]);
            } else {
                Log::error('WhatsApp task comment sending failed: ' . ($result['error'] ?? 'Unknown error'), [
                    'task_id' => $this->taskData->id,
                    'comment_id' => $this->commentData->id,
                    'recipient_user_id' => $this->recipientUser->id,
                    'template' => $whatsappTemplate,
                    'number' => $whatsappNumber
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('WhatsApp task comment sending failed: ' . $e->getMessage(), [
                'task_id' => $this->taskData->id ?? 'unknown',
                'comment_id' => $this->commentData->id ?? 'unknown',
                'recipient_user_id' => $this->recipientUser->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendSystemNotification($config)
    {
        try {
            $teamNotificationType = TeamNotificationType::find($config->team_notification_types);
            
            if (!$teamNotificationType || !Auth::user()) {
                return;
            }
            
            $variables = $this->getTaskCommentVariables();
            
            // Process the notification message with variables
            $message = $teamNotificationType->processTemplate($variables);
            
            // Create system notification
            TeamNotification::create([
                'notification_type_id' => $teamNotificationType->id,
                'title' => $teamNotificationType->title,
                'message' => $message,
                'link' => route('team.task.show', $this->taskData->id),
                'data' => [
                    'task_id' => $this->taskData->id,
                    'comment_id' => $this->commentData->id,
                    'task_title' => $variables['task_title'] ?? '',
                    'comment_content' => $variables['comment_content'] ?? '',
                    'comment_user' => $variables['comment_user'] ?? '',
                ],
                'user_id' => $this->recipientUser->id, // Notify recipient user
                'is_seen' => false,
                'created_by' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Task comment system notification creation failed: ' . $e->getMessage(), [
                'task_id' => $this->taskData->id ?? 'unknown',
                'comment_id' => $this->commentData->id ?? 'unknown',
                'recipient_user_id' => $this->recipientUser->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getTaskCommentVariables()
    {
        return [
            'task_title' => $this->taskData->title,
            'task_description' => $this->taskData->description ?? '',
            'task_priority' => $this->taskData->priority->name ?? 'Normal',
            'task_status' => $this->taskData->status->name ?? 'Open',
            'task_category' => $this->taskData->category->name ?? 'General',
            'comment_content' => substr($this->commentData->content, 0, 100) . (strlen($this->commentData->content) > 100 ? '...' : ''),
            'comment_user' => $this->commentData->user->name ?? 'Unknown',
            'recipient_user' => $this->recipientUser->name,
            'auth_user_name' => Auth::user()->name ?? 'System',
            'due_date' => $this->taskData->due_date ? $this->taskData->due_date->format('d/m/Y H:i') : 'Not set',
            'created_by' => $this->taskData->creator->name ?? 'Unknown',
        ];
    }

    private function processTemplate($template)
    {
        $variables = $this->getTaskCommentVariables();
        
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }
}
