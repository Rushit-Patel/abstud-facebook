@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'Email Automation']
    ];
@endphp

<x-team.layout.app title="Email Automation" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex items-center gap-3 w-full mb-5">
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.email.campaigns.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Create Campaign
                    </a>
                </div>
            </div>
            
            <div class="grid gap-2 lg:gap-2">
                {{-- Statistics Cards --}}
                <x-team.card title="Email Automation Statistics" headerClass="">
                    <div class="grid lg:grid-cols-4 gap-y-5 lg:gap-7.5 items-stretch pb-5">
                        {{-- Total Campaigns --}}
                        <div class="lg:col-span-1">
                            <div class="kt-card h-full">
                                <div class="kt-card-content p-7.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col gap-2">
                                            <span class="text-2xl font-semibold text-gray-900">{{ $totalCampaigns }}</span>
                                            <span class="text-sm font-medium text-gray-600">Total Campaigns</span>
                                        </div>
                                        <div class="flex items-center justify-center size-12 bg-primary-light rounded-lg">
                                            <i class="ki-filled ki-message-text-2 text-xl text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Active Campaigns --}}
                        <div class="lg:col-span-1">
                            <div class="kt-card h-full">
                                <div class="kt-card-content p-7.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col gap-2">
                                            <span class="text-2xl font-semibold text-gray-900">{{ $activeCampaigns }}</span>
                                            <span class="text-sm font-medium text-gray-600">Active Campaigns</span>
                                        </div>
                                        <div class="flex items-center justify-center size-12 bg-success-light rounded-lg">
                                            <i class="ki-filled ki-check-circle text-xl text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emails Sent -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold text-gray-900">{{ number_format($totalEmailsSent) }}</span>
                            <span class="text-sm font-medium text-gray-600">Emails Sent</span>
                        </div>
                        <div class="flex items-center justify-center size-12 bg-info-light rounded-lg">
                            <i class="ki-filled ki-send text-xl text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Emails -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold text-gray-900">{{ $pendingEmails }}</span>
                            <span class="text-sm font-medium text-gray-600">Pending Emails</span>
                        </div>
                        <div class="flex items-center justify-center size-12 bg-warning-light rounded-lg">
                            <i class="ki-filled ki-time text-xl text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fixed">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
            
            <!-- Recent Campaigns -->
            <div class="xl:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Campaigns</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('team.automation.email.campaigns.index') }}" class="btn btn-light btn-sm">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentCampaigns->count() > 0)
                            <div class="flex flex-col gap-4">
                                @foreach($recentCampaigns as $campaign)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center size-10 bg-primary-light rounded-lg">
                                                <i class="ki-filled ki-message-text-2 text-primary"></i>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ route('team.automation.email.campaigns.show', $campaign) }}" 
                                                   class="text-sm font-semibold text-gray-900 hover:text-primary">
                                                    {{ $campaign->name }}
                                                </a>
                                                <span class="text-xs text-gray-600">
                                                    {{ ucfirst(str_replace('_', ' ', $campaign->trigger_type)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="badge {{ $campaign->is_active ? 'badge-success' : 'badge-light' }}">
                                                {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="flex flex-col items-center gap-3">
                                    <i class="ki-filled ki-message-text-2 text-4xl text-gray-400"></i>
                                    <p class="text-gray-600">No campaigns created yet</p>
                                    <a href="{{ route('team.automation.email.campaigns.create') }}" class="btn btn-primary btn-sm">
                                        Create Your First Campaign
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="flex flex-col gap-5 lg:gap-7.5">
                
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('team.automation.email.campaigns.create') }}" 
                               class="kt-btn kt-btn-light kt-btn-sm justify-start">
                                <i class="ki-filled ki-plus"></i>
                                Create Campaign
                            </a>
                            <a href="{{ route('team.automation.email.templates') }}" 
                               class="kt-btn kt-btn-light kt-btn-sm justify-start">
                                <i class="ki-filled ki-design-1"></i>
                                Manage Templates
                            </a>
                            <a href="{{ route('team.automation.email.logs') }}" 
                               class="kt-btn kt-btn-light kt-btn-sm justify-start">
                                <i class="ki-filled ki-file-sheet"></i>
                                View Email Logs
                            </a>
                            <a href="{{ route('team.automation.analytics') }}" 
                               class="kt-btn kt-btn-light kt-btn-sm justify-start">
                                <i class="ki-filled ki-chart-line"></i>
                                View Analytics
                            </a>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Automation (Coming Soon) -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">WhatsApp Automation</h3>
                        <span class="badge badge-info">Coming Soon</span>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <div class="flex flex-col items-center gap-3">
                                <i class="ki-filled ki-whatsapp text-4xl text-green-500"></i>
                                <p class="text-sm text-gray-600">
                                    WhatsApp automation will be available soon. 
                                    Stay tuned for automated WhatsApp messaging capabilities.
                                </p>
                                <button class="btn btn-light btn-sm" disabled>
                                    <i class="ki-filled ki-notification-bing"></i>
                                    Notify When Ready
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Recent Email Logs -->
    @if($recentLogs->count() > 0)
    <div class="container-fixed mt-7.5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Email Activity</h3>
                <a href="{{ route('team.automation.email.logs') }}" class="btn btn-light btn-sm">
                    View All Logs
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-auto">
                        <thead>
                            <tr>
                                <th class="min-w-[200px]">Recipient</th>
                                <th class="min-w-[150px]">Campaign</th>
                                <th class="min-w-[120px]">Status</th>
                                <th class="min-w-[120px]">Sent At</th>
                                <th class="w-[60px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogs as $log)
                                <tr>
                                    <td>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $log->recipient_email }}
                                            </span>
                                            @if($log->clientLead && $log->clientLead->client)
                                                <span class="text-xs text-gray-600">
                                                    {{ $log->clientLead->client->first_name }} {{ $log->clientLead->client->last_name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm text-gray-900">{{ $log->campaign->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->status === 'sent' ? 'badge-success' : ($log->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-gray-600">
                                            {{ $log->sent_at ? $log->sent_at->format('M j, Y H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            @if($log->status === 'failed')
                                                <button class="btn btn-light btn-xs" 
                                                        onclick="retryEmail({{ $log->id }})">
                                                    <i class="ki-filled ki-refresh"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        function retryEmail(logId) {
            fetch(`/team/automation/email/logs/${logId}/retry`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while retrying the email.');
            });
        }
    </script>
    @endpush
     </x-slot>
</x-team.layout.app>
