@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Notification Config', 'url' => route('team.settings.notification-config.index')],
    ['title' => 'Edit Configuration']
];
@endphp

<x-team.layout.app title="Edit Notification Configuration" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Configuration: {{ ucwords(str_replace('_', ' ', $notificationConfig->slug)) }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Configure notification channels and template mappings
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.notification-config.show', $notificationConfig) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-eye"></i>
                        View Details
                    </a>
                    <a href="{{ route('team.settings.notification-config.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.notification-config.update', $notificationConfig) }}" method="POST" class="form">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-7.5">
                    <!-- Notification Information Card -->
                    <div class="lg:col-span-1">
                        <x-team.card title="Notification Information" class="h-full">
                            <div class="space-y-4">
                                <div>
                                    <label class="kt-form-label text-sm font-medium text-gray-700">Notification Type</label>
                                    <div class="text-base font-semibold text-mono">
                                        {{ ucwords(str_replace('_', ' ', $notificationConfig->slug)) }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="kt-form-label text-sm font-medium text-gray-700">Handler Class</label>
                                    <div class="text-sm text-gray-600 font-mono bg-gray-50 p-2 rounded">
                                        {{ $notificationConfig->class }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="kt-form-label text-sm font-medium text-gray-700">Slug</label>
                                    <div class="text-sm text-gray-600">
                                        {{ $notificationConfig->slug }}
                                    </div>
                                </div>
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Channel Configuration -->
                    <div class="lg:col-span-2">
                        <div class="grid gap-5">
                            <!-- Email Configuration -->
                            <x-team.card title="Email Configuration" class="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="flex flex-col gap-3">
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="email_enabled"
                                                type="checkbox"
                                                value="1"
                                                {{ old('email_enabled', $notificationConfig->email_enabled) ? 'checked' : '' }}
                                                id="email_enabled"
                                            />
                                            <span class="kt-checkbox-label text-sm font-medium">
                                                Enable Email Notifications
                                            </span>
                                        </label>
                                        <p class="text-xs text-gray-500">Check to send notifications via email</p>
                                    </div>
                                    
                                    <div class="email-template-section" style="{{ old('email_enabled', $notificationConfig->email_enabled) ? '' : 'display: none;' }}">
                                        <x-team.forms.select
                                            name="email_template_id"
                                            label="Email Template"
                                            :options="$emailTemplates"
                                            :selected="old('email_template_id', $notificationConfig->email_template_id)"
                                            placeholder="Select email template"
                                        />
                                    </div>
                                </div>
                            </x-team.card>

                            <!-- WhatsApp Configuration -->
                            <x-team.card title="WhatsApp Configuration" class="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="flex flex-col gap-3">
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="whatsapp_enabled"
                                                type="checkbox"
                                                value="1"
                                                {{ old('whatsapp_enabled', $notificationConfig->whatsapp_enabled) ? 'checked' : '' }}
                                                id="whatsapp_enabled"
                                            />
                                            <span class="kt-checkbox-label text-sm font-medium">
                                                Enable WhatsApp Notifications
                                            </span>
                                        </label>
                                        <p class="text-xs text-gray-500">Check to send notifications via WhatsApp</p>
                                    </div>
                                    
                                    <div class="whatsapp-template-section" style="{{ old('whatsapp_enabled', $notificationConfig->whatsapp_enabled) ? '' : 'display: none;' }}">
                                        <x-team.forms.select
                                            name="whatsapp_template"
                                            label="WhatsApp Template"
                                            :options="$whatsappTemplates"
                                            :selected="old('whatsapp_template', $notificationConfig->whatsapp_template)"
                                            placeholder="Select WhatsApp template"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Template from your configured WhatsApp template variable mappings</p>
                                    </div>
                                </div>
                            </x-team.card>

                            <!-- System Notification Configuration -->
                            <x-team.card title="System Notification Configuration" class="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="flex flex-col gap-3">
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="system_enabled"
                                                type="checkbox"
                                                value="1"
                                                {{ old('system_enabled', $notificationConfig->system_enabled) ? 'checked' : '' }}
                                                id="system_enabled"
                                            />
                                            <span class="kt-checkbox-label text-sm font-medium">
                                                Enable System Notifications
                                            </span>
                                        </label>
                                        <p class="text-xs text-gray-500">Check to show in-app notifications</p>
                                    </div>
                                    
                                    <div class="system-template-section" style="{{ old('system_enabled', $notificationConfig->system_enabled) ? '' : 'display: none;' }}">
                                        <x-team.forms.select
                                            name="team_notification_types"
                                            label="Team Notification Type"
                                            :options="$teamNotificationTypes"
                                            :selected="old('team_notification_types', $notificationConfig->team_notification_types)"
                                            placeholder="Select notification type"
                                        />
                                    </div>
                                </div>
                            </x-team.card>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <x-team.card title="" class="border-t">
                    <div class="flex justify-end gap-2.5 pt-5">
                        <a href="{{ route('team.settings.notification-config.index') }}" class="kt-btn kt-btn-secondary">
                            <i class="ki-filled ki-arrow-left"></i>
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Configuration
                        </button>
                    </div>
                </x-team.card>
            </form>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Toggle email template section
                $('#email_enabled').change(function() {
                    if ($(this).is(':checked')) {
                        $('.email-template-section').slideDown();
                    } else {
                        $('.email-template-section').slideUp();
                        $('select[name="email_template_id"]').val('');
                    }
                });

                // Toggle WhatsApp template section
                $('#whatsapp_enabled').change(function() {
                    if ($(this).is(':checked')) {
                        $('.whatsapp-template-section').slideDown();
                    } else {
                        $('.whatsapp-template-section').slideUp();
                        $('input[name="whatsapp_template"]').val('');
                    }
                });

                // Toggle system notification section
                $('#system_enabled').change(function() {
                    if ($(this).is(':checked')) {
                        $('.system-template-section').slideDown();
                    } else {
                        $('.system-template-section').slideUp();
                        $('select[name="team_notification_types"]').val('');
                    }
                });

                // Form submission handling
                $('form').on('submit', function() {
                    // Disable submit button to prevent double submission
                    $(this).find('button[type="submit"]').prop('disabled', true);
                    
                    // Show loading state
                    $(this).find('button[type="submit"]').html('<i class="ki-filled ki-loading"></i> Updating...');
                });
            });
        </script>
    @endpush
</x-team.layout.app>
