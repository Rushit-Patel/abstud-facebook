@php
    $statusName = $task->status?->name ?? 'No Status';
    $statusColor = $task->status?->color ?? '#6B7280';
    $badgeClass = match($statusName) {
        'To Do' => 'bg-gray-100 text-gray-800 border-gray-200',
        'In Progress' => 'bg-blue-100 text-blue-800 border-blue-200',
        'Review' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'Completed' => 'bg-green-100 text-green-800 border-green-200',
        'On Hold' => 'bg-orange-100 text-orange-800 border-orange-200',
        'Cancelled' => 'bg-red-100 text-red-800 border-red-200',
        default => 'bg-gray-100 text-gray-800 border-gray-200'
    };
@endphp

<div class="flex items-center gap-2">
    <div class="w-2 h-2 rounded-full" style="background-color: {{ $statusColor }}"></div>
    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border {{ $badgeClass }}">
        {{ $statusName }}
    </span>
</div>
