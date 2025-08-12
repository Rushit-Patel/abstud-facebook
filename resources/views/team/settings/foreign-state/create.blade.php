@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Foreign State', 'url' => route('team.settings.foreign-state.index')],
    ['title' => 'Create Foreign State']
];
@endphp

<x-team.layout.app title="Create Foreign State" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Foreign State
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new foreign-state to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.foreign-state.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Foreign State Information" headerClass="">
                <form action="{{ route('team.settings.foreign-state.store') }}" method="POST" class="form">
                    @csrf
                    <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Basic Information -->
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid gap-5">
                                    <!-- Country Selection -->
                                    <div class="flex flex-col gap-1">
                                        <label class="kt-form-label font-normal text-mono required"><b>Country <span style="color: #E7004A;">*</span></b></label>
                                        <select name="country_id" class="kt-select" required>
                                            <option value="">Select status</option>
                                            @foreach($foreign_countries as $foreign_counry)
                                                <option value="{{ $foreign_counry->id }}" {{ old('country_id') == $foreign_counry->id ? 'selected' : '' }}>
                                                    {{ $foreign_counry->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- State Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="State Name"
                                        type="text"
                                        placeholder="Enter State name"
                                        :value="old('name')"
                                        required />
                                </div>
                            </x-team.card>
                        </div>

                    <!-- Currency & Regional -->
                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">
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
                                                Active (Enable this state for selection)
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.foreign-state.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Foreign State
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
