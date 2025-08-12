<?php

// Database Seeder: TeamNotificationTypeSeeder.php
namespace Database\Seeders;

use App\Models\TeamNotificationType;
use Illuminate\Database\Seeder;

class TeamNotificationTypeSeeder extends Seeder
{
    public function run()
    {
        $notificationTypes = [
            [
                'type_key' => 'new_lead_assign',
                'title' => 'New Lead Assigned',
                'description' => 'New lead "{client_name}" has been assigned to you by {auth_user_name}. Phone: {client_phone}',
                'icon' => 'ki-filled ki-delivery-3',
                'color' => 'red', // red
            ],
            [
                'type_key' => 'assign_token',
                'title' => 'Token Assigned',
                'description' => 'You have been assigned {token_count} tokens by {assigned_by} for {purpose}',
                'icon' => 'ki-filled ki-teacher',
                'color' => 'yellow',
            ],
            [
                'type_key' => 'task_completed',
                'title' => 'Task Completed',
                'description' => 'Task "{task_title}" has been completed by {completed_by}',
                'icon' => 'ki-filled ki-check-circle',
                'color' => 'green',
            ],
            [
                'type_key' => 'new_task_assign',
                'title' => 'New Task Assigned',
                'description' => 'Task "{task_title}" has been assigned to you by {auth_user_name}. Due date: {due_date}',
                'icon' => 'ki-filled ki-book',
                'color' => 'blue', // Blue
            ],
            [
                'type_key' => 'task_comment',
                'title' => 'New Task Comment',
                'description' => 'New comment on task "{task_title}" by {auth_user_name}',
                'icon' => 'ki-filled ki-book',
                'color' => 'blue', // Blue
            ]
        ];

        foreach ($notificationTypes as $type) {
            TeamNotificationType::updateOrCreate(
                ['type_key' => $type['type_key']],
                $type
            );
        }
    }
}
