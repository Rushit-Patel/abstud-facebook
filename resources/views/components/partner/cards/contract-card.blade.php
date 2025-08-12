{{-- Partner Contract Card --}}
@props([
    'contractTitle',
    'partnerName',
    'startDate',
    'endDate',
    'value',
    'status' => 'active', // 'active', 'pending', 'expired', 'terminated'
    'renewalDate' => null
])

@php
$statusClasses = [
    'active' => 'border-green-200 bg-green-50',
    'pending' => 'border-yellow-200 bg-yellow-50',
    'expired' => 'border-red-200 bg-red-50',
    'terminated' => 'border-gray-200 bg-gray-50',
];

$statusBadges = [
    'active' => 'bg-green-100 text-green-800',
    'pending' => 'bg-yellow-100 text-yellow-800',
    'expired' => 'bg-red-100 text-red-800',
    'terminated' => 'bg-gray-100 text-gray-800',
];
@endphp

<div class="partner-contract-card {{ $statusClasses[$status] }} border rounded-lg p-6 transition-all duration-200 hover:shadow-lg">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $contractTitle }}</h3>
            <p class="text-sm text-gray-600">{{ $partnerName }}</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadges[$status] }}">
            {{ ucfirst($status) }}
        </span>
    </div>
    
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <p class="text-sm text-gray-500">Start Date</p>
            <p class="text-sm font-medium text-gray-900">{{ $startDate }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">End Date</p>
            <p class="text-sm font-medium text-gray-900">{{ $endDate }}</p>
        </div>
    </div>
    
    <div class="mb-4">
        <p class="text-sm text-gray-500">Contract Value</p>
        <p class="text-lg font-bold text-blue-600">{{ $value }}</p>
    </div>
    
    @if($renewalDate && $status === 'active')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-blue-700">Renewal due: {{ $renewalDate }}</span>
            </div>
        </div>
    @endif
    
    <div class="flex space-x-3">
        <x-shared.ui.button variant="primary" size="sm">View Details</x-shared.ui.button>
        <x-shared.ui.button variant="secondary" size="sm">Edit Contract</x-shared.ui.button>
    </div>
    
    @if($slot->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-gray-200">
            {{ $slot }}
        </div>
    @endif
</div>
