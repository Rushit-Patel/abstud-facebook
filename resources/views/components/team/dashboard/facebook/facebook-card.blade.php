@php
    $branchId = Auth::user()->branch_id ?? 1;
    $businessAccount = App\Models\FacebookBusinessAccount::where('branch_id', $branchId)->first();
    $isConnected = $businessAccount && $businessAccount->isConnected();
    
    // Check if Facebook credentials are configured
    $facebookConfigured = !empty(config('services.facebook.client_id')) && 
                         config('services.facebook.client_id') !== 'your_facebook_app_id' &&
                         !empty(config('services.facebook.client_secret')) && 
                         config('services.facebook.client_secret') !== 'your_facebook_app_secret';
    
    if ($isConnected) {
        $stats = app(App\Services\FacebookLeadIntegrationService::class)->getProcessingStats($businessAccount->id);
        $todayLeads = app(App\Services\FacebookLeadIntegrationService::class)->getTodayLeadsCount($businessAccount->id);
    }
@endphp

<style>
    .entry-callout-bg {
        background-image: url(/default/images/2600x1600/2.png);
    }
    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>

@if(!$isConnected)
    {{-- Not Connected State --}}
    <x-team.card class="mb-2" bodyClass="kt-card-content p-10 bg-[length:80%] rtl:[background-position:-70%_25%] [background-position:175%_25%] bg-no-repeat entry-callout-bg">
        <div class="flex flex-col justify-center gap-4">
            <!-- Facebook Icon -->
            <div class="flex -space-x-2">
                <div class="w-10 h-10 rounded-full border-2 border-white bg-blue-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Heading -->
            <h2 class="text-xl font-semibold text-gray-900">
                Connect Today & Join
                <br>
                the
                <span class="text-blue-600">Facebook Lead Network</span>
            </h2>
            
            <!-- Description -->
            <p class="text-sm font-normal text-gray-600 leading-5.5">
                Enhance your projects with premium Facebook lead
                <br>
                integration. Join the Facebook community today
                <br>
                for real-time leads and better conversions.
            </p>
            
            <!-- Setup Button -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                @if(!$facebookConfigured)
                    <div class="space-y-3">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-amber-800">Facebook App Configuration Required</span>
                            </div>
                            <p class="text-xs text-amber-700 mt-1">Please configure your Facebook App credentials in .env file</p>
                        </div>
                        <button disabled class="w-full bg-gray-400 text-white font-medium py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Configure Facebook App First</span>
                        </button>
                    </div>
                @else
                    <a href="{{ route('facebook.auth.redirect') }}" class=" bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-3 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Connect with Facebook</span>
                    </a>
                @endif
            </div>
        </div>
    </x-team.card>
@else
    {{-- Connected State --}}
    <x-team.card class="mb-2" bodyClass="kt-card-content p-6">
        <div class="flex flex-col gap-6">
            <!-- Header with Status -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $businessAccount->business_name }}</h3>
                        <p class="text-sm text-gray-600">Facebook Business Account</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full pulse-dot"></div>
                    <span class="text-sm font-medium text-green-700">Connected</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_leads'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Leads</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $todayLeads ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Today</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['success_rate'] ?? 0 }}%</div>
                    <div class="text-xs text-gray-500 mt-1">Success Rate</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <a href="{{ route('facebook.dashboard') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 text-center">
                    View Dashboard
                </a>
                <a href="{{ route('facebook.leads') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 text-center">
                    Manage Leads
                </a>
                <button data-kt-modal-trigger="#facebook-settings-modal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>

            <!-- Quick Actions -->
            @if($stats['failed'] ?? 0 > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-red-800">{{ $stats['failed'] }} failed leads need attention</span>
                        </div>
                        <a href="{{ route('facebook.leads', ['status' => 'failed']) }}" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Fix Now
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </x-team.card>
@endif

<!-- Settings Modal (only show when connected) -->
@if($isConnected)
    @include('team.facebook.modals.settings-modal', ['businessAccount' => $businessAccount])
@endif