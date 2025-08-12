{{-- Team Sidebar Link --}}
@props([
    'route',
    'active' => null,
    'icon' => null,
    'badge' => null,
    'external' => false
])

@php
$activeRoute = $active ?? $route;
$isActive = request()->routeIs($activeRoute);
$url = $external ? $route : route($route);

$linkClasses = $isActive 
    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-500'
    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white';
@endphp

<a href="{{ $url }}" 
   @click="window.innerWidth < 1024 && toggleSidebar()"
   class="team-sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $linkClasses }}"
   @if($isActive) aria-current="page" @endif>
    
    @if($icon)
        <span class="mr-3 flex-shrink-0 text-current">
            {!! $icon !!}
        </span>
    @endif
    
    <span class="flex-1 truncate">{{ $slot }}</span>
    
    @if($badge)
        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                     {{ $isActive ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
            {{ $badge }}
        </span>
    @endif
    
    @if($external)
        <svg class="ml-2 w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
        </svg>
    @endif
</a>
