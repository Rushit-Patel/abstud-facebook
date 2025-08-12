@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Notification Configuration Management']
];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush

<x-team.layout.app title="Notification Configuration Management" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Notification Configuration Management
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Configure notification channels and templates for system notifications
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="kt-badge kt-badge-light kt-badge-info">
                        <i class="ki-filled ki-information-2"></i>
                        System Generated - Edit Only
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-7.5">
                <x-team.card class="bg-blue-50">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center size-12 bg-blue-100 rounded-full">
                            <i class="ki-filled ki-sms text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\NotificationConfig::where('email_enabled', true)->count() }}</div>
                            <div class="text-sm text-blue-700">Email Enabled</div>
                        </div>
                    </div>
                </x-team.card>
                
                <x-team.card class="bg-green-50">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center size-12 bg-green-100 rounded-full">
                            <i class="ki-filled ki-whatsapp text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ \App\Models\NotificationConfig::where('whatsapp_enabled', true)->count() }}</div>
                            <div class="text-sm text-green-700">WhatsApp Enabled</div>
                        </div>
                    </div>
                </x-team.card>
                
                <x-team.card class="bg-purple-50">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center size-12 bg-purple-100 rounded-full">
                            <i class="ki-filled ki-notification-status text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">{{ \App\Models\NotificationConfig::where('system_enabled', true)->count() }}</div>
                            <div class="text-sm text-purple-700">System Enabled</div>
                        </div>
                    </div>
                </x-team.card>
                
                <x-team.card class="bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center size-12 bg-gray-100 rounded-full">
                            <i class="ki-filled ki-setting-4 text-gray-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-600">{{ \App\Models\NotificationConfig::count() }}</div>
                            <div class="text-sm text-gray-700">Total Configs</div>
                        </div>
                    </div>
                </x-team.card>
            </div>

            <x-team.card title="Notification Configuration List" headerClass="">
                <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch pb-5">
                    <div class="lg:col-span-1">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </x-team.card>
        </div>
    </x-slot>

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        
        <script>
            // Add any additional JavaScript functionality here
            $(document).ready(function() {
                // Handle channel toggle buttons if needed
                $('.toggle-channel').on('click', function() {
                    const configId = $(this).data('config-id');
                    const channel = $(this).data('channel');
                    const currentStatus = $(this).data('status');
                    
                    // Implementation for quick toggle functionality
                    // You can implement AJAX calls here for quick toggles
                });
            });
        </script>
    @endpush
</x-team.layout.app>
