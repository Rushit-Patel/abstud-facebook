@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'English Proficiency Test', 'url' => route('team.settings.english-proficiency-test.index')],
    ['title' => 'Create English Proficiency Test']
];
@endphp

<x-team.layout.app title="Create English Proficiency Test" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New English Proficiency Test
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new english-proficiency-test to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.english-proficiency-test.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="English Proficiency Test Information" headerClass="">
                <form action="{{ route('team.settings.english-proficiency-test.store') }}" method="POST" class="form">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid gap-5">
                                    <!-- Test Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="English Proficiency Test Name"
                                        type="text"
                                        placeholder="Enter english proficiency test name"
                                        :value="old('name')"
                                        required />

                                        <x-team.forms.input
                                        name="result_days"
                                        label="Result Day"
                                        type="number"
                                        placeholder="Enter result day name"
                                        :value="old('result_days')"
                                        required />
                                </div>
                            </x-team.card>
                        </div>

                        <!-- Status -->
                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <x-team.card title="Status Settings">

                                 <x-team.forms.input
                                        name="priority"
                                        label="Priority"
                                        type="number"
                                        placeholder="Enter priority e.g 1"
                                        :value="old('priority')"
                                        required />

                                    <div class="mt-3">
                                        <x-team.forms.select
                                            name="coaching_id"
                                            label="Coaching"
                                            :options="$coaching"
                                            :selected="old('coaching_id')"
                                            placeholder="Select coaching"
                                            searchable="true"
                                            required="true"
                                            id="coaching_id" />
                                    </div>

                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">Status</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm" name="status" type="checkbox" value="1"
                                                {{ old('status', true) ? 'checked' : '' }} />
                                            <span class="kt-checkbox-label">Uncheck to make this english-proficiency-test inactive</span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <x-team.card title="Module List">
                            <div id="repeater-wrapper" class="space-y-5">
                                <div class="repeater-item grid grid-cols-1 md:grid-cols-4 gap-5 border p-4 rounded-lg relative bg-gray-50">
                                    <x-team.forms.input name="modules[0][name]" label="Module Name" placeholder="e.g. Listening" required />
                                    <x-team.forms.input name="modules[0][minimum_score]" label="Minimum Score" placeholder="e.g. 0" required />
                                    <x-team.forms.input name="modules[0][maximum_score]" label="Maximum Score" placeholder="e.g. 9" required />
                                    <x-team.forms.input name="modules[0][range_score]" label="Range Score" placeholder="e.g. 0.5" required/>

                                    <button type="button" class="absolute top-2 right-2 text-red-600 font-bold remove-btn hidden" onclick="removeModule(this)">
                                        ×
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="kt-btn kt-btn-primary mt-4" onclick="addModule()">
                                + Add Module
                            </button>
                        </x-team.card>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.english-proficiency-test.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create English Proficiency Test
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

<script>
    let moduleIndex = 1;

    function addModule() {
        const wrapper = document.getElementById('repeater-wrapper');

        const item = document.createElement('div');
        item.className = 'repeater-item grid grid-cols-1 md:grid-cols-4 gap-5 border p-4 rounded-lg relative bg-gray-50';

        item.innerHTML = `
                <x-team.forms.input name="modules[${moduleIndex}][name]" label="Module Name" placeholder="e.g. Listening" required />
                <x-team.forms.input name="modules[${moduleIndex}][minimum_score]" label="Minimum Score" placeholder="e.g. 0" required />
                <x-team.forms.input name="modules[${moduleIndex}][maximum_score]" label="Maximum Score" placeholder="e.g. 9" required />
                <x-team.forms.input name="modules[${moduleIndex}][range_score]" label="Range Score" placeholder="e.g. 0.5" required/>

            <button type="button" class="absolute top-2 right-2 text-red-600 font-bold remove-btn" onclick="removeModule(this)">×</button>
        `;
        wrapper.appendChild(item);
        moduleIndex++;
    }

    function removeModule(btn) {
        btn.closest('.repeater-item').remove();
    }
</script>

    @endpush
</x-team.layout.app>
