<?php

namespace App\Services;

use App\Models\ClientLead;
use Auth;

class TemplateVariableService
{
    /**
     * Get all available template variables with descriptions (for UI)
     */
    public static function getAllVariables(): array
    {
        return [
            'Client Information' => [
                'client_id' => 'Unique ID of the client',
                'client_name' => 'Full name of the client (First + Last)',
                'client_full_name' => 'Full name with middle name',
                'first_name' => 'First name of the client',
                'middle_name' => 'Middle name of the client',
                'last_name' => 'Last name of the client',
                'client_email' => 'Email address of the client',
                'client_phone' => 'Phone number of the client',
                'client_whatsapp' => 'WhatsApp number of the client',
                'client_address' => 'Address of the client',
                'client_gender' => 'Gender of the client',
                'client_marital_status' => 'Marital status of the client',
                'client_date_of_birth' => 'Date of birth of the client',
            ],
            
            'Lead Information' => [
                'lead_id' => 'Unique ID of the lead',
                'lead_date' => 'Date when the lead was created',
                'lead_type' => 'Type of lead',
                'lead_status' => 'Current status of the lead',
                'lead_sub_status' => 'Sub-status of the lead',
                'lead_source' => 'Source of the lead',
                'lead_tag' => 'Tag associated with the lead',
                'lead_remark' => 'Specific remark for the lead',
                'lead_general_remark' => 'General remark for the lead',
            ],
            
            'Related Information' => [
                'branch_name' => 'Name of the branch office',
                'branch_id' => 'ID of the branch office',
                'assigned_agent' => 'Name of the assigned sales agent',
                'assigned_agent_email' => 'Email of the assigned sales agent',
                'purpose' => 'Purpose of the lead/inquiry',
                'purpose_id' => 'ID of the purpose',
                'country' => 'Foreign country of interest',
                'country_id' => 'ID of the foreign country',
                'coaching_type' => 'Type of coaching/service',
                'coaching_id' => 'ID of the coaching type',
                'status_name' => 'Name of the lead status',
            ],
            
            'System Information' => [
                'app_name' => 'Application name',
                'app_url' => 'Application URL',
                'company_name' => 'Company name',
                'company_email' => 'Company email address',
                'support_email' => 'Support email address',
                'current_date' => 'Current date (Y-m-d)',
                'current_time' => 'Current time (H:i:s)',
                'current_datetime' => 'Current date and time',
                'current_year' => 'Current year',
                'current_month' => 'Current month name',
                'current_day' => 'Current day of month',
                'contact_url' => 'Contact page URL',
                'login_url' => 'Login page URL',
                'dashboard_url' => 'Dashboard URL',
            ],
        ];
    }

    /**
     * Get sample values for all variables (for preview/testing)
     */
    public static function getSampleValues(): array
    {
        $now = now();
        
        return [
            // Client Information
            'client_id' => '12345',
            'client_name' => 'John Doe',
            'client_full_name' => 'John Michael Doe',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'client_email' => 'john.doe@example.com',
            'client_phone' => '+1-555-0123',
            'client_whatsapp' => '+1-555-0123',
            'client_address' => '123 Main St, City, State',
            'client_gender' => 'Male',
            'client_marital_status' => 'Single',
            'client_date_of_birth' => '1990-01-15',
            
            // Lead Information
            'lead_id' => '67890',
            'lead_date' => $now->format('Y-m-d'),
            'lead_type' => 'Inquiry',
            'lead_status' => 'Active',
            'lead_sub_status' => 'New',
            'lead_source' => 'Website',
            'lead_tag' => 'Hot',
            'lead_remark' => 'Interested in IELTS coaching',
            'lead_general_remark' => 'Very motivated student',
            
            // Related Information
            'branch_name' => 'Main Office',
            'branch_id' => '1',
            'assigned_agent' => 'Jane Smith',
            'assigned_agent_email' => 'jane.smith@company.com',
            'purpose' => 'Higher Education',
            'purpose_id' => '1',
            'country' => 'Canada',
            'country_id' => '1',
            'coaching_type' => 'IELTS',
            'coaching_id' => '1',
            'status_name' => 'Active Lead',
            
            // System Information
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'company_name' => config('app.name'),
            'company_email' => config('mail.from.address'),
            'support_email' => config('mail.from.address'),
            'current_date' => $now->format('Y-m-d'),
            'current_time' => $now->format('H:i:s'),
            'current_datetime' => $now->format('Y-m-d H:i:s'),
            'current_year' => $now->year,
            'current_month' => $now->format('F'),
            'current_day' => $now->format('d'),
            'contact_url' => config('app.url') . '/contact',
            'login_url' => config('app.url') . '/login',
            'dashboard_url' => config('app.url') . '/dashboard',
        ];
    }

    /**
     * Get all variables from ClientLead and its relationships
     */
    public static function getLeadVariables(ClientLead $clientLead): array
    {
        $variables = [];
        if(Auth::user()){
            $authUserName = Auth::user()->name ?? '';
        }else{
            $authUserName = '';
        }
        // Client Information
        if ($clientLead->client) {
            $client = $clientLead->client;
            $variables = array_merge($variables, [
                'client_id' => $client->id,
                'client_name' => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                'client_full_name' => trim(($client->first_name ?? '') . ' ' . ($client->middle_name ?? '') . ' ' . ($client->last_name ?? '')),
                'first_name' => $client->first_name ?? '',
                'middle_name' => $client->middle_name ?? '',
                'last_name' => $client->last_name ?? '',
                'client_email' => $client->email_id ?? '',
                'client_phone' => $client->mobile_no ?? '',
                'client_whatsapp' => $client->whatsapp_no ?? '',
                'client_address' => $client->address ?? '',
                'client_gender' => $client->gender ?? '',
                'client_marital_status' => $client->maratial_status ?? '',
                'client_date_of_birth' => $client->date_of_birth ?? '',
                'auth_user_name' => $authUserName,
            ]);
        }

        // Lead Information
        $variables = array_merge($variables, [
            'lead_id' => $clientLead->id,
            'lead_date' => $clientLead->client_date ?? '',
            'lead_type' => $clientLead->lead_type ?? '',
            'lead_status' => $clientLead->status ?? '',
            'lead_sub_status' => $clientLead->sub_status ?? '',
            'lead_source' => $clientLead->source ?? '',
            'lead_tag' => $clientLead->tag ?? '',
            'lead_remark' => $clientLead->remark ?? '',
            'lead_general_remark' => $clientLead->genral_remark ?? '',
        ]);

        // Branch Information
        if ($clientLead->getBranch) {
            $variables['branch_name'] = $clientLead->getBranch->name ?? '';
            $variables['branch_id'] = $clientLead->getBranch->id ?? '';
        }

        // Assigned Owner
        if ($clientLead->assignedOwner) {
            $variables['assigned_agent'] = $clientLead->assignedOwner->name ?? '';
            $variables['assigned_agent_email'] = $clientLead->assignedOwner->email ?? '';
        }

        // Purpose
        if ($clientLead->getPurpose) {
            $variables['purpose'] = $clientLead->getPurpose->name ?? '';
            $variables['purpose_id'] = $clientLead->getPurpose->id ?? '';
        }

        // Country
        if ($clientLead->getForeignCountry) {
            $variables['country'] = $clientLead->getForeignCountry->name ?? '';
            $variables['country_id'] = $clientLead->getForeignCountry->id ?? '';
        }

        // Coaching
        if ($clientLead->getCoaching) {
            $variables['coaching_type'] = $clientLead->getCoaching->name ?? '';
            $variables['coaching_id'] = $clientLead->getCoaching->id ?? '';
        }

        // Status
        if ($clientLead->getStatus) {
            $variables['status_name'] = $clientLead->getStatus->name ?? '';
        }

        // Add system variables
        $variables = array_merge($variables, self::getSystemVariables());

        return $variables;
    }

    /**
     * Get system and date variables
     */
    public static function getSystemVariables(): array
    {
        $now = now();
        
        return [
            // Company Information
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'company_name' => config('app.name'),
            'company_email' => config('mail.from.address'),
            'support_email' => config('mail.from.address'),
            
            // Date/Time Variables
            'current_date' => $now->format('Y-m-d'),
            'current_time' => $now->format('H:i:s'),
            'current_datetime' => $now->format('Y-m-d H:i:s'),
            'current_year' => $now->year,
            'current_month' => $now->format('F'),
            'current_day' => $now->format('d'),
            
            // Action URLs
            'contact_url' => config('app.url') . '/contact',
            'login_url' => config('app.url') . '/login',
            'dashboard_url' => config('app.url') . '/dashboard',
        ];
    }

    /**
     * Replace variables in content
     */
    public static function replaceVariables(string $content, array $variables): string
    {
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
            $content = str_replace('{' . $key . '}', $value, $content);
            $content = str_replace('{ ' . $key . ' }', $value, $content);
        }
        return $content;
    }

    /**
     * Get variables from ClientLead and replace in content
     */
    public static function processTemplate(string $content, ClientLead $clientLead)
    {
        $variables = self::getLeadVariables($clientLead);
        return self::replaceVariables($content, $variables);
    }
}
