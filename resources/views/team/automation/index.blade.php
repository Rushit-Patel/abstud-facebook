@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation']
    ];
@endphp

<x-team.layout.app title="Automation Dashboard" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="grid gap-2 lg:gap-2">
                {{-- Automation Types Section --}}
                <x-team.card title="Automation Dashboard" headerClass="">
                    <div class="grid lg:grid-cols-2 gap-y-5 lg:gap-7.5 items-stretch pb-5">

                        {{-- Email Automation Card --}}
                        <div class="lg:col-span-1">
                            <div class="kt-card h-full">
                                <div class="kt-card-content p-7.5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex flex-col gap-3 flex-1">
                                            <div class="flex flex-col gap-1">
                                                <h3 class="text-lg font-semibold text-gray-900">Email Automation</h3>
                                                <p class="text-sm text-gray-600">
                                                    Automate email campaigns, follow-ups, and notifications based on
                                                    lead behavior and status changes.
                                                </p>
                                            </div>

                                            {{-- Email Stats --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="flex flex-col gap-1">
                                                    <span
                                                        class="text-lg font-semibold text-gray-900">{{ $emailStats['activeCampaigns'] }}</span>
                                                    <span class="text-xs text-gray-600">Active Campaigns</span>
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <span
                                                        class="text-lg font-semibold text-gray-900">{{ number_format($emailStats['totalEmailsSent']) }}</span>
                                                    <span class="text-xs text-gray-600">Emails Sent</span>
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2 pt-2">
                                                <a href="{{ route('team.automation.email.campaigns.index') }}"
                                                    class="kt-btn kt-btn-primary kt-btn-sm">
                                                    <i class="ki-filled ki-setting-2"></i>
                                                    Manage Email
                                                </a>
                                                <a href="{{ route('team.automation.email.campaigns.create') }}"
                                                    class="kt-btn kt-btn-light kt-btn-sm">
                                                    <i class="ki-filled ki-plus"></i>
                                                    Create Campaign
                                                </a>
                                            </div>

                                            @if($emailStats['pendingEmails'] > 0)
                                                <div class="bg-warning-light p-3 rounded-lg">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ki-filled ki-time text-warning"></i>
                                                        <span class="text-sm text-warning font-medium">
                                                            {{ $emailStats['pendingEmails'] }} emails pending
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- WhatsApp Automation Card --}}
                        <div class="lg:col-span-1">
                            <div class="kt-card h-full">
                                <div class="kt-card-content p-7.5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex flex-col gap-3 flex-1">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-lg font-semibold text-gray-900">WhatsApp Automation</h3>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                Send automated WhatsApp messages, reminders, and updates to your leads
                                                and clients.
                                            </p>

                                            {{-- WhatsApp Stats --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="flex flex-col gap-1">
                                                    <span
                                                        class="text-lg font-semibold text-gray-900">{{ $whatsappStats['activeProviders'] }}</span>
                                                    <span class="text-xs text-gray-600">Active Campaigns</span>
                                                </div>
                                                <div class="flex flex-col gap-1">
                                                    <span
                                                        class="text-lg font-semibold text-gray-900">{{ number_format($whatsappStats['totalMessagesSent']) }}</span>
                                                    <span class="text-xs text-gray-600">Messages Sent</span>
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2 pt-2">
                                                <a href="{{ route('team.automation.whatsapp.campaigns.index') }}"
                                                    class="kt-btn kt-btn-primary kt-btn-sm">
                                                    <i class="ki-filled ki-notification-bing"></i>
                                                    Manage WhatsApp
                                                </a>
                                                <a href="{{ route('team.automation.whatsapp.campaigns.create') }}"
                                                    class="kt-btn kt-btn-light kt-btn-sm">
                                                    <i class="ki-filled ki-plus"></i>
                                                    Create Campaign
                                                </a>
                                            </div>

                                            @if($whatsappStats['pendingMessages'] > 0)
                                                <div class="bg-warning-light p-3 rounded-lg">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ki-filled ki-time text-warning"></i>
                                                        <span class="text-sm text-warning font-medium">
                                                            {{ $whatsappStats['pendingMessages'] }} messages pending
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($whatsappStats['failedMessages'] > 0)
                                                <div class="bg-danger-light p-3 rounded-lg">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ki-filled ki-cross-circle text-danger"></i>
                                                        <span class="text-sm text-danger font-medium">
                                                            {{ $whatsappStats['failedMessages'] }} failed messages
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-team.card>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="col-span-1">
                        {{-- Email Automation Section --}}
                        <x-team.card title="Email Automation" headerClass="">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- View Email Analytics --}}
                                {{-- <a href="{{ route('team.automation.analytics') }}"
                                    class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                        <i class="ki-filled ki-graph-2 text-white"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-gray-900">View Analytics</div>
                                        <div class="text-xs text-gray-600">Track performance</div>
                                    </div>
                                </a> --}}

                                {{-- Manage Email Templates --}}
                                {{-- <a href="{{ route('team.settings.email-templates.index') }}"
                                    class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                        <i class="ki-filled ki-design-1 text-white"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-gray-900">Email Templates</div>
                                        <div class="text-xs text-gray-600">Manage templates</div>
                                    </div>
                                </a> --}}

                                {{-- View Email Logs --}}
                                {{-- <a href="{{ route('team.automation.email.logs') }}"
                                    class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                        <i class="ki-filled ki-file-sheet text-white"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-gray-900">Email Logs</div>
                                        <div class="text-xs text-gray-600">View activity</div>
                                    </div>
                                </a> --}}

                            </div>
                        </x-team.card>
                    </div>
                    <div class="col-span-1">
                        {{-- WhatsApp Automation Section --}}
                        <x-team.card title="WhatsApp Automation" headerClass="">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                                {{-- Create WhatsApp Campaign --}}
                                {{-- <a href="{{ route('team.automation.whatsapp.campaigns.create') }}"
                                    class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                        <i class="ki-filled ki-plus text-white"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-gray-900">Create Campaign</div>
                                        <div class="text-xs text-gray-600">Bulk campaigns</div>
                                    </div>
                                </a> --}}

                                {{-- WhatsApp Templates --}}
                                {{-- <a href="{{ route('team.settings.whatsapp-templates.index') }}"
                                    class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                        <i class="ki-filled ki-design-1 text-white"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-gray-900">Templates</div>
                                        <div class="text-xs text-gray-600">Manage templates</div>
                                    </div>
                                </a> --}}

                                {{-- WhatsApp Logs --}}
                                {{-- <a href="{{ route('team.automation.whatsapp.logs') }}"
                                    class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                                    <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                        <i class="ki-filled ki-file-sheet text-white"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-gray-900">Message Logs</div>
                                        <div class="text-xs text-gray-600">View logs</div>
                                    </div>
                                </a> --}}
                            </div>
                        </x-team.card>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>