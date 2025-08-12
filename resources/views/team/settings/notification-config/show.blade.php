@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Notification Config', 'url' => route('team.settings.notification-config.index')],
    ['title' => 'View Configuration']
];
@endphp

<x-team.layout.app title="View Notification Configuration" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Configuration: {{ ucwords(str_replace('_', ' ', $notificationConfig->slug)) }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Detailed view of notification configuration
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.notification-config.edit', $notificationConfig) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-setting-2"></i>
                        Edit Configuration
                    </a>
                    <a href="{{ route('team.settings.notification-config.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Basic Information -->
                <div class="lg:col-span-1">
                    <x-team.card title="Basic Information" class="mb-5">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-600">Notification Type:</span>
                                <span class="text-sm font-bold text-mono text-right">
                                    {{ ucwords(str_replace('_', ' ', $notificationConfig->slug)) }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-600">Slug:</span>
                                <span class="text-sm text-gray-900 font-mono text-right">{{ $notificationConfig->slug }}</span>
                            </div>
                            
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-600">Handler Class:</span>
                                <span class="text-xs text-gray-600 font-mono bg-gray-100 px-2 py-1 rounded text-right max-w-[200px] break-all">
                                    {{ $notificationConfig->class }}
                                </span>
                            </div>
                        </div>
                    </x-team.card>

                    <!-- Channel Status Overview -->
                    <x-team.card title="Channel Status" class="">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Email:</span>
                                @if($notificationConfig->email_enabled)
                                    <span class="kt-badge kt-badge-success kt-badge-light">Enabled</span>
                                @else
                                    <span class="kt-badge kt-badge-secondary kt-badge-light">Disabled</span>
                                @endif
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">WhatsApp:</span>
                                @if($notificationConfig->whatsapp_enabled)
                                    <span class="kt-badge kt-badge-info kt-badge-light">Enabled</span>
                                @else
                                    <span class="kt-badge kt-badge-secondary kt-badge-light">Disabled</span>
                                @endif
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">System:</span>
                                @if($notificationConfig->system_enabled)
                                    <span class="kt-badge kt-badge-primary kt-badge-light">Enabled</span>
                                @else
                                    <span class="kt-badge kt-badge-secondary kt-badge-light">Disabled</span>
                                @endif
                            </div>
                        </div>
                    </x-team.card>
                </div>

                <!-- Configuration Details -->
                <div class="lg:col-span-2">
                    <div class="grid gap-5">
                        <!-- Email Configuration -->
                        <x-team.card title="Email Configuration" class="">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Status:</span>
                                    @if($notificationConfig->email_enabled)
                                        <span class="kt-badge kt-badge-success">Enabled</span>
                                    @else
                                        <span class="kt-badge kt-badge-secondary">Disabled</span>
                                    @endif
                                </div>
                                
                                @if($notificationConfig->email_enabled)
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-medium text-gray-600">Email Template:</span>
                                        <div class="text-right">
                                            @if($notificationConfig->emailTemplate)
                                                <div class="text-sm font-medium text-primary">{{ $notificationConfig->emailTemplate->name }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $notificationConfig->email_template_id }}</div>
                                                @if($notificationConfig->emailTemplate->subject)
                                                    <div class="text-xs text-gray-600 mt-1">Subject: {{ $notificationConfig->emailTemplate->subject }}</div>
                                                @endif
                                            @else
                                                <span class="kt-badge kt-badge-warning kt-badge-light">Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">Email notifications are disabled</div>
                                @endif
                            </div>
                        </x-team.card>

                        <!-- WhatsApp Configuration -->
                        <x-team.card title="WhatsApp Configuration" class="">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Status:</span>
                                    @if($notificationConfig->whatsapp_enabled)
                                        <span class="kt-badge kt-badge-info">Enabled</span>
                                    @else
                                        <span class="kt-badge kt-badge-secondary">Disabled</span>
                                    @endif
                                </div>
                                
                                @if($notificationConfig->whatsapp_enabled)
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-medium text-gray-600">Template:</span>
                                        <div class="text-right">
                                            @if($notificationConfig->whatsapp_template)
                                                <div class="text-sm font-medium text-primary font-mono">{{ $notificationConfig->whatsapp_template }}</div>
                                                <div class="text-xs text-gray-500">Template Name/ID</div>
                                            @else
                                                <span class="kt-badge kt-badge-warning kt-badge-light">Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">WhatsApp notifications are disabled</div>
                                @endif
                            </div>
                        </x-team.card>

                        <!-- System Notification Configuration -->
                        <x-team.card title="System Notification Configuration" class="">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Status:</span>
                                    @if($notificationConfig->system_enabled)
                                        <span class="kt-badge kt-badge-primary">Enabled</span>
                                    @else
                                        <span class="kt-badge kt-badge-secondary">Disabled</span>
                                    @endif
                                </div>
                                
                                @if($notificationConfig->system_enabled)
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm font-medium text-gray-600">Notification Type:</span>
                                        <div class="text-right">
                                            @if($notificationConfig->teamNotificationType)
                                                <div class="text-sm font-medium text-primary">{{ $notificationConfig->teamNotificationType->title }}</div>
                                                <div class="text-xs text-gray-500">Key: {{ $notificationConfig->teamNotificationType->type_key }}</div>
                                                @if($notificationConfig->teamNotificationType->description)
                                                    <div class="text-xs text-gray-600 mt-1 max-w-[300px] text-right">{{ Str::limit($notificationConfig->teamNotificationType->description, 100) }}</div>
                                                @endif
                                            @else
                                                <span class="kt-badge kt-badge-warning kt-badge-light">Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">System notifications are disabled</div>
                                @endif
                            </div>
                        </x-team.card>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <x-team.card title="System Information" class="mt-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Created:</span>
                        <span class="text-sm text-gray-900">{{ $notificationConfig->created_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Last Modified:</span>
                        <span class="text-sm text-gray-900">{{ $notificationConfig->updated_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                </div>
            </x-team.card>
        </div>
    </x-slot>
</x-team.layout.app>
