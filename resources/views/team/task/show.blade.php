@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Task Management', 'url' => route('team.task.index')],
    ['title' => $task->title]
];
@endphp

@push('styles')
    <style>
        .priority-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring-circle {
            stroke-dasharray: 251;
            stroke-dashoffset: 251;
            transition: stroke-dashoffset 0.35s;
        }
        .comment-item {
            border-left: 3px solid #e5e7eb;
            padding-left: 1rem;
            margin-left: 0.5rem;
        }
        .comment-item.reply {
            border-left-color: #3b82f6;
            margin-left: 2rem;
        }
        .timeline-item {
            position: relative;
            padding-left: 2.5rem;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 2rem;
            bottom: -1.5rem;
            width: 2px;
            background: linear-gradient(180deg, #e5e7eb 0%, #f3f4f6 100%);
        }
        .timeline-item:last-child::before {
            display: none;
        }
        .timeline-dot {
            position: absolute;
            left: 0.75rem;
            top: 1rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: 3px solid white;
            box-shadow: 0 0 0 1px #e5e7eb, 0 2px 4px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .timeline-content {
            margin-left: 0.5rem;
        }
        .timeline-container {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .timeline-container::-webkit-scrollbar {
            width: 6px;
        }
        .timeline-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .timeline-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .timeline-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .replying-to {
            background-color: #dbeafe !important;
            border-left-color: #3b82f6 !important;
        }
    </style>
@endpush

<x-team.layout.app title="{{ $task->title }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <div class="flex items-center gap-3">
                        <span class="priority-indicator" style="background-color: {{ $task->priority->color ?? '#6B7280' }}"></span>
                        <h1 class="text-xl font-medium leading-none text-mono">
                            {{ $task->title }}
                        </h1>
                        <span class="status-badge" style="background-color: {{ $task->status->color }}20; color: {{ $task->status->color }};">
                            {{ $task->status->name }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-secondary-foreground">
                        @if($task->category)
                            <span class="flex items-center gap-1">
                                <i class="ki-filled ki-category"></i>
                                {{ $task->category->name }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <i class="ki-filled ki-user"></i>
                            Created by {{ $task->creator->name }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="ki-filled ki-calendar"></i>
                            {{ $task->created_at->format('M d, Y H:i') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    @if(!$task->is_archived)
                        <form action="{{ route('team.task.toggle-archive', $task) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="kt-btn kt-btn-secondary">
                                <i class="ki-filled ki-archive"></i>
                                Archive
                            </button>
                        </form>
                    @else
                        <form action="{{ route('team.task.toggle-archive', $task) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="kt-btn kt-btn-warning">
                                <i class="ki-filled ki-questionnaire-tablet"></i>
                                Unarchive
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('team.task.edit', $task) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-notepad-edit"></i>
                        Edit Task
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-7.5">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-7.5">
                    <!-- Task Description -->
                    <x-team.card title="Description" headerClass="">
                        @if($task->description)
                            <div class="prose max-w-none">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        @else
                            <p class="text-secondary-foreground italic">No description provided.</p>
                        @endif
                    </x-team.card>

                    <!-- Comments Section -->
                    <x-team.card title="Comments" headerClass="">
                        <!-- Add Comment Form -->
                        <form action="{{ route('team.task.store-comment', $task) }}" method="POST" class="mb-6 p-4 bg-gray-50 rounded-lg">
                            @csrf
                            <input type="hidden" id="parentId" name="parent_id" value="">
                            <div class="space-y-3">
                                <x-team.forms.textarea
                                    id="commentContent"
                                    class="kt-input"
                                    name="content"
                                    label="Add a comment"
                                    rows="3"
                                    placeholder="Add a comment..."
                                    required
                                />
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" id="isInternal" name="is_internal" value="1" class="kt-checkbox">
                                            <span class="text-sm">Internal comment</span>
                                        </label>
                                        <span id="replyIndicator" class="text-xs text-blue-600 hidden">
                                            Replying to comment... <button type="button" onclick="cancelReply()" class="text-red-600 underline">Cancel</button>
                                        </span>
                                    </div>
                                    <button type="submit" class="kt-btn kt-btn-sm kt-btn-primary">
                                        <i class="ki-filled ki-plus"></i>
                                        Add Comment
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Comments List -->
                        <div id="commentsList">
                            @foreach($task->comments->whereNull('parent_id') as $comment)
                                <div class="comment-item mb-4" data-comment-id="{{ $comment->id }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-primary">
                                                {{ substr($comment->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-medium text-sm">{{ $comment->user->name }}</span>
                                                @if($comment->is_internal)
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Internal</span>
                                                @endif
                                                <span class="text-xs text-secondary-foreground">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-mono mb-2">
                                                {!! nl2br(e($comment->content)) !!}
                                            </div>
                                            <button 
                                                onclick="replyToComment({{ $comment->id }})" 
                                                class="text-xs text-primary hover:underline"
                                            >
                                                Reply
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Replies -->
                                    @foreach($comment->replies as $reply)
                                        <div class="comment-item reply mt-3" data-comment-id="{{ $reply->id }}">
                                            <div class="flex items-start gap-3">
                                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-medium text-blue-600">
                                                        {{ substr($reply->user->name, 0, 2) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-medium text-sm">{{ $reply->user->name }}</span>
                                                        @if($reply->is_internal)
                                                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Internal</span>
                                                        @endif
                                                        <span class="text-xs text-secondary-foreground">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-mono">
                                                        {!! nl2br(e($reply->content)) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            @if($task->comments->isEmpty())
                                <div class="text-center py-8 text-secondary-foreground">
                                    <i class="ki-filled ki-message-question text-2xl mb-2"></i>
                                    <p>No comments yet. Be the first to comment!</p>
                                </div>
                            @endif
                        </div>
                    </x-team.card>

                    <!-- Activity Timeline -->
                    <x-team.card title="Activity Timeline" headerClass="">
                        <div class="timeline-container max-h-96 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                            <div class="space-y-4 relative">
                                @foreach($task->activityLogs as $log)
                                    <div class="timeline-item group">
                                        <div class="timeline-dot group-hover:scale-110 transition-transform duration-200"></div>
                                        <div class="timeline-content bg-white rounded-lg border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3 flex-1">
                                                    <!-- User Avatar -->
                                                    <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-xs font-semibold text-primary">
                                                            {{ $log->user ? substr($log->user->name, 0, 2) : 'S' }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Activity Content -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="font-semibold text-sm text-gray-900">
                                                                {{ $log->user->name ?? 'System' }}
                                                            </span>
                                                            @if($log->action)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                    @if($log->action === 'created') bg-green-100 text-green-800
                                                                    @elseif($log->action === 'assigned') bg-blue-100 text-blue-800
                                                                    @elseif($log->action === 'completed') bg-purple-100 text-purple-800
                                                                    @elseif($log->action === 'status_changed') bg-orange-100 text-orange-800
                                                                    @else bg-gray-100 text-gray-800
                                                                    @endif">
                                                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="text-sm text-gray-700 leading-relaxed">
                                                            {{ $log->description }}
                                                        </div>
                                                        
                                                        @if($log->metadata && is_array($log->metadata))
                                                            <div class="mt-2 text-xs text-gray-500">
                                                                @foreach($log->metadata as $key => $value)
                                                                    @if($key !== 'description')
                                                                        <span class="inline-block mr-3">
                                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Timestamp -->
                                                <div class="flex flex-col items-end text-right flex-shrink-0">
                                                    <div class="text-xs text-gray-500 mb-1">
                                                        {{ $log->created_at->format('M d, Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $log->created_at->format('H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-0.5">
                                                        {{ $log->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($task->activityLogs->isEmpty())
                                    <div class="text-center py-12 text-secondary-foreground">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="ki-filled ki-time text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Activity Yet</h3>
                                        <p class="text-gray-500">Task activity will appear here as actions are performed.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Scroll indicator -->
                            @if($task->activityLogs->count() > 5)
                                <div class="text-center py-2 border-t border-gray-100 mt-4">
                                    <div class="text-xs text-gray-400 flex items-center justify-center gap-2">
                                        <i class="ki-filled ki-scroll-down text-xs"></i>
                                        Scroll to see more activities
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-team.card>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-7.5">
                    <!-- Task Info -->
                    <x-team.card title="Task Information" headerClass="">
                        <div class="space-y-4">
                            <!-- Progress -->
                            <div id="progressSection">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium">Progress</span>
                                    <span class="text-sm text-secondary-foreground">{{ $task->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: {{ $task->progress }}%"></div>
                                </div>
                                <!-- Progress Update Form -->
                                <form action="{{ route('team.task.update-progress', $task) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex gap-2">
                                        <input 
                                            type="range" 
                                            id="progressSlider" 
                                            name="progress"
                                            min="0" 
                                            max="100" 
                                            value="{{ $task->progress }}" 
                                            class="flex-1"
                                        >
                                        <button type="submit" class="kt-btn kt-btn-sm kt-btn-secondary">
                                            <i class="ki-filled ki-mouse-square"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Priority -->
                            @if($task->priority)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium">Priority</span>
                                    <div class="flex items-center gap-2">
                                        <span class="priority-indicator" style="background-color: {{ $task->priority->color }}"></span>
                                        <span class="text-sm">{{ $task->priority->name }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Due Date -->
                            @if($task->due_date)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium">Due Date</span>
                                    <span class="text-sm {{ $task->due_date < now() && !$task->status->is_completed ? 'text-red-600' : '' }}">
                                        {{ $task->due_date->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            @endif

                            <!-- Estimated Hours -->
                            @if($task->estimated_hours)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium">Estimated</span>
                                    <span class="text-sm">{{ $task->estimated_hours }}h</span>
                                </div>
                            @endif

                            <!-- Actual Hours -->
                            @if($task->actual_hours)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium">Actual</span>
                                    <span class="text-sm">{{ $task->actual_hours }}h</span>
                                </div>
                            @endif
                        </div>
                    </x-team.card>

                    <!-- Assignments -->
                    <x-team.card title="Assignments" headerClass="">
                        @if($task->assignments->count() > 0)
                            <div class="space-y-3">
                                @foreach($task->assignments as $assignment)
                                    <div class="flex items-center justify-between p-3 rounded-lg @if($assignment->is_active) bg-gray-50 @else bg-red-50 @endif">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary">
                                                    {{ substr($assignment->user->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm">{{ $assignment->user->name }}</div>
                                                <div class="text-xs text-secondary-foreground">{{ ucfirst($assignment->role) }}</div>
                                            </div>
                                        </div>
                                        @if($assignment->estimated_hours)
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                {{ $assignment->estimated_hours }}h
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-secondary-foreground">
                                <i class="ki-filled ki-people text-2xl mb-2"></i>
                                <p>No assignments yet.</p>
                            </div>
                        @endif
                    </x-team.card>

                    <!-- Tags -->
                    @if($task->tags && count($task->tags) > 0)
                        <x-team.card title="Tags" headerClass="">
                            <div class="flex flex-wrap gap-2">
                                @foreach($task->tags as $tag)
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </x-team.card>
                    @endif

                    <!-- Attachments -->
                    <x-team.card title="Attachments" headerClass="">
                        <!-- Attachments List -->
                        <div id="attachmentsList">
                            @if($task->attachments->count() > 0)
                                @foreach($task->attachments as $attachment)
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div class="flex items-center gap-2">
                                            <i class="ki-filled ki-document text-gray-500"></i>
                                            <span class="text-sm">{{ $attachment->original_name ?? 'File' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ Storage::url($attachment->file_path) }}" 
                                               target="_blank" 
                                               class="text-xs text-primary hover:underline">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-secondary-foreground">
                                    <i class="ki-filled ki-document text-xl mb-2"></i>
                                    <p class="text-sm">No attachments</p>
                                </div>
                            @endif
                        </div>
                    </x-team.card>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let replyToId = null;

                // Progress slider visual update only
                $('#progressSlider').on('input', function() {
                    var progress = $(this).val();
                    $('#progressSection .bg-primary').css('width', progress + '%');
                    $('#progressSection .text-secondary-foreground').text(progress + '%');
                });

                // Reply to comment functionality without AJAX
                window.replyToComment = function(commentId) {
                    // Remove previous highlighting
                    $('.comment-item').removeClass('replying-to');
                    
                    // Set the parent_id in the hidden field
                    $('#parentId').val(commentId);
                    
                    // Highlight the comment being replied to
                    $('[data-comment-id="' + commentId + '"]').addClass('replying-to');
                    
                    // Update the placeholder text
                    $('#commentContent').attr('placeholder', 'Reply to comment...').focus();
                    
                    // Show reply indicator
                    $('#replyIndicator').removeClass('hidden');
                    
                    // Scroll to the comment form
                    $('form[action*="store-comment"]').get(0).scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                };

                // Cancel reply functionality
                window.cancelReply = function() {
                    // Remove highlighting
                    $('.comment-item').removeClass('replying-to');
                    
                    // Clear the parent_id
                    $('#parentId').val('');
                    
                    // Reset placeholder text
                    $('#commentContent').attr('placeholder', 'Add a comment...');
                    
                    // Hide reply indicator
                    $('#replyIndicator').addClass('hidden');
                };
            });
        </script>
    @endpush
</x-team.layout.app>
