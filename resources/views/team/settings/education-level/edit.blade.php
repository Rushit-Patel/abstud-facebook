@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Education Level', 'url' => route('team.settings.education-level.index')],
    ['title' => 'Edit Education Level']
];
@endphp

<x-team.layout.app title="Edit Education Level" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Education Level: {{ $educationLevel->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update education-level information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.education-level.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Education Level Information" headerClass="">
                <form action="{{ route('team.settings.education-level.update', $educationLevel) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid gap-5">
                                    <x-team.forms.input
                                        name="name"
                                        label="Education Level Name"
                                        type="text"
                                        placeholder="Enter education-level name"
                                        :value="old('name', $educationLevel->name)"
                                        required />
                                </div>
                            </x-team.card>
                        </div>

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
                                                {{ old('status', $educationLevel->status) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this educationLevel inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Priority Settings">
                                    <div class="flex flex-col gap-1 mt-4">
                                        <x-team.forms.input
                                            name="priority"
                                            label="Priority"
                                            type="number"
                                            placeholder="Enter priority "
                                            :value="old('priority', $educationLevel->priority)"
                                            required />
                                    </div>
                                </x-team.card>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <x-team.card title="Required Optional Check">
                                    <div class="flex-wrap items-center">
                                        @php
                                            $selected = old('education_levels', $selectedRequiredFields ?? []);
                                        @endphp

                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="Education Board"
                                            style="inline"
                                            value="board"
                                            :checked="in_array('board', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="Language"
                                            style="inline"
                                            value="language"
                                            :checked="in_array('language', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="Stream"
                                            style="inline"
                                            value="stream"
                                            :checked="in_array('stream', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="Passing Year"
                                            style="inline"
                                            value="passing_year"
                                            :checked="in_array('passing_year', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="Result"
                                            style="inline"
                                            value="result"
                                            :checked="in_array('result', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="No of Backlog"
                                            style="inline"
                                            value="no_of_backlog"
                                            :checked="in_array('no_of_backlog', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                        <x-team.forms.checkbox
                                            name="education_levels[]"
                                            label="Institute"
                                            style="inline"
                                            value="institute"
                                            :checked="in_array('institute', $selected)"
                                            style="default"
                                            size="sm"
                                            class="mb-2"
                                        />
                                    </div>
                                </x-team.card>
                            </div>
                        </div>


                    </div>


                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.education-level.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Education Level
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
