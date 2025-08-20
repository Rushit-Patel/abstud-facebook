@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Lead Forms', 'url' => route('facebook.lead-forms')],
        ['title' => $leadForm->form_name, 'url' => route('facebook.lead-forms.show', $leadForm)],
        ['title' => 'Field Mappings']
    ];
@endphp

<x-team.layout.app title="Field Mappings - {{ $leadForm->form_name }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="container-fixed">
            <!-- Header Section -->
            <div class="card mb-5 lg:mb-7.5 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="ki-filled ki-facebook text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h1 class="text-2xl font-semibold text-gray-900">{{ $leadForm->form_name }}</h1>
                                    <div class="w-2 h-2 rounded-full {{ $leadForm->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                </div>
                                <p class="text-gray-600">Map Facebook lead fields to your client management system</p>
                                <div class="flex items-center gap-4 text-sm text-gray-500 mt-2">
                                    <span>Page: {{ $leadForm->facebookPage->page_name }}</span>
                                    <span>•</span>
                                    <span>{{ $leadForm->facebookParameterMappings->count() }} Active Mappings</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="btn btn-light btn-sm">
                                <i class="ki-filled ki-arrow-left"></i>
                                Back to Form
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5">
                <!-- Main Mapping Configuration -->
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title flex items-center gap-2">
                                <i class="ki-filled ki-setting-2 text-blue-600"></i>
                                Field Mapping Configuration
                            </h3>
                            <div class="flex items-center gap-2">
                                <button type="button" class="btn btn-sm btn-light" id="addMappingRow">
                                    <i class="ki-filled ki-plus"></i>
                                    Add Mapping
                                </button>
                                <button type="button" class="btn btn-sm btn-info" id="previewMappings">
                                    <i class="ki-filled ki-eye"></i>
                                    Preview
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($leadForm->facebookParameterMappings->count() > 0)
                                <form method="POST" action="{{ route('facebook.lead-forms.mappings.save', $leadForm) }}">
                                    @csrf
                                    <div class="space-y-4">
                                        @foreach($leadForm->facebookParameterMappings as $index => $mapping)
                                            <div class="bg-gray-50 rounded-lg p-4 mapping-row">
                                                <div class="grid lg:grid-cols-12 gap-4 items-center">
                                                    <!-- Facebook Field -->
                                                    <div class="lg:col-span-3">
                                                        <label class="form-label text-xs font-semibold text-gray-600 mb-1">Facebook Field</label>
                                                        <input type="text" 
                                                               name="mappings[{{ $index }}][facebook_field_name]" 
                                                               value="{{ $mapping->facebook_field_name }}"
                                                               class="input input-sm"
                                                               placeholder="e.g., full_name, email"
                                                               required>
                                                    </div>
                                                    
                                                    <!-- Field Type -->
                                                    <div class="lg:col-span-2">
                                                        <label class="form-label text-xs font-semibold text-gray-600 mb-1">Type</label>
                                                        <select name="mappings[{{ $index }}][facebook_field_type]" class="select select-sm" required>
                                                            <option value="text" {{ $mapping->facebook_field_type === 'text' ? 'selected' : '' }}>Text</option>
                                                            <option value="email" {{ $mapping->facebook_field_type === 'email' ? 'selected' : '' }}>Email</option>
                                                            <option value="phone" {{ $mapping->facebook_field_type === 'phone' ? 'selected' : '' }}>Phone</option>
                                                            <option value="select" {{ $mapping->facebook_field_type === 'select' ? 'selected' : '' }}>Select</option>
                                                            <option value="textarea" {{ $mapping->facebook_field_type === 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                            <option value="date" {{ $mapping->facebook_field_type === 'date' ? 'selected' : '' }}>Date</option>
                                                            <option value="number" {{ $mapping->facebook_field_type === 'number' ? 'selected' : '' }}>Number</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Maps To Arrow -->
                                                    <div class="lg:col-span-1 text-center">
                                                        <i class="ki-filled ki-arrow-right text-blue-500 text-lg"></i>
                                                    </div>
                                                    
                                                    <!-- System Field -->
                                                    <div class="lg:col-span-4">
                                                        <label class="form-label text-xs font-semibold text-gray-600 mb-1">Client System Field</label>
                                                        <select name="mappings[{{ $index }}][system_field_name]" class="select select-sm system-field-select" required>
                                                            <option value="">Select Client Field...</option>
                                                            @foreach($systemVariables as $category => $variables)
                                                                <optgroup label="{{ $category }}">
                                                                    @foreach($variables as $varKey => $varDescription)
                                                                        <option value="{{ $varKey }}" 
                                                                                title="{{ $varDescription }}"
                                                                                {{ $mapping->system_field_name === $varKey ? 'selected' : '' }}>
                                                                            {{ $varKey }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Controls -->
                                                    <div class="lg:col-span-2 flex items-center justify-end gap-2">
                                                        <label class="checkbox-group">
                                                            <input type="checkbox" 
                                                                   name="mappings[{{ $index }}][is_required]" 
                                                                   value="1" 
                                                                   {{ $mapping->is_required ? 'checked' : '' }}
                                                                   class="checkbox checkbox-sm">
                                                            <span class="checkbox-label text-xs">Required</span>
                                                        </label>
                                                        <label class="checkbox-group">
                                                            <input type="checkbox" 
                                                                   name="mappings[{{ $index }}][is_active]" 
                                                                   value="1" 
                                                                   {{ $mapping->is_active ? 'checked' : '' }}
                                                                   class="checkbox checkbox-sm">
                                                            <span class="checkbox-label text-xs">Active</span>
                                                        </label>
                                                        <button type="button" class="btn btn-sm btn-icon btn-light remove-mapping" title="Remove Mapping">
                                                            <i class="ki-filled ki-trash text-red-500"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                                        <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ki-filled ki-check"></i>
                                            Save Mappings
                                        </button>
                                    </div>
                                </form>
                            @else
                                <!-- Empty State -->
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="ki-filled ki-setting-2 text-3xl text-blue-500"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Field Mappings Yet</h3>
                                    <p class="text-gray-600 mb-4 max-w-md mx-auto">Start mapping Facebook lead form fields to your client management system to automatically capture and organize lead data.</p>
                                    <button type="button" class="btn btn-primary" id="addFirstMapping">
                                        <i class="ki-filled ki-plus"></i>
                                        Add First Mapping
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Client Fields Reference -->
                <div class="lg:col-span-1">
                    <div class="card sticky top-4">
                        <div class="card-header">
                            <h3 class="card-title flex items-center gap-2">
                                <i class="ki-filled ki-profile-user text-green-600"></i>
                                Available Client Fields
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="space-y-4">
                                @foreach($systemVariables as $category => $variables)
                                    <div>
                                        <h4 class="font-semibold text-sm text-gray-900 mb-3 flex items-center gap-2">
                                            @if($category === 'Client Information')
                                                <i class="ki-filled ki-profile-circle text-blue-500 text-sm"></i>
                                            @else
                                                <i class="ki-filled ki-note-2 text-orange-500 text-sm"></i>
                                            @endif
                                            {{ $category }}
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($variables as $varKey => $varDescription)
                                                <div class="client-field-item group cursor-pointer p-2 rounded-lg hover:bg-blue-50 transition-colors" 
                                                     data-field="{{ $varKey }}" 
                                                     data-description="{{ $varDescription }}"
                                                     title="Click to use this field">
                                                    <div class="flex items-start justify-between">
                                                        <div class="min-w-0 flex-1">
                                                            <code class="text-xs font-mono text-blue-600 block truncate">{{ $varKey }}</code>
                                                            <span class="text-xs text-gray-500 mt-1 block">{{ Str::limit($varDescription, 40) }}</span>
                                                        </div>
                                                        <i class="ki-filled ki-plus text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity text-xs mt-1"></i>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Quick Tips -->
                            <div class="mt-6 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                <h5 class="font-semibold text-sm text-yellow-800 mb-2">💡 Quick Tips</h5>
                                <ul class="text-xs text-yellow-700 space-y-1">
                                    <li>• Click on any field above to quickly use it</li>
                                    <li>• Common Facebook fields: full_name, email, phone_number</li>
                                    <li>• Mark important fields as required</li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
    function addMappingRow(data = {}) {
        const mainContainer = document.querySelector('.space-y-4');
        if (!mainContainer) {
            // If no mappings exist, reload page to show proper form structure
            location.reload();
            return;
        }
        
        const mappingRow = document.createElement('div');
        mappingRow.className = 'bg-gray-50 rounded-lg p-4 mapping-row';
        
        mappingRow.innerHTML = `
            <div class="grid lg:grid-cols-12 gap-4 items-center">
                <!-- Facebook Field -->
                <div class="lg:col-span-3">
                    <label class="form-label text-xs font-semibold text-gray-600 mb-1">Facebook Field</label>
                    <input type="text" 
                           name="mappings[${mappingIndex}][facebook_field_name]" 
                           value="${data.facebook_field_name || ''}"
                           class="input input-sm"
                           placeholder="e.g., full_name, email"
                           required>
                </div>
                
                <!-- Field Type -->
                <div class="lg:col-span-2">
                    <label class="form-label text-xs font-semibold text-gray-600 mb-1">Type</label>
                    <select name="mappings[${mappingIndex}][facebook_field_type]" class="select select-sm" required>
                        <option value="">Select...</option>
                        <option value="text" ${data.facebook_field_type === 'text' ? 'selected' : ''}>Text</option>
                        <option value="email" ${data.facebook_field_type === 'email' ? 'selected' : ''}>Email</option>
                        <option value="phone" ${data.facebook_field_type === 'phone' ? 'selected' : ''}>Phone</option>
                        <option value="select" ${data.facebook_field_type === 'select' ? 'selected' : ''}>Select</option>
                        <option value="textarea" ${data.facebook_field_type === 'textarea' ? 'selected' : ''}>Textarea</option>
                        <option value="date" ${data.facebook_field_type === 'date' ? 'selected' : ''}>Date</option>
                        <option value="number" ${data.facebook_field_type === 'number' ? 'selected' : ''}>Number</option>
                    </select>
                </div>
                
                <!-- Maps To Arrow -->
                <div class="lg:col-span-1 text-center">
                    <i class="ki-filled ki-arrow-right text-blue-500 text-lg"></i>
                </div>
                
                <!-- System Field -->
                <div class="lg:col-span-4">
                    <label class="form-label text-xs font-semibold text-gray-600 mb-1">Client System Field</label>
                    <select name="mappings[${mappingIndex}][system_field_name]" class="select select-sm system-field-select" required>
                        <option value="">Select Client Field...</option>
                        @foreach($systemVariables as $category => $variables)
                            <optgroup label="{{ $category }}">
                                @foreach($variables as $varKey => $varDescription)
                                    <option value="{{ $varKey }}" title="{{ $varDescription }}"
                                            ${data.system_field_name === '{{ $varKey }}' ? 'selected' : ''}>
                                        {{ $varKey }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                
                <!-- Controls -->
                <div class="lg:col-span-2 flex items-center justify-end gap-2">
                    <label class="checkbox-group">
                        <input type="checkbox" 
                               name="mappings[${mappingIndex}][is_required]" 
                               value="1" 
                               ${data.is_required === '1' ? 'checked' : ''}
                               class="checkbox checkbox-sm">
                        <span class="checkbox-label text-xs">Required</span>
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" 
                               name="mappings[${mappingIndex}][is_active]" 
                               value="1" 
                               ${data.is_active !== '0' ? 'checked' : ''}
                               class="checkbox checkbox-sm">
                        <span class="checkbox-label text-xs">Active</span>
                    </label>
                    <button type="button" class="btn btn-sm btn-icon btn-light remove-mapping" title="Remove Mapping">
                        <i class="ki-filled ki-trash text-red-500"></i>
                    </button>
                </div>
            </div>
        `;
        
        mainContainer.appendChild(mappingRow);
        mappingIndex++;
        
        // Focus on the first input of the new row
        mappingRow.querySelector('input').focus();
    }
    
    // Event listeners
    document.getElementById('addMappingRow')?.addEventListener('click', () => addMappingRow());
    document.getElementById('addFirstMapping')?.addEventListener('click', () => addMappingRow());
    
    // Remove mapping row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-mapping')) {
            e.target.closest('.mapping-row').remove();
        }
    });
    
    // Quick field selection from sidebar
    document.addEventListener('click', function(e) {
        const fieldItem = e.target.closest('.client-field-item');
        if (fieldItem) {
            const fieldName = fieldItem.dataset.field;
            
            // Find the last system field select that's empty
            const selects = document.querySelectorAll('.system-field-select');
            let targetSelect = null;
            
            for (let select of selects) {
                if (select.value === '') {
                    targetSelect = select;
                    break;
                }
            }
            
            // If no empty select found, add a new row
            if (!targetSelect) {
                addMappingRow();
                // Get the newly added select
                const newSelects = document.querySelectorAll('.system-field-select');
                targetSelect = newSelects[newSelects.length - 1];
            }
            
            if (targetSelect) {
                targetSelect.value = fieldName;
                targetSelect.dispatchEvent(new Event('change'));
                
                // Show success feedback
                KTToast.show({
                    text: `${fieldName} field selected`,
                    type: 'success',
                    placement: 'top-center',
                    timeout: 2000
                });
            }
        }
    });
    
    // Preview mappings
    document.getElementById('previewMappings')?.addEventListener('click', function() {
        const mappings = [];
        document.querySelectorAll('.mapping-row').forEach(row => {
            const facebookField = row.querySelector('[name*="[facebook_field_name]"]')?.value;
            const systemField = row.querySelector('[name*="[system_field_name]"]')?.value;
            const fieldType = row.querySelector('[name*="[facebook_field_type]"]')?.value;
            const isRequired = row.querySelector('[name*="[is_required]"]')?.checked;
            const isActive = row.querySelector('[name*="[is_active]"]')?.checked;
            
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
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="ki-filled ki-eye text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg">Mapping Preview</h4>
                        <p class="text-gray-600">${mappings.length} active mapping(s) configured</p>
                    </div>
                </div>
                
                <div class="space-y-3">
        `;
        
        mappings.forEach((mapping, index) => {
            previewHtml += `
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <code class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">${mapping.facebook_field}</code>
                            <i class="ki-filled ki-arrow-right text-gray-400"></i>
                            <code class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">${mapping.system_field}</code>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="badge badge-sm badge-secondary">${mapping.field_type}</span>
                            ${mapping.is_active ? '<span class="badge badge-sm badge-success">Active</span>' : '<span class="badge badge-sm badge-secondary">Inactive</span>'}
                            ${mapping.is_required ? '<span class="badge badge-sm badge-warning">Required</span>' : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        if (mappings.length === 0) {
            previewHtml += `
                <div class="text-center py-8">
                    <i class="ki-filled ki-information-2 text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-600">No mappings configured yet</p>
                </div>
            `;
        }
        
        previewHtml += `
                </div>
            </div>
        `;
        
        // Show in a modal or alert
        KTModal.getInstance(document.querySelector('#preview_modal'))?.show() || 
        alert('Preview:\n\n' + mappings.map(m => `${m.facebook_field} → ${m.system_field}`).join('\n'));
    });
});
</script>
@endpush
