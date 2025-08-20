@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => '#'],
        ['title' => 'Facebook Integration', 'url' => '#'],
        ['title' => 'Pages Demo']
    ];
    
    // Demo data for visualization
    $demoPages = [
        (object) [
            'id' => 1,
            'page_name' => 'TechCoder Solutions',
            'facebook_page_id' => '123456789012345',
            'page_category' => 'Software Company',
            'fan_count' => 1250,
            'profile_picture_url' => 'https://via.placeholder.com/120x120/1877f2/ffffff?text=TC',
            'cover_image_url' => 'https://via.placeholder.com/820x312/4267b2/ffffff?text=TechCoder+Solutions+Cover',
            'page_access_token' => 'EAABwzLixnjYBO...[truncated]...xyz123',
            'is_active' => true,
            'webhook_subscribed' => true,
            'webhook_subscribed_at' => now(),
            'facebookLeadForms' => collect([
                (object) ['id' => 1, 'name' => 'Contact Form'],
                (object) ['id' => 2, 'name' => 'Newsletter Signup']
            ])
        ],
        (object) [
            'id' => 2,
            'page_name' => 'Digital Marketing Pro',
            'facebook_page_id' => '567890123456789',
            'page_category' => 'Marketing Agency',
            'fan_count' => 2840,
            'profile_picture_url' => 'https://via.placeholder.com/120x120/e91e63/ffffff?text=DM',
            'cover_image_url' => 'https://via.placeholder.com/820x312/ad1457/ffffff?text=Digital+Marketing+Pro+Cover',
            'page_access_token' => 'EAABwzLixnjYBO...[truncated]...abc456',
            'is_active' => true,
            'webhook_subscribed' => false,
            'webhook_subscribed_at' => null,
            'facebookLeadForms' => collect([
                (object) ['id' => 3, 'name' => 'Service Inquiry']
            ])
        ],
        (object) [
            'id' => 3,
            'page_name' => 'Education Hub',
            'facebook_page_id' => '345678901234567',
            'page_category' => 'Education',
            'fan_count' => 5670,
            'profile_picture_url' => 'https://via.placeholder.com/120x120/ff9800/ffffff?text=EH',
            'cover_image_url' => null, // No cover image
            'page_access_token' => null, // No access token
            'is_active' => false,
            'webhook_subscribed' => false,
            'webhook_subscribed_at' => null,
            'facebookLeadForms' => collect()
        ]
    ];
    
    $pages = collect($demoPages);
    $businessAccount = (object) ['id' => 1, 'name' => 'Demo Business Account'];
@endphp

<x-team.layout.app title="Facebook Integration - Pages Demo" :breadcrumbs="$breadcrumbs">
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
                        <h1 class="text-2xl font-bold text-gray-900">Facebook Pages (Demo)</h1>
                        <p class="text-gray-600">Enhanced view with cover images and detailed page information</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Sync Pages
                    </button>
                    <a href="#" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Webhook Status Overview -->
            @php
                $subscribedPages = $pages->where('webhook_subscribed', true);
                $unsubscribedPages = $pages->where('webhook_subscribed', false);
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Webhook Status Overview</h3>
                    <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
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

            <!-- Pages List -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($pages as $page)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Page Cover Image -->
                        @if($page->cover_image_url)
                            <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                                <img src="{{ $page->cover_image_url }}" alt="{{ $page->page_name }} Cover" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                            </div>
                        @else
                            <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <!-- Page Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    @if($page->profile_picture_url)
                                        <img src="{{ $page->profile_picture_url }}" alt="{{ $page->page_name }}" 
                                             class="w-12 h-12 rounded-lg object-cover border-2 border-white shadow-md -mt-8 relative z-10">
                                    @else
                                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center -mt-8 relative z-10 border-2 border-white shadow-md">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-lg">{{ $page->page_name }}</h3>
                                        @if($page->page_category)
                                            <p class="text-sm text-gray-600">{{ $page->page_category }}</p>
                                        @endif
                                        @if($page->fan_count)
                                            <p class="text-xs text-gray-500">{{ number_format($page->fan_count) }} followers</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $page->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Page Details Section -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Page Details</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Page ID:</span>
                                        <span class="font-mono text-xs bg-white px-2 py-1 rounded border">{{ $page->facebook_page_id }}</span>
                                    </div>
                                    <div class="flex items-start justify-between text-sm">
                                        <span class="text-gray-600">Access Token:</span>
                                        <div class="flex items-center gap-2">
                                            @if($page->page_access_token)
                                                <span class="font-mono text-xs bg-white px-2 py-1 rounded border max-w-32 truncate" 
                                                      title="{{ $page->page_access_token }}">{{ substr($page->page_access_token, 0, 20) }}...</span>
                                                <button onclick="copyToClipboard('{{ $page->page_access_token }}')" 
                                                        class="text-blue-600 hover:text-blue-700 text-xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-red-500 text-xs">No token</span>
                                            @endif
                                        </div>
                                    </div>
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
                                    <span class="font-medium">{{ $page->facebookLeadForms->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Webhook Status:</span>
                                    <span class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full {{ $page->webhook_subscribed ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                        <span class="font-medium text-xs {{ $page->webhook_subscribed ? 'text-green-700' : 'text-gray-500' }}">
                                            {{ $page->webhook_subscribed ? 'Subscribed' : 'Not Subscribed' }}
                                        </span>
                                    </span>
                                </div>
                                @if($page->webhook_subscribed && $page->webhook_subscribed_at)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Subscribed:</span>
                                    <span class="font-medium text-xs">{{ $page->webhook_subscribed_at->format('M d, Y') }}</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium">Aug 20, 2025</span>
                                </div>
                            </div>

                            <!-- Page Actions -->
                            <div class="flex flex-col gap-2">
                                <div class="flex gap-2">
                                    <button class="flex-1 {{ $page->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                        {{ $page->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                        Sync Forms
                                    </button>
                                </div>

                                <!-- Webhook Subscription Actions -->
                                <div class="flex gap-2">
                                    @if($page->webhook_subscribed)
                                        <button class="flex-1 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                            Unsubscribe
                                        </button>
                                    @else
                                        <button class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                            Subscribe
                                        </button>
                                    @endif
                                    
                                    <button class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                        Refresh Data
                                    </button>
                                    
                                    @if($page->facebookLeadForms->count() > 0)
                                        <a href="#" class="flex-1 block bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium py-2 px-3 rounded-lg transition-colors text-center">
                                            Forms ({{ $page->facebookLeadForms->count() }})
                                        </a>
                                    @endif
                                </div>

                                @if(!$page->webhook_subscribed)
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-2">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.5 0L4.268 6.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <div class="text-sm">
                                                <p class="text-yellow-800 font-medium">Webhook Required</p>
                                                <p class="text-yellow-700">Subscribe to webhook to receive real-time leads from this page.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-slot>

    <x-slot name="scripts">
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    toast.textContent = 'Access token copied to clipboard!';
                    document.body.appendChild(toast);
                    
                    // Remove toast after 3 seconds
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 3000);
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    toast.textContent = 'Access token copied to clipboard!';
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 3000);
                });
            }
        </script>
    </x-slot>
</x-team.layout.app>
