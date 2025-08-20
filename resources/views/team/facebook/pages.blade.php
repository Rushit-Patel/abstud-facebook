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
                <!-- Webhook Status Overview -->
                @php
                    $subscribedPages = $pages->where('webhook_subscribed', true);
                    $unsubscribedPages = $pages->where('webhook_subscribed', false);
                @endphp
                
                @if($pages->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Webhook Status Overview</h3>
                            <a href="{{ route('facebook.webhook-settings') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Webhook Settings →
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 text-sm">Total Pages</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ $pages->count() }}</span>
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-green-700 text-sm">Subscribed</span>
                                    <span class="text-2xl font-bold text-green-700">{{ $subscribedPages->count() }}</span>
                                </div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-orange-700 text-sm">Not Subscribed</span>
                                    <span class="text-2xl font-bold text-orange-700">{{ $unsubscribedPages->count() }}</span>
                                </div>
                            </div>
                        </div>

                        @if($unsubscribedPages->count() > 0)
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.5 0L4.268 6.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-yellow-800 font-medium">Real-time Lead Notifications</h4>
                                        <p class="text-yellow-700 text-sm mt-1">{{ $unsubscribedPages->count() }} page(s) are not subscribed to webhook. Subscribe them to receive real-time lead notifications as soon as someone submits a form.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Pages List -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($pages as $page)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Page Cover Image (Minimal Height) -->
                            @if($page->cover_image_url)
                                <div class="h-20 relative">
                                    <img src="{{ $page->cover_image_url }}" alt="{{ $page->page_name }} Cover" 
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                            @else
                                <div class="h-20 bg-gradient-to-r from-blue-600 to-blue-700"></div>
                            @endif
                            
                            <div class="p-5">
                                <!-- Page Header (Simplified) -->
                                <div class="flex items-start gap-3 mb-4">
                                    @if($page->profile_picture_url)
                                        <img src="{{ $page->profile_picture_url }}" alt="{{ $page->page_name }}" 
                                             class="w-10 h-10 rounded-lg object-cover border-2 border-white shadow-sm -mt-7 relative z-10 bg-white">
                                    @else
                                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center -mt-7 relative z-10 border-2 border-white shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-base truncate">{{ $page->page_name }}</h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($page->page_category)
                                                <span class="text-sm text-gray-600">{{ $page->page_category }}</span>
                                            @endif
                                            @if($page->fan_count)
                                                <span class="text-xs text-gray-500">• {{ number_format($page->fan_count) }} followers</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium shrink-0
                                        {{ $page->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- Page Details (Compact) -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Page ID:</span>
                                        <div class="flex items-center gap-1">
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ substr($page->facebook_page_id, 0, 12) }}...</code>
                                            <button onclick="copyToClipboard('{{ $page->facebook_page_id }}')" 
                                                    class="text-gray-500 hover:text-blue-600 transition-colors" title="Copy Page ID">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Access Token:</span>
                                        <div class="flex items-center gap-1">
                                            @if($page->page_access_token)
                                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ substr($page->page_access_token, 0, 12) }}...</code>
                                                <button onclick="copyToClipboard('{{ $page->page_access_token }}')" 
                                                        class="text-gray-500 hover:text-blue-600 transition-colors" title="Copy Access Token">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-xs text-red-500">No token</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Stats (Minimal) -->
                                <div class="flex items-center justify-between text-sm mb-4 pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-600">Forms: <span class="font-medium text-gray-900">{{ $page->facebookLeadForms->count() }}</span></span>
                                        <div class="flex items-center gap-1">
                                            <div class="w-2 h-2 rounded-full {{ $page->webhook_subscribed ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                            <span class="text-xs {{ $page->webhook_subscribed ? 'text-green-700' : 'text-gray-500' }}">
                                                {{ $page->webhook_subscribed ? 'Webhook' : 'No Webhook' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions (Simplified) -->
                                <div class="flex gap-2">
                                    <form action="{{ route('facebook.pages.toggle', $page) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full text-sm py-2 px-3 rounded border transition-colors
                                            {{ $page->is_active ? 'border-red-300 text-red-700 hover:bg-red-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $page->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    
                                    @if(!$page->webhook_subscribed)
                                        <form action="{{ route('facebook.pages.subscribe', $page) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded transition-colors">
                                                Subscribe
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('facebook.pages.unsubscribe', $page) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full border border-orange-300 text-orange-700 hover:bg-orange-50 text-sm py-2 px-3 rounded transition-colors">
                                                Unsubscribe
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <div data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px" data-kt-dropdown-placement="bottom-end" class="">
                                        <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors" data-kt-dropdown-toggle="true">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <div class="kt-dropdown-menu p-0 w-48 hidden" data-kt-dropdown-menu="true">
                                            <div class="flex flex-col py-2">
                                                <form action="{{ route('facebook.pages.sync-forms', $page) }}" method="POST" class="contents">
                                                    @csrf
                                                    <button type="submit" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="ki-filled ki-arrows-circle text-base mr-3 text-blue-600"></i>
                                                        Sync Forms
                                                    </button>
                                                </form>
                                                <form action="{{ route('facebook.pages.refresh', $page) }}" method="POST" class="contents">
                                                    @csrf
                                                    <button type="submit" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="ki-filled ki-arrows-loop text-base mr-3 text-indigo-600"></i>
                                                        Refresh Data
                                                    </button>
                                                </form>
                                                @if($page->facebookLeadForms->count() > 0)
                                                    <a href="{{ route('facebook.lead-forms') }}?page_id={{ $page->id }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="ki-filled ki-file-sheet text-base mr-3 text-green-600"></i>
                                                        View Forms ({{ $page->facebookLeadForms->count() }})
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

    <x-slot name="scripts">
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    KTToast.show({
                        message: "Copied to clipboard!",
                        icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                        progress: true,
                        pauseOnHover: true,
                        variant: "success",
                        duration: 3000
                    });
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    KTToast.show({
                        message: "Copied to clipboard!",
                        icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                        progress: true,
                        pauseOnHover: true,
                        variant: "success",
                        duration: 3000
                    });
                });
            }
        </script>
    </x-slot>
</x-team.layout.app>
