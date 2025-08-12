<!-- Facebook Settings Modal -->
<x-team.modal 
    id="facebook-settings-modal" 
    size="xl"
    centered="true">
    
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Facebook Settings</h2>
                    <p class="text-sm text-gray-600">Manage your Facebook integration settings</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded-full pulse-dot"></div>
                <span class="text-sm font-medium text-green-700">Connected</span>
            </div>
        </div>
    </x-slot>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button class="settings-tab active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600" data-tab="general">
                General
            </button>
            <button class="settings-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="forms">
                Lead Forms
            </button>
            <button class="settings-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="mapping">
                Field Mapping
            </button>
            <button class="settings-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="webhooks">
                Webhooks
            </button>
            <button class="settings-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="advanced">
                Advanced
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="max-h-96 overflow-y-auto">
        
        <!-- General Tab -->
        <div class="tab-content active" data-tab="general">
            <div class="space-y-6">
                <!-- Account Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Connected Account</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $businessAccount->business_name ?? 'Facebook Business' }}</p>
                                <p class="text-sm text-gray-600">Business Account ID: {{ $businessAccount->business_account_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('facebook.auth.disconnect') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200" 
                                    onclick="return confirm('Are you sure you want to disconnect your Facebook account? This will stop lead synchronization.')">
                                Disconnect
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Basic Settings -->
                <form id="general-settings-form" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-team.forms.input 
                            name="business_name" 
                            label="Business Name" 
                            value="{{ $businessAccount->business_name ?? '' }}"
                            placeholder="Enter business name" />
                            
                        <x-team.forms.input 
                            name="contact_email" 
                            type="email"
                            label="Contact Email" 
                            value="{{ $businessAccount->contact_email ?? '' }}"
                            placeholder="contact@business.com" />
                    </div>

                    <x-team.forms.input 
                        name="webhook_url" 
                        label="Webhook URL" 
                        value="{{ request()->getSchemeAndHttpHost() }}/webhooks/facebook"
                        disabled="true"
                        help="This URL is automatically configured for Facebook webhooks" />

                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Auto-process Leads</h4>
                            <p class="text-sm text-gray-600">Automatically process incoming Facebook leads</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_process" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lead Forms Tab -->
        <div class="tab-content" data-tab="forms">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Connected Lead Forms</h3>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Refresh Forms
                    </button>
                </div>

                <!-- Lead Forms List -->
                <div class="space-y-3">
                    @if(isset($businessAccount))
                        @forelse($businessAccount->leadForms ?? [] as $form)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $form->name }}</h4>
                                        <p class="text-sm text-gray-600">Form ID: {{ $form->form_id }}</p>
                                        <p class="text-sm text-gray-500">Page: {{ $form->page->name ?? 'Unknown' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs font-medium bg-{{ $form->status === 'active' ? 'green' : 'gray' }}-100 text-{{ $form->status === 'active' ? 'green' : 'gray' }}-800 rounded-full">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" {{ $form->status === 'active' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-600">No lead forms found</p>
                                <p class="text-sm text-gray-500">Create lead forms in Facebook Business Manager first</p>
                            </div>
                        @endforelse
                    @endif
                </div>
            </div>
        </div>

        <!-- Field Mapping Tab -->
        <div class="tab-content" data-tab="mapping">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Field Mapping</h3>
                    <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Add Mapping
                    </button>
                </div>

                <!-- Default Mappings -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">Default Field Mappings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-800">Facebook Field</span>
                            <span class="text-blue-800">System Field</span>
                        </div>
                        <div></div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">full_name</span>
                            <span class="text-gray-700">→ name</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">email</span>
                            <span class="text-gray-700">→ email</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">phone_number</span>
                            <span class="text-gray-700">→ phone</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">city</span>
                            <span class="text-gray-700">→ city</span>
                        </div>
                    </div>
                </div>

                <!-- Custom Mappings -->
                <div class="space-y-3">
                    <h4 class="font-medium text-gray-900">Custom Field Mappings</h4>
                    
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <x-team.forms.input 
                                name="facebook_field" 
                                label="Facebook Field" 
                                placeholder="facebook_field_name" />
                                
                            <x-team.forms.input 
                                name="system_field" 
                                label="System Field" 
                                placeholder="system_field_name" />
                                
                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 h-fit">
                                Add Mapping
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Webhooks Tab -->
        <div class="tab-content" data-tab="webhooks">
            <div class="space-y-6">
                <h3 class="font-semibold text-gray-900">Webhook Configuration</h3>

                <!-- Webhook Status -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium text-green-800">Webhooks are active and receiving data</span>
                    </div>
                </div>

                <!-- Webhook Settings -->
                <form class="space-y-6">
                    <x-team.forms.input 
                        name="webhook_url" 
                        label="Webhook URL" 
                        value="{{ request()->getSchemeAndHttpHost() }}/webhooks/facebook"
                        disabled="true" />

                    <x-team.forms.input 
                        name="verify_token" 
                        label="Verify Token" 
                        value="facebook_webhook_verify_token_{{ $businessAccount->id ?? 'default' }}"
                        disabled="true"
                        help="This token is used to verify webhook requests from Facebook" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Lead Updates</h4>
                                <p class="text-sm text-gray-600">Receive new lead notifications</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Test Webhooks</h4>
                                <p class="text-sm text-gray-600">Receive test webhook events</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </form>

                <!-- Test Webhook -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Test Webhook Connection</h4>
                    <p class="text-sm text-gray-600 mb-4">Send a test webhook to verify your connection is working properly.</p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Send Test Webhook
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Tab -->
        <div class="tab-content" data-tab="advanced">
            <div class="space-y-6">
                <h3 class="font-semibold text-gray-900">Advanced Settings</h3>

                <!-- API Configuration -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900">API Configuration</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-team.forms.input 
                            name="api_version" 
                            label="API Version" 
                            value="v18.0"
                            disabled="true" />
                            
                        <x-team.forms.input 
                            name="rate_limit" 
                            label="Rate Limit (requests/hour)" 
                            value="200"
                            type="number" />
                    </div>
                </div>

                <!-- Data Retention -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900">Data Retention</h4>
                    
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h5 class="font-medium text-gray-900">Auto-cleanup Old Leads</h5>
                            <p class="text-sm text-gray-600">Automatically delete leads older than specified days</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <x-team.forms.input 
                        name="retention_days" 
                        label="Retention Period (days)" 
                        value="365"
                        type="number"
                        help="Leads older than this will be automatically deleted" />
                </div>

                <!-- Danger Zone -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-900 mb-3">Danger Zone</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-red-800">Reset Integration</p>
                                <p class="text-sm text-red-600">Clear all Facebook data and start fresh</p>
                            </div>
                            <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Reset
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-red-800">Delete Integration</p>
                                <p class="text-sm text-red-600">Permanently delete all Facebook integration data</p>
                            </div>
                            <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex items-center justify-between w-full">
            <button data-kt-modal-dismiss="#facebook-settings-modal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                Close
            </button>
            
            <button id="save-settings-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                Save Changes
            </button>
        </div>
    </x-slot>
</x-team.modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabs = document.querySelectorAll('.settings-tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Update tab styles
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // Update content visibility
            contents.forEach(content => {
                if (content.dataset.tab === targetTab) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        });
    });

    // Save settings functionality
    document.getElementById('save-settings-btn').addEventListener('click', function() {
        // Get active tab
        const activeTab = document.querySelector('.settings-tab.active').dataset.tab;
        
        // Show loading state
        this.innerHTML = `
            <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        this.disabled = true;

        // Simulate save operation
        setTimeout(() => {
            this.innerHTML = 'Save Changes';
            this.disabled = false;
            
            // Show success message
            alert('Settings saved successfully!');
        }, 1500);
    });
});
</script>

<style>
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

.pulse-dot {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
</style>
