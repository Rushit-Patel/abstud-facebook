<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailCampaign;
use App\Models\EmailAutomationRule;

class EmailAutomationSeeder extends Seeder
{
    public function run(): void
    {
        // Create Email Templates for Automation
        $this->createEmailTemplates();
        
        // Create Email Campaigns
        $this->createEmailCampaigns();
    }

    private function createEmailTemplates(): void
    {
        $templates = [
            [
                'name' => 'New Lead Welcome Email',
                'slug' => 'new-lead-welcome',
                'subject' => 'Welcome {client_name}! Let\'s Start Your Journey with {company_name}',
                'category' => 'automation',
                'description' => 'Welcome email sent to new leads',
                'html_template' => $this->getWelcomeEmailTemplate(),
                'variables' => [
                    'client_name', 'client_first_name', 'company_name', 
                    'lead_purpose', 'current_date'
                ],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Follow-up Overdue Reminder',
                'slug' => 'follow-up-overdue',
                'subject' => 'Action Required: Overdue Follow-up for {client_name}',
                'category' => 'automation',
                'description' => 'Reminder for overdue follow-ups',
                'html_template' => $this->getFollowUpReminderTemplate(),
                'variables' => [
                    'client_name', 'lead_status', 'days_overdue', 'follow_up_link'
                ],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Coaching Interest Nurture',
                'slug' => 'coaching-nurture',
                'subject' => 'Unlock Your Potential with Our Coaching Programs',
                'category' => 'automation',
                'description' => 'Nurture email for coaching interested leads',
                'html_template' => $this->getCoachingNurtureTemplate(),
                'variables' => [
                    'client_name', 'company_name', 'coaching_programs_link'
                ],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Status Change Notification',
                'slug' => 'status-change-notification',
                'subject' => 'Your Application Status has been Updated',
                'category' => 'automation',
                'description' => 'Notification when lead status changes',
                'html_template' => $this->getStatusChangeTemplate(),
                'variables' => [
                    'client_name', 'old_status', 'new_status', 'company_name'
                ],
                'is_system' => true,
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }

    private function createEmailCampaigns(): void
    {
        // Welcome Email Campaign
        $welcomeCampaign = EmailCampaign::create([
            'name' => 'New Lead Welcome',
            'slug' => 'new-lead-welcome',
            'description' => 'Welcome email for all new leads',
            'trigger_type' => 'status_change',
            'trigger_conditions' => [
                'status_from' => null,
                'status_to' => 'New'
            ],
            'email_template_id' => EmailTemplate::where('slug', 'new-lead-welcome')->first()->id,
            'delay_minutes' => 5, // 5 minutes delay
            'is_active' => true,
            'priority' => 1
        ]);

        // Add rules for welcome campaign
        EmailAutomationRule::create([
            'campaign_id' => $welcomeCampaign->id,
            'field_name' => 'status',
            'operator' => 'equals',
            'field_value' => ['New']
        ]);

        // Follow-up Overdue Campaign
        $followUpCampaign = EmailCampaign::create([
            'name' => 'Follow-up Overdue Alert',
            'slug' => 'follow-up-overdue',
            'description' => 'Alert for overdue follow-ups',
            'trigger_type' => 'follow_up_due',
            'trigger_conditions' => [
                'days_overdue' => 1
            ],
            'email_template_id' => EmailTemplate::where('slug', 'follow-up-overdue')->first()->id,
            'delay_minutes' => 60, // 1 hour delay
            'is_active' => true,
            'priority' => 1
        ]);

        // Coaching Nurture Campaign (Time-based)
        $coachingCampaign = EmailCampaign::create([
            'name' => 'Coaching Interest Nurture',
            'slug' => 'coaching-nurture',
            'description' => 'Nurture leads interested in coaching',
            'trigger_type' => 'time_based',
            'trigger_conditions' => [
                'days_since_created' => 3
            ],
            'email_template_id' => EmailTemplate::where('slug', 'coaching-nurture')->first()->id,
            'delay_minutes' => 0,
            'is_active' => true,
            'priority' => 2
        ]);

        // Add rules for coaching campaign
        EmailAutomationRule::create([
            'campaign_id' => $coachingCampaign->id,
            'field_name' => 'purpose',
            'operator' => 'contains',
            'field_value' => ['coaching', 'Coaching']
        ]);

        // Status Change Notification Campaign
        $statusChangeCampaign = EmailCampaign::create([
            'name' => 'Status Change Notification',
            'slug' => 'status-change-notification',
            'description' => 'Notify clients when status changes',
            'trigger_type' => 'status_change',
            'trigger_conditions' => [
                'notify_client' => true
            ],
            'email_template_id' => EmailTemplate::where('slug', 'status-change-notification')->first()->id,
            'delay_minutes' => 10,
            'is_active' => true,
            'priority' => 1
        ]);

        // Add rules for status change (exclude 'New' status as it's handled by welcome)
        EmailAutomationRule::create([
            'campaign_id' => $statusChangeCampaign->id,
            'field_name' => 'status',
            'operator' => 'not_in',
            'field_value' => ['New', 'Cancelled', 'Invalid']
        ]);
    }

    private function getWelcomeEmailTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {company_name}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0; font-size: 28px;">Welcome to {company_name}!</h1>
        <p style="margin: 10px 0 0; font-size: 18px;">We\'re excited to help you achieve your goals</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #333; margin-bottom: 20px;">Hello {client_first_name},</h2>
        
        <p>Thank you for your interest in our services! We\'ve received your inquiry regarding <strong>{lead_purpose}</strong> and we\'re here to help you every step of the way.</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <h3 style="margin-top: 0; color: #667eea;">What happens next?</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Our team will review your requirements</li>
                <li>You\'ll receive a personalized consultation</li>
                <li>We\'ll create a customized plan for your success</li>
            </ul>
        </div>
        
        <p>In the meantime, feel free to explore our website or contact us if you have any questions.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="#" style="background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Explore Our Services</a>
        </div>
        
        <p style="font-size: 14px; color: #666; margin-top: 30px;">
            Best regards,<br>
            The {company_name} Team<br>
            <em>Date: {current_date}</em>
        </p>
    </div>
</body>
</html>';
    }

    private function getFollowUpReminderTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-up Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #ff6b6b; color: white; padding: 20px; text-align: center; border-radius: 8px;">
        <h1 style="margin: 0; font-size: 24px;">⚠️ Follow-up Required</h1>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
        <h2>Action Required: Follow-up Overdue</h2>
        
        <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #ff6b6b;">
            <p><strong>Client:</strong> {client_name}</p>
            <p><strong>Current Status:</strong> {lead_status}</p>
            <p><strong>Days Overdue:</strong> {days_overdue}</p>
        </div>
        
        <p>This follow-up is overdue and requires immediate attention. Please review the client\'s status and take appropriate action.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{follow_up_link}" style="background: #ff6b6b; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">View Client Details</a>
        </div>
    </div>
</body>
</html>';
    }

    private function getCoachingNurtureTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coaching Programs</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%); color: #333; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0; font-size: 28px;">🎯 Unlock Your Potential</h1>
        <p style="margin: 10px 0 0; font-size: 18px;">Personalized Coaching Programs</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2>Hi {client_name},</h2>
        
        <p>We noticed you\'re interested in our coaching programs. That\'s fantastic! Our coaching services have helped hundreds of students achieve their dreams.</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #e17055; margin-top: 0;">🌟 Why Choose Our Coaching?</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Personalized Approach:</strong> Tailored to your specific goals</li>
                <li><strong>Expert Trainers:</strong> Industry-experienced professionals</li>
                <li><strong>Proven Track Record:</strong> 95% success rate</li>
                <li><strong>Flexible Schedules:</strong> Learn at your own pace</li>
            </ul>
        </div>
        
        <div style="background: #e17055; color: white; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
            <h3 style="margin: 0;">🎁 Special Offer</h3>
            <p style="margin: 10px 0;">Book a free consultation this week and get 20% off your first month!</p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{coaching_programs_link}" style="background: #e17055; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">View Coaching Programs</a>
        </div>
        
        <p style="font-size: 14px; color: #666;">
            Ready to take the next step? Contact us today!<br><br>
            Best regards,<br>
            The {company_name} Team
        </p>
    </div>
</body>
</html>';
    }

    private function getStatusChangeTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0; font-size: 28px;">📋 Status Update</h1>
        <p style="margin: 10px 0 0; font-size: 18px;">Your Application Progress</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2>Hello {client_name},</h2>
        
        <p>We wanted to keep you updated on the progress of your application with {company_name}.</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #74b9ff;">
            <h3 style="margin-top: 0; color: #74b9ff;">Status Change</h3>
            <p><strong>Previous Status:</strong> <span style="color: #636e72;">{old_status}</span></p>
            <p><strong>Current Status:</strong> <span style="color: #00b894; font-weight: bold;">{new_status}</span></p>
        </div>
        
        <p>This status change reflects the current stage of your application process. Our team continues to work diligently on your case.</p>
        
        <div style="background: #e8f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0; color: #2d3436;"><strong>Next Steps:</strong> Our team will contact you within the next 24-48 hours with detailed information about the next phase of your application.</p>
        </div>
        
        <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>
        
        <p style="font-size: 14px; color: #666; margin-top: 30px;">
            Thank you for choosing {company_name}!<br><br>
            Best regards,<br>
            The {company_name} Team
        </p>
    </div>
</body>
</html>';
    }
}
