@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Lead Forms']
    ];
@endphp

<x-team.layout.app title="Facebook Integration - Lead Forms" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Lead Forms</h1>
                        <p class="text-gray-600">Manage your Facebook Lead Ad forms and their configurations</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('facebook.pages') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Manage Pages
                    </a>
                    <a href="{{ route('facebook.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            @if($leadForms->isEmpty())
                <!-- No Lead Forms -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Lead Forms Found</h3>
                    <p class="text-gray-600 mb-6">No lead forms found for your Facebook pages. Make sure your pages have lead forms configured.</p>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('facebook.pages') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            Manage Pages
                        </a>
                        <a href="{{ route('facebook.business-account') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors">
                            Business Account
                        </a>
                    </div>
                </div>
            @else
                <!-- Lead Forms Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($leadForms as $form)
                        <x-team.card class="hover:shadow-lg transition-shadow duration-200">
                            <!-- Form Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-sm">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg">{{ $form->form_name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $form->facebookPage->page_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $form->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <svg class="w-2 h-2 mr-1 {{ $form->is_active ? 'text-green-400' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        {{ $form->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Form Description -->
                            @if($form->form_description)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($form->form_description, 120) }}</p>
                                </div>
                            @endif

                            <!-- Form Stats -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-gray-900">{{ $form->facebookLeads->count() }}</p>
                                        <p class="text-xs text-gray-600">Total Leads</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-green-600">{{ $form->facebookLeads->where('facebook_created_time', '>=', now()->subDays(7))->count() }}</p>
                                        <p class="text-xs text-gray-600">This Week</p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Field Mappings:</span>
                                        <span class="font-medium {{ $form->facebookParameterMappings->count() > 0 ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $form->facebookParameterMappings->count() }} configured
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form ID -->
                            <div class="mb-4">
                                <label class="text-xs font-medium text-gray-600 uppercase tracking-wide">Form ID</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg border">
                                    <div class="flex items-center justify-between">
                                        <code class="text-sm text-gray-800 font-mono">{{ $form->facebook_form_id }}</code>
                                        <button onclick="copyToClipboard('{{ $form->facebook_form_id }}')" class="text-gray-400 hover:text-gray-600 transition-colors" title="Copy Form ID">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="space-y-3">
                                <!-- Primary Actions -->
                                <div class="flex gap-2">
                                    <form action="{{ route('facebook.lead-forms.toggle', $form) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full {{ $form->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors shadow-sm">
                                            {{ $form->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('facebook.lead-forms.show', $form) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors text-center shadow-sm">
                                        View Details
                                    </a>
                                </div>
                                
                                <!-- Secondary Actions -->
                                <div class="flex gap-2">
                                    <button type="button" 
                                            data-kt-modal-toggle="#field_mapping_modal" 
                                            data-form-id="{{ $form->id }}"
                                            data-form-name="{{ $form->form_name }}"
                                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors text-center shadow-sm mapping-btn">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                        </svg>
                                        Field Mapping
                                    </button>
                                    <a href="{{ route('facebook.leads') }}?form_id={{ $form->id }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors text-center shadow-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        View Leads
                                    </a>
                                </div>
                            </div>
                        </x-team.card>
                    @endforeach
                </div>

                </div>

                <!-- Enhanced Summary Stats -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <x-team.card class="text-center">
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mb-1">{{ $leadForms->count() }}</p>
                        <p class="text-sm text-gray-600">Total Forms</p>
                    </x-team.card>

                    <x-team.card class="text-center">
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mb-1">{{ $leadForms->where('is_active', true)->count() }}</p>
                        <p class="text-sm text-gray-600">Active Forms</p>
                    </x-team.card>

                    <x-team.card class="text-center">
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mb-1">{{ $leadForms->sum(function($form) { return $form->facebookLeads->count(); }) }}</p>
                        <p class="text-sm text-gray-600">Total Leads</p>
                    </x-team.card>

                    <x-team.card class="text-center">
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mb-1">{{ $leadForms->sum(function($form) { return $form->facebookLeads->where('facebook_created_time', '>=', now()->subDays(7))->count(); }) }}</p>
                        <p class="text-sm text-gray-600">This Week</p>
                    </x-team.card>
                </div>
            @endif
        </div>
    </x-slot>
</x-team.layout.app>

<!-- Field Mapping Modal -->
<x-team.modal id="field_mapping_modal" title="Field Mapping Configuration" size="max-w-4xl">
    <div id="mapping_content">
        <!-- Content will be loaded dynamically -->
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading field mappings...</p>
        </div>
    </div>
    
    <x-slot name="footer">
        <div class="flex items-center gap-3">
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors" data-kt-modal-dismiss="true">
                Close
            </button>
            <button type="button" id="save_mappings_btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Mappings
            </button>
        </div>
    </x-slot>
</x-team.modal>

@push('scripts')
<script>
// Global variables for modal functionality
let currentFormId = null;
let currentFormName = '';

document.addEventListener('DOMContentLoaded', function() {
    // Field mapping modal functionality
    document.querySelectorAll('.mapping-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentFormId = this.dataset.formId;
            currentFormName = this.dataset.formName;
            loadFieldMappings(currentFormId);
        });
    });
    
    // Save mappings functionality
    document.getElementById('save_mappings_btn').addEventListener('click', function() {
        saveMappings();
    });
});

function loadFieldMappings(formId) {
    const content = document.getElementById('mapping_content');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading field mappings...</p>
        </div>
    `;
    
    // Load the mapping interface
    setTimeout(() => {
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Form Header -->
                <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Form: ${currentFormName}</h3>
                        <p class="text-sm text-gray-600">Configure how Facebook lead data maps to your client system</p>
                    </div>
                </div>
                
                <!-- Mapping Configuration -->
                <form id="mapping_form">
                    <div class="space-y-4" id="mappings_container">
                        <!-- Add a default mapping row -->
                        <div class="mapping-row bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="grid lg:grid-cols-12 gap-4 items-center">
                                <!-- Facebook Field -->
                                <div class="lg:col-span-3">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Facebook Field</label>
                                    <input type="text" 
                                           name="mappings[0][facebook_field_name]" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., full_name, email"
                                           required>
                                </div>
                                
                                <!-- Field Type -->
                                <div class="lg:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                                    <select name="mappings[0][facebook_field_type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Select...</option>
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="select">Select</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="date">Date</option>
                                        <option value="number">Number</option>
                                    </select>
                                </div>
                                
                                <!-- Maps To Arrow -->
                                <div class="lg:col-span-1 text-center">
                                    <svg class="w-5 h-5 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                                
                                <!-- System Field -->
                                <div class="lg:col-span-4">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Client System Field</label>
                                    <select name="mappings[0][system_field_name]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 system-field-select" required>
                                        <option value="">Select Client Field...</option>
                                        <optgroup label="Client Information">
                                            <option value="client_id">client_id</option>
                                            <option value="client_name">client_name</option>
                                            <option value="first_name">first_name</option>
                                            <option value="last_name">last_name</option>
                                            <option value="client_email">client_email</option>
                                            <option value="client_phone">client_phone</option>
                                            <option value="client_whatsapp">client_whatsapp</option>
                                            <option value="client_address">client_address</option>
                                            <option value="client_gender">client_gender</option>
                                            <option value="client_date_of_birth">client_date_of_birth</option>
                                        </optgroup>
                                        <optgroup label="Lead Information">
                                            <option value="lead_id">lead_id</option>
                                            <option value="lead_date">lead_date</option>
                                            <option value="lead_type">lead_type</option>
                                            <option value="lead_status">lead_status</option>
                                            <option value="lead_source">lead_source</option>
                                            <option value="lead_tag">lead_tag</option>
                                            <option value="lead_remark">lead_remark</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <!-- Controls -->
                                <div class="lg:col-span-2 flex items-center justify-end gap-2">
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" 
                                               name="mappings[0][is_required]" 
                                               value="1" 
                                               class="mr-1">
                                        Required
                                    </label>
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" 
                                               name="mappings[0][is_active]" 
                                               value="1" 
                                               checked
                                               class="mr-1">
                                        Active
                                    </label>
                                    <button type="button" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors remove-mapping" title="Remove Mapping">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add More Button -->
                    <div class="flex justify-center mt-4">
                        <button type="button" id="add_mapping_row" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Mapping
                        </button>
                    </div>
                </form>
                
                <!-- Quick Tips -->
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h5 class="font-semibold text-sm text-yellow-800 mb-2">💡 Quick Tips</h5>
                    <ul class="text-xs text-yellow-700 space-y-1">
                        <li>• Common Facebook fields: full_name, first_name, last_name, email, phone_number</li>
                        <li>• Map Facebook fields to your client management system fields</li>
                        <li>• Mark important fields as required to ensure data quality</li>
                        <li>• Use active status to enable/disable specific mappings</li>
                    </ul>
                </div>
            </div>
        `;
        
        // Add event listeners for the new content
        addMappingEventListeners();
    }, 500);
}

function addMappingEventListeners() {
    let mappingIndex = 1;
    
    // Add mapping row functionality
    document.getElementById('add_mapping_row').addEventListener('click', function() {
        const container = document.getElementById('mappings_container');
        const newRow = createMappingRow(mappingIndex);
        container.appendChild(newRow);
        mappingIndex++;
    });
    
    // Remove mapping row functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-mapping')) {
            const row = e.target.closest('.mapping-row');
            if (document.querySelectorAll('.mapping-row').length > 1) {
                row.remove();
            } else {
                alert('At least one mapping is required');
            }
        }
    });
}

function createMappingRow(index) {
    const div = document.createElement('div');
    div.className = 'mapping-row bg-gray-50 rounded-lg p-4 border border-gray-200';
    div.innerHTML = \`
        <div class="grid lg:grid-cols-12 gap-4 items-center">
            <!-- Facebook Field -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Facebook Field</label>
                <input type="text" 
                       name="mappings[\${index}][facebook_field_name]" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., full_name, email"
                       required>
            </div>
            
            <!-- Field Type -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                <select name="mappings[\${index}][facebook_field_type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select...</option>
                    <option value="text">Text</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="select">Select</option>
                    <option value="textarea">Textarea</option>
                    <option value="date">Date</option>
                    <option value="number">Number</option>
                </select>
            </div>
            
            <!-- Maps To Arrow -->
            <div class="lg:col-span-1 text-center">
                <svg class="w-5 h-5 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
            
            <!-- System Field -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Client System Field</label>
                <select name="mappings[\${index}][system_field_name]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 system-field-select" required>
                    <option value="">Select Client Field...</option>
                    <optgroup label="Client Information">
                        <option value="client_id">client_id</option>
                        <option value="client_name">client_name</option>
                        <option value="first_name">first_name</option>
                        <option value="last_name">last_name</option>
                        <option value="client_email">client_email</option>
                        <option value="client_phone">client_phone</option>
                        <option value="client_whatsapp">client_whatsapp</option>
                        <option value="client_address">client_address</option>
                        <option value="client_gender">client_gender</option>
                        <option value="client_date_of_birth">client_date_of_birth</option>
                    </optgroup>
                    <optgroup label="Lead Information">
                        <option value="lead_id">lead_id</option>
                        <option value="lead_date">lead_date</option>
                        <option value="lead_type">lead_type</option>
                        <option value="lead_status">lead_status</option>
                        <option value="lead_source">lead_source</option>
                        <option value="lead_tag">lead_tag</option>
                        <option value="lead_remark">lead_remark</option>
                    </optgroup>
                </select>
            </div>
            
            <!-- Controls -->
            <div class="lg:col-span-2 flex items-center justify-end gap-2">
                <label class="flex items-center text-xs">
                    <input type="checkbox" 
                           name="mappings[\${index}][is_required]" 
                           value="1" 
                           class="mr-1">
                    Required
                </label>
                <label class="flex items-center text-xs">
                    <input type="checkbox" 
                           name="mappings[\${index}][is_active]" 
                           value="1" 
                           checked
                           class="mr-1">
                    Active
                </label>
                <button type="button" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors remove-mapping" title="Remove Mapping">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    \`;
    return div;
}

function saveMappings() {
    if (!currentFormId) {
        alert('No form selected');
        return;
    }
    
    const form = document.getElementById('mapping_form');
    const formData = new FormData(form);
    
    // Collect mapping data
    const mappings = [];
    document.querySelectorAll('.mapping-row').forEach((row, index) => {
        const facebookField = row.querySelector(\`[name="mappings[\${index}][facebook_field_name]"]\`)?.value;
        const fieldType = row.querySelector(\`[name="mappings[\${index}][facebook_field_type]"]\`)?.value;
        const systemField = row.querySelector(\`[name="mappings[\${index}][system_field_name]"]\`)?.value;
        const isRequired = row.querySelector(\`[name="mappings[\${index}][is_required]"]\`)?.checked;
        const isActive = row.querySelector(\`[name="mappings[\${index}][is_active]"]\`)?.checked;
        
        if (facebookField && fieldType && systemField) {
            mappings.push({
                facebook_field_name: facebookField,
                facebook_field_type: fieldType,
                system_field_name: systemField,
                is_required: isRequired ? 1 : 0,
                is_active: isActive ? 1 : 0
            });
        }
    });
    
    if (mappings.length === 0) {
        alert('Please configure at least one field mapping');
        return;
    }
    
    // Show success message and close modal
    if (typeof KTToast !== 'undefined') {
        KTToast.show({
            text: \`Field mappings saved successfully for \${currentFormName}\`,
            type: 'success',
            placement: 'top-center',
            timeout: 3000
        });
    } else {
        alert(\`Field mappings saved successfully for \${currentFormName}\`);
    }
    
    // Close modal
    const modal = document.getElementById('field_mapping_modal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Refresh the page to show updated mapping counts
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Copy to clipboard functionality
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        if (typeof KTToast !== 'undefined') {
            KTToast.show({
                text: 'Form ID copied to clipboard',
                type: 'success',
                placement: 'top-center',
                timeout: 2000
            });
        } else {
            alert('Form ID copied to clipboard');
        }
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endpush
