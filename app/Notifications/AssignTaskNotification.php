<?php

namespace App\Notifications;

use App\Models\Task;
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

class AssignTaskNotification
{
    protected $taskData;
    protected $assignedUser;
    
    public function __construct($task, $assignedUser)
    {
        $this->taskData = $task;
        $this->assignedUser = $assignedUser;
    }

    public function send()
    {
        $config = NotificationConfig::where('slug', 'assign_task_notification')->first();

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
            
            $emailTo = $this->assignedUser->email;
            
            if (!$emailTo) {
                return;
            }

            $subject = $this->processTemplate($emailTemplate->subject);
            $body = $this->processTemplate($emailTemplate->html_template);

            // Send email
            Mail::to($emailTo)->send(new EmailTemplateMail($subject, $body));
            
        } catch (\Exception $e) {
            Log::error('Task assignment email sending failed: ' . $e->getMessage());
        }
    }

    private function sendWhatsapp($config)
    {
        try {
            $whatsappTemplate = $config->whatsapp_template;
            
            // Get WhatsApp number from assigned user
            $whatsappNumber = null;
            if ($this->assignedUser->whatsapp_no && $this->assignedUser->whatsapp_country_code) {
                $whatsappNumber = $this->assignedUser->whatsapp_country_code . $this->assignedUser->whatsapp_no;
            } elseif ($this->assignedUser->mobile_no && $this->assignedUser->country_code) {
                $whatsappNumber = $this->assignedUser->country_code . $this->assignedUser->mobile_no;
            }

            if (!$whatsappNumber) {
                Log::warning('No WhatsApp number found for user', ['user_id' => $this->assignedUser->id]);
                return;
            }

            // Get variable mappings for this template
            $mappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($whatsappTemplate);
            
            // Get all available variables from our task data
            $allVariables = $this->getTaskVariables();
            
            // Build WhatsApp template parameters based on mappings
            $templateParams = [];
            foreach ($mappings as $whatsappVar => $systemVar) {
                $templateParams[$whatsappVar] = $allVariables[$systemVar] ?? '';
            }

            // Send WhatsApp message using the service
            $whatsappService = new WhatsappService();
            $result = $whatsappService->sendTemplate($whatsappNumber, $whatsappTemplate, $templateParams);
            
            if ($result['success']) {
                Log::info('WhatsApp task assignment notification sent successfully', [
                    'task_id' => $this->taskData->id,
                    'assigned_user_id' => $this->assignedUser->id,
                    'template' => $whatsappTemplate,
                    'number' => $whatsappNumber,
                    'message_id' => $result['message_id'] ?? null
                ]);
            } else {
                Log::error('WhatsApp task assignment sending failed: ' . ($result['error'] ?? 'Unknown error'), [
                    'task_id' => $this->taskData->id,
                    'assigned_user_id' => $this->assignedUser->id,
                    'template' => $whatsappTemplate,
                    'number' => $whatsappNumber
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('WhatsApp task assignment sending failed: ' . $e->getMessage(), [
                'task_id' => $this->taskData->id ?? 'unknown',
                'assigned_user_id' => $this->assignedUser->id ?? 'unknown',
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
            
            $variables = $this->getTaskVariables();
            
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
                    'task_title' => $variables['task_title'] ?? '',
                    'task_priority' => $variables['task_priority'] ?? '',
                    'assigned_user' => $variables['assigned_user'] ?? '',
                    'due_date' => $variables['due_date'] ?? '',
                ],
                'user_id' => $this->assignedUser->id, // Notify assigned user
                'is_seen' => false,
                'created_by' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Task assignment system notification creation failed: ' . $e->getMessage(), [
                'task_id' => $this->taskData->id ?? 'unknown',
                'assigned_user_id' => $this->assignedUser->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getTaskVariables()
    {
        return [
            'task_title' => $this->taskData->title,
            'task_description' => $this->taskData->description ?? '',
            'task_priority' => $this->taskData->priority->name ?? 'Normal',
            'task_status' => $this->taskData->status->name ?? 'Open',
            'task_category' => $this->taskData->category->name ?? 'General',
            'assigned_user' => $this->assignedUser->name,
            'assigned_user_email' => $this->assignedUser->email,
            'auth_user_name' => Auth::user()->name ?? 'System',
            'due_date' => $this->taskData->due_date ? $this->taskData->due_date->format('d/m/Y H:i') : 'Not set',
            'start_date' => $this->taskData->start_date ? $this->taskData->start_date->format('d/m/Y H:i') : 'Not set',
            'estimated_hours' => $this->taskData->estimated_hours ?? 'Not specified',
            'created_by' => $this->taskData->creator->name ?? 'Unknown',
        ];
    }

    private function processTemplate($template)
    {
        $variables = $this->getTaskVariables();
        
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }
}
