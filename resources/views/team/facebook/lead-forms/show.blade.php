@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Lead Forms', 'url' => route('facebook.lead-forms')],
        ['title' => $leadForm->form_name]
    ];
@endphp

<x-team.layout.app title="Facebook Lead Form - {{ $leadForm->form_name }}" :breadcrumbs="$breadcrumbs">
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
                        <h1 class="text-2xl font-bold text-gray-900">{{ $leadForm->form_name }}</h1>
                        <p class="text-gray-600">{{ $leadForm->facebookPage->page_name }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('facebook.lead-forms.toggle', $leadForm) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="{{ $leadForm->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            {{ $leadForm->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <a href="{{ route('facebook.lead-forms') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Forms
                    </a>
                </div>
            </div>

            <!-- Form Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Form Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Form Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Form Name</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-900">{{ $leadForm->form_name }}</span>
                                </div>
                            </div>

                            @if($leadForm->form_description)
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Description</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-900">{{ $leadForm->form_description }}</span>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-gray-600">Facebook Form ID</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <code class="text-sm text-gray-800">{{ $leadForm->facebook_form_id }}</code>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Facebook Page</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-900">{{ $leadForm->facebookPage->page_name }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Status</label>
                                <div class="mt-1">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                        {{ $leadForm->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $leadForm->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Created</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-900">{{ $leadForm->created_at->format('F d, Y \a\t H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="space-y-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Leads</span>
                                <span class="text-xl font-bold text-blue-600">{{ $leadForm->facebookLeads->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">This Week</span>
                                <span class="text-xl font-bold text-green-600">{{ $leadForm->facebookLeads->where('facebook_created_time', '>=', now()->subWeek())->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Today</span>
                                <span class="text-xl font-bold text-purple-600">{{ $leadForm->facebookLeads->where('facebook_created_time', '>=', now()->startOfDay())->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Parameter Mappings</span>
                                <span class="text-xl font-bold text-orange-600">{{ $leadForm->facebookParameterMappings->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Custom Mappings</span>
                                <span class="text-xl font-bold text-red-600">{{ $leadForm->facebookCustomFieldMappings->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ route('facebook.lead-forms.mappings', $leadForm) }}" class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">Field Mappings</span>
                            </a>
                            <a href="{{ route('facebook.lead-forms.custom-mappings', $leadForm) }}" class="flex items-center gap-3 p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">Custom Mappings</span>
                            </a>
                            <a href="{{ route('facebook.leads') }}?form_id={{ $leadForm->id }}" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">View All Leads</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parameter Mappings -->
            @if($leadForm->facebookParameterMappings->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Parameter Mappings</h3>
                        <a href="{{ route('facebook.lead-forms.mappings', $leadForm) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                            Manage Mappings
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Facebook Field</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Type</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">System Field</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Required</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leadForm->facebookParameterMappings as $mapping)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3 px-4">{{ $mapping->facebook_field_name }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ $mapping->facebook_field_type }}</span>
                                        </td>
                                        <td class="py-3 px-4">{{ $mapping->system_field_name }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 {{ $mapping->is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }} text-xs rounded-full">
                                                {{ $mapping->is_required ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 {{ $mapping->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} text-xs rounded-full">
                                                {{ $mapping->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Custom Field Mappings -->
            @if($leadForm->facebookCustomFieldMappings->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Custom Field Mappings</h3>
                        <a href="{{ route('facebook.lead-forms.custom-mappings', $leadForm) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                            Manage Custom Mappings
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Custom Question</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">System Field</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Data Type</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leadForm->facebookCustomFieldMappings as $mapping)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3 px-4">{{ $mapping->facebook_custom_question }}</td>
                                        <td class="py-3 px-4">{{ $mapping->system_field_name }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $mapping->data_type }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 {{ $mapping->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} text-xs rounded-full">
                                                {{ $mapping->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Recent Leads -->
            @if($leadForm->facebookLeads->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Leads</h3>
                        <a href="{{ route('facebook.leads') }}?form_id={{ $leadForm->id }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                            View All Leads
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Lead ID</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Name</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leadForm->facebookLeads->take(10) as $lead)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3 px-4">
                                            <a href="{{ route('facebook.leads.show', $lead) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $lead->facebook_lead_id }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4">{{ $lead->name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 {{ $lead->status === 'processed' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }} text-xs rounded-full">
                                                {{ ucfirst($lead->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">{{ $lead->facebook_created_time->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leads Yet</h3>
                    <p class="text-gray-600">No leads have been received for this form yet.</p>
                </div>
            @endif
        </div>
    </x-slot>
</x-team.layout.app>
