@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Foreign Country', 'url' => route('team.settings.foreign-country.index')],
    ['title' => 'Edit Foreign Country']
];
@endphp

<x-team.layout.app title="Edit Foreign Country" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Foreign Country: {{ $ForeignCountry->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update coaching information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.foreign-country.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Foreign Country Information" headerClass="">
                <form action="{{ route('team.settings.foreign-country.update', $ForeignCountry) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')

                                    <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Basic Information -->
                    <div class="col-span-1">
                        <x-team.card title="Basic Information">
                            <div class="grid gap-5">
                                <!-- Country Name -->
                                <x-team.forms.input
                                    name="name"
                                    label="Country Name"
                                    type="text"
                                    placeholder="Enter country name"
                                    :value="old('name', $ForeignCountry->name)"
                                    required />
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Currency & Regional -->
                    <div class="col-span-1">
                        <div class="grid gap-5 lg:gap-7.5">

                            <!-- Status -->
                            <x-team.card title="Status Settings">
                                <x-team.forms.input
                                    name="priority"
                                    label="Priority"
                                    type="number"
                                    placeholder="Enter priority "
                                    :value="old('priority', $ForeignCountry->priority)"
                                    required />

                                <div class="flex flex-col gap-1 mt-4">
                                    <label class="kt-form-label font-normal text-mono">Status</label>
                                    <label class="kt-label">
                                        <input class="kt-checkbox kt-checkbox-sm"
                                            name="status"
                                            type="checkbox"
                                            value="1"
                                            {{ old('status', $ForeignCountry->status) ? 'checked' : '' }}
                                        />
                                        <span class="kt-checkbox-label">
                                            Active (Enable this country for selection)
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
                                {{ $ForeignCountry->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Last Updated</label>
                            <div class="text-sm text-gray-700">
                                {{ $ForeignCountry->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.foreign-country.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Foreign Country
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
