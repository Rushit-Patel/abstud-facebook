@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Todo', 'url' => route('team.todo.index')],
        ['title' => 'Todo Management']
    ];
    $statuses = [
        [
            'key' => 'pending',
            'title' => 'Pending',
            'color' => 'destructive',
            'icon' => 'ki-plus-circle'
        ],
        [
            'key' => 'in_progress',
            'title' => 'In Progress',
            'color' => 'primary',
            'icon' => 'ki-timer'
        ],
        [
            'key' => 'done',
            'title' => 'Done',
            'color' => 'success',
            'icon' => 'ki-check-circle'
        ]
    ];
@endphp
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/todo.css') }}">
    <style>
        /* Enhanced Drag and Drop Styles */
        .dragging {
            transform: rotate(5deg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border: 2px dashed #3b82f6;
        }

        .dragging-active .drop-zone-active {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 2px dashed #3b82f6;
            border-radius: 8px;
        }

        .drop-zone-active::before {
            content: 'Drop here to update status';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            z-index: 10;
            pointer-events: none;
            opacity: 0;
            animation: fadeInDrop 0.3s ease-in-out forwards;
        }

        @keyframes fadeInDrop {
            to {
                opacity: 1;
            }
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
        }

        .sortable-chosen {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .sortable-drag {
            transform: rotate(5deg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Task and Todo card hover effects */
        .task:hover {
            border-left-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .todo:hover {
            border-left-color: #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
        }

        /* Status indicator animations */
        .status-indicator {
            transition: all 0.3s ease;
        }

        .status-indicator:hover {
            transform: scale(1.2);
        }

        /* Progress bar animation */
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        /* Loading state */
        .updating {
            position: relative;
            pointer-events: none;
        }

        .updating::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            z-index: 10;
        }

        /* Notification styles */
        .toast-notification {
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Column header enhancements */
        .todo-column {
            position: relative;
            transition: all 0.3s ease;
        }

        .todo-column.drop-zone-active {
            position: relative;
        }

        .todo-column.drop-zone-active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(59, 130, 246, 0.05);
            border: 2px dashed #3b82f6;
            border-radius: 8px;
            pointer-events: none;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        /* Card enhancement for better visual feedback */
        .card {
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .dragging {
            transform: rotate(5deg) scale(1.05);
            opacity: 0.9;
        }
    </style>
@endpush
<x-team.layout.app title="Todo Board" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed mb-5">
            <!-- Enhanced Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <h3 class="text-2xl font-bold text-gray-900">Todo Board</h3>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span class="kt-badge kt-badge-outline kt-badge-blue-100 text-blue-800">
                            <span id="total-tasks">0</span> Total Tasks
                        </span>
                        <span class="kt-badge kt-badge-outline kt-badge-green-100 text-green-800">
                            <span id="filtered-tasks">0</span> Showing
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- View Toggle Buttons -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1">
                        <button id="todoViewBtn" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-white text-gray-900 shadow-sm">
                            <i class="ki-filled ki-calendar-2 text-sm mr-1"></i>
                            Todo View
                        </button>
                        <button id="allTasksViewBtn" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-600 hover:text-gray-900">
                            <i class="ki-filled ki-element-11 text-sm mr-1"></i>
                            All Tasks
                        </button>
                    </div>
                    
                    <div class="kt-menu" data-kt-menu="true">
                        <button class="kt-btn kt-btn-icon kt-btn-sm kt-btn-outline" data-kt-drawer-toggle="#todoFilterModal">
                            <i class="ki-filled ki-filter"></i>
                        </button>
                    </div>
                    <button data-kt-modal-toggle="#todoModal" class="kt-btn kt-btn-sm kt-btn-primary shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <i class="ki-filled ki-plus text-sm mr-2"></i>
                        Add New Todo
                    </button>
                </div>
            </div>

            <!-- Enhanced Kanban Board -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($statuses as $status)
                    <x-team.card 
                        cardClass="h-[calc(100vh-12rem)] flex flex-col"
                        bodyClass="p-0 flex-1 overflow-hidden"
                    >
                        <x-slot name="header">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-3">
                                    <span class="kt-badge  kt-badge-outline kt-badge-{{ $status['color'] }}">{{ $status['title'] }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="kt-badge kt-badge-outline kt-badge-{{ $status['color'] }} rounded-full status-count" data-status="{{ $status['key'] }}">
                                        0
                                    </span>
                                </div>
                            </div>

                        </x-slot>
                        
                        <!-- Todo Column -->
                        <div id="{{ $status['key'] }}" class="h-full overflow-y-auto w-full p-4 bg-gray-50/50">
                            <div class="space-y-3 w-full todo-column" data-status="{{ $status['key'] }}">
                                <!-- Todos will be loaded via AJAX -->
                                <div class="text-center py-8 text-gray-500">
                                    <i class="ki-filled ki-loading animate-spin text-2xl mb-2"></i>
                                    <p>Loading todos...</p>
                                </div>
                            </div>
                        </div>
                    </x-team.card>
                @endforeach
            </div>
        </div>

        {{-- Add Todo Modal --}}
        <x-team.modal id="todoModal" title="Add New Todo" size="max-w-md">
            <form id="todoForm" action="{{ route('team.todos.store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="grid grid-rows-1 gap-5 py-5">
                    <x-team.forms.input
                        name="title"
                        label="Title"
                        placeholder="Enter todo title..."
                        required="true"
                    />

                    <x-team.forms.textarea
                        name="description"
                        label="Description"
                        placeholder="Enter todo description..."
                        rows="3"
                        required="true"
                    />
                    <hr>
                    <x-team.forms.switch
                        name="schedule"
                        style="block"
                        label="You can schedule this todo"
                    />
                    <div class="due_date_div" style="display: none;">
                        <x-team.forms.datepicker
                            name="due_date"
                            label="Due Date"
                            minDate="{{ now()->format('d/m/Y') }}"
                            placeholder="Select due date..."
                        />
                    </div>
                    <hr>
                    <x-team.forms.switch
                        name="assign"
                        style="block"
                        label="Assign this todo to a team member"
                    />
                    <div class="user_div" style="display: none;">
                        <x-team.forms.select
                            :options="$teamMembers"
                            name="user_id"
                            label="Assign to a team member"
                        />
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" data-kt-modal-dismiss="true" class="kt-btn kt-btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" form="todoForm" class="kt-btn kt-btn-primary">
                        Add Todo
                    </button>
                </div>
            </form>

        </x-team.modal>

        {{-- View Todo Modal --}}
        <x-team.modal id="viewTodoModal" title="Todo Details" size="max-w-lg">
            <div class="py-4">
                <!-- Title Card -->
                <x-team.card cardClass="mb-4 border-l-4 border-l-blue-500" bodyClass="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 id="view_title" class="text-lg font-semibold text-gray-900 mb-1"></h3>
                            <div class="flex items-center gap-2">
                                <span id="view_status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                                <span id="view_due_date_badge" class="hidden inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="ki-filled ki-calendar-2 text-xs mr-1"></i>
                                    <span id="view_due_text"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </x-team.card>

                <!-- Description Card -->
                <x-team.card cardClass="mb-4" bodyClass="p-4" id="view_description_card">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="ki-filled ki-note-2 text-sm text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Description</h4>
                            <p id="view_description" class="text-sm text-gray-600 leading-relaxed"></p>
                        </div>
                    </div>
                </x-team.card>

                <!-- Assignment & Timeline Card -->
                <x-team.card cardClass="mb-4" bodyClass="p-4">
                    <div class="space-y-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Assignment Section -->
                        <div class="flex items-start gap-3" id="view_assignment_section">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ki-filled ki-user text-sm text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Assigned To</h4>
                                <p id="view_assigned_to" class="text-sm text-gray-900 font-medium"></p>
                            </div>
                        </div>

                        <!-- Creator Section -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ki-filled ki-user text-sm text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Created By</h4>
                                <p id="view_created_by" class="text-sm text-gray-900 font-medium"></p>
                            </div>
                        </div>

                        <!-- Timeline Section -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ki-filled ki-time text-sm text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Created On</h4>
                                <p id="view_created_at" class="text-sm text-gray-600"></p>
                            </div>
                        </div>

                        <!-- Due Date Section (only show if exists) -->
                        <div class="flex items-start gap-3 hidden" id="view_due_date_section">
                            <div class="flex items-start gap-3 w-full">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="ki-filled ki-calendar-2 text-sm text-orange-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Due Date</h4>
                                    <p id="view_due_date_full" class="text-sm text-gray-900 font-medium"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-team.card>
            </div>
        </x-team.modal>

        {{-- Edit Todo Modal --}}
        <x-team.modal id="editTodoModal" title="Edit Todo" size="max-w-md">
            <form id="editTodoForm" method="POST" action="">
                @method('POST')
                @csrf
                <input type="hidden" name="todo_id">
                <div class="grid grid-rows-1 gap-5 py-5">
                    <x-team.forms.input
                        name="title"
                        label="Title"
                        required="true"
                    />

                    <x-team.forms.textarea
                        name="description"
                        label="Description"
                        rows="3"
                        required="true"
                    />
                    <hr>
                    <x-team.forms.switch
                        name="edit_schedule"
                        style="block"
                        label="You can schedule this todo"
                    />
                    <div class="edit_due_date_div" style="display: none;">
                        <x-team.forms.datepicker
                            name="due_date"
                            label="Due Date"
                            minDate="{{ now()->format('d/m/Y') }}"
                            placeholder="Select due date..."
                        />
                    </div>
                    <hr>
                    <x-team.forms.switch
                        name="edit_assign"
                        style="block"
                        label="Assign this todo to a team member"
                    />
                    <div class="edit_user_div" style="display: none;">
                        <x-team.forms.select
                            :options="$teamMembers"
                            name="user_id"
                            label="Assign to a team member"
                        />
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" data-kt-modal-dismiss="true" class="kt-btn kt-btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" form="editTodoForm" class="kt-btn kt-btn-primary">
                        Update Todo
                    </button>
                </div>
            </form>
        </x-team.modal>

        {{-- Delete Todo Modal --}}
        <x-team.modals.delete-modal 
            id="deleteTodoModal" 
            title="Delete Todo" 
            size="max-w-sm" 
            formId="deleteTodoForm" 
            message="Are you sure you want to delete this todo? This action cannot be undone."
        />
        
        <x-team.drawer.drawer id="todoFilterModal" title="Filter Tasks">
            <x-slot name="body">
                <form id="todoFilterForm">
                    @csrf
                    <div class="grid gap-4">
                        <!-- View Info -->
                        <div class="flex flex-col gap-3 px-5">
                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center gap-2 text-blue-800 text-sm">
                                    <i class="ki-filled ki-information-5"></i>
                                    <span id="currentViewInfo">Currently showing: Today's todos & recurring tasks</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lead Status Filter -->
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.range-datepicker
                                name="dateRange"
                                label="Date Range"
                                id="dateRange"
                                placeholder="Select date range..."
                            />
                        </div>
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select
                                name="assignmentFilter"
                                id="assignmentFilter"
                                label="Assignment"
                                :options="collect([
                                    '' => 'All Tasks',
                                    'my-todos' => 'My Tasks Only',
                                    'assigned-to-me' => 'Assigned to Me',
                                    'created-by-me' => 'Created by Me',
                                    'unassigned' => 'Unassigned'
                                ])->merge($teamMembers->mapWithKeys(fn($member) => ['user-'.$member->id => $member->name]))"
                            />
                        </div>
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.input
                                type="text"
                                name="searchTodos"
                                label="Search Tasks"
                                id="searchTodos"
                                placeholder="Search tasks by title or description..."
                                icon="ki-magnifier"
                            />
                        </div>
                    </div>
                    <div class="kt-card-footer grid grid-cols-2 gap-2.5 mt-5">
                        <button type="button" class="kt-btn kt-btn-outline" onclick="clearFilters()">
                            Clear Filters
                        </button>
                        <button type="submit" class="kt-btn kt-btn-primary" id="applyFiltersBtn">
                            <i class="ki-filled ki-check"></i>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </x-slot>
        </x-team.drawer.drawer>
    </x-slot>

    @push('scripts')
    <!-- Add SortableJS library -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let currentFilters = {};
        let currentView = 'todo'; // Default to todo view
        let filterTimeout = null;

        // =====================================================
        // UTILITY FUNCTIONS
        // =====================================================
        
        function closeModal(modalId) {
            const $modal = $('#' + modalId);
            if ($modal.length && window.KTModal && KTModal.getInstance) {
                const modalInstance = KTModal.getInstance($modal[0]);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        }

        function closeDrawer(drawerId) {
            const $drawer = $('#' + drawerId);
            if ($drawer.length && window.KTDrawer && KTDrawer.getInstance) {
                const drawerInstance = KTDrawer.getInstance($drawer[0]);
                if (drawerInstance) {
                    drawerInstance.hide();
                }
            }
        }

        // =====================================================
        // VIEW TOGGLE FUNCTIONS
        // =====================================================
        
        function switchView(view) {
            currentView = view;
            
            // Update button states
            const $todoBtn = $('#todoViewBtn');
            const $allTasksBtn = $('#allTasksViewBtn');
            const $viewInfo = $('#currentViewInfo');
            
            if (view === 'todo') {
                $todoBtn.attr('class', 'px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-white text-gray-900 shadow-sm');
                $allTasksBtn.attr('class', 'px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-600 hover:text-gray-900');
                $viewInfo.text("Currently showing: Today's todos & recurring tasks");
            } else {
                $todoBtn.attr('class', 'px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-600 hover:text-gray-900');
                $allTasksBtn.attr('class', 'px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-white text-gray-900 shadow-sm');
                $viewInfo.text("Currently showing: All tasks with permissions");
            }
            
            // Reload tasks with new view
            loadTodos(Object.assign({}, currentFilters, { view: view }));
        }

        // =====================================================
        // DATA LOADING FUNCTIONS
        // =====================================================
        
        function loadTodos(filters = {}) {
            // Show loading state
            $('.todo-column').html(`
                <div class="text-center py-8 text-gray-500">
                    <i class="ki-filled ki-loading animate-spin text-2xl mb-2"></i>
                    <p>Loading ${currentView === 'todo' ? 'todos' : 'tasks'}...</p>
                </div>
            `);

            // Add current view to filters
            filters.view = currentView;

            $.ajax({
                url: '{{ route("team.todos.get") }}',
                method: 'GET',
                data: filters,
                success: function(response) {
                    if (response.success) {
                        // Update columns with both todos and tasks
                        $.each(['pending', 'in_progress', 'done'], function(index, status) {
                            let combinedHtml = '';
                            
                            // Add todos
                            if (response.todos && response.todos[status]) {
                                combinedHtml += response.todos[status];
                            }
                            
                            // Add tasks
                            if (response.tasks && response.tasks[status]) {
                                combinedHtml += response.tasks[status];
                            }
                            
                            // If no content, show empty state
                            if (!combinedHtml.trim()) {
                                combinedHtml = `
                                    <div class="text-center py-8 text-gray-400">
                                        <i class="ki-filled ki-note-2 text-2xl mb-2"></i>
                                        <p class="text-sm">No ${status.replace('_', ' ')} items</p>
                                    </div>
                                `;
                            }
                            
                            $(`.todo-column[data-status="${status}"]`).html(combinedHtml);
                        });
                        
                        // Update counters with combined counts
                        let combinedCounts = {
                            total: (response.counts?.total || 0) + (response.taskCounts?.total || 0),
                            pending: (response.counts?.pending || 0) + (response.taskCounts?.pending || 0),
                            in_progress: (response.counts?.in_progress || 0) + (response.taskCounts?.in_progress || 0),
                            done: (response.counts?.done || 0) + (response.taskCounts?.done || 0)
                        };
                        
                        updateCounters(combinedCounts);
                        initializeDragAndDrop();
                    }
                },
                error: function() {
                    $('.todo-column').html(`
                        <div class="text-center py-8 text-red-500">
                            <i class="ki-filled ki-warning text-2xl mb-2"></i>
                            <p>Error loading ${currentView === 'todo' ? 'todos' : 'tasks'}. Please try again.</p>
                        </div>
                    `);
                }
            });
        }

        function updateCounters(counts) {
            $('#total-tasks').text(counts.total || 0);
            $('#filtered-tasks').text(counts.total || 0);
            $('.status-count[data-status="pending"]').text(counts.pending || 0);
            $('.status-count[data-status="in_progress"]').text(counts.in_progress || 0);
            $('.status-count[data-status="done"]').text(counts.done || 0);
        }

        // =====================================================
        // FILTER FUNCTIONS
        // =====================================================
        
        function applyFilters() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                const filters = {
                    search: $('#searchTodos').val(),
                    assignment: $('#assignmentFilter').val(),
                    date_range: $('#dateRange').val()
                };

                // Remove empty filters
                $.each(filters, function(key, value) {
                    if (!value) delete filters[key];
                });

                currentFilters = filters;
                loadTodos(filters);
                closeDrawer('todoFilterModal');
            }, 500);
        }

        function clearFilters() {
            $('#searchTodos').val('');
            $('#assignmentFilter').val('').trigger('change');
            $('#dateRange').val('');
            currentFilters = {};
            loadTodos();
        }

        // =====================================================
        // MODAL DATA FUNCTIONS
        // =====================================================
        
        function setViewDataFromElement(element) {
            const taskData = $(element).attr('data-task-data');
            const task = JSON.parse(atob(taskData));
            setViewData(task);
        }

        function setEditDataFromElement(element) {
            const taskData = $(element).attr('data-task-data');
            const actionUrl = $(element).attr('data-action-url');
            const task = JSON.parse(atob(taskData));
            setEditData(task, actionUrl);
        }

        function setViewData(task) {
            $('#view_title').text(task.title || 'Untitled Task');
            
            // Description
            if (task.description && task.description.trim()) {
                $('#view_description').text(task.description);
                $('#view_description_card').show();
            } else {
                $('#view_description_card').hide();
            }
            
            // Status
            let statusClass = '', statusText = '', statusIcon = '';
            if (task.status) {
                const statusName = task.status.name.toLowerCase();
                if (['completed', 'done', 'finished', 'closed'].includes(statusName)) {
                    statusClass = 'bg-green-100 text-green-800';
                    statusText = 'Done';
                    statusIcon = 'ki-check-circle';
                } else if (['in progress', 'working', 'active', 'started'].includes(statusName)) {
                    statusClass = 'bg-blue-100 text-blue-800';
                    statusText = 'In Progress';
                    statusIcon = 'ki-timer';
                } else {
                    statusClass = 'bg-red-100 text-red-800';
                    statusText = 'Pending';
                    statusIcon = 'ki-plus-circle';
                }
            } else {
                statusClass = 'bg-red-100 text-red-800';
                statusText = 'Pending';
                statusIcon = 'ki-plus-circle';
            }
            
            $('#view_status').attr('class', `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}`)
                           .html(`<i class="ki-filled ${statusIcon} text-xs mr-1"></i>${statusText}`);
            
            // Assignment
            if (task.assignees && task.assignees.length > 0) {
                if (task.assignees.length === 1) {
                    $('#view_assigned_to').text(task.assignees[0].name);
                } else {
                    $('#view_assigned_to').text(`${task.assignees.length} users assigned`);
                }
            } else {
                $('#view_assigned_to').text('Unassigned').addClass('italic text-gray-500');
            }
            
            // Created by and date
            $('#view_created_by').text(task.creator?.name || 'Unknown User');
            
            if (task.created_at) {
                const createdDate = new Date(task.created_at);
                const dateText = createdDate.toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
                const timeText = createdDate.toLocaleTimeString('en-US', {
                    hour: '2-digit', minute: '2-digit'
                });
                $('#view_created_at').text(`${dateText} at ${timeText}`);
            }
            
            // Due date handling
            if (task.due_date) {
                const dueDate = new Date(task.due_date);
                const today = new Date();
                const dateText = dueDate.toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
                
                $('#view_due_date_badge').removeClass('hidden');
                $('#view_due_date_section').removeClass('hidden');
                $('#view_due_date_full').text(dateText);
                
                if (dueDate.toDateString() === today.toDateString()) {
                    $('#view_due_text').text('Due Today');
                } else if (dueDate < today) {
                    $('#view_due_text').text('Overdue');
                } else {
                    $('#view_due_text').text(dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                }
            } else {
                $('#view_due_date_badge').addClass('hidden');
                $('#view_due_date_section').addClass('hidden');
            }
        }

        function setEditData(task, actionUrl) {
            const form = $('#editTodoForm');
            form.find('input[name="todo_id"]').val(task.id);
            form.find('input[name="title"]').val(task.title);
            form.find('textarea[name="description"]').val(task.description);
            form.attr('action', actionUrl);
            
            // Due date
            if (task.due_date) {
                $('#edit_schedule').prop('checked', true);
                $('.edit_due_date_div').show();
                const dueDate = new Date(task.due_date);
                const formattedDate = String(dueDate.getDate()).padStart(2, '0') + '/' + 
                                    String(dueDate.getMonth() + 1).padStart(2, '0') + '/' + 
                                    dueDate.getFullYear();
                form.find('input[name="due_date"]').val(formattedDate).prop('required', true);
            } else {
                $('#edit_schedule').prop('checked', false);
                $('.edit_due_date_div').hide();
                form.find('input[name="due_date"]').prop('required', false);
            }
            
            // Assignment
            if (task.assignees && task.assignees.length > 0) {
                $('#edit_assign').prop('checked', true);
                $('.edit_user_div').show();
                var select = form.find('select[name="user_id"]');
                select.val(task.assignees[0].id).trigger('change'); // Use first assignee
                select.prop('required', true);
            } else {
                $('#edit_assign').prop('checked', false);
                $('.edit_user_div').hide();
                form.find('select[name="user_id"]').prop('required', false);
            }
        }

        // =====================================================
        // DRAG & DROP
        // =====================================================
        
        function initializeDragAndDrop() {
            $('.todo-column').each(function() {
                new Sortable(this, {
                    group: 'todos',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onStart: function(evt) {
                        $(evt.item).addClass('dragging').css('opacity', '0.8');
                        $('body').addClass('dragging-active');
                        
                        // Add visual feedback to valid drop zones
                        $('.todo-column').not(evt.from).addClass('drop-zone-active');
                    },
                    onEnd: function (evt) {
                        $(evt.item).removeClass('dragging').css('opacity', '1');
                        $('body').removeClass('dragging-active');
                        $('.todo-column').removeClass('drop-zone-active');
                        
                        const itemId = $(evt.item).data('id');
                        const newStatus = $(evt.to).data('status');
                        const oldStatus = $(evt.from).data('status');
                        const isTask = $(evt.item).hasClass('task');

                        if (oldStatus !== newStatus) {
                            updateItemStatus(itemId, isTask, newStatus, oldStatus, evt.item);
                        }
                    }
                });
            });
        }

        function updateItemStatus(itemId, isTask, newStatus, oldStatus, itemElement) {
            // Show loading state
            const $item = $(itemElement);
            $item.css('pointer-events', 'none');
            
            // Add loading indicator
            const loadingHtml = '<div class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded-lg"><div class="flex items-center gap-2 text-blue-600"><i class="ki-filled ki-loading animate-spin"></i><span class="text-sm">Updating...</span></div></div>';
            $item.css('position', 'relative').append(loadingHtml);

            // Determine which endpoint to use based on item type
            let updateUrl;
            let requestData;
            
            if (isTask) {
                updateUrl = `/team/todos/tasks/${itemId}/update-status`;
                requestData = { status_id: newStatus };
            } else {
                updateUrl = `/team/todos/${itemId}/update-status`;
                requestData = { status_id: newStatus };
            }

            $.ajax({
                url: updateUrl,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                data: JSON.stringify(requestData),
                success: function(data) {
                    if (data.success) {
                        showNotification(`${isTask ? 'Task' : 'Todo'} status updated successfully!`, 'success');
                        
                        // Update visual elements if data is provided
                        if (data.item) {
                            updateItemDisplay($item, data.item, isTask);
                        }
                        
                        // Remove loading state
                        $item.css('pointer-events', 'auto').find('.absolute.inset-0').remove();
                        
                        // Add success animation
                        $item.addClass('animate-pulse');
                        setTimeout(() => {
                            $item.removeClass('animate-pulse');
                        }, 1000);
                        
                    } else {
                        throw new Error(data.message || 'Update failed');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating status:', error);
                    showNotification('Failed to update status. Please try again.', 'error');
                    
                    // Revert the move on error
                    const $originalColumn = $(`.todo-column[data-status="${oldStatus}"]`);
                    if ($originalColumn.length) {
                        $originalColumn.append($item);
                    }
                    
                    // Remove loading state
                    $item.css('pointer-events', 'auto').find('.absolute.inset-0').remove();
                }
            });
        }

        function updateItemDisplay($item, itemData, isTask) {
            // Update status indicator if present
            const $statusIndicator = $item.find('.absolute.top-2.right-2 div');
            if ($statusIndicator.length && itemData.status) {
                const statusColor = getStatusColor(itemData.status.slug);
                $statusIndicator.removeClass().addClass(`w-3 h-3 rounded-full ${statusColor} border-2 border-white shadow-sm`);
                $statusIndicator.attr('title', `Status: ${itemData.status.name}`);
            }
            
            // Update status badge if present
            const $statusBadge = $item.find('.inline-flex.items-center').last();
            if ($statusBadge.length && itemData.status) {
                const $statusIcon = $statusBadge.find('i');
                const $statusText = $statusBadge.find('span').last();
                
                if ($statusText.length) {
                    $statusText.text(itemData.status.name);
                    const statusClass = getStatusBadgeClass(itemData.status.slug);
                    $statusBadge.removeClass().addClass(`inline-flex items-center px-2 py-1 rounded text-xs font-medium border ${statusClass}`);
                    
                    // Update icon
                    const statusIcon = getStatusIcon(itemData.status.slug);
                    $statusIcon.removeClass().addClass(`ki-filled ${statusIcon} text-xs mr-1`);
                }
            }
            
            // Update progress bar if it's a task with progress
            if (isTask && itemData.progress !== undefined) {
                const $progressBar = $item.find('.bg-blue-500.h-1\\.5');
                const $progressText = $item.find('.font-medium').filter((i, el) => $(el).text().includes('%'));
                
                if ($progressBar.length && $progressText.length) {
                    $progressBar.css('width', `${itemData.progress}%`);
                    $progressText.text(`${itemData.progress}%`);
                }
            }
        }

        function getStatusColor(statusSlug) {
            const colors = {
                'completed': 'bg-green-500',
                'in-progress': 'bg-blue-500',
                'review': 'bg-purple-500',
                'pending': 'bg-gray-400',
                'to-do': 'bg-gray-400'
            };
            return colors[statusSlug] || 'bg-gray-400';
        }

        function getStatusBadgeClass(statusSlug) {
            const classes = {
                'completed': 'bg-green-100 text-green-800 border-green-200',
                'in-progress': 'bg-blue-100 text-blue-800 border-blue-200',
                'review': 'bg-purple-100 text-purple-800 border-purple-200',
                'pending': 'bg-gray-100 text-gray-800 border-gray-200',
                'to-do': 'bg-gray-100 text-gray-800 border-gray-200'
            };
            return classes[statusSlug] || 'bg-gray-100 text-gray-800 border-gray-200';
        }

        function getStatusIcon(statusSlug) {
            const icons = {
                'completed': 'ki-check-circle',
                'in-progress': 'ki-timer',
                'review': 'ki-eye',
                'pending': 'ki-plus-circle',
                'to-do': 'ki-plus-circle'
            };
            return icons[statusSlug] || 'ki-plus-circle';
        }

        function showNotification(message, type = 'info') {
            // Remove existing notifications
            $('.toast-notification').remove();
            
            // Create notification element
            const colors = {
                success: 'bg-green-50 border-green-200 text-green-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            };
            
            const icons = {
                success: 'ki-check-circle',
                error: 'ki-cross-circle',
                info: 'ki-information-2'
            };
            
            const notificationHtml = `
                <div class="toast-notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg border transition-all duration-300 transform translate-x-full ${colors[type]}">
                    <div class="flex items-center gap-2">
                        <i class="ki-filled ${icons[type]}"></i>
                        <span class="font-medium">${message}</span>
                        <button class="ml-2 hover:opacity-70 close-notification">
                            <i class="ki-filled ki-cross text-sm"></i>
                        </button>
                    </div>
                </div>
            `;
            
            $('body').append(notificationHtml);
            const $notification = $('.toast-notification');
            
            // Animate in
            setTimeout(() => {
                $notification.removeClass('translate-x-full');
            }, 10);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if ($notification.length) {
                    $notification.addClass('translate-x-full');
                    setTimeout(() => {
                        $notification.remove();
                    }, 300);
                }
            }, 5000);
        }

        // Close notification handler
        $(document).on('click', '.close-notification', function() {
            const $notification = $(this).closest('.toast-notification');
            $notification.addClass('translate-x-full');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        });

        // =====================================================
        // EVENT HANDLERS
        // =====================================================
        
        $(document).ready(function() {
            // View toggle handlers
            $('#todoViewBtn').on('click', function() {
                switchView('todo');
            });
            
            $('#allTasksViewBtn').on('click', function() {
                switchView('all');
            });
            
            // Initialize filters
            $('#todoFilterForm').on('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });
            
            $('#applyFiltersBtn').on('click', function(e) {
                e.preventDefault();
                applyFilters();
            });

            // Form switches
            $('#schedule').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.due_date_div').show();
                    $('#due_date').prop('required', true);
                } else {
                    $('.due_date_div').hide();
                    $('#due_date').prop('required', false);
                }
            });

            $('#assign').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.user_div').show();
                    $('#user_id').prop('required', true);
                } else {
                    $('.user_div').hide();
                    $('#user_id').prop('required', false);
                }
            });

            $('#edit_schedule').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.edit_due_date_div').show();
                    $('#editTodoForm input[name="due_date"]').prop('required', true);
                } else {
                    $('.edit_due_date_div').hide();
                    $('#editTodoForm input[name="due_date"]').prop('required', false);
                }
            });

            $('#edit_assign').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.edit_user_div').show();
                    $('#editTodoForm select[name="user_id"]').prop('required', true);
                } else {
                    $('.edit_user_div').hide();
                    $('#editTodoForm select[name="user_id"]').prop('required', false);
                }
            });

            // Load initial todos
            loadTodos();
        });
    </script>
    @endpush
</x-team.layout.app>
