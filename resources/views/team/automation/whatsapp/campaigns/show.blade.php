@php
    $breadcrumbItems = [
        ['title' => 'Automation', 'link' => route('team.automation.index')],
        ['title' => 'WhatsApp', 'link' => route('team.automation.whatsapp.index')],
        ['title' => 'Campaigns', 'link' => route('team.automation.whatsapp.campaigns.index')],
        ['title' => $campaign->name, 'link' => '']
    ];
@endphp

<x-team.layout.app title="{{ $campaign->name }} - WhatsApp Campaign" :breadcrumbs="$breadcrumbs">

    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
                
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
                                            <label class="form-label font-semibold">WhatsApp Provider</label>
                                            <div class="text-gray-700">
                                                <span class="badge badge-info">{{ ucfirst(optional($campaign->provider)->name) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="form-label font-semibold">Message Type</label>
                                            <div class="text-gray-700">
                                                <span class="badge badge-secondary">{{ ucfirst($campaign->message_type) }}</span>
                                            </div>
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
                                        
                                        @if($campaign->retry_attempts)
                                        <div>
                                            <label class="form-label font-semibold">Retry Attempts</label>
                                            <div class="text-gray-700">{{ $campaign->retry_attempts }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </x-team.card>

                        {{-- Message Content --}}
                        <x-team.card title="Message Content" headerClass="">
                            <div class="py-5">
                                <div class="space-y-4">
                                    @if($campaign->message_content)
                                    <div>
                                        <label class="form-label font-semibold">Message Text</label>
                                        <div class="p-4 bg-gray-50 rounded-lg border">
                                            <div class="text-gray-700 whitespace-pre-wrap">{{ $campaign->message_content }}</div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($campaign->template_name)
                                    <div>
                                        <label class="form-label font-semibold">Template Name</label>
                                        <div class="text-gray-700">{{ $campaign->template_name }}</div>
                                    </div>
                                    @endif
                                    
                                    @if($campaign->template_language)
                                    <div>
                                        <label class="form-label font-semibold">Template Language</label>
                                        <div class="text-gray-700">{{ $campaign->template_language }}</div>
                                    </div>
                                    @endif
                                    
                                    @if($campaign->template_parameters)
                                    <div>
                                        <label class="form-label font-semibold">Template Parameters</label>
                                        <div class="p-4 bg-gray-50 rounded-lg border">
                                            <div class="text-gray-700">
                                                @if(is_array($campaign->template_parameters))
                                                    @foreach($campaign->template_parameters as $key => $value)
                                                        <div class="mb-2">
                                                            <strong>{{ $key }}:</strong> {{ $value }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    {{ $campaign->template_parameters }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
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

                        {{-- Campaign Messages --}}
                        @if($campaign->messages && optional($campaign->messages)->count() > 0)
                        <x-team.card title="Message History" headerClass="">
                            <div class="py-5">
                                <div class="space-y-3">
                                    @foreach($campaign->messages->take(10) as $message)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="ki-filled ki-message-text text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm">
                                                    Message sent to {{ $message->clientLead->client->name ?? 'Unknown Client' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $message->phone_number }} • {{ $message->created_at->format('M d, Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge {{ $message->status === 'sent' ? 'badge-success' : ($message->status === 'pending' ? 'badge-warning' : 'badge-danger') }} text-xs">
                                                {{ ucfirst($message->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                    @if($campaign->messages->count() > 10)
                                    <div class="text-center pt-3">
                                        <span class="text-sm text-gray-500">
                                            Showing 10 of {{ optional($campaign->messages)->count() }} total messages
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
                            <a href="{{ route('team.automation.whatsapp.campaigns.edit', $campaign->id) }}" class="kt-btn kt-btn-primary w-full">
                                <i class="ki-filled ki-pencil"></i>
                                Edit Campaign
                            </a>

                            <button type="button" class="kt-btn kt-btn-info w-full" data-kt-modal-toggle="#testMessageModal">
                                <i class="ki-filled ki-whatsapp"></i>
                                Send Test Message
                            </button>
                        </div>
                    </x-team.card>

                    {{-- Campaign Stats --}}
                    @if($campaign->messages && $campaign->messages->count() > 0)
                    <x-team.card title="Campaign Statistics" headerClass="">
                        <div class="py-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Messages</span>
                                <span class="font-semibold">{{ optional($campaign->messages)->count() ?? 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Messages Sent</span>
                                <span class="font-semibold text-green-600">{{ optional($campaign->messages)->where('status', 'sent')->count() ?? 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Messages Failed</span>
                                <span class="font-semibold text-red-600">{{ optional($campaign->messages)->where('status', 'failed')->count() ?? 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Success Rate</span>
                                <span class="font-semibold">
                                    @php
                                        $total = $campaign->messages->count();
                                        if($total > 0){
                                            $sent = $campaign->messages->where('status', 'sent')->count();
                                        }else{
                                            $sent = 0;
                                        }
                                        $rate = $total > 0 ? round(($sent / $total) * 100, 1) : 0;
                                    @endphp
                                    {{ $rate }}%
                                </span>
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
                    @endif
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
                                <i class="ki-filled ki-whatsapp text-purple-500 mt-0.5"></i>
                                <div class="text-gray-600">Type: {{ ucfirst($campaign->trigger_type ?? 'manual') }}</div>
                            </div>
                        </div>
                    </x-team.card>

                </div>
            </div>
        </div>

        {{-- Send Test Message Modal --}}
        <x-team.modal id="testMessageModal" title="Send Test WhatsApp Message" size="max-w-md">
            <form method="POST" action="{{ route('team.automation.whatsapp.campaigns.send-test', $campaign->id) }}">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Send a test message to verify your WhatsApp campaign configuration.
                    </p>
                </div>
                
                <div class="mb-4">
                    <x-team.forms.mobile-input
                        name="test_phone"
                        label="Phone Number"
                        placeholder="Enter mobile number"
                        countryCodeName="test_phone_country_code"
                        required="true" />
                </div>

                <div class="mb-4">
                    <x-team.forms.input
                        name="test_name"
                        label="Test Name (Optional)"
                        placeholder="Enter name for variable replacement"
                        class="w-full" />
                    <small class="text-gray-500">This will replace {name} placeholders in the message</small>
                </div>

                @if($campaign->message_type === 'template' && $campaign->template_name)
                <div class="mb-4">
                    <label class="form-label font-semibold">Template Variable Mappings</label>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        @php
                            // Get template variable mappings from database
                            $templateMappings = \App\Models\WhatsappTemplateVariableMapping::getMappingsForTemplate($campaign->template_name);
                        @endphp
                        @if(!empty($templateMappings) && count($templateMappings) > 0)
                            <div class="text-sm space-y-2">
                                <div class="font-semibold text-blue-800">Automatic Variable Mapping:</div>
                                @foreach($templateMappings as $whatsappVar => $systemVar)
                                    <div class="flex items-center justify-between">
                                        <span class="text-blue-700">{{ $whatsappVar }}</span>
                                        <span class="text-gray-500">→</span>
                                        <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $systemVar)) }}</span>
                                    </div>
                                @endforeach
                                <div class="mt-3 text-xs text-blue-600">
                                    <i class="ki-filled ki-information-4 mr-1"></i>
                                    Variables will be automatically populated from client/lead data during actual campaign execution.
                                    Test message will use sample data for these variables.
                                </div>
                            </div>
                        @else
                            <div class="text-sm text-amber-700">
                                <i class="ki-filled ki-information-4 mr-1"></i>
                                No variable mappings configured for this template. 
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($campaign->message_type === 'text')
                <div class="mb-4">
                    <label class="form-label font-semibold">Message Preview</label>
                    <div class="p-3 bg-gray-50 rounded-lg border">
                        <div class="text-gray-700 whitespace-pre-wrap">{{ $campaign->message_content }}</div>
                    </div>
                </div>
                @elseif($campaign->message_type === 'template')
                <div class="mb-4">
                    <label class="form-label font-semibold">Template Info</label>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="text-sm">
                            <strong>Template Name:</strong> {{ $campaign->template_name }}<br>
                            <strong>Language:</strong> {{ $campaign->template_language ?? 'en_US' }}
                        </div>
                        <div class="mt-2 text-xs text-blue-600">
                            Variables will be automatically mapped from system data
                        </div>
                    </div>
                </div>
                @endif

                    <div class="flex justify-end gap-2">
                        <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">Cancel</button>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-whatsapp"></i>
                            Send Test Message
                        </button>
                    </div>
            </form>
        </x-team.modal>

        @push('scripts')
        <script>
        $(document).ready(function() {
            // Clear form when modal is hidden
            $('#testMessageModal').on('kt-modal-hide', function () {
                $(this).find('form')[0].reset();
            });
        });
        </script>
        @endpush
    </x-slot>
</x-team.layout.app>
