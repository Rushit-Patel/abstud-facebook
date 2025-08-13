@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration Overview']
    ];
@endphp

<x-team.layout.app title="Facebook Integration Overview" :breadcrumbs="$breadcrumbs">
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
                        <h1 class="text-2xl font-bold text-gray-900">Facebook Integration</h1>
                        <p class="text-gray-600">Integration status and configuration overview</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('facebook.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Full Dashboard
                    </a>
                </div>
            </div>

            <!-- Integration Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Facebook App Configuration -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 {{ $overview['facebook_configured'] ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                            @if($overview['facebook_configured'])
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $overview['facebook_configured'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $overview['facebook_configured'] ? 'Configured' : 'Not Configured' }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Facebook App</h3>
                    <p class="text-sm text-gray-600">App ID and Secret configuration</p>
                </div>

                <!-- Webhook Configuration -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 {{ $overview['webhook_configured'] ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                            @if($overview['webhook_configured'])
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $overview['webhook_configured'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $overview['webhook_configured'] ? 'Configured' : 'Not Configured' }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Webhook</h3>
                    <p class="text-sm text-gray-600">Real-time lead notifications</p>
                    @if($overview['webhook_configured'])
                        <div class="mt-2">
                            <a href="{{ route('facebook.webhook-settings') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View Settings →</a>
                        </div>
                    @endif
                </div>

                <!-- Business Account -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 {{ $overview['business_account_connected'] ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                            @if($overview['business_account_connected'])
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $overview['business_account_connected'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $overview['business_account_connected'] ? 'Connected' : 'Not Connected' }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Business Account</h3>
                    <p class="text-sm text-gray-600">Facebook Business connection</p>
                </div>

                <!-- Pages Subscribed -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 {{ $overview['subscribed_pages'] > 0 ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 {{ $overview['subscribed_pages'] > 0 ? 'text-green-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ $overview['subscribed_pages'] }}/{{ $overview['total_pages'] }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Pages Subscribed</h3>
                    <p class="text-sm text-gray-600">Webhook subscriptions</p>
                </div>
            </div>

            <!-- Statistics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Pages</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $overview['total_pages'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    @if($overview['total_pages'] > 0)
                        <div class="mt-2">
                            <a href="{{ route('facebook.pages') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Manage Pages →</a>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Lead Forms</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $overview['total_forms'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    @if($overview['total_forms'] > 0)
                        <div class="mt-2">
                            <a href="{{ route('facebook.lead-forms') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View Forms →</a>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Leads</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($overview['total_leads']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    @if($overview['total_leads'] > 0)
                        <div class="mt-2">
                            <a href="{{ route('facebook.leads') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">View Leads →</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @if(!$overview['facebook_configured'])
                        <a href="#" onclick="alert('Please configure Facebook App ID and Secret in your .env file')" class="flex items-center gap-3 p-4 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Configure Facebook App</p>
                                <p class="text-sm text-gray-600">Set up App ID and Secret</p>
                            </div>
                        </a>
                    @endif

                    @if(!$overview['webhook_configured'])
                        <a href="{{ route('facebook.webhook-settings') }}" class="flex items-center gap-3 p-4 border border-orange-200 rounded-lg hover:bg-orange-50 transition-colors">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Setup Webhook</p>
                                <p class="text-sm text-gray-600">Configure real-time notifications</p>
                            </div>
                        </a>
                    @endif

                    @if(!$overview['business_account_connected'])
                        <a href="{{ route('facebook.auth.redirect') }}" class="flex items-center gap-3 p-4 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Connect Facebook</p>
                                <p class="text-sm text-gray-600">Link your business account</p>
                            </div>
                        </a>
                    @endif

                    @if($overview['business_account_connected'] && $overview['total_pages'] > 0 && $overview['subscribed_pages'] < $overview['total_pages'])
                        <a href="{{ route('facebook.pages') }}" class="flex items-center gap-3 p-4 border border-purple-200 rounded-lg hover:bg-purple-50 transition-colors">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.5 5H9l5 5H4.5V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Subscribe Pages</p>
                                <p class="text-sm text-gray-600">Enable webhook for pages</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>
