@extends('team.layouts.app')

@section('title', 'Custom Field Mappings - ' . $leadForm->form_name)

@section('content')
<div class="container-fluid">
    <div class="grid">
        <!-- Header -->
        <div class="card mb-5 lg:mb-7.5">
            <div class="card-header">
                <div class="flex items-center gap-4">
                    <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="btn btn-sm btn-light">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Lead Form
                    </a>
                    <div class="flex flex-col">
                        <h1 class="text-xl font-semibold text-gray-900">Custom Field Mappings</h1>
                        <p class="text-sm text-gray-600">Map Facebook custom questions to your system fields</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lead Form Info Card -->
        <div class="card mb-5 lg:mb-7.5">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="ki-filled ki-facebook text-xl text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ $leadForm->form_name }}</h3>
                        <div class="flex items-center gap-4 text-sm text-gray-600 mt-1">
                            <span>Page: {{ $leadForm->facebookPage->page_name }}</span>
                            <span>•</span>
                            <span>Custom Mappings: {{ $leadForm->facebookCustomFieldMappings->count() }}</span>
                            <span>•</span>
                            <span>Active: {{ $leadForm->facebookCustomFieldMappings->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($leadForm->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Questions from Facebook -->
        @if(!empty($leadForm->questions) && is_array($leadForm->questions))
            <div class="card mb-5 lg:mb-7.5">
                <div class="card-header">
                    <h3 class="card-title">Facebook Form Questions</h3>
                    <span class="badge badge-info">{{ count($leadForm->questions) }} questions found</span>
                </div>
                <div class="card-body">
                    <div class="grid lg:grid-cols-2 gap-4">
                        @foreach($leadForm->questions as $question)
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="ki-filled ki-question text-xs text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm text-gray-900 mb-1">{{ $question['question'] ?? 'N/A' }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $question['type'] ?? 'text' }}</span>
                                            @if($question['required'] ?? false)
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Required</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Custom Mapping Configuration -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="card-title">Custom Question Mappings</h3>
                    <button type="button" class="btn btn-primary" data-modal-toggle="#add_custom_mapping_modal">
                        <i class="ki-filled ki-plus"></i>
                        Add New Custom Mapping
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($leadForm->facebookCustomFieldMappings->count() > 0)
                    <form action="{{ route('facebook.lead-forms.custom-mappings.save', $leadForm) }}" method="POST" id="customMappingsForm">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="min-w-64">Facebook Custom Question</th>
                                        <th class="min-w-48">System Field</th>
                                        <th class="min-w-32">Data Type</th>
                                        <th class="min-w-24 text-center">Active</th>
                                        <th class="min-w-20 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customMappingsTableBody">
                                    @foreach($leadForm->facebookCustomFieldMappings as $index => $mapping)
                                        <tr data-mapping-id="{{ $mapping->id }}">
                                            <td>
                                                <input type="hidden" name="custom_mappings[{{ $index }}][id]" value="{{ $mapping->id }}">
                                                <textarea name="custom_mappings[{{ $index }}][facebook_custom_question]" 
                                                          class="input input-sm" rows="2" 
                                                          placeholder="Enter the exact Facebook custom question..." required>{{ $mapping->facebook_custom_question }}</textarea>
                                            </td>
                                            <td>
                                                <select name="custom_mappings[{{ $index }}][system_field_name]" class="select select-sm" required>
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
                                            <td>
                                                <select name="custom_mappings[{{ $index }}][data_type]" class="select select-sm" required>
                                                    <option value="text" {{ $mapping->data_type === 'text' ? 'selected' : '' }}>Text</option>
                                                    <option value="number" {{ $mapping->data_type === 'number' ? 'selected' : '' }}>Number</option>
                                                    <option value="date" {{ $mapping->data_type === 'date' ? 'selected' : '' }}>Date</option>
                                                    <option value="boolean" {{ $mapping->data_type === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                                    <option value="email" {{ $mapping->data_type === 'email' ? 'selected' : '' }}>Email</option>
                                                    <option value="phone" {{ $mapping->data_type === 'phone' ? 'selected' : '' }}>Phone</option>
                                                    <option value="url" {{ $mapping->data_type === 'url' ? 'selected' : '' }}>URL</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="custom_mappings[{{ $index }}][is_active]" 
                                                       value="1" {{ $mapping->is_active ? 'checked' : '' }}
                                                       class="checkbox checkbox-sm">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-custom-mapping">
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
                                <button type="button" class="btn btn-secondary" id="addCustomMappingRow">
                                    <i class="ki-filled ki-plus"></i>
                                    Add Row
                                </button>
                                <button type="button" class="btn btn-info" id="previewCustomMappings">
                                    <i class="ki-filled ki-eye"></i>
                                    Preview Mapping
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-filled ki-check"></i>
                                    Save Custom Mappings
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ki-filled ki-questionnaire-tablet text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Custom Field Mappings</h3>
                        <p class="text-gray-600 mb-4">Map Facebook custom questions to your system fields for better lead processing.</p>
                        <button type="button" class="btn btn-primary" data-modal-toggle="#add_custom_mapping_modal">
                            <i class="ki-filled ki-plus"></i>
                            Add First Custom Mapping
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Data Type Guidelines -->
        <div class="card mt-5 lg:mt-7.5">
            <div class="card-header">
                <h3 class="card-title">Data Type Guidelines</h3>
            </div>
            <div class="card-body">
                <div class="grid lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-sm text-gray-900 mb-3">Data Type Descriptions</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-light min-w-16">text</span>
                                <span class="text-gray-600">String/text values (default)</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-info min-w-16">number</span>
                                <span class="text-gray-600">Numeric values (integers, decimals)</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-warning min-w-16">date</span>
                                <span class="text-gray-600">Date values (will be parsed)</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-success min-w-16">boolean</span>
                                <span class="text-gray-600">True/false, yes/no values</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-primary min-w-16">email</span>
                                <span class="text-gray-600">Email addresses (validated)</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-purple min-w-16">phone</span>
                                <span class="text-gray-600">Phone numbers (formatted)</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="badge badge-sm badge-secondary min-w-16">url</span>
                                <span class="text-gray-600">Website URLs</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-gray-900 mb-3">Best Practices</h4>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li>Use exact question text from Facebook form</li>
                            <li>Select appropriate data types for validation</li>
                            <li>Map to relevant system fields for consistency</li>
                            <li>Test mappings with sample data first</li>
                            <li>Keep active mappings for current form fields</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Custom Mapping Modal -->
<div class="modal" data-modal="true" id="add_custom_mapping_modal">
    <div class="modal-content max-w-2xl">
        <div class="modal-header">
            <h3 class="modal-title">Add New Custom Field Mapping</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addCustomMappingForm">
                <div class="mb-4">
                    <label class="form-label">Facebook Custom Question</label>
                    <textarea name="facebook_custom_question" class="input" rows="3" 
                              placeholder="Enter the exact custom question from your Facebook lead form..." required></textarea>
                    <div class="text-2sm text-gray-600 mt-1">
                        Copy the exact question text as it appears in your Facebook lead form
                    </div>
                </div>
                
                <div class="grid lg:grid-cols-2 gap-4 mb-4">
                    <div>
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
                    <div>
                        <label class="form-label">Data Type</label>
                        <select name="data_type" class="select" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="boolean">Boolean</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="url">URL</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
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
                <button type="button" class="btn btn-primary" id="addCustomMappingBtn">
                    <i class="ki-filled ki-plus"></i>
                    Add Custom Mapping
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal" data-modal="true" id="preview_custom_modal">
    <div class="modal-content max-w-4xl">
        <div class="modal-header">
            <h3 class="modal-title">Custom Mapping Preview</h3>
            <button class="btn btn-sm btn-icon btn-light" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="previewCustomContent">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-dismiss="true">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let customMappingIndex = {{ $leadForm->facebookCustomFieldMappings->count() }};
    
    // Add new custom mapping row
    document.getElementById('addCustomMappingRow').addEventListener('click', function() {
        addCustomMappingRow();
    });
    
    // Add custom mapping from modal
    document.getElementById('addCustomMappingBtn').addEventListener('click', function() {
        const form = document.getElementById('addCustomMappingForm');
        const formData = new FormData(form);
        
        const rowData = {
            facebook_custom_question: formData.get('facebook_custom_question'),
            system_field_name: formData.get('system_field_name'),
            data_type: formData.get('data_type'),
            is_active: formData.get('is_active') ? '1' : '0'
        };
        
        if (rowData.facebook_custom_question && rowData.system_field_name && rowData.data_type) {
            addCustomMappingRow(rowData);
            form.reset();
            // Close modal
            document.querySelector('[data-modal-dismiss="true"]').click();
        }
    });
    
    // Remove custom mapping row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-custom-mapping')) {
            e.target.closest('tr').remove();
        }
    });
    
    // Preview custom mappings
    document.getElementById('previewCustomMappings').addEventListener('click', function() {
        showCustomMappingPreview();
    });
    
    function addCustomMappingRow(data = {}) {
        const tbody = document.getElementById('customMappingsTableBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <textarea name="custom_mappings[${customMappingIndex}][facebook_custom_question]" 
                          class="input input-sm" rows="2" 
                          placeholder="Enter the exact Facebook custom question..." required>${data.facebook_custom_question || ''}</textarea>
            </td>
            <td>
                <select name="custom_mappings[${customMappingIndex}][system_field_name]" class="select select-sm" required>
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
            <td>
                <select name="custom_mappings[${customMappingIndex}][data_type]" class="select select-sm" required>
                    <option value="text" ${data.data_type === 'text' ? 'selected' : ''}>Text</option>
                    <option value="number" ${data.data_type === 'number' ? 'selected' : ''}>Number</option>
                    <option value="date" ${data.data_type === 'date' ? 'selected' : ''}>Date</option>
                    <option value="boolean" ${data.data_type === 'boolean' ? 'selected' : ''}>Boolean</option>
                    <option value="email" ${data.data_type === 'email' ? 'selected' : ''}>Email</option>
                    <option value="phone" ${data.data_type === 'phone' ? 'selected' : ''}>Phone</option>
                    <option value="url" ${data.data_type === 'url' ? 'selected' : ''}>URL</option>
                </select>
            </td>
            <td class="text-center">
                <input type="checkbox" name="custom_mappings[${customMappingIndex}][is_active]" 
                       value="1" ${data.is_active !== '0' ? 'checked' : ''}
                       class="checkbox checkbox-sm">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-custom-mapping">
                    <i class="ki-filled ki-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        customMappingIndex++;
    }
    
    function showCustomMappingPreview() {
        const mappings = [];
        document.querySelectorAll('#customMappingsTableBody tr').forEach(row => {
            const facebookQuestion = row.querySelector('[name*="[facebook_custom_question]"]').value;
            const systemField = row.querySelector('[name*="[system_field_name]"]').value;
            const dataType = row.querySelector('[name*="[data_type]"]').value;
            const isActive = row.querySelector('[name*="[is_active]"]').checked;
            
            if (facebookQuestion && systemField) {
                mappings.push({
                    facebook_question: facebookQuestion,
                    system_field: systemField,
                    data_type: dataType,
                    is_active: isActive
                });
            }
        });
        
        let previewHtml = `
            <div class="space-y-4">
                <h4 class="font-semibold">Current Custom Mappings (${mappings.length} total)</h4>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Facebook Custom Question</th>
                                <th>Maps To</th>
                                <th>System Field</th>
                                <th>Data Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        mappings.forEach(mapping => {
            previewHtml += `
                <tr>
                    <td>
                        <div class="max-w-xs">
                            <p class="text-sm font-medium text-blue-900">${mapping.facebook_question}</p>
                        </div>
                    </td>
                    <td><i class="ki-filled ki-arrow-right text-gray-400"></i></td>
                    <td><code class="text-purple-600">${mapping.system_field}</code></td>
                    <td><span class="badge badge-sm badge-secondary">${mapping.data_type}</span></td>
                    <td>
                        ${mapping.is_active ? '<span class="badge badge-sm badge-success">Active</span>' : '<span class="badge badge-sm badge-secondary">Inactive</span>'}
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
        
        document.getElementById('previewCustomContent').innerHTML = previewHtml;
        document.querySelector('[data-modal="true"]#preview_custom_modal').classList.add('open');
    }
});
</script>
@endpush
