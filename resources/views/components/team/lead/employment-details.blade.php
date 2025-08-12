<!-- Employment Card -->
<x-team.card title="Employment Information" headerClass="bg-secondary-100">
    <div class="employment-item rounded-lg mb-2 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Employment Checkbox -->
            <div class="col-span-4">
                <div class="flex flex-wrap gap-4">
                    <x-team.forms.checkbox
                        id="is_employmentCheckbox"
                        name="is_employment"
                        :value="1"
                        label="Employment Information?"
                        style="inline"
                        class="kt-switch kt-switch-lg"
                        :checked="old('is_employment') || (isset($employmentData) && $employmentData->count() > 0)"
                    />
                </div>
            </div>
        </div>

        <!-- Employment Repeater Section -->
        <div id="employment-section" class="mt-5 hidden">
            <div id="employment-repeater">
                <div data-repeater-list="employment">
                    @if (isset($employmentData) && $employmentData->count() > 0)
                        @foreach ($employmentData as $employment)
                            {{-- <div class="employment-item rounded-lg mb-3 relative bg-secondary-50"> --}}
                            <div class="employment-item rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                            <button type="button" class="absolute top-0 right-0 text-red-500 remove-employment" data-repeater-delete>
                                <i class="ki-filled ki-trash text-lg text-destructive"></i>
                            </button>
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                                    <input type="hidden" name="id" value="{{ $employment->id }}">
                                    <x-team.forms.input
                                        name="company_name"
                                        label="Company"
                                        type="text"
                                        placeholder="Enter company name"
                                        :value="$employment->company_name"
                                        required />

                                    <x-team.forms.input
                                        name="designation"
                                        label="Designation"
                                        type="text"
                                        placeholder="Enter designation"
                                        :value="$employment->designation"
                                        required />

                                    <x-team.forms.datepicker
                                        label="Start Date"
                                        name="start_date"
                                        id="employment_start_date"
                                        placeholder="Select employment start date"
                                        required="true"
                                        dateFormat="Y-m-d"
                                        maxDate="today"
                                        :value="!empty($employment->start_date) ? \Carbon\Carbon::parse($employment->start_date)->format('d-m-Y') : null"
                                        class="w-full flatpickr" />

                                    <x-team.forms.checkbox
                                        id="employment_is_working"
                                        name="is_working"
                                        :value="1"
                                        label="Currently Working ?"
                                        style="inline"
                                        class="kt-switch kt-switch-lg"
                                        :checked="$employment->is_working"
                                    />

                                    <div class="field-end-date">
                                        <x-team.forms.datepicker
                                            label="End Date"
                                            name="end_date"
                                            id="employment_end_date"
                                            placeholder="Select employment end date"
                                            required="true"
                                            dateFormat="Y-m-d"
                                            maxDate="today"
                                            :value="!empty($employment->end_date) ? \Carbon\Carbon::parse($employment->end_date)->format('d-m-Y') : null"
                                            class="w-full flatpickr" />
                                    </div>

                                    <div class="field-no-of-year">
                                        <x-team.forms.input
                                            name="no_of_year"
                                            label="No Of Years"
                                            type="text"
                                            placeholder="Enter no of year"
                                            :value="$employment->no_of_year"
                                            required />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- FIXED: Added 'employment-item' class -->
                        <div class="employment-item rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                            <button type="button" class="absolute top-0 right-0 text-red-500 remove-employment" data-repeater-delete>
                                <i class="ki-filled ki-trash text-lg text-destructive"></i>
                            </button>

                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                                <x-team.forms.input
                                    name="company_name"
                                    label="Company"
                                    type="text"
                                    placeholder="Enter company name"
                                    required />

                                <x-team.forms.input
                                    name="designation"
                                    label="Designation"
                                    type="text"
                                    placeholder="Enter designation"
                                    required />

                                <x-team.forms.datepicker
                                    label="Start Date"
                                    name="start_date"
                                    id="employment_start_date"
                                    placeholder="Select employment start date"
                                    required="true"
                                    dateFormat="Y-m-d"
                                    maxDate="today"
                                    class="w-full flatpickr" />

                                <x-team.forms.checkbox
                                    id="employment_is_working"
                                    name="is_working"
                                    :value="1"
                                    label="Currently Working ?"
                                    style="inline"
                                    class="kt-switch kt-switch-lg"
                                />

                                <div class="field-end-date">
                                    <x-team.forms.datepicker
                                        label="End Date"
                                        name="end_date"
                                        id="employment_end_date"
                                        placeholder="Select employment end date"
                                        required="true"
                                        dateFormat="Y-m-d"
                                        maxDate="today"
                                        class="w-full flatpickr" />
                                </div>

                                <div class="field-no-of-year">
                                    <x-team.forms.input
                                        name="no_of_year"
                                        label="No Of Years"
                                        type="text"
                                        placeholder="Enter no of year"
                                        required />
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Add More Button -->
                <div class="mt-4">
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-primary" data-repeater-create>+ Add Employment</button>
                </div>
            </div>
        </div>
    </div>
</x-team.card>
