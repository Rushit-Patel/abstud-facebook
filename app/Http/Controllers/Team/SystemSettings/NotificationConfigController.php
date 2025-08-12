<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\NotificationConfig;
use App\Models\EmailTemplate;
use App\Models\TeamNotificationType;
use App\Models\WhatsappTemplateVariableMapping;
use App\DataTables\Team\Setting\NotificationConfigDataTable;
use Illuminate\Http\Request;

class NotificationConfigController extends Controller
{
    /**
     * Display a listing of notification configurations
     */
    public function index(NotificationConfigDataTable $dataTable)
    {
        return $dataTable->render('team.settings.notification-config.index');
    }

    /**
     * Show the form for editing notification configuration mappings
     */
    public function edit(NotificationConfig $notificationConfig)
    {
        // Get available email templates for dropdown
        $emailTemplates = EmailTemplate::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('-- Select Email Template --', '');

        // Get available team notification types for dropdown
        $teamNotificationTypes = TeamNotificationType::where('is_active', true)
            ->orderBy('title')
            ->pluck('title', 'id')
            ->prepend('-- Select Notification Type --', '');

        // Get unique WhatsApp templates that have mappings
        $whatsappTemplates = collect(WhatsappTemplateVariableMapping::getTemplatesWithMappings())
            ->sort()
            ->mapWithKeys(fn($template) => [$template => $template])
            ->prepend('-- Select WhatsApp Template --', '');

        return view('team.settings.notification-config.edit', compact(
            'notificationConfig',
            'emailTemplates',
            'teamNotificationTypes',
            'whatsappTemplates'
        ));
    }

    /**
     * Update notification configuration mappings
     */
    public function update(Request $request, NotificationConfig $notificationConfig)
    {
        try {
            $validated = $request->validate([
                'email_enabled' => 'boolean',
                'email_template_id' => 'nullable|exists:email_templates,id',
                'whatsapp_enabled' => 'boolean',
                'whatsapp_template' => 'nullable|string|max:255',
                'system_enabled' => 'boolean',
                'team_notification_types' => 'nullable|exists:team_notification_types,id',
            ], [
                'email_template_id.exists' => 'Selected email template does not exist.',
                'team_notification_types.exists' => 'Selected team notification type does not exist.',
                'whatsapp_template.max' => 'WhatsApp template name cannot exceed 255 characters.',
            ]);

            // Set boolean values properly
            $validated['email_enabled'] = $request->has('email_enabled');
            $validated['whatsapp_enabled'] = $request->has('whatsapp_enabled');
            $validated['system_enabled'] = $request->has('system_enabled');

            // Clear email template if email is disabled
            if (!$validated['email_enabled']) {
                $validated['email_template_id'] = null;
            }

            // Clear whatsapp template if whatsapp is disabled
            if (!$validated['whatsapp_enabled']) {
                $validated['whatsapp_template'] = null;
            }

            // Clear team notification type if system notifications are disabled
            if (!$validated['system_enabled']) {
                $validated['team_notification_types'] = null;
            }

            $notificationConfig->update($validated);

            return redirect()->route('team.settings.notification-config.index')
                ->with('success', "Notification configuration for '{$notificationConfig->slug}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating notification configuration: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed view of notification configuration
     */
    public function show(NotificationConfig $notificationConfig)
    {
        $notificationConfig->load(['emailTemplate', 'teamNotificationType']);
        
        return view('team.settings.notification-config.show', compact('notificationConfig'));
    }

    /**
     * Toggle status of a specific channel (email, whatsapp, system)
     */
    public function toggleChannel(Request $request, NotificationConfig $notificationConfig)
    {
        try {
            $channel = $request->input('channel');
            $status = $request->input('status');

            $allowedChannels = ['email_enabled', 'whatsapp_enabled', 'system_enabled'];
            
            if (!in_array($channel, $allowedChannels)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid channel specified.'
                ], 400);
            }

            $updateData = [$channel => $status];

            // If disabling a channel, clear related template/type
            if (!$status) {
                switch ($channel) {
                    case 'email_enabled':
                        $updateData['email_template_id'] = null;
                        break;
                    case 'whatsapp_enabled':
                        $updateData['whatsapp_template'] = null;
                        break;
                    case 'system_enabled':
                        $updateData['team_notification_types'] = null;
                        break;
                }
            }

            $notificationConfig->update($updateData);

            $channelName = str_replace('_enabled', '', $channel);
            $statusText = $status ? 'enabled' : 'disabled';

            return response()->json([
                'success' => true,
                'message' => ucfirst($channelName) . " notifications have been {$statusText} for '{$notificationConfig->slug}'.",
                'new_status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating notification channel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update multiple notification configurations
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $updates = $request->validate([
                'configs' => 'required|array',
                'configs.*.id' => 'required|exists:notification_configs,id',
                'configs.*.email_enabled' => 'boolean',
                'configs.*.whatsapp_enabled' => 'boolean',
                'configs.*.system_enabled' => 'boolean',
            ]);

            $updatedCount = 0;

            foreach ($updates['configs'] as $configData) {
                $config = NotificationConfig::find($configData['id']);
                if ($config) {
                    $config->update([
                        'email_enabled' => $configData['email_enabled'] ?? false,
                        'whatsapp_enabled' => $configData['whatsapp_enabled'] ?? false,
                        'system_enabled' => $configData['system_enabled'] ?? false,
                    ]);
                    $updatedCount++;
                }
            }

            return redirect()->route('team.settings.notification-config.index')
                ->with('success', "Successfully updated {$updatedCount} notification configurations.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating notification configurations: ' . $e->getMessage());
        }
    }
}
