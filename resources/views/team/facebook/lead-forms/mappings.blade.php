@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Lead Forms', 'url' => route('facebook.lead-forms')],
        ['title' => $leadForm->form_name, 'url' => route('facebook.lead-forms.show', $leadForm)],
        ['title' => 'Parameter Mappings']
    ];
@endphp

<x-team.layout.app title="Parameter Mappings - {{ $leadForm->form_name }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Lead Form
                    </a>
                    <div class="flex flex-col">
                        <h1 class="text-2xl font-semibold text-gray-900">Parameter Mappings</h1>
                        <p class="text-gray-600">Map Facebook lead form fields to your system fields</p>
                    </div>
                </div>
            </div>

            <!-- Lead Form Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ $leadForm->form_name }}</h3>
                        <div class="flex items-center gap-4 text-sm text-gray-600 mt-1">
                            <span>Page: {{ $leadForm->facebookPage->page_name }}</span>
                            <span>•</span>
                            <span>Total Mappings: {{ $leadForm->facebookParameterMappings->count() }}</span>
                            <span>•</span>
                            <span>Active: {{ $leadForm->facebookParameterMappings->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($leadForm->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mapping Configuration -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Facebook Field Mappings</h3>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" data-modal-toggle="#add_mapping_modal">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Mapping
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    @if($leadForm->facebookParameterMappings->count() > 0)
                        <form action="{{ route('facebook.lead-forms.mappings.save', $leadForm) }}" method="POST" id="mappingsForm">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-48">Facebook Field</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-32">Field Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-48">System Field</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-24">Required</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-24">Active</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-20">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mappingsTableBody" class="bg-white divide-y divide-gray-200">
                                        @foreach($leadForm->facebookParameterMappings as $index => $mapping)
                                            <tr data-mapping-id="{{ $mapping->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="hidden" name="mappings[{{ $index }}][id]" value="{{ $mapping->id }}">
                                                    <input type="text" name="mappings[{{ $index }}][facebook_field_name]" 
                                                           value="{{ $mapping->facebook_field_name }}" 
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                           placeholder="e.g., full_name, email, phone_number" required>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <select name="mappings[{{ $index }}][facebook_field_type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                                        <option value="text" {{ $mapping->facebook_field_type === 'text' ? 'selected' : '' }}>Text</option>
                                                        <option value="email" {{ $mapping->facebook_field_type === 'email' ? 'selected' : '' }}>Email</option>
                                                        <option value="phone" {{ $mapping->facebook_field_type === 'phone' ? 'selected' : '' }}>Phone</option>
                                                        <option value="select" {{ $mapping->facebook_field_type === 'select' ? 'selected' : '' }}>Select</option>
                                                        <option value="textarea" {{ $mapping->facebook_field_type === 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                        <option value="date" {{ $mapping->facebook_field_type === 'date' ? 'selected' : '' }}>Date</option>
                                                        <option value="number" {{ $mapping->facebook_field_type === 'number' ? 'selected' : '' }}>Number</option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <select name="mappings[{{ $index }}][system_field_name]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 system-field-select" required>
                                                        <option value="">Select System Field...</option>
                                                        @foreach($systemVariables as $category => $variables)
                                                            <optgroup label="{{ $category }}">
                                                                @foreach($variables as $varKey => $varDescription)
                                                                    <option value="{{ $varKey }}" 
                                                                            title="{{ $varDescription }}"
                                                                            {{ $mapping->system_field_name === $varKey ? 'selected' : '' }}>
                                                                        {{ $varKey }} - {{ $varDescription }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="mappings[{{ $index }}][is_required]" 
                                                       value="1" {{ $mapping->is_required ? 'checked' : '' }}
                                                       class="checkbox checkbox-sm">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="mappings[{{ $index }}][is_active]" 
                                                       value="1" {{ $mapping->is_active ? 'checked' : '' }}
                                                       class="checkbox checkbox-sm">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-mapping">
                                                    <i class="ki-filled ki-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex items-center justify-between mt-6">
                            <div class="flex items-center gap-4">
                                <button type="button" class="btn btn-secondary" id="addMappingRow">
                                    <i class="ki-filled ki-plus"></i>
                                    Add Row
                                </button>
                                <button type="button" class="btn btn-info" id="previewMappings">
                                    <i class="ki-filled ki-eye"></i>
                                    Preview Mapping
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-filled ki-check"></i>
                                    Save Mappings
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ki-filled ki-setting-2 text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Parameter Mappings</h3>
                        <p class="text-gray-600 mb-4">Start by adding your first Facebook field to system field mapping.</p>
                        <button type="button" class="btn btn-primary" data-modal-toggle="#add_mapping_modal">
                            <i class="ki-filled ki-plus"></i>
                            Add First Mapping
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- System Variables Reference Card -->
        <div class="card mt-5 lg:mt-7.5">
            <div class="card-header">
                <h3 class="card-title">System Variables Reference</h3>
                <button type="button" class="btn btn-sm btn-light" data-modal-toggle="#variables_reference_modal">
                    <i class="ki-filled ki-information-2"></i>
                    View All Variables
                </button>
            </div>
            <div class="card-body">
                <div class="grid lg:grid-cols-3 gap-4">
                    @foreach($systemVariables as $category => $variables)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-sm text-gray-900 mb-3">{{ $category }}</h4>
                            <div class="space-y-1">
                                @foreach(array_slice($variables, 0, 5) as $varKey => $varDescription)
                                    <div class="text-xs">
                                        <code class="text-purple-600">{{ $varKey }}</code>
                                        <span class="text-gray-600 ml-1">{{ Str::limit($varDescription, 30) }}</span>
                                    </div>
                                @endforeach
                                @if(count($variables) > 5)
                                    <div class="text-xs text-gray-500">+{{ count($variables) - 5 }} more...</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Mapping Modal -->
<div class="modal" data-modal="true" id="add_mapping_modal">
    <div class="modal-content max-w-2xl">
        <div class="modal-header">
            <h3 class="modal-title">Add New Parameter Mapping</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addMappingForm">
                <div class="grid lg:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Facebook Field Name</label>
                        <input type="text" name="facebook_field_name" class="input" 
                               placeholder="e.g., full_name, email, phone_number" required>
                        <div class="text-2sm text-gray-600 mt-1">
                            Common fields: full_name, first_name, last_name, email, phone_number, city, country
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Facebook Field Type</label>
                        <select name="facebook_field_type" class="select" required>
                            <option value="">Select type...</option>
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="select">Select</option>
                            <option value="textarea">Textarea</option>
                            <option value="date">Date</option>
                            <option value="number">Number</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">System Field</label>
                    <select name="system_field_name" class="select" required>
                        <option value="">Select system field...</option>
                        @foreach($systemVariables as $category => $variables)
                            <optgroup label="{{ $category }}">
                                @foreach($variables as $varKey => $varDescription)
                                    <option value="{{ $varKey }}" title="{{ $varDescription }}">
                                        {{ $varKey }} - {{ $varDescription }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-6 mb-4">
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_required" value="1" class="checkbox">
                        <span class="checkbox-label">Required Field</span>
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" name="is_active" value="1" class="checkbox" checked>
                        <span class="checkbox-label">Active</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="flex items-center gap-3">
                <button class="btn btn-secondary" data-modal-dismiss="true">Cancel</button>
                <button type="button" class="btn btn-primary" id="addMappingBtn">
                    <i class="ki-filled ki-plus"></i>
                    Add Mapping
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Variables Reference Modal -->
<div class="modal" data-modal="true" id="variables_reference_modal">
    <div class="modal-content max-w-4xl">
        <div class="modal-header">
            <h3 class="modal-title">System Variables Reference</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <input type="text" id="variableSearch" class="input" placeholder="Search variables...">
            </div>
            <div class="grid lg:grid-cols-2 gap-6">
                @foreach($systemVariables as $category => $variables)
                    <div class="variable-category">
                        <h4 class="font-semibold text-gray-900 mb-3">{{ $category }}</h4>
                        <div class="space-y-2">
                            @foreach($variables as $varKey => $varDescription)
                                <div class="variable-item flex items-start gap-3 p-2 rounded hover:bg-gray-50">
                                    <code class="text-purple-600 text-sm font-mono min-w-0 flex-shrink-0">{{ $varKey }}</code>
                                    <span class="text-gray-600 text-sm">{{ $varDescription }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-dismiss="true">Close</button>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal" data-modal="true" id="preview_modal">
    <div class="modal-content max-w-4xl">
        <div class="modal-header">
            <h3 class="modal-title">Mapping Preview</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-dismiss="true">Close</button>
        </div>
        </div>
    </div>
    </x-slot>
</x-team.layout.app>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let mappingIndex = {{ $leadForm->facebookParameterMappings->count() }};
    
    // Add new mapping row
    document.getElementById('addMappingRow').addEventListener('click', function() {
        addMappingRow();
    });
    
    // Add mapping from modal
    document.getElementById('addMappingBtn').addEventListener('click', function() {
        const form = document.getElementById('addMappingForm');
        const formData = new FormData(form);
        
        const rowData = {
            facebook_field_name: formData.get('facebook_field_name'),
            facebook_field_type: formData.get('facebook_field_type'),
            system_field_name: formData.get('system_field_name'),
            is_required: formData.get('is_required') ? '1' : '0',
            is_active: formData.get('is_active') ? '1' : '0'
        };
        
        if (rowData.facebook_field_name && rowData.facebook_field_type && rowData.system_field_name) {
            addMappingRow(rowData);
            form.reset();
            // Close modal
            document.querySelector('[data-modal-dismiss="true"]').click();
        }
    });
    
    // Remove mapping row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-mapping')) {
            e.target.closest('tr').remove();
        }
    });
    
    // Preview mappings
    document.getElementById('previewMappings').addEventListener('click', function() {
        showMappingPreview();
    });
    
    // Variable search
    document.getElementById('variableSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.variable-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    });
    
    function addMappingRow(data = {}) {
        const tbody = document.getElementById('mappingsTableBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" name="mappings[${mappingIndex}][facebook_field_name]" 
                       value="${data.facebook_field_name || ''}" 
                       class="input input-sm" 
                       placeholder="e.g., full_name, email, phone_number" required>
            </td>
            <td>
                <select name="mappings[${mappingIndex}][facebook_field_type]" class="select select-sm" required>
                    <option value="">Select type...</option>
                    <option value="text" ${data.facebook_field_type === 'text' ? 'selected' : ''}>Text</option>
                    <option value="email" ${data.facebook_field_type === 'email' ? 'selected' : ''}>Email</option>
                    <option value="phone" ${data.facebook_field_type === 'phone' ? 'selected' : ''}>Phone</option>
                    <option value="select" ${data.facebook_field_type === 'select' ? 'selected' : ''}>Select</option>
                    <option value="textarea" ${data.facebook_field_type === 'textarea' ? 'selected' : ''}>Textarea</option>
                    <option value="date" ${data.facebook_field_type === 'date' ? 'selected' : ''}>Date</option>
                    <option value="number" ${data.facebook_field_type === 'number' ? 'selected' : ''}>Number</option>
                </select>
            </td>
            <td>
                <select name="mappings[${mappingIndex}][system_field_name]" class="select select-sm system-field-select" required>
                    <option value="">Select System Field...</option>
                    @foreach($systemVariables as $category => $variables)
                        <optgroup label="{{ $category }}">
                            @foreach($variables as $varKey => $varDescription)
                                <option value="{{ $varKey }}" title="{{ $varDescription }}"
                                        ${data.system_field_name === '{{ $varKey }}' ? 'selected' : ''}>
                                    {{ $varKey }} - {{ $varDescription }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </td>
            <td class="text-center">
                <input type="checkbox" name="mappings[${mappingIndex}][is_required]" 
                       value="1" ${data.is_required === '1' ? 'checked' : ''}
                       class="checkbox checkbox-sm">
            </td>
            <td class="text-center">
                <input type="checkbox" name="mappings[${mappingIndex}][is_active]" 
                       value="1" ${data.is_active !== '0' ? 'checked' : ''}
                       class="checkbox checkbox-sm">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-mapping">
                    <i class="ki-filled ki-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        mappingIndex++;
    }
    
    function showMappingPreview() {
        const mappings = [];
        document.querySelectorAll('#mappingsTableBody tr').forEach(row => {
            const facebookField = row.querySelector('[name*="[facebook_field_name]"]').value;
            const systemField = row.querySelector('[name*="[system_field_name]"]').value;
            const fieldType = row.querySelector('[name*="[facebook_field_type]"]').value;
            const isRequired = row.querySelector('[name*="[is_required]"]').checked;
            const isActive = row.querySelector('[name*="[is_active]"]').checked;
            
            if (facebookField && systemField) {
                mappings.push({
                    facebook_field: facebookField,
                    system_field: systemField,
                    field_type: fieldType,
                    is_required: isRequired,
                    is_active: isActive
                });
            }
        });
        
        let previewHtml = `
            <div class="space-y-4">
                <h4 class="font-semibold">Current Mappings (${mappings.length} total)</h4>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Facebook Field</th>
                                <th>Maps To</th>
                                <th>System Field</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        mappings.forEach(mapping => {
            previewHtml += `
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <code class="text-blue-600">${mapping.facebook_field}</code>
                            <span class="badge badge-sm badge-secondary">${mapping.field_type}</span>
                        </div>
                    </td>
                    <td><i class="ki-filled ki-arrow-right text-gray-400"></i></td>
                    <td><code class="text-purple-600">${mapping.system_field}</code></td>
                    <td>
                        <div class="flex items-center gap-1">
                            ${mapping.is_active ? '<span class="badge badge-sm badge-success">Active</span>' : '<span class="badge badge-sm badge-secondary">Inactive</span>'}
                            ${mapping.is_required ? '<span class="badge badge-sm badge-warning">Required</span>' : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
        
        previewHtml += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        document.getElementById('previewContent').innerHTML = previewHtml;
        document.querySelector('[data-modal="true"]#preview_modal').classList.add('open');
    }
});
</script>
@endpush
