@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'WhatsApp']
    ];
@endphp

<x-team.layout.app title="WhatsApp Automation" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="grid gap-2 lg:gap-2">
                
                {{-- Statistics Section --}}
                <x-team.card title="WhatsApp Overview" headerClass="">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        
                        {{-- Total Messages Sent --}}
                        <div class="flex flex-col items-center gap-2 p-4 bg-success-light rounded-lg">
                            <div class="flex items-center justify-center size-12 bg-success rounded-lg">
                                <i class="ki-filled ki-message-check text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($whatsappStats['totalMessagesSent']) }}</div>
                                <div class="text-sm text-gray-600">Messages Sent</div>
                            </div>
                        </div>

                        {{-- Pending Messages --}}
                        <div class="flex flex-col items-center gap-2 p-4 bg-warning-light rounded-lg">
                            <div class="flex items-center justify-center size-12 bg-warning rounded-lg">
                                <i class="ki-filled ki-time text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-gray-900">{{ $whatsappStats['pendingMessages'] }}</div>
                                <div class="text-sm text-gray-600">Pending</div>
                            </div>
                        </div>

                        {{-- Failed Messages --}}
                        <div class="flex flex-col items-center gap-2 p-4 bg-danger-light rounded-lg">
                            <div class="flex items-center justify-center size-12 bg-danger rounded-lg">
                                <i class="ki-filled ki-cross-circle text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-gray-900">{{ $whatsappStats['failedMessages'] }}</div>
                                <div class="text-sm text-gray-600">Failed</div>
                            </div>
                        </div>

                        {{-- Active Campaigns --}}
                        <div class="flex flex-col items-center gap-2 p-4 bg-info-light rounded-lg">
                            <div class="flex items-center justify-center size-12 bg-info rounded-lg">
                                <i class="ki-filled ki-rocket text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-gray-900">{{ $whatsappStats['activeCampaigns'] }}</div>
                                <div class="text-sm text-gray-600">Active Campaigns</div>
                            </div>
                        </div>

                        {{-- Active Providers --}}
                        <div class="flex flex-col items-center gap-2 p-4 bg-primary-light rounded-lg">
                            <div class="flex items-center justify-center size-12 bg-primary rounded-lg">
                                <i class="ki-filled ki-setting-2 text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-gray-900">{{ $whatsappStats['activeProviders'] }}</div>
                                <div class="text-sm text-gray-600">Active Providers</div>
                            </div>
                        </div>

                    </div>
                </x-team.card>

                {{-- Quick Campaign Actions --}}
                <x-team.card title="Campaign Quick Actions" headerClass="">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Create Bulk Campaign --}}
                        <a href="{{ route('team.automation.whatsapp.campaigns.create') }}?type=bulk" 
                           class="flex flex-col items-center gap-3 p-4 bg-primary-light rounded-lg hover:bg-primary/10 transition-colors">
                            <div class="flex items-center justify-center size-10 bg-primary rounded-lg">
                                <i class="ki-filled ki-notification-bing text-white"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-900">Bulk Campaign</div>
                                <div class="text-xs text-gray-600">Send to multiple recipients</div>
                            </div>
                        </a>

                        {{-- Create Auto Campaign --}}
                        <a href="{{ route('team.automation.whatsapp.campaigns.create') }}?type=automation" 
                           class="flex flex-col items-center gap-3 p-4 bg-success-light rounded-lg hover:bg-success/10 transition-colors">
                            <div class="flex items-center justify-center size-10 bg-success rounded-lg">
                                <i class="ki-filled ki-rocket text-white"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-900">Auto Campaign</div>
                                <div class="text-xs text-gray-600">Trigger-based automation</div>
                            </div>
                        </a>

                        {{-- Manage All Campaigns --}}
                        <a href="{{ route('team.automation.whatsapp.campaigns.index') }}" 
                           class="flex flex-col items-center gap-3 p-4 bg-info-light rounded-lg hover:bg-info/10 transition-colors">
                            <div class="flex items-center justify-center size-10 bg-info rounded-lg">
                                <i class="ki-filled ki-setting-4 text-white"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-900">Manage Campaigns</div>
                                <div class="text-xs text-gray-600">View all campaigns</div>
                            </div>
                        </a>

                    </div>
                </x-team.card>

                {{-- Quick Actions Section --}}
                <div class="grid lg:grid-cols-2 gap-2 lg:gap-2">
                    
                    {{-- Send Single Message --}}
                    <x-team.card title="Send WhatsApp Message" headerClass="">
                        <form id="sendMessageForm" class="space-y-4">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" 
                                       id="phone_number" 
                                       name="phone_number" 
                                       class="kt-input" 
                                       placeholder="e.g., +1234567890"
                                       required>
                                <div class="text-xs text-gray-500 mt-1">Include country code (e.g., +91 for India)</div>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="4" 
                                          class="kt-input" 
                                          placeholder="Type your message here..."
                                          maxlength="1000"
                                          required></textarea>
                                <div class="text-xs text-gray-500 mt-1" id="messageCounter">0/1000 characters</div>
                            </div>

                            <div>
                                <label for="provider_slug" class="block text-sm font-medium text-gray-700 mb-1">Provider (Optional)</label>
                                <select id="provider_slug" name="provider_slug" class="kt-select">
                                    <option value="">Auto-select (Recommended)</option>
                                    @foreach($activeProviders as $provider)
                                        <option value="{{ $provider->slug }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-message-check"></i>
                                    Send Message
                                </button>
                                <button type="button" onclick="clearMessageForm()" class="kt-btn kt-btn-light">
                                    <i class="ki-filled ki-cross"></i>
                                    Clear
                                </button>
                            </div>
                        </form>
                    </x-team.card>

                    {{-- Send Template Message --}}
                    <x-team.card title="Send Template Message" headerClass="">
                        <form id="sendTemplateForm" class="space-y-4">
                            <div>
                                <label for="template_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" 
                                       id="template_phone" 
                                       name="phone_number" 
                                       class="kt-input" 
                                       placeholder="e.g., +1234567890"
                                       required>
                            </div>

                            <div>
                                <label for="template_name" class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                                <select id="template_name" name="template_name" class="kt-select" required>
                                    <option value="">Select Template</option>
                                    @foreach($availableTemplates as $template)
                                        <option value="{{ $template }}">{{ $template }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="templateVariables" class="space-y-3" style="display: none;">
                                <div class="text-sm font-medium text-gray-700">Template Variables:</div>
                                <div id="variableInputs"></div>
                            </div>

                            <div>
                                <label for="template_provider" class="block text-sm font-medium text-gray-700 mb-1">Provider (Optional)</label>
                                <select id="template_provider" name="provider_slug" class="kt-select">
                                    <option value="">Auto-select (Recommended)</option>
                                    @foreach($activeProviders as $provider)
                                        <option value="{{ $provider->slug }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-message-check"></i>
                                    Send Template
                                </button>
                                <button type="button" onclick="clearTemplateForm()" class="kt-btn kt-btn-light">
                                    <i class="ki-filled ki-cross"></i>
                                    Clear
                                </button>
                            </div>
                        </form>
                    </x-team.card>

                </div>

                {{-- Recent Messages Section --}}
                @if($recentMessages->count() > 0)
                <x-team.card title="Recent Messages" headerClass="">
                    <x-slot name="headerActions">
                        <a href="{{ route('team.automation.whatsapp.logs') }}" class="kt-btn kt-btn-sm kt-btn-light">
                            <i class="ki-filled ki-eye"></i>
                            View All Logs
                        </a>
                    </x-slot>

                    <div class="overflow-x-auto">
                        <table class="kt-table">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="kt-table-th text-left">Phone Number</th>
                                    <th class="kt-table-th text-left">Message</th>
                                    <th class="kt-table-th text-left">Provider</th>
                                    <th class="kt-table-th text-left">Status</th>
                                    <th class="kt-table-th text-left">Sent At</th>
                                    <th class="kt-table-th text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMessages as $message)
                                <tr class="border-b border-gray-100">
                                    <td class="kt-table-td">
                                        <div class="font-medium">{{ $message->phone_number }}</div>
                                    </td>
                                    <td class="kt-table-td">
                                        <div class="max-w-xs truncate">
                                            @if($message->message_type === 'template')
                                                @php
                                                    $content = json_decode($message->message_content, true);
                                                @endphp
                                                <span class="text-primary">Template:</span> {{ $content['template_name'] ?? 'Unknown' }}
                                            @else
                                                {{ Str::limit($message->message_content, 50) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="kt-table-td">
                                        <span class="text-sm">{{ $message->provider->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="kt-table-td">
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'sent' => 'success',
                                                'delivered' => 'success',
                                                'read' => 'info',
                                                'failed' => 'danger'
                                            ];
                                            $color = $statusColors[$message->status] ?? 'secondary';
                                        @endphp
                                        <span class="kt-badge kt-badge-{{ $color }}">{{ ucfirst($message->status) }}</span>
                                    </td>
                                    <td class="kt-table-td">
                                        @if($message->sent_at)
                                            <div class="text-sm">{{ $message->sent_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $message->sent_at->format('H:i') }}</div>
                                        @else
                                            <span class="text-gray-400">Not sent</span>
                                        @endif
                                    </td>
                                    <td class="kt-table-td text-center">
                                        @if($message->status === 'failed')
                                        <button onclick="retryMessage({{ $message->id }})" 
                                                class="kt-btn kt-btn-sm kt-btn-light">
                                            <i class="ki-filled ki-refresh"></i>
                                            Retry
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-team.card>
                @endif

                {{-- Provider Status Section --}}
                <x-team.card title="Provider Status" headerClass="">
                    <div class="grid lg:grid-cols-3 gap-4">
                        @foreach($activeProviders as $provider)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold">{{ $provider->name }}</h4>
                                    <span class="kt-badge kt-badge-success kt-badge-sm">Active</span>
                                </div>
                                <div class="text-sm text-gray-500">Priority: {{ $provider->priority }}</div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Rate Limit:</span>
                                    <span>{{ $provider->rate_limit_per_minute }}/min</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Endpoint:</span>
                                    <span class="text-gray-500 truncate max-w-32">{{ $provider->api_endpoint }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($activeProviders->isEmpty())
                        <div class="col-span-3 text-center py-8">
                            <div class="text-gray-500 mb-4">No active WhatsApp providers configured</div>
                            <a href="{{ route('team.settings.whatsapp-templates.index') }}" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-setting-2"></i>
                                Configure Providers
                            </a>
                        </div>
                        @endif
                    </div>
                </x-team.card>

            </div>
        </div>
    </x-slot>

    <x-slot name="scripts">
        <script>
            // Message character counter
            document.getElementById('message').addEventListener('input', function() {
                const counter = document.getElementById('messageCounter');
                const length = this.value.length;
                counter.textContent = `${length}/1000 characters`;
                
                if (length > 900) {
                    counter.classList.add('text-warning');
                } else {
                    counter.classList.remove('text-warning');
                }
            });

            // Template selection handler
            document.getElementById('template_name').addEventListener('change', function() {
                const templateName = this.value;
                const variablesContainer = document.getElementById('templateVariables');
                const variableInputs = document.getElementById('variableInputs');

                if (templateName) {
                    // Fetch template variables
                    fetch(`{{ route('team.automation.whatsapp.template-variables', ':template') }}`.replace(':template', templateName))
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.variables.length > 0) {
                                variableInputs.innerHTML = '';
                                data.variables.forEach(variable => {
                                    const div = document.createElement('div');
                                    div.innerHTML = `
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            ${variable.whatsapp_variable} (${variable.description})
                                        </label>
                                        <input type="text" 
                                               name="variables[${variable.whatsapp_variable}]" 
                                               class="kt-input" 
                                               placeholder="Enter value for ${variable.system_variable}">
                                    `;
                                    variableInputs.appendChild(div);
                                });
                                variablesContainer.style.display = 'block';
                            } else {
                                variablesContainer.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching template variables:', error);
                            variablesContainer.style.display = 'none';
                        });
                } else {
                    variablesContainer.style.display = 'none';
                }
            });

            // Send message form handler
            document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="ki-filled ki-loading animate-spin"></i> Sending...';
                submitButton.disabled = true;

                fetch('{{ route('team.automation.whatsapp.send-message') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Message queued successfully!', 'success');
                        clearMessageForm();
                    } else {
                        showNotification(data.message || 'Failed to send message', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while sending the message', 'error');
                })
                .finally(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            });

            // Send template form handler
            document.getElementById('sendTemplateForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="ki-filled ki-loading animate-spin"></i> Sending...';
                submitButton.disabled = true;

                fetch('{{ route('team.automation.whatsapp.send-template') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Template message queued successfully!', 'success');
                        clearTemplateForm();
                    } else {
                        showNotification(data.message || 'Failed to send template', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while sending the template', 'error');
                })
                .finally(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            });

            // Clear forms functions
            function clearMessageForm() {
                document.getElementById('sendMessageForm').reset();
                document.getElementById('messageCounter').textContent = '0/1000 characters';
            }

            function clearTemplateForm() {
                document.getElementById('sendTemplateForm').reset();
                document.getElementById('templateVariables').style.display = 'none';
            }

            // Retry message function
            function retryMessage(messageId) {
                if (!confirm('Are you sure you want to retry sending this message?')) {
                    return;
                }

                fetch(`{{ route('team.automation.whatsapp.logs.retry', ':id') }}`.replace(':id', messageId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Message queued for retry!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Failed to retry message', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while retrying the message', 'error');
                });
            }

            // Notification function (you may need to adjust this based on your notification system)
            function showNotification(message, type) {
                // Replace this with your actual notification system
                if (type === 'success') {
                    alert('✓ ' + message);
                } else {
                    alert('✗ ' + message);
                }
            }
        </script>
    </x-slot>
</x-team.layout.app>
