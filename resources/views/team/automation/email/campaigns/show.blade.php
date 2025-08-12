@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'Email Campaigns', 'url' => route('team.automation.email.campaigns.index')],
        ['title' => $campaign->name]
    ];
@endphp

<x-team.layout.app :title="'Campaign: ' . $campaign->name" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        {{ $campaign->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        <span class="badge {{ $campaign->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span>•</span>
                        <span>{{ ucfirst($campaign->execution_type) }} Campaign</span>
                        @if($campaign->schedule_frequency)
                            <span>•</span>
                            <span>{{ ucfirst($campaign->schedule_frequency) }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.email.campaigns.edit', $campaign->id) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-pencil"></i>
                        Edit Campaign
                    </a>
                    <a href="{{ route('team.automation.email.campaigns.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Campaigns
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
                
                {{-- Main Content --}}
                <div class="xl:col-span-2">
                    <div class="flex flex-col gap-5">
                        
                        {{-- Campaign Overview --}}
                        <x-team.card title="Campaign Overview" headerClass="">
                            <div class="py-5">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                    
                                    {{-- Basic Info --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="form-label font-semibold">Campaign Name</label>
                                            <div class="text-gray-700">{{ $campaign->name }}</div>
                                        </div>
                                        
                                        @if($campaign->description)
                                        <div>
                                            <label class="form-label font-semibold">Description</label>
                                            <div class="text-gray-700">{{ $campaign->description }}</div>
                                        </div>
                                        @endif
                                        
                                        <div>
                                            <label class="form-label font-semibold">Email Template</label>
                                            <div class="text-gray-700">{{ $campaign->emailTemplate->subject ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    
                                    {{-- Settings --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="form-label font-semibold">Priority</label>
                                            <div class="text-gray-700">
                                                @switch($campaign->priority)
                                                    @case(1)
                                                        <span class="badge badge-danger">High</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge badge-warning">Medium</span>
                                                        @break
                                                    @case(3)
                                                        <span class="badge badge-secondary">Low</span>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="form-label font-semibold">Delay</label>
                                            <div class="text-gray-700">{{ $campaign->delay_minutes }} minutes</div>
                                        </div>
                                        
                                        <div>
                                            <label class="form-label font-semibold">Status</label>
                                            <div class="text-gray-700">
                                                <span class="badge {{ $campaign->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-team.card>

                        {{-- Execution Settings --}}
                        <x-team.card title="Execution Settings" headerClass="">
                            <div class="py-5">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                    
                                    <div>
                                        <label class="form-label font-semibold">Execution Type</label>
                                        <div class="text-gray-700">
                                            {{ $campaign->execution_type === 'one_time' ? 'One Time' : 'Set in Automation' }}
                                        </div>
                                    </div>
                                    
                                    @if($campaign->execution_type === 'one_time' && $campaign->scheduled_at)
                                        <div>
                                            <label class="form-label font-semibold">Scheduled Date</label>
                                            <div class="text-gray-700">{{ $campaign->scheduled_at->format('M d, Y H:i') }}</div>
                                        </div>
                                    @endif
                                    
                                    @if($campaign->execution_type === 'automation')
                                        <div>
                                            <label class="form-label font-semibold">Schedule Frequency</label>
                                            <div class="text-gray-700">{{ ucfirst($campaign->schedule_frequency) }}</div>
                                        </div>
                                        
                                        @if($campaign->schedule_config)
                                            <div class="lg:col-span-2">
                                                <label class="form-label font-semibold">Schedule Configuration</label>
                                                <div class="text-gray-700">
                                                    @if($campaign->schedule_frequency === 'daily')
                                                        Daily at {{ str_pad($campaign->schedule_config['hour'] ?? 9, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($campaign->schedule_config['minute'] ?? 0, 2, '0', STR_PAD_LEFT) }}
                                                    @elseif($campaign->schedule_frequency === 'weekly')
                                                        @php
                                                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                            $dayName = $days[$campaign->schedule_config['day_of_week'] ?? 1] ?? 'Monday';
                                                        @endphp
                                                        Weekly on {{ $dayName }} at {{ str_pad($campaign->schedule_config['hour'] ?? 9, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($campaign->schedule_config['minute'] ?? 0, 2, '0', STR_PAD_LEFT) }}
                                                    @elseif($campaign->schedule_frequency === 'monthly')
                                                        Monthly on day {{ $campaign->schedule_config['day_of_month'] ?? 1 }} at {{ str_pad($campaign->schedule_config['hour'] ?? 9, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($campaign->schedule_config['minute'] ?? 0, 2, '0', STR_PAD_LEFT) }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($campaign->next_run_at)
                                            <div>
                                                <label class="form-label font-semibold">Next Run</label>
                                                <div class="text-gray-700">{{ $campaign->next_run_at->format('M d, Y H:i') }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($campaign->last_run_at)
                                            <div>
                                                <label class="form-label font-semibold">Last Run</label>
                                                <div class="text-gray-700">{{ $campaign->last_run_at->format('M d, Y H:i') }}</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </x-team.card>

                        {{-- Lead Filters --}}
                        @if($campaign->processed_lead_filters && count($campaign->processed_lead_filters) > 0)
                        <x-team.card title="Lead Filters" headerClass="">
                            <div class="py-5">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                    @foreach($campaign->processed_lead_filters as $filterType => $displayNames)
                                        <div>
                                            <label class="form-label font-semibold">{{ ucfirst(str_replace('_', ' ', $filterType)) }}</label>
                                            <div class="text-gray-700">{{ $displayNames }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </x-team.card>
                        @endif

                        {{-- Campaign Rules --}}
                        @if($campaign->rules && $campaign->rules->count() > 0)
                        <x-team.card title="Campaign Rules" headerClass="">
                            <div class="py-5">
                                <div class="space-y-4">
                                    @foreach($campaign->rules as $rule)
                                    <div class="p-4 bg-gray-50 rounded-lg border">
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                            <div>
                                                <label class="form-label font-semibold text-sm">Field</label>
                                                <div class="text-gray-700">{{ $rule->field_name }}</div>
                                            </div>
                                            <div>
                                                <label class="form-label font-semibold text-sm">Operator</label>
                                                <div class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $rule->operator)) }}</div>
                                            </div>
                                            <div>
                                                <label class="form-label font-semibold text-sm">Value</label>
                                                <div class="text-gray-700">
                                                    @if(is_array($rule->field_value))
                                                        {{ implode(', ', $rule->field_value) }}
                                                    @else
                                                        {{ $rule->field_value }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </x-team.card>
                        @endif

                        {{-- Campaign Logs --}}
                        @if($campaign->logs && $campaign->logs->count() > 0)
                        <x-team.card title="Campaign History" headerClass="">
                            <div class="py-5">
                                <div class="space-y-3">
                                    @foreach($campaign->logs->take(10) as $log)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="ki-filled ki-message-text text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm">
                                                    Email sent to {{ $log->clientLead->client->name ?? 'Unknown Client' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $log->created_at->format('M d, Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge badge-success text-xs">Sent</span>
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                    @if($campaign->logs->count() > 10)
                                    <div class="text-center pt-3">
                                        <span class="text-sm text-gray-500">
                                            Showing 10 of {{ $campaign->logs->count() }} total logs
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </x-team.card>
                        @endif

                    </div>
                </div>
                
                {{-- Sidebar --}}
                <div class="flex flex-col gap-5">
                    
                    {{-- Quick Actions --}}
                    <x-team.card title="Quick Actions" headerClass="">
                        <div class="flex flex-col gap-3 py-5">
                            <a href="{{ route('team.automation.email.campaigns.edit', $campaign->id) }}" class="kt-btn kt-btn-primary w-full">
                                <i class="ki-filled ki-pencil"></i>
                                Edit Campaign
                            </a>
                            
                            <button type="button" class="kt-btn kt-btn-secondary w-full" onclick="toggleCampaign({{ $campaign->id }})">
                                <i class="ki-filled ki-{{ $campaign->is_active ? 'pause' : 'play' }}"></i>
                                {{ $campaign->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            
                            <button type="button" class="kt-btn kt-btn-info w-full" data-kt-modal-toggle="#testEmailModal" onclick="document.getElementById('testEmailForm').dataset.campaignId = {{ $campaign->id }}">
                                <i class="ki-filled ki-message-text"></i>
                                Send Test Email
                            </button>
                            
                            <button type="button" class="kt-btn kt-btn-danger w-full" onclick="deleteCampaign({{ $campaign->id }})">
                                <i class="ki-filled ki-trash"></i>
                                Delete Campaign
                            </button>
                        </div>
                    </x-team.card>

                    {{-- Campaign Stats --}}
                    <x-team.card title="Campaign Statistics" headerClass="">
                        <div class="py-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Emails Sent</span>
                                <span class="font-semibold">{{ $campaign->logs->count() ?? 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Created</span>
                                <span class="font-semibold text-sm">{{ $campaign->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Last Updated</span>
                                <span class="font-semibold text-sm">{{ $campaign->updated_at->format('M d, Y') }}</span>
                            </div>
                            
                            @if($campaign->next_run_at)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Next Run</span>
                                <span class="font-semibold text-sm">{{ $campaign->next_run_at->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>
                    </x-team.card>

                    {{-- Campaign Info --}}
                    <x-team.card title="Campaign Details" headerClass="">
                        <div class="text-sm py-5 space-y-3">
                            <div class="flex items-start gap-2">
                                <i class="ki-filled ki-information text-blue-500 mt-0.5"></i>
                                <div class="text-gray-600">Campaign ID: #{{ $campaign->id }}</div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="ki-filled ki-code text-green-500 mt-0.5"></i>
                                <div class="text-gray-600">Slug: {{ $campaign->slug }}</div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="ki-filled ki-gear text-purple-500 mt-0.5"></i>
                                <div class="text-gray-600">Type: {{ ucfirst($campaign->trigger_type ?? 'manual') }}</div>
                            </div>
                        </div>
                    </x-team.card>

                </div>
            </div>
        </div>

        {{-- Test Email Modal --}}
        <x-team.modal id="testEmailModal" title="Send Test Email" size="max-w-md">
            <form id="testEmailForm" onsubmit="sendTestEmail(); return false;">
                <div class="mb-4">
                    <x-team.forms.input
                        name="test_email"
                        type="email"
                        label="Test Email Address"
                        placeholder="Enter email address to receive test email"
                        required
                    />
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" data-kt-modal-dismiss="true" class="kt-btn kt-btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-send"></i>
                        Send Test Email
                    </button>
                </div>
            </form>
        </x-team.modal>

        @push('scripts')
        <script>
            function toggleCampaign(campaignId) {
                if (confirm('Are you sure you want to toggle this campaign status?')) {
                    fetch(`/team/automation/email/campaigns/${campaignId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error toggling campaign status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error toggling campaign status');
                    });
                }
            }

            function deleteCampaign(campaignId) {
                if (confirm('Are you sure you want to delete this campaign? This action cannot be undone.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/team/automation/email/campaigns/${campaignId}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function sendTestEmail() {
                const form = document.getElementById('testEmailForm');
                const campaignId = form.dataset.campaignId;
                const formData = new FormData(form);

                // Disable submit button during request
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="ki-filled ki-loading"></i> Sending...';

                fetch(`/team/automation/email/campaigns/${campaignId}/test`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal using theme's modal dismiss
                        KTToast.show({
                            message: data.message || 'Test email sent successfully!',
                            icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                            variant: "success",
                        });
                        const dismissBtn = form.querySelector('[data-kt-modal-dismiss]');
                        if (dismissBtn) dismissBtn.click();
                        form.reset();
                    } else {
                        alert('Error sending test email: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error sending test email. Please try again.');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            }
        </script>
        @endpush
    </x-slot>
</x-team.layout.app>
