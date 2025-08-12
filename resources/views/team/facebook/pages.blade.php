@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Pages']
    ];
@endphp

<x-team.layout.app title="Facebook Integration - Pages" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Facebook Pages</h1>
                        <p class="text-gray-600">Manage your Facebook pages and their lead forms</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if($businessAccount)
                        <form action="{{ route('facebook.business-account.sync-pages') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Sync Pages
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('facebook.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            @if(!$businessAccount)
                <!-- No Business Account -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Business Account Connected</h3>
                    <p class="text-gray-600 mb-6">Please connect your Facebook Business Account first to manage pages.</p>
                    <a href="{{ route('facebook.business-account') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                        Connect Business Account
                    </a>
                </div>
            @elseif($pages->isEmpty())
                <!-- No Pages -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Pages Found</h3>
                    <p class="text-gray-600 mb-6">No Facebook pages found for your business account. Try syncing your pages.</p>
                    <form action="{{ route('facebook.business-account.sync-pages') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            Sync Pages Now
                        </button>
                    </form>
                </div>
            @else
                <!-- Pages List -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($pages as $page)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <!-- Page Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $page->page_name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $page->facebook_page_id }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $page->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Page Stats -->
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Lead Forms:</span>
                                    <span class="font-medium">{{ $page->facebookLeadForms->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Active Forms:</span>
                                    <span class="font-medium">{{ $page->facebookLeadForms->where('is_active', true)->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium">{{ $page->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Page Actions -->
                            <div class="flex flex-col gap-2">
                                <div class="flex gap-2">
                                    <form action="{{ route('facebook.pages.toggle', $page) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full {{ $page->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                            {{ $page->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('facebook.pages.sync-forms', $page) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                            Sync Forms
                                        </button>
                                    </form>
                                </div>
                                @if($page->facebookLeadForms->count() > 0)
                                    <a href="{{ route('facebook.lead-forms') }}?page_id={{ $page->id }}" class="block bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium py-2 px-3 rounded-lg transition-colors text-center">
                                        View Lead Forms ({{ $page->facebookLeadForms->count() }})
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination if needed -->
                @if(method_exists($pages, 'links'))
                    <div class="mt-8">
                        {{ $pages->links() }}
                    </div>
                @endif
            @endif
        </div>
    </x-slot>
</x-team.layout.app>
