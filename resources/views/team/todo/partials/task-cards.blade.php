@foreach ($statusTasks as $task)
<x-team.card 
    cardClass="task hover:shadow-md transition-shadow duration-200 cursor-move group border-l-4 border-l-blue-500 relative"
    bodyClass="p-4"
    data-id="{{ $task->id }}"
    data-type="task"
>
    <!-- Status Indicator -->
    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
        @php
            $statusColor = match($task->status->slug ?? 'pending') {
                'completed' => 'bg-green-500',
                'in-progress', 'review' => 'bg-blue-500',
                'pending', 'to-do' => 'bg-gray-400',
                default => 'bg-gray-400'
            };
        @endphp
        <div class="w-3 h-3 rounded-full {{ $statusColor }} border-2 border-white shadow-sm" 
             title="Status: {{ $task->status->name ?? 'Unknown' }}"></div>
    </div>

    <div class="flex justify-between items-start mb-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="ki-filled ki-setting-2 text-xs mr-1"></i>
                    Task
                </span>
                @if($task->priority)
                    @php
                        $priorityClass = match($task->priority->name) {
                            'High' => 'bg-red-100 text-red-800 border-red-200',
                            'Medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'Low' => 'bg-green-100 text-green-800 border-green-200',
                            'Critical' => 'bg-purple-100 text-purple-800 border-purple-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $priorityClass }}">
                        <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $task->priority->color }}"></div>
                        {{ $task->priority->name }}
                    </span>
                @endif
            </div>
            <h3 class="text-sm font-semibold truncate group-hover:text-blue-600 transition-colors">
                {{ $task->title }}
            </h3>
            <p class="text-xs mt-1 line-clamp-2 text-gray-600">
                {{ $task->description ?? 'No description provided' }}
            </p>
        </div>
        <div class="flex items-center gap-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('team.task.show', $task->id) }}"
                class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-md transition-colors" 
                title="View Task">
                <i class="ki-filled ki-eye text-sm"></i>
            </a>
            <a href="{{ route('team.task.edit', $task->id) }}"
                class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors" 
                title="Edit Task">
                <i class="ki-filled ki-pencil text-sm"></i>
            </a>
        </div>
    </div>
    
    <!-- Task Category -->
    @if($task->category)
        <div class="mb-2">
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border"
                  style="background-color: {{ $task->category->color }}20; color: {{ $task->category->color }}; border-color: {{ $task->category->color }}40;">
                <i class="ki-filled ki-category text-xs mr-1"></i>
                {{ $task->category->name }}
            </span>
        </div>
    @endif

    <!-- Task Status Display -->
    <div class="mb-2">
        @php
            $statusClass = match($task->status->slug ?? 'pending') {
                'completed' => 'bg-green-100 text-green-800 border-green-200',
                'in-progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                'review' => 'bg-purple-100 text-purple-800 border-purple-200',
                'pending', 'to-do' => 'bg-gray-100 text-gray-800 border-gray-200',
                default => 'bg-gray-100 text-gray-800 border-gray-200'
            };
            $statusIcon = match($task->status->slug ?? 'pending') {
                'completed' => 'ki-check-circle',
                'in-progress' => 'ki-timer',
                'review' => 'ki-eye',
                'pending', 'to-do' => 'ki-plus-circle',
                default => 'ki-plus-circle'
            };
        @endphp
        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border {{ $statusClass }}">
            <i class="ki-filled {{ $statusIcon }} text-xs mr-1"></i>
            {{ $task->status->name ?? 'Unknown Status' }}
        </span>
    </div>

    <!-- Due Date Info -->
    @if($task->due_date)
        <div class="flex items-center gap-1 mb-2 text-xs">
            <i class="ki-filled ki-calendar-2 text-orange-500"></i>
            <span class="text-orange-600 font-medium">
                Due: {{ $task->due_date->format('M d, Y') }}
                @if($task->due_date->isPast() && !$task->is_completed)
                    <span class="text-red-500 font-semibold">(Overdue)</span>
                @elseif($task->due_date->isToday())
                    <span class="text-amber-500 font-semibold">(Today)</span>
                @elseif($task->due_date->isTomorrow())
                    <span class="text-blue-500 font-semibold">(Tomorrow)</span>
                @endif
            </span>
        </div>
    @endif

    <!-- Assignees -->
    @if($task->assignees && $task->assignees->count() > 0)
        <div class="flex items-center gap-2 mb-2">
            <div class="flex -space-x-1">
                @foreach($task->assignees->take(3) as $assignee)
                    <div class="flex items-center justify-center w-6 h-6 bg-blue-500/10 rounded-full border-2 border-white shadow-sm" 
                         title="{{ $assignee->name }}">
                        <span class="text-xs font-medium text-blue-600">
                            {{ strtoupper(substr($assignee->name, 0, 2)) }}
                        </span>
                    </div>
                @endforeach
                @if($task->assignees->count() > 3)
                    <div class="flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full border-2 border-white">
                        <span class="text-xs font-medium text-gray-600">+{{ $task->assignees->count() - 3 }}</span>
                    </div>
                @endif
            </div>
            <span class="text-xs text-gray-500">
                {{ $task->assignees->count() === 1 ? $task->assignees->first()->name : $task->assignees->count() . ' assignees' }}
            </span>
        </div>
    @endif
    
    <!-- Progress Bar (if progress is set) -->
    @if($task->progress > 0)
        <div class="mb-2">
            <div class="flex items-center justify-between text-xs mb-1">
                <span class="text-gray-600">Progress</span>
                <span class="font-medium">{{ $task->progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300" 
                     style="width: {{ $task->progress }}%"></div>
            </div>
        </div>
    @endif
    
    <!-- Estimated Hours -->
    @if($task->estimated_hours)
        <div class="flex items-center gap-1 mb-2 text-xs">
            <i class="ki-filled ki-time text-blue-500"></i>
            <span class="text-blue-600 font-medium">
                {{ $task->estimated_hours }}h estimated
            </span>
            @if($task->actual_hours && $task->actual_hours > 0)
                <span class="text-gray-500">
                    ({{ $task->actual_hours }}h logged)
                </span>
            @endif
        </div>
    @endif
    
    <!-- Enhanced Metadata -->
    <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
        <div class="flex items-center gap-1">
            <i class="ki-filled ki-user-plus text-xs"></i>
            <span>By: {{ $task->creator->name ?? 'Unknown' }}</span>
        </div>
        <div class="flex items-center gap-1">
            <i class="ki-filled ki-time text-xs"></i>
            <span>{{ $task->updated_at->diffForHumans() }}</span>
        </div>
    </div>
</x-team.card>
@endforeach
