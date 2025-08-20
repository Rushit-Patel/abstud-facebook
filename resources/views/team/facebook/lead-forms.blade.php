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
                                {{-- <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Field Mappings:</span>
                                        <span class="font-medium {{ $form->facebookParameterMappings->count() > 0 ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $form->facebookParameterMappings->count() }} configured
                                        </span>
                                    </div>
                                </div> --}}
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
                                </div>
                                
                                <!-- Secondary Actions -->
                                <div class="flex gap-2">
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
