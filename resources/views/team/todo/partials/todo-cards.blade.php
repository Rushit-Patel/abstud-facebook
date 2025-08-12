@foreach ($statusTodos as $todo)
<x-team.card 
    cardClass="todo hover:shadow-md transition-shadow duration-200 cursor-move group border-l-4 border-l-green-500"
    bodyClass="p-4"
    data-id="{{ $todo->id }}"
>
    <div class="flex justify-between items-start mb-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <i class="ki-filled ki-check-circle text-xs mr-1"></i>
                    Todo
                </span>
            </div>
            <h3 class="text-sm font-semibold truncate group-hover:text-blue-600 transition-colors">
                {{ $todo->title }}
            </h3>
            <p class="text-xs mt-1 line-clamp-2">
                {{ $todo->description }}
            </p>
        </div>
        <div class="flex items-center gap-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button data-kt-modal-toggle="#viewTodoModal" 
                    data-todo-id="{{ $todo->id }}"
                    data-todo-data="{{ base64_encode(json_encode($todo)) }}"
                    onclick="setViewDataFromElement(this)"
                    class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-md transition-colors" 
                    title="View">
                <i class="ki-filled ki-eye text-sm"></i>
            </button>
            <button data-kt-modal-toggle="#editTodoModal" 
                    data-todo-id="{{ $todo->id }}"
                    data-todo-data="{{ base64_encode(json_encode($todo)) }}"
                    data-action-url="{{ route('team.todos.update', $todo->id) }}"
                    onclick="setEditDataFromElement(this)"
                    class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors" 
                    title="Edit">
                <i class="ki-filled ki-pencil text-sm"></i>
            </button>
            <button data-kt-modal-toggle="#deleteTodoModal"
                    onclick="setDeleteId({{ $todo->id }})"
                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                    title="Delete">
                <i class="ki-filled ki-trash text-sm"></i>
            </button>
        </div>
    </div>
    
    <!-- Assignment and Due Date Info -->
    @if($todo->due_date && $todo->due_date->format('Y-m-d') !== date('Y-m-d'))
        <div class="flex items-center gap-1 mb-2 text-xs">
            <i class="ki-filled ki-calendar-2 text-orange-500"></i>
            <span class="text-orange-600 font-medium">
                Due: {{ $todo->due_date->format('M d, Y') }}
                @if($todo->due_date->isPast())
                    <span class="text-red-500 font-semibold">(Overdue)</span>
                @elseif($todo->due_date->isToday())
                    <span class="text-amber-500 font-semibold">(Today)</span>
                @elseif($todo->due_date->isTomorrow())
                    <span class="text-blue-500 font-semibold">(Tomorrow)</span>
                @endif
            </span>
        </div>
    @endif

    @if($todo->user_id !== $todo->added_by)
        <div class="flex items-center gap-1 mb-2 text-xs">
            <i class="ki-filled ki-user text-purple-500"></i>
            <span class="text-purple-600">
                Assigned to: <span class="font-medium">{{ $todo->assignedUser->name ?? 'Unknown' }}</span>
            </span>
        </div>
    @endif
    
    <!-- Enhanced Metadata -->
    <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
        <div class="flex items-center gap-1">
            <i class="ki-filled ki-user-plus text-xs"></i>
            <span>By: {{ $todo->addedByUser->name ?? 'Unknown' }}</span>
        </div>
        <div class="flex items-center gap-1">
            <i class="ki-filled ki-time text-xs"></i>
            <span>{{ $todo->updated_at->diffForHumans() }}</span>
        </div>
    </div>
</x-team.card>
@endforeach
