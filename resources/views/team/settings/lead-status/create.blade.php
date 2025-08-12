@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Lead Status', 'url' => route('team.settings.lead-status.index')],
    ['title' => 'Create Lead Status']
];
@endphp

<x-team.layout.app title="Create Lead Status" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Lead Status
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new lead-status to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.lead-status.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Lead Status Information" headerClass="">
                <form action="{{ route('team.settings.lead-status.store') }}" method="POST" class="form">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid gap-5">
                                    <x-team.forms.input
                                        name="name"
                                        label="Lead Status Name"
                                        type="text"
                                        placeholder="Enter lead status name"
                                        :value="old('name')"
                                        required />
                                </div>
                            </x-team.card>
                        </div>
                        <!-- Status -->

                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">
                                    <div class="flex flex-col gap-1">
                                        <label class="kt-form-label font-normal text-mono">Status</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="status"
                                                type="checkbox"
                                                value="1"
                                                {{ old('status', true) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this lead status inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.lead-status.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Lead Status
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
