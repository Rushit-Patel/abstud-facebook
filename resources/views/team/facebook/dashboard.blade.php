@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Dashboard']
    ];
@endphp

<x-team.layout.app title="Facebook Integration - Dashboard" :breadcrumbs="$breadcrumbs">
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
                        <h1 class="text-2xl font-semibold text-gray-900">Facebook Lead Integration</h1>
                        <p class="text-gray-600">Monitor and manage your Facebook leads</p>
                    </div>
                </div>
                
                @if($businessAccount)
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 bg-green-50 px-3 py-2 rounded-lg">
                            <div class="w-3 h-3 bg-green-500 rounded-full pulse-dot"></div>
                            <span class="text-sm font-medium text-green-700">Connected</span>
                        </div>
                        <button data-kt-modal-trigger="#facebook-settings-modal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </button>
                    </div>
                @else
                    <a href="{{ route('facebook.auth.redirect') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 inline-flex items-center">
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Connect with Facebook
                    </a>
                @endif
            </div>

            @if(!$businessAccount)
                <!-- Not Connected State -->
                <div class="grid gap-6 mb-8">
                    <x-team.card>
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Facebook Integration</h2>
                            <p class="text-gray-600 mb-6">Connect your Facebook Business account to start receiving leads automatically</p>
                            
                            <button data-kt-modal-trigger="#facebook-setup-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 inline-flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Get Started</span>
                            </button>
                        </div>
                    </x-team.card>
                </div>
            @else
                <!-- Connected State Dashboard -->
                <div class="grid gap-6 mb-8">
                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Leads -->
                        <x-team.card bodyClass="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Leads</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_leads'] ?? 0 }}</p>
                                    <p class="text-sm text-green-600 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        All time
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Today's Leads -->
                        <x-team.card bodyClass="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Today's Leads</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $todayLeads ?? 0 }}</p>
                                    <p class="text-sm text-blue-600 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Last 24 hours
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Success Rate -->
                        <x-team.card bodyClass="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Success Rate</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $stats['success_rate'] ?? 0 }}%</p>
                                    <p class="text-sm text-orange-600 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Processing rate
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Failed Leads -->
                        <x-team.card bodyClass="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Failed Leads</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $stats['failed'] ?? 0 }}</p>
                                    <p class="text-sm text-red-600 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Need attention
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Facebook Pages Overview -->
                    @if($pages->isNotEmpty())
                    <x-team.card title="Facebook Pages" titleClass="text-lg font-semibold">
                        <x-slot name="header">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Facebook Pages</h3>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('facebook.business-account.sync-pages') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Sync Pages
                                        </button>
                                    </form>
                                    <span class="text-gray-400">|</span>
                                    <a href="{{ route('facebook.pages') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View All
                                    </a>
                                </div>
                            </div>
                        </x-slot>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($pages as $page)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        @if($page->profile_picture_url)
                                            <img src="{{ $page->profile_picture_url }}" alt="{{ $page->page_name }}" class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-medium text-gray-900 truncate">{{ $page->page_name }}</h4>
                                                <span class="flex-shrink-0 w-3 h-3 {{ $page->is_active ? 'bg-green-500' : 'bg-gray-300' }} rounded-full"></span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $page->page_category ?? 'Facebook Page' }}</p>
                                            <div class="flex items-center justify-between mt-2">
                                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                                    @if($page->fan_count)
                                                        <span>{{ number_format($page->fan_count) }} followers</span>
                                                    @endif
                                                    <span>{{ $page->facebook_lead_forms_count }} forms</span>
                                                </div>
                                                <a href="{{ route('facebook.pages') }}?page_id={{ $page->id }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                    View →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($pages->count() === 0)
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-gray-600 mb-2">No Facebook pages found</p>
                                <p class="text-sm text-gray-500 mb-4">Sync your Facebook pages to start managing lead forms</p>
                                <form action="{{ route('facebook.business-account.sync-pages') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                        Sync Pages Now
                                    </button>
                                </form>
                            </div>
                        @endif
                    </x-team.card>
                    @endif

                    <!-- Recent Leads and Quick Actions -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Leads -->
                        <x-team.card title="Recent Leads" titleClass="text-lg font-semibold">
                            <x-slot name="header">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Recent Leads</h3>
                                    <a href="{{ route('facebook.leads') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View All
                                    </a>
                                </div>
                            </x-slot>
                            
                            <div class="space-y-4">
                                @forelse($recentLeads ?? [] as $lead)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-700">
                                                    {{ substr($lead['name'], 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $lead['name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $lead['email'] }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-xs font-medium bg-{{ $lead['status'] === 'processed' ? 'green' : ($lead['status'] === 'failed' ? 'red' : 'yellow') }}-100 text-{{ $lead['status'] === 'processed' ? 'green' : ($lead['status'] === 'failed' ? 'red' : 'yellow') }}-800 rounded-full">
                                                {{ ucfirst($lead['status']) }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">{{ Carbon\Carbon::parse($lead['created_at'])->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-gray-600">No leads yet</p>
                                        <p class="text-sm text-gray-500">Your Facebook leads will appear here</p>
                                    </div>
                                @endforelse
                            </div>
                        </x-team.card>

                        <!-- Quick Actions -->
                        <x-team.card title="Quick Actions" titleClass="text-lg font-semibold">
                            <div class="space-y-4">
                                <!-- Facebook Account Info -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-blue-900">{{ $businessAccount->business_name }}</h4>
                                            <p class="text-sm text-blue-700">Connected Business Account</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-1 gap-3">
                                    <a href="{{ route('facebook.leads') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Manage Leads</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('facebook.lead-forms') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Lead Forms</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('facebook.pages') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            <span class="font-medium text-gray-900">Facebook Pages</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('facebook.system-variables') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">System Variables</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <button data-kt-modal-trigger="#facebook-settings-modal" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-left w-full">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Settings</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </x-team.card>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <!-- Include Modals -->
    @if(!$businessAccount)
        @include('team.facebook.modals.setup-modal')
    @else
        @include('team.facebook.modals.settings-modal', ['businessAccount' => $businessAccount])
    @endif
</x-team.layout.app>

<style>
.pulse-dot {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
</style>
