{{-- Student Progress Card --}}
@props([
    'title',
    'progress',
    'totalItems',
    'completedItems',
    'dueDate' => null,
    'status' => 'active' // 'active', 'completed', 'overdue'
])

@php
$progressPercentage = $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0;

$statusClasses = [
    'active' => 'border-blue-200 bg-blue-50',
    'completed' => 'border-green-200 bg-green-50',
    'overdue' => 'border-red-200 bg-red-50',
];

$statusColors = [
    'active' => 'bg-blue-500',
    'completed' => 'bg-green-500',
    'overdue' => 'bg-red-500',
];
@endphp

<div class="student-progress-card {{ $statusClasses[$status] }} border rounded-lg p-6 transition-all duration-200 hover:shadow-md">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                     {{ $status === 'completed' ? 'bg-green-100 text-green-800' : 
                        ($status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
            {{ ucfirst($status) }}
        </span>
    </div>
    
    {{-- Progress Bar --}}
    <div class="mb-4">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Progress</span>
            <span>{{ $completedItems }}/{{ $totalItems }} completed</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="{{ $statusColors[$status] }} h-2 rounded-full transition-all duration-300" 
                 style="width: {{ $progressPercentage }}%"></div>
        </div>
        <div class="text-right text-sm text-gray-600 mt-1">
            {{ round($progressPercentage) }}%
        </div>
    </div>
    
    @if($dueDate)
        <div class="flex items-center text-sm text-gray-600">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
            </svg>
            Due: {{ $dueDate }}
        </div>
    @endif
    
    @if($slot->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-gray-200">
            {{ $slot }}
        </div>
    @endif
</div>
