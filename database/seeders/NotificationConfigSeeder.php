<?php

namespace Database\Seeders;

use App\Models\NotificationConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        NotificationConfig::updateOrCreate(
            ['slug' => 'new_lead_notification'],
            [
                'class' => 'App\\Notifications\\NewLeadNotification',
                'email_enabled' => true,
                'email_template_id' => 3,
                'whatsapp_enabled' => true,
                'whatsapp_template' => null,
                'system_enabled' => true,
                'team_notification_types' => 1,
            ]
        );

        NotificationConfig::updateOrCreate(
            ['slug' => 'assign_lead_notification'],
            [
                'class' => 'App\\Notifications\\AssignLeadNotification',
                'email_enabled' => false,
                'email_template_id' => null,
                'whatsapp_enabled' => false,
                'whatsapp_template' => null,
                'system_enabled' => true,
                'team_notification_types' => 1,
            ]
        );

        NotificationConfig::updateOrCreate(
            ['slug' => 'new_task_notification'],
            [
                'class' => 'App\\Notifications\\NewTaskNotification',
                'email_enabled' => true,
                'email_template_id' => 1,
                'whatsapp_enabled' => false,
                'whatsapp_template' => null,
                'system_enabled' => true,
                'team_notification_types' => 4,
            ]
        );

        NotificationConfig::updateOrCreate(
            ['slug' => 'task_completed_notification'],
            [
                'class' => 'App\\Notifications\\TaskCompletedNotification',
                'email_enabled' => false,
                'whatsapp_enabled' => false,
                'whatsapp_template' => null,
                'system_enabled' => true,
                'team_notification_types' => 3,
            ]
        );

        NotificationConfig::updateOrCreate(
            ['slug' => 'task_comment_notification'],
            [
                'class' => 'App\\Notifications\\TaskCommentNotification',
                'email_enabled' => false,
                'whatsapp_enabled' => false,
                'whatsapp_template' => null,
                'system_enabled' => true,
                'team_notification_types' => 5,
            ]
        );

        NotificationConfig::updateOrCreate(
            ['slug' => 'assign_task_notification'],
            [
                'class' => 'App\\Notifications\\AssignTaskNotification',
                'email_enabled' => true,
                'email_template_id' => 2,
                'whatsapp_enabled' => false,
                'whatsapp_template' => null,
                'system_enabled' => true,
                'team_notification_types' => 4,
            ]
        );
    }
}
