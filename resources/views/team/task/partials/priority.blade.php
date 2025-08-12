@php
    $priorityName = $task->priority?->name ?? 'No Priority';
    $priorityColor = $task->priority?->color ?? '#6B7280';
    $badgeClass = match($priorityName) {
        'High' => 'bg-red-100 text-red-800 border-red-200',
        'Medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'Low' => 'bg-green-100 text-green-800 border-green-200',
        'Critical' => 'bg-purple-100 text-purple-800 border-purple-200',
        default => 'bg-gray-100 text-gray-800 border-gray-200'
    };
@endphp

<div class="flex items-center gap-2">
    <div class="w-2 h-2 rounded-full" style="background-color: {{ $priorityColor }}"></div>
    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border {{ $badgeClass }}">
        {{ $priorityName }}
    </span>
</div>
