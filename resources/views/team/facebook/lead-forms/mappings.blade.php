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
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $leadForm->form_name }}</h1>
                        <p class="text-gray-600">Map Facebook lead fields to your client system</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500 mt-1">
                            <span>Page: {{ $leadForm->facebookPage->page_name }}</span>
                            <span>•</span>
                            <span>{{ $leadForm->facebookParameterMappings->count() }} Mappings</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Form
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-5">
                <!-- Main Mapping Configuration -->
                <div class="lg:col-span-2">
                    <x-team.card title="Field Mapping Configuration" titleClass="text-primary">
                        <x-slot name="header">
                            <div class="flex items-center gap-2 ml-auto">
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" data-kt-modal-trigger="#add_mapping_modal">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Mapping
                                </button>
                                <button type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors" id="previewMappings">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Preview
                                </button>
                            </div>
                        </x-slot>

                        @if($leadForm->facebookParameterMappings->count() > 0)
                            <form method="POST" action="{{ route('facebook.lead-forms.mappings.save', $leadForm) }}">
                                @csrf
                                <div class="space-y-4" id="mappingsContainer">
                                    @foreach($leadForm->facebookParameterMappings as $index => $mapping)
                                        <div class="bg-gray-50 rounded-lg p-4 mapping-row border border-gray-200">
                                            <div class="grid lg:grid-cols-12 gap-4 items-center">
                                                <!-- Facebook Field -->
                                                <div class="lg:col-span-3">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Facebook Field</label>
                                                    <input type="text" 
                                                           name="mappings[{{ $index }}][facebook_field_name]" 
                                                           value="{{ $mapping->facebook_field_name }}"
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                           placeholder="e.g., full_name, email"
                                                           required>
                                                </div>
                                                
                                                <!-- Field Type -->
                                                <div class="lg:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                                                    <select name="mappings[{{ $index }}][facebook_field_type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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
                                                    <svg class="w-5 h-5 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                    </svg>
                                                </div>
                                                
                                                <!-- System Field -->
                                                <div class="lg:col-span-4">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Client System Field</label>
                                                    <select name="mappings[{{ $index }}][system_field_name]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 system-field-select" required>
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
                                                    <label class="flex items-center text-xs">
                                                        <input type="checkbox" 
                                                               name="mappings[{{ $index }}][is_required]" 
                                                               value="1" 
                                                               {{ $mapping->is_required ? 'checked' : '' }}
                                                               class="mr-1">
                                                        Required
                                                    </label>
                                                    <label class="flex items-center text-xs">
                                                        <input type="checkbox" 
                                                               name="mappings[{{ $index }}][is_active]" 
                                                               value="1" 
                                                               {{ $mapping->is_active ? 'checked' : '' }}
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
                                    @endforeach
                                </div>
                                
                                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                                    <a href="{{ route('facebook.lead-forms.show', $leadForm) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">Cancel</a>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Save Mappings
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Field Mappings Yet</h3>
                                <p class="text-gray-600 mb-4 max-w-md mx-auto">Start mapping Facebook lead form fields to your client management system to automatically capture and organize lead data.</p>
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors" data-kt-modal-trigger="#add_mapping_modal">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add First Mapping
                                </button>
                            </div>
                        @endif
                    </x-team.card>
                </div>

                <!-- Sidebar - Client Fields Reference -->
                <div class="lg:col-span-1">
                    <x-team.card title="Available Client Fields" titleClass="text-primary">
                        <div class="space-y-4">
                            @foreach($systemVariables as $category => $variables)
                                <div>
                                    <h4 class="font-semibold text-sm text-gray-900 mb-3 flex items-center gap-2">
                                        @if($category === 'Client Information')
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        @endif
                                        {{ $category }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($variables as $varKey => $varDescription)
                                            <div class="client-field-item group cursor-pointer p-2 rounded-lg hover:bg-blue-50 transition-colors border border-transparent hover:border-blue-200" 
                                                 data-field="{{ $varKey }}" 
                                                 data-description="{{ $varDescription }}"
                                                 title="Click to use this field">
                                                <div class="flex items-start justify-between">
                                                    <div class="min-w-0 flex-1">
                                                        <code class="text-xs font-mono text-blue-600 block truncate">{{ $varKey }}</code>
                                                        <span class="text-xs text-gray-500 mt-1 block">{{ Str::limit($varDescription, 40) }}</span>
                                                    </div>
                                                    <svg class="w-3 h-3 text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
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
                    </x-team.card>
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>
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

<!-- Add Mapping Modal -->
<x-team.modal id="add_mapping_modal" title="Add New Field Mapping" size="max-w-2xl">
    <form id="addMappingForm">
        <div class="grid lg:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Field Name</label>
                <input type="text" name="facebook_field_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="e.g., full_name, email, phone_number" required>
                <div class="text-xs text-gray-600 mt-1">
                    Common fields: full_name, first_name, last_name, email, phone_number, city, country
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook Field Type</label>
                <select name="facebook_field_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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
            <label class="block text-sm font-semibold text-gray-700 mb-2">Client System Field</label>
            <select name="system_field_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Select client field...</option>
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
            <label class="flex items-center">
                <input type="checkbox" name="is_required" value="1" class="mr-2">
                <span class="text-sm">Required Field</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" class="mr-2" checked>
                <span class="text-sm">Active</span>
            </label>
        </div>
    </form>
    
    <x-slot name="footer">
        <div class="flex items-center gap-3">
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors" data-kt-modal-dismiss="true">Cancel</button>
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" id="addMappingBtn">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Mapping
            </button>
        </div>
    </x-slot>
</x-team.modal>

<!-- Variables Reference Modal -->
<x-team.modal id="variables_reference_modal" title="System Variables Reference" size="max-w-4xl">
    <div class="mb-4">
        <input type="text" id="variableSearch" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Search variables...">
    </div>
    <div class="grid lg:grid-cols-2 gap-6">
        @foreach($systemVariables as $category => $variables)
            <div class="variable-category">
                <h4 class="font-semibold text-gray-900 mb-3">{{ $category }}</h4>
                <div class="space-y-2">
                    @foreach($variables as $varKey => $varDescription)
                        <div class="variable-item flex items-start gap-3 p-2 rounded hover:bg-gray-50 border border-transparent hover:border-gray-200">
                            <code class="text-purple-600 text-sm font-mono min-w-0 flex-shrink-0">{{ $varKey }}</code>
                            <span class="text-gray-600 text-sm">{{ $varDescription }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    
    <x-slot name="footer">
        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors" data-kt-modal-dismiss="true">Close</button>
    </x-slot>
</x-team.modal>

<!-- Preview Modal -->
<x-team.modal id="preview_modal" title="Mapping Preview" size="max-w-4xl">
    <div id="previewContent">
        <!-- Preview content will be loaded here -->
    </div>
    
    <x-slot name="footer">
        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors" data-kt-modal-dismiss="true">Close</button>
    </x-slot>
</x-team.modal>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let mappingIndex = {{ $leadForm->facebookParameterMappings->count() }};
    
    // Add new mapping row
    function addMappingRow(data = {}) {
        const container = document.getElementById('mappingsContainer');
        if (!container) {
            // If no container exists, reload page to show proper form structure
            location.reload();
            return;
        }
        
        const mappingRow = document.createElement('div');
        mappingRow.className = 'bg-gray-50 rounded-lg p-4 mapping-row border border-gray-200';
        
        mappingRow.innerHTML = `
            <div class="grid lg:grid-cols-12 gap-4 items-center">
                <!-- Facebook Field -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Facebook Field</label>
                    <input type="text" 
                           name="mappings[${mappingIndex}][facebook_field_name]" 
                           value="${data.facebook_field_name || ''}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., full_name, email"
                           required>
                </div>
                
                <!-- Field Type -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                    <select name="mappings[${mappingIndex}][facebook_field_type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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
                    <svg class="w-5 h-5 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
                
                <!-- System Field -->
                <div class="lg:col-span-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Client System Field</label>
                    <select name="mappings[${mappingIndex}][system_field_name]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 system-field-select" required>
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
                    <label class="flex items-center text-xs">
                        <input type="checkbox" 
                               name="mappings[${mappingIndex}][is_required]" 
                               value="1" 
                               ${data.is_required === '1' ? 'checked' : ''}
                               class="mr-1">
                        Required
                    </label>
                    <label class="flex items-center text-xs">
                        <input type="checkbox" 
                               name="mappings[${mappingIndex}][is_active]" 
                               value="1" 
                               ${data.is_active !== '0' ? 'checked' : ''}
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
        `;
        
        container.appendChild(mappingRow);
        mappingIndex++;
        
        // Focus on the first input of the new row
        mappingRow.querySelector('input').focus();
    }
    
    // Add mapping from modal
    document.getElementById('addMappingBtn')?.addEventListener('click', function() {
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
            
            // Close modal using KT modal
            const modal = document.getElementById('add_mapping_modal');
            if (modal) {
                modal.classList.remove('show');
            }
            
            // Show success toast
            if (typeof KTToast !== 'undefined') {
                KTToast.show({
                    text: 'Mapping added successfully',
                    type: 'success',
                    placement: 'top-center',
                    timeout: 2000
                });
            }
        } else {
            alert('Please fill in all required fields');
        }
    });
    
    // Remove mapping row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-mapping')) {
            e.target.closest('.mapping-row').remove();
            
            if (typeof KTToast !== 'undefined') {
                KTToast.show({
                    text: 'Mapping removed',
                    type: 'info',
                    placement: 'top-center',
                    timeout: 2000
                });
            }
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
            
            // If no empty select found, show modal to add new mapping
            if (!targetSelect) {
                const modal = document.getElementById('add_mapping_modal');
                const systemFieldSelect = modal.querySelector('select[name="system_field_name"]');
                if (systemFieldSelect) {
                    systemFieldSelect.value = fieldName;
                }
                
                // Show modal
                if (modal) {
                    modal.classList.add('show');
                }
            } else {
                targetSelect.value = fieldName;
                targetSelect.dispatchEvent(new Event('change'));
            }
            
            // Show success feedback
            if (typeof KTToast !== 'undefined') {
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
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
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
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <code class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">${mapping.facebook_field}</code>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            <code class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-mono">${mapping.system_field}</code>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">${mapping.field_type}</span>
                            ${mapping.is_active ? '<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Active</span>' : '<span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">Inactive</span>'}
                            ${mapping.is_required ? '<span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Required</span>' : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        if (mappings.length === 0) {
            previewHtml += `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-600">No mappings configured yet</p>
                </div>
            `;
        }
        
        previewHtml += `
                </div>
            </div>
        `;
        
        document.getElementById('previewContent').innerHTML = previewHtml;
        
        // Show preview modal
        const modal = document.getElementById('preview_modal');
        if (modal) {
            modal.classList.add('show');
        }
    });
    
    // Variable search functionality
    document.getElementById('variableSearch')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.variable-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    });
});
</script>
@endpush
