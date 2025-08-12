{{-- Team Logo Component --}}
@props([
    'compact' => false,
    'size' => 'md',
    'showText' => true,
    'companyData' => []
])

@php
$logoSizes = [
    'sm' => 'h-6',
    'md' => 'h-8', 
    'lg' => 'h-10',
    'xl' => 'h-12'
];

$company = $companyData ?: [
    'name' => config('app.name', 'AbstudERP'),
    'logo' => null
];
@endphp

<div class="team-logo-container flex items-center">
    @if($company['logo'])
        <img src="{{ $company['logo'] }}" 
             alt="{{ $company['name'] }}" 
             class="{{ $logoSizes[$size] }} w-auto object-contain">
    @else
        {{-- Default logo if no image provided --}}
        <div class="team-logo-default {{ $logoSizes[$size] }} aspect-square bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
            <svg class="w-1/2 h-1/2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
    @endif
    
    @if($showText && !$compact)
        <span class="ml-3 text-lg font-semibold text-gray-900 dark:text-white hidden sm:block">
            {{ $company['name'] }}
        </span>
    @elseif($showText && $compact)
        <span class="ml-2 text-lg font-medium text-gray-900 dark:text-white hidden md:block">
            {{ $company['name'] }}
        </span>
    @endif
</div>
