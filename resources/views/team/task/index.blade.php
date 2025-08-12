@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Task Management']
];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush

<x-team.layout.app title="Task Management" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Task Management
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage tasks, assignments, and track progress
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.task.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Create Task
                    </a>
                </div>
            </div>
            <!-- Task Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-7.5">
                <x-team.card cardClass="task-card">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <i class="ki-filled ki-calendar text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-mono">{{ $totalTasks }}</div>
                            <div class="text-sm text-secondary-foreground">Total Tasks</div>
                        </div>
                    </div>
                </x-team.card>

                <x-team.card cardClass="task-card">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                            <i class="ki-filled ki-timer text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-mono">{{ $inProgressTasks }}</div>
                            <div class="text-sm text-secondary-foreground">In Progress</div>
                        </div>
                    </div>
                </x-team.card>

                <x-team.card cardClass="task-card">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                            <i class="ki-filled ki-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-mono">{{ $completedTasks }}</div>
                            <div class="text-sm text-secondary-foreground">Completed</div>
                        </div>
                    </div>
                </x-team.card>

                <x-team.card cardClass="task-card">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                            <i class="ki-filled ki-notification-on text-destructive text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-mono">{{ $overdueTasks }}</div>
                            <div class="text-sm text-secondary-foreground">Overdue</div>
                        </div>
                    </div>
                </x-team.card>
            </div>

            <!-- Tasks List -->
            <x-team.card title="Tasks" headerClass="">
                <x-slot name="header">
                    <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-drawer-toggle="#task_filter_drawer">
                        <i class="ki-filled ki-filter"> </i> Filter
                    </button>
                </x-slot>
                
                <!-- Filter Badges Container -->
                <div id="taskFilterBadge" class="mb-4" style="display: none;"></div>
                
                {{ $dataTable->table() }}
            </x-team.card>

            <x-team.drawer.drawer id="task_filter_drawer" title="Filter Tasks">
                <x-slot name="body">
                    <form id="taskFilterForm" method="POST" >
                        @csrf
                        @method('POST')
                        {{-- Date Range Filter --}}
                        <div class="flex flex-col gap-3 px-5">
                            <span class="text-sm font-medium text-mono">
                                Task Date Range
                            </span>
                            <x-team.forms.range-datepicker
                                name="date"
                                value="{{ request('date') }}"
                                placeholder="Select Date Range"
                                class="w-full"
                            />
                        </div>
                        <div class="border-b border-border mb-4 mt-5"></div>
                        {{-- Task Status Filter --}}
                        <div class="flex items-center gap-1 px-5 mb-3">
                            <span class="text-sm font-medium text-mono">
                                Status
                            </span>
                        </div>
                        <div class="px-5">
                            <div class="flex flex-wrap gap-2.5 mb-2">
                                @foreach ($statuses as $status)
                                    <x-team.forms.checkbox
                                        name="status[]"
                                        value="{{ $status->id }}"
                                        label="{{ $status->name }}"
                                        style="badge"
                                        checked="{{ in_array($status->id, (array) request('status')) }}"
                                    />
                                @endforeach
                            </div>
                        </div>

                        @haspermission('task:show-all')
                        <div class="border-b border-border mb-4 mt-5"></div>
                            <div class="flex items-center gap-1 px-5 mb-3">
                                <span class="text-sm font-medium text-mono">
                                    Branch
                                </span>
                            </div>
                            <div class="px-5">
                                <div class="flex flex-wrap gap-2.5 mb-2">
                                    @foreach ($branches as $branch)
                                    <x-team.forms.checkbox
                                            name="branch[]"
                                            id="branch_filter"
                                            value="{{ $branch->id }}"
                                            label="{{ $branch->branch_name }}"
                                            style="inline"
                                            checked="{{ in_array($branch->id, (array) request('branch', [])) }}"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endhaspermission

                        @haspermission('task:show-branch')
                        <input type="hidden" name="branch[]" id="branch_filter" value="{{ auth()->user()->branch_id }}">
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select
                                    name="owner[]"
                                    id="owner"
                                    label="User"
                                    :options="[]"
                                    :selected="old('owner')"
                                    placeholder="Select user"
                                    searchable="true"
                                    multiple="true"
                                />
                        </div>
                        @endhaspermission

                        <div class="border-b border-border mb-4 mt-5"></div>
                        
                        {{-- Task Priority Filter --}}
                        <div class="flex items-center gap-1 px-5 mb-3">
                            <span class="text-sm font-medium text-mono">
                                Priority
                            </span>
                        </div>
                        <div class="px-5">
                            <div class="flex flex-wrap gap-2.5 mb-2">
                                @if(isset($priorities))
                                    @foreach ($priorities as $priority)
                                        <x-team.forms.checkbox
                                            name="priority[]"
                                            value="{{ $priority->id }}"
                                            label="{{ $priority->name }}"
                                            style="badge"
                                            checked="{{ in_array($priority->id, (array) request('priority')) }}"
                                        />
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="border-b border-border mb-4 mt-5"></div>
                        {{-- Task Assignee Filter --}}
                        <div class="flex flex-col gap-3 px-5">
                            <span class="text-sm font-medium text-mono">
                                Assigned To
                            </span>
                            <x-team.forms.select
                                name="assigned_to[]"
                                id="assigned_to"
                                :options="$users"
                                :selected="old('assigned_to')"
                                placeholder="Select assignee"
                                searchable="true"
                                multiple="true"
                            />
                        </div>
                        <div class="border-b border-border mb-4 mt-5"></div>
                        
                        {{-- Task Category Filter --}}
                        <div class="flex items-center gap-1 px-5 mb-3">
                            <span class="text-sm font-medium text-mono">
                                Category
                            </span>
                        </div>
                        <div class="px-5">
                            <div class="flex flex-wrap gap-2.5 mb-2">
                                @if(isset($categories))
                                    @foreach ($categories as $category)
                                        <x-team.forms.checkbox
                                            name="category[]"
                                            value="{{ $category->id }}"
                                            label="{{ $category->name }}"
                                            style="badge"
                                            checked="{{ in_array($category->id, (array) request('category')) }}"
                                        />
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        

                        <div class="border-b border-border mb-4 mt-5"></div>
                        
                        {{-- Task Due Date Filter --}}
                        <div class="flex flex-col gap-3 px-5">
                            <span class="text-sm font-medium text-mono">
                                Due Date Range
                            </span>
                            <x-team.forms.range-datepicker
                                name="due_date"
                                value="{{ request('due_date') }}"
                                placeholder="Select Due Date Range"
                                class="w-full"
                            />
                        </div>

                        <div class="border-b border-border mb-4 mt-5"></div>
                        
                    </form>
                </x-slot>
                <x-slot name="footer">
                    <button type="button" class="kt-btn kt-btn-outline" onclick="resetTaskFilters()">
                        Reset
                    </button>
                    <button type="submit" form="taskFilterForm" class="kt-btn kt-btn-primary">
                        Apply Filters
                    </button>
                </x-slot>
            </x-team.drawer.drawer>
        </div>
    </x-slot>

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @include('team.task.datatables.filter-js')
        @include('team.task.task-js')
        
        <script>
            $(document).ready(function() {
                // Additional custom scripts can go here
            });
        </script>
    @endpush
</x-team.layout.app>
