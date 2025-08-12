@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Batch', 'url' => route('team.settings.batch.index')],
    ['title' => 'Create Batch']
];
@endphp

<x-team.layout.app title="Create Batch" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Batch
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new Batch to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.batch.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Batch Information" headerClass="">
                <form action="{{ route('team.settings.batch.store') }}" method="POST" class="form">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid gap-5">
                                    <!-- Batch Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="Batch Name"
                                        type="text"
                                        placeholder="Enter batch name"
                                        :value="old('name')"
                                        required />

                                    <!-- Coaching Selection -->
                                    <div class="flex flex-col gap-1">
                                        <label class="kt-form-label font-normal text-mono required"><b>Coaching <span style="color: #E7004A;">*</span></b></label>
                                        <select name="coaching_id" class="kt-select" required>
                                            <option value="">Select Coaching</option>
                                            @foreach($coachings as $coaching)
                                                <option value="{{ $coaching->id }}" {{ old('coaching_id') == $coaching->id ? 'selected' : '' }}>
                                                    {{ $coaching->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('coaching_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Batch Time -->
                                    <x-team.forms.input
                                        name="time"
                                        label="Batch Time"
                                        type="text"
                                        placeholder="Enter time"
                                        :value="old('time')"
                                        required />

                                    <!-- Batch Capacity -->
                                    <x-team.forms.input
                                        name="capacity"
                                        label="Capacity"
                                        type="number"
                                        placeholder="Enter capacity"
                                        :value="old('capacity')"
                                        required />

                                    <!-- Branch Selection -->
                                    <div class="flex flex-col gap-1">
                                        <label class="kt-form-label font-normal text-mono required"><b>Branch <span style="color: #E7004A;">*</span></b></label>
                                        <select name="branch_id" class="kt-select" required>
                                            <option value="">Select Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->branch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </x-team.card>
                        </div>

                        <!-- Status -->
                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">
                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">Demo Batch</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="is_demo"
                                                type="checkbox"
                                                value="1"
                                                {{ old('is_demo', false) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                check/uncheck if this batch is for demo?
                                            </span>
                                        </label>
                                    </div>

                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">Status</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="status"
                                                type="checkbox"
                                                value="1"
                                                {{ old('status', true) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this batch inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.batch.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Batch
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
