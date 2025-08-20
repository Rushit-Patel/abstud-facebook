<?php

namespace App\Notifications;

use App\Models\ClientLead;
use App\Models\NotificationConfig;
use App\Models\EmailTemplate;
use App\Mail\EmailTemplateMail;
use App\Models\TeamNotificationType;
use App\Models\TeamNotification;
use App\Models\User;
use App\Services\WhatsappService;
use Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\TemplateVariableService;
use App\Models\WhatsappTemplateVariableMapping;

class NewLeadNotification
{
    protected $leadData;
    
    public function __construct($clientLead)
    {
        $this->leadData = $clientLead;
    }

    public function send()
    {
        $config = NotificationConfig::where('slug', 'new_lead_notification')->first();
        
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
            
            $emailTo = $this->leadData->client->email_id;
            
            if (!$emailTo) {
                return;
            }

            $subject = TemplateVariableService::processTemplate($emailTemplate->subject, $this->leadData);
            $body = TemplateVariableService::processTemplate($emailTemplate->html_template, $this->leadData);

            // Send email
            Mail::to($emailTo)->send(new EmailTemplateMail($subject, $body));
            
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
        }
    }

    private function sendWhatsapp($config)
    {
        try {
            $whatsappTemplate = $config->whatsapp_template;
            // Get WhatsApp number
            if($whatsappTemplate == null || $whatsappTemplate == ''){
                Log::error('WhatsApp template is not configured for new lead notification', [
                    'lead_id' => $this->leadData->id ?? 'unknown'
                ]);
                return;
            }
            if($this->leadData->client->whatsapp_no!='' && $this->leadData->client->whatsapp_country_code!=''){
                $whatsappNumber = $this->leadData->client->whatsapp_country_code.''.$this->leadData->client->whatsapp_no;
            }else{
                $whatsappNumber = $this->leadData->client->country_code.''.$this->leadData->client->mobile_no;
            }

            // Get variable mappings for this template
            $mappings = WhatsappTemplateVariableMapping::getMappingsForTemplate($whatsappTemplate);
            
            // Get all available variables from our lead data
            $allVariables = TemplateVariableService::getLeadVariables($this->leadData);
            
            // Build WhatsApp template parameters based on mappings
            $templateParams = [];
            foreach ($mappings as $whatsappVar => $systemVar) {
                $templateParams[$whatsappVar] = $allVariables[$systemVar] ?? '';
            }

            // Send WhatsApp message using the service
            $whatsappService = new WhatsappService();
            $result = $whatsappService->sendTemplate($whatsappNumber, $whatsappTemplate, $templateParams);
            
            if ($result['success']) {
                Log::info('WhatsApp notification sent successfully', [
                    'lead_id' => $this->leadData->id,
                    'template' => $whatsappTemplate,
                    'number' => $whatsappNumber,
                    'message_id' => $result['message_id'] ?? null
                ]);
            } else {
                Log::error('WhatsApp sending failed: ' . ($result['error'] ?? 'Unknown error'), [
                    'lead_id' => $this->leadData->id,
                    'template' => $whatsappTemplate,
                    'number' => $whatsappNumber
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed: ' . $e->getMessage(), [
                'lead_id' => $this->leadData->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendSystemNotification($config)
    {
        try {
            $teamNotificationType = TeamNotificationType::find($config->team_notification_types);
            
            if (!$teamNotificationType && Auth::user()) {
                return;
            }
            if(Auth::user()){
                $created_by = Auth::user()->id;
            }else{
                $created_by_data = User::where('branch_id', $this->leadData->client->branch_id)->first();
                $created_by = $created_by_data->id;
            }

            $variables = TemplateVariableService::getLeadVariables($this->leadData);
            
            // Process the notification message with variables
            $message = $teamNotificationType->processTemplate($variables);
            
            // Create system notification
            TeamNotification::create([
                'notification_type_id' => $teamNotificationType->id,
                'title' => $teamNotificationType->title,
                'message' => $message,
                'link' =>  route('team.client.show', $this->leadData->client->id),
                'data' => [
                    'lead_id' => $this->leadData->id,
                    'client_name' => $variables['client_name'] ?? '',
                    'lead_source' => $variables['lead_source'] ?? '',
                    'assigned_agent' => $variables['assigned_agent'] ?? '',
                ],
                'user_id' => $created_by,
                'is_seen' => false,
                'created_by' => $created_by,
            ]);
        } catch (\Exception $e) {
            Log::error('System notification creation failed: ' . $e->getMessage(), [
                'lead_id' => $this->leadData->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
}
