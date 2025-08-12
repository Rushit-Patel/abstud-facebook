@if($task->assignees->count() > 0)
    <div class="flex items-center gap-1">
        @foreach($task->assignees->take(3) as $assignee)
            <div class="flex items-center justify-center w-8 h-8 bg-primary/10 rounded-full border-2 border-white shadow-sm" 
                 title="{{ $assignee->name }}">
                <span class="text-xs font-medium text-primary">
                    {{ strtoupper(substr($assignee->name, 0, 2)) }}
                </span>
            </div>
        @endforeach
        @if($task->assignees->count() > 3)
            <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full border-2 border-white">
                <span class="text-xs font-medium text-gray-600">+{{ $task->assignees->count() - 3 }}</span>
            </div>
        @endif
    </div>
    <div class="mt-1">
        <span class="text-xs text-muted-foreground">
            {{ $task->assignees->pluck('name')->take(2)->join(', ') }}
            @if($task->assignees->count() > 2)
                <span class="text-muted-foreground">& {{ $task->assignees->count() - 2 }} more</span>
            @endif
        </span>
    </div>
@else
    <span class="text-xs text-muted-foreground">No assignees</span>
@endif
