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
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <!-- Form Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $form->form_name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $form->facebookPage->page_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $form->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $form->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Form Description -->
                            @if($form->form_description)
                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($form->form_description, 100) }}</p>
                            @endif

                            <!-- Form Stats -->
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Total Leads:</span>
                                    <span class="font-medium">{{ $form->facebookLeads->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Recent Leads:</span>
                                    <span class="font-medium">{{ $form->facebookLeads->where('facebook_created_time', '>=', now()->subDays(7))->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium">{{ $form->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Form ID -->
                            <div class="mb-4">
                                <label class="text-xs font-medium text-gray-600">Form ID</label>
                                <div class="mt-1 p-2 bg-gray-50 rounded-lg">
                                    <code class="text-xs text-gray-800">{{ $form->facebook_form_id }}</code>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <form action="{{ route('facebook.lead-forms.toggle', $form) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full {{ $form->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                            {{ $form->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('facebook.lead-forms.show', $form) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors text-center">
                                        Details
                                    </a>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('facebook.lead-forms.mappings', $form) }}" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors text-center">
                                        Field Mapping
                                    </a>
                                    <a href="{{ route('facebook.leads') }}?form_id={{ $form->id }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium py-2 px-3 rounded-lg transition-colors text-center">
                                        View Leads
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary Stats -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leadForms->count() }}</p>
                                <p class="text-sm text-gray-600">Total Forms</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leadForms->where('is_active', true)->count() }}</p>
                                <p class="text-sm text-gray-600">Active Forms</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leadForms->sum(function($form) { return $form->facebookLeads->count(); }) }}</p>
                                <p class="text-sm text-gray-600">Total Leads</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leadForms->sum(function($form) { return $form->facebookLeads->where('facebook_created_time', '>=', now()->subDays(7))->count(); }) }}</p>
                                <p class="text-sm text-gray-600">This Week</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>
</x-team.layout.app>
