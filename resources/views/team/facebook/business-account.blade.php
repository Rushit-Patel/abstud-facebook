@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Business Account']
    ];
@endphp

<x-team.layout.app title="Facebook Integration - Business Account" :breadcrumbs="$breadcrumbs">
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
                        <h1 class="text-2xl font-bold text-gray-900">Business Account Management</h1>
                        <p class="text-gray-600">Configure and manage your Facebook Business Account</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('facebook.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            @if($businessAccount)
                <!-- Connected Account Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Connected Account</h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600">Business Name:</span>
                                    <span class="font-medium">{{ $businessAccount->business_name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $businessAccount->status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($businessAccount->status) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600">Connected:</span>
                                    <span class="text-sm">{{ $businessAccount->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                @if($businessAccount->token_expires_at)
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600">Token Expires:</span>
                                    <span class="text-sm">{{ $businessAccount->token_expires_at->format('M d, Y H:i') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="space-x-2">
                            <form action="{{ route('facebook.business-account.refresh-token') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="access_token" value="{{ $businessAccount->access_token }}">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Refresh Token
                                </button>
                            </form>
                            <form action="{{ route('facebook.business-account.disconnect') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                        onclick="return confirm('Are you sure you want to disconnect this account?')">
                                    Disconnect
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Facebook Business ID</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <code class="text-sm">{{ $businessAccount->facebook_business_id }}</code>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">App ID</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <code class="text-sm">{{ $businessAccount->app_id }}</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ route('facebook.pages') }}" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">Manage Pages</span>
                            </a>
                            <a href="{{ route('facebook.lead-forms') }}" class="flex items-center gap-3 p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">Lead Forms</span>
                            </a>
                            <a href="{{ route('facebook.leads') }}" class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">View Leads</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Account Connected -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Business Account Connected</h3>
                    <p class="text-gray-600 mb-6">Connect your Facebook Business Account to start receiving leads from Facebook Lead Ads.</p>
                    <a href="{{ route('facebook.auth.redirect') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Connect Facebook Account
                    </a>
                </div>
            @endif
        </div>
    </x-slot>
</x-team.layout.app>
