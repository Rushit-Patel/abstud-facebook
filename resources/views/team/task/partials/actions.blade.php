@can('task:view')
    <div class="flex items-center gap-1">
        <a href="{{ route('team.task.show', $task) }}" 
           class="inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" 
           title="View Task">
            <i class="ki-filled ki-eye text-sm"></i>
        </a>
        
        @can('task:edit')
            <a href="{{ route('team.task.edit', $task) }}" 
               class="inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
               title="Edit Task">
                <i class="ki-filled ki-notepad-edit text-sm"></i>
            </a>
        @endcan
        
        @can('task:delete')
            <button type="button" 
                    class="inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                    title="Delete Task"
                    onclick="deleteTask({{ $task->id }})">
                <i class="ki-filled ki-trash text-sm"></i>
            </button>
        @endcan
    </div>
@endcan
