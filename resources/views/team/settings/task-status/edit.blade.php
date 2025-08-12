@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Task Status', 'url' => route('team.settings.task-status.index')],
    ['title' => 'Edit Task Status']
];
@endphp

<x-team.layout.app title="Edit Task Status" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Task Status: {{ $taskStatus->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update task-status information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.task-status.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Task Status Information" headerClass="">
                <form action="{{ route('team.settings.task-status.update', $taskStatus) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Country Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="Name"
                                        type="text"
                                        placeholder="Enter task-priority"
                                        :value="old('name', $taskStatus->name)"
                                        required />
                                    <x-team.forms.input
                                        name="slug"
                                        label="slug"
                                        type="text"
                                        placeholder="Enter slug"
                                        :value="old('slug', $taskStatus->slug)"
                                        required />
                                    <x-team.forms.input
                                        name="color"
                                        label="color"
                                        type="color"
                                        placeholder="Enter color"
                                        :value="old('color', $taskStatus->color)"
                                        required />
                                    <x-team.forms.input
                                        name="order"
                                        label="Order"
                                        type="number"
                                        placeholder="Enter order"
                                        :value="old('order', $taskStatus->order)"
                                        required />
                                </div>
                            </x-team.card>
                        </div>
                    </div>

                     <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="is completed Settings">
                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">is completed</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="is_completed"
                                                type="checkbox"
                                                value="1"
                                                {{ old('is_completed', $taskStatus->is_completed) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this task is-completed inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">
                                <div class="flex flex-col gap-1 mt-4">
                                    <label class="kt-form-label font-normal text-mono">Status</label>
                                    <label class="kt-label">
                                        <input class="kt-checkbox kt-checkbox-sm"
                                            name="is_active"
                                            type="checkbox"
                                            value="1"
                                            {{ old('is_active', $taskStatus->is_active) ? 'checked' : '' }}
                                        />
                                        <span class="kt-checkbox-label">
                                            Uncheck to make this task-status inactive
                                        </span>
                                    </label>
                                </div>
                            </x-team.card>
                            </div>
                        </div>
                    </div>


                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5 border-t border-gray-200">
                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Created At</label>
                            <div class="text-sm text-gray-700">
                                {{ $taskStatus->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Last Updated</label>
                            <div class="text-sm text-gray-700">
                                {{ $taskStatus->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.task-status.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Task Status
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            // Form validation and enhancement
            $(document).ready(function() {
                // Add any additional form enhancements here

                // Focus on name field
                $('#name').focus();

                // Form submission handling
                $('form').on('submit', function() {
                    // Disable submit button to prevent double submission
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        </script>
    @endpush
</x-team.layout.app>
