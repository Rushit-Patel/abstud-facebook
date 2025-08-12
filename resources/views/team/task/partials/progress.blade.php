@php
    $progress = $task->progress ?? 0;
    $progressColor = match(true) {
        $progress >= 80 => 'bg-green-500',
        $progress >= 60 => 'bg-blue-500',
        $progress >= 40 => 'bg-yellow-500',
        $progress >= 20 => 'bg-orange-500',
        default => 'bg-red-500'
    };
@endphp

<div class="flex items-center gap-2 w-full">
    <div class="flex-1 bg-gray-200 rounded-full h-2">
        <div class="h-2 rounded-full transition-all duration-300 {{ $progressColor }}" 
             style="width: {{ $progress }}%"></div>
    </div>
    <span class="text-xs font-medium text-secondary-foreground min-w-[35px] text-right">
        {{ $progress }}%
    </span>
</div>
