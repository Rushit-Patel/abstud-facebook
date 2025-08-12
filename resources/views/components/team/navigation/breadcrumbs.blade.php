{{-- Team Breadcrumbs Component --}}
@props([
    'breadcrumbs' => [],
    'homeRoute' => 'team.dashboard',
    'homeLabel' => 'Dashboard',
    'currentPage' => null,
    'showHome' => true,
    'separator' => 'chevron', // 'chevron', 'slash', 'arrow'
    'size' => 'md' // 'sm', 'md', 'lg'
])

@php
$sizeClasses = [
    'sm' => 'px-4 py-2 text-xs',
    'md' => 'px-6 py-3 text-sm', 
    'lg' => 'px-8 py-4 text-base'
];

$separators = [
    'chevron' => '<svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
    'slash' => '<span class="text-gray-400 mx-2">/</span>',
    'arrow' => '<svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>'
];
@endphp

<nav class="team-breadcrumbs bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 {{ $sizeClasses[$size] }}" aria-label="Breadcrumb">
    <div class="flex items-center space-x-1">
        
        @if($showHome)
            {{-- Home/Dashboard Link --}}
            <a href="{{ route($homeRoute) }}" 
               class="team-breadcrumb-home inline-flex items-center text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-200 group">
                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                @if($size !== 'sm')
                    <span class="ml-1 hidden sm:inline">{{ $homeLabel }}</span>
                @endif
                <span class="sr-only">{{ $homeLabel }}</span>
            </a>
        @endif

        @if(count($breadcrumbs) > 0)
            @foreach($breadcrumbs as $index => $breadcrumb)
                {{-- Separator --}}
                {!! $separators[$separator] !!}

                @if(isset($breadcrumb['url']) && !$loop->last)
                    {{-- Linked Breadcrumb --}}
                    <a href="{{ $breadcrumb['url'] }}" 
                       class="team-breadcrumb-link text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-200 font-medium hover:underline">
                        @if(isset($breadcrumb['icon']))
                            {!! $breadcrumb['icon'] !!}
                        @endif
                        {{ $breadcrumb['title'] }}
                    </a>
                @else
                    {{-- Current Page (not linked) --}}
                    <span class="team-breadcrumb-current text-gray-900 dark:text-white font-semibold flex items-center">
                        @if(isset($breadcrumb['icon']))
                            {!! $breadcrumb['icon'] !!}
                        @endif
                        {{ $breadcrumb['title'] }}
                    </span>
                @endif
            @endforeach
        @elseif($currentPage)
            {{-- Fallback: Show current page if no breadcrumbs --}}
            {!! $separators[$separator] !!}
            <span class="team-breadcrumb-current text-gray-900 dark:text-white font-semibold">
                {{ $currentPage }}
            </span>
        @endif

        {{-- Optional: Add page actions slot --}}
        @if(isset($actions))
            <div class="ml-auto flex items-center space-x-2">
                {{ $actions }}
            </div>
        @endif
    </div>
    
    {{-- Optional: Show breadcrumb metadata --}}
    @if(isset($metadata))
        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ $metadata }}
        </div>
    @endif
</nav>
