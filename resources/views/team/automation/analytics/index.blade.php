@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'Analytics']
    ];
@endphp

<x-team.layout.app title="Automation Analytics" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            {{-- Page Header --}}
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Automation Analytics
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Track performance and insights of your email automation campaigns
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Automation
                    </a>
                </div>
            </div>

            {{-- Overview Statistics --}}
            <div class="grid lg:grid-cols-4 gap-5 lg:gap-7.5 mb-7.5">
                
                {{-- Total Emails Sent --}}
                <x-team.card title="" headerClass="border-0 p-0" class="h-full">
                    <div class="flex items-center justify-between p-7.5">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold text-gray-900">{{ number_format($totalEmailsSent) }}</span>
                            <span class="text-sm font-medium text-gray-600">Emails Sent</span>
                        </div>
                        <div class="flex items-center justify-center size-12 bg-success-light rounded-lg">
                            <i class="ki-filled ki-send text-xl text-success"></i>
                        </div>
                    </div>
                </x-team.card>

                {{-- Pending Emails --}}
                <x-team.card title="" headerClass="border-0 p-0" class="h-full">
                    <div class="flex items-center justify-between p-7.5">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold text-gray-900">{{ number_format($totalPending) }}</span>
                            <span class="text-sm font-medium text-gray-600">Pending Emails</span>
                        </div>
                        <div class="flex items-center justify-center size-12 bg-warning-light rounded-lg">
                            <i class="ki-filled ki-time text-xl text-warning"></i>
                        </div>
                    </div>
                </x-team.card>

                {{-- Failed Emails --}}
                <x-team.card title="" headerClass="border-0 p-0" class="h-full">
                    <div class="flex items-center justify-between p-7.5">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold text-gray-900">{{ number_format($totalFailed) }}</span>
                            <span class="text-sm font-medium text-gray-600">Failed Emails</span>
                        </div>
                        <div class="flex items-center justify-center size-12 bg-danger-light rounded-lg">
                            <i class="ki-filled ki-cross-circle text-xl text-danger"></i>
                        </div>
                    </div>
                </x-team.card>

                {{-- Active Campaigns --}}
                <x-team.card title="" headerClass="border-0 p-0" class="h-full">
                    <div class="flex items-center justify-between p-7.5">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold text-gray-900">{{ $totalCampaigns }}</span>
                            <span class="text-sm font-medium text-gray-600">Total Campaigns</span>
                        </div>
                        <div class="flex items-center justify-center size-12 bg-primary-light rounded-lg">
                            <i class="ki-filled ki-message-text-2 text-xl text-primary"></i>
                        </div>
                    </div>
                </x-team.card>

            </div>

            {{-- Main Analytics Content --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
                
                {{-- Email Status Distribution --}}
                <div class="xl:col-span-2">
                    <x-team.card title="Email Status Distribution" headerClass="">
                        @if(array_sum($statusDistribution) > 0)
                            <div class="flex flex-col gap-4 py-5">
                                @foreach($statusDistribution as $status => $count)
                                    @php
                                        $total = array_sum($statusDistribution);
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                        $colorClass = match($status) {
                                            'sent' => 'bg-success',
                                            'pending' => 'bg-warning', 
                                            'failed' => 'bg-danger',
                                            default => 'bg-gray-300'
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-3 h-3 rounded {{ $colorClass }}"></div>
                                            <span class="text-sm font-medium text-gray-900 capitalize">{{ $status }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-600">{{ number_format($count) }}</span>
                                            <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="ki-filled ki-chart-pie text-4xl text-gray-400"></i>
                                <p class="text-gray-600 mt-2">No email data available yet</p>
                            </div>
                        @endif
                    </x-team.card>
                </div>

                {{-- Recent Activity --}}
                <div>
                    <x-team.card title="Recent Activity" headerClass="">
                        @if($recentActivity->count() > 0)
                            <div class="flex flex-col gap-3 py-5">
                                @foreach($recentActivity->take(8) as $activity)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center justify-center size-8 {{ $activity->status === 'sent' ? 'bg-success-light' : ($activity->status === 'failed' ? 'bg-danger-light' : 'bg-warning-light') }} rounded">
                                            <i class="ki-filled {{ $activity->status === 'sent' ? 'ki-check' : ($activity->status === 'failed' ? 'ki-cross' : 'ki-time') }} text-xs {{ $activity->status === 'sent' ? 'text-success' : ($activity->status === 'failed' ? 'text-danger' : 'text-warning') }}"></i>
                                        </div>
                                        <div class="flex flex-col flex-1 gap-1">
                                            <div class="text-xs text-gray-900">{{ $activity->campaign->name }}</div>
                                            <div class="text-xs text-gray-600">
                                                @if($activity->clientLead && $activity->clientLead->client)
                                                    {{ $activity->clientLead->client->first_name }} {{ $activity->clientLead->client->last_name }}
                                                @else
                                                    {{ $activity->recipient_email }}
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="ki-filled ki-notification-bing text-3xl text-gray-400"></i>
                                <p class="text-gray-600 text-sm mt-2">No recent activity</p>
                            </div>
                        @endif
                    </x-team.card>
                </div>

            </div>

            {{-- Campaign Performance --}}
            @if($campaignStats->count() > 0)
            <div class="mt-7.5">
                <x-team.card title="Campaign Performance" headerClass="border-b border-gray-200 pb-5">
                    <x-slot name="headerAction">
                        <div class="flex items-center gap-2">
                            <button class="kt-btn kt-btn--light kt-btn--sm">
                                <i class="ki-filled ki-filter"></i>
                                Filter
                            </button>
                            <button class="kt-btn kt-btn--light kt-btn--sm">
                                <i class="ki-filled ki-exit-down"></i>
                                Export
                            </button>
                        </div>
                    </x-slot>

                    <div class="py-5">
                        <div class="table-responsive">
                            <table class="table table-auto table-striped">
                                <thead>
                                    <tr class="text-left">
                                        <th class="min-w-[200px] px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                            Campaign Details
                                        </th>
                                        <th class="min-w-[120px] px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50 text-center">
                                            Total Emails
                                        </th>
                                        <th class="min-w-[100px] px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50 text-center">
                                            Delivered
                                        </th>
                                        <th class="min-w-[100px] px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50 text-center">
                                            Failed
                                        </th>
                                        <th class="min-w-[140px] px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50 text-center">
                                            Success Rate
                                        </th>
                                        <th class="min-w-[100px] px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50 text-center">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($campaignStats as $campaign)
                                        @php
                                            $successRate = $campaign->total_emails > 0 ? round(($campaign->sent_emails / $campaign->total_emails) * 100, 1) : 0;
                                            $failureRate = $campaign->total_emails > 0 ? round(($campaign->failed_emails / $campaign->total_emails) * 100, 1) : 0;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-4 py-4">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex items-center justify-center size-10 bg-primary-light rounded-lg">
                                                            <i class="ki-filled ki-message-text-2 text-primary"></i>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-sm font-semibold text-gray-900 leading-tight">{{ $campaign->name }}</span>
                                                            <span class="text-xs text-gray-500 mt-1">{{ $campaign->description ?: 'No description' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 ml-13">
                                                        <span class="kt-badge kt-badge--light kt-badge--sm">
                                                            <i class="ki-filled ki-flash text-xs mr-1"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $campaign->trigger_type)) }}
                                                        </span>
                                                        @if($campaign->created_at)
                                                            <span class="text-xs text-gray-400">Created {{ $campaign->created_at->diffForHumans() }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="text-lg font-bold text-gray-900">{{ number_format($campaign->total_emails) }}</span>
                                                    <span class="text-xs text-gray-500">emails</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ki-filled ki-check-circle text-success text-sm"></i>
                                                        <span class="text-sm font-semibold text-success">{{ number_format($campaign->sent_emails) }}</span>
                                                    </div>
                                                    <span class="text-xs text-gray-500">delivered</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ki-filled ki-cross-circle text-danger text-sm"></i>
                                                        <span class="text-sm font-semibold text-danger">{{ number_format($campaign->failed_emails) }}</span>
                                                    </div>
                                                    <span class="text-xs text-gray-500">failed</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold {{ $successRate >= 90 ? 'text-success' : ($successRate >= 70 ? 'text-warning' : 'text-danger') }}">
                                                            {{ $successRate }}%
                                                        </span>
                                                        @if($successRate >= 90)
                                                            <i class="ki-filled ki-arrow-up text-success text-xs"></i>
                                                        @elseif($successRate >= 70)
                                                            <i class="ki-filled ki-minus text-warning text-xs"></i>
                                                        @else
                                                            <i class="ki-filled ki-arrow-down text-danger text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                                        <div class="h-2 rounded-full {{ $successRate >= 90 ? 'bg-success' : ($successRate >= 70 ? 'bg-warning' : 'bg-danger') }}" 
                                                             style="width: {{ $successRate }}%"></div>
                                                    </div>
                                                    @if($failureRate > 0)
                                                        <span class="text-xs text-gray-400">{{ $failureRate }}% failed</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex flex-col items-center gap-2">
                                                    <span class="kt-badge {{ $campaign->is_active ? 'kt-badge-success' : 'kt-badge-secondary' }} kt-badge--inline">
                                                        <i class="ki-filled {{ $campaign->is_active ? 'ki-check-circle' : 'ki-pause-circle' }} text-xs mr-1"></i>
                                                        {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                    @if($campaign->is_active && $campaign->next_run_at)
                                                        <span class="text-xs text-gray-500">
                                                            Next: {{ $campaign->next_run_at->format('M j, H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination if needed --}}
                        @if(method_exists($campaignStats, 'links') && $campaignStats->hasPages())
                            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                                <div class="text-sm text-gray-700">
                                    Showing {{ $campaignStats->firstItem() }} to {{ $campaignStats->lastItem() }} of {{ $campaignStats->total() }} campaigns
                                </div>
                                {{ $campaignStats->links() }}
                            </div>
                        @endif
                    </div>
                </x-team.card>
            </div>
            @else
                {{-- Empty State --}}
                <div class="mt-7.5">
                    <x-team.card title="Campaign Performance" headerClass="">
                        <div class="text-center py-12">
                            <div class="flex items-center justify-center size-16 bg-gray-100 rounded-full mx-auto mb-4">
                                <i class="ki-filled ki-chart-line text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Campaign Data Available</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Start creating email campaigns to see performance analytics and insights here.
                            </p>
                            <a href="{{ route('team.automation.email-campaigns.create') }}" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-plus"></i>
                                Create Your First Campaign
                            </a>
                        </div>
                    </x-team.card>
                </div>
            @endif
        </div>
    </x-slot>
</x-team.layout.app>


