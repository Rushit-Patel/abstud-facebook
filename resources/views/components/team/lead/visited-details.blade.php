<!-- Single Checkbox outside the repeater -->
<div class="mb-5 mt-2">
    <x-team.forms.checkbox
        id="is_visitedCheckbox"
        name="is_visited"
        :value="1"
        label="Visited Any Country?"
        style="inline"
        class="kt-switch kt-switch-lg"
        :checked="old('is_visited') || (isset($visitedDataDatas) && $visitedDataDatas->count() > 0)"
    />
</div>

<!-- Repeater container -->
<div id="visited-section" class="hidden">
    <div id="visited-repeater">
        <div data-repeater-list="visited_visa">
            @if (isset($visitedDataDatas) && $visitedDataDatas->count() > 0)
                @foreach ($visitedDataDatas as $visitedDataData)
                    <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                        <button type="button" class="absolute top-0 right-0 text-red-500 remove-visited" data-repeater-delete>
                            <i class="ki-filled ki-trash text-lg text-destructive"></i>
                        </button>

                        <input type="hidden" name="id" value="{{ $visitedDataData->id }}">
                        <div class="visited-visa-fields grid grid-cols-1 lg:grid-cols-4 gap-5 mt-5">
                            <x-team.forms.select
                                name="visited_country"
                                label="Visited Country"
                                :options="$visitedCountry"
                                :selected="$visitedDataData->visited_country"
                                placeholder="Select country"
                                required
                            />

                            <x-team.forms.select
                                name="visited_visa_type"
                                label="Visa Type"
                                :options="$visitedVisaType"
                                :selected="$visitedDataData->visited_visa_type"
                                placeholder="Select visa type"
                                required
                            />

                            <x-team.forms.datepicker
                                label="Start Date"
                                name="start_date"
                                id="start_date"
                                placeholder="Select visited start date"
                                required="true"
                                dateFormat="Y-m-d"
                                maxDate="today"
                                :value="!empty($visitedDataData->start_date) ? \Carbon\Carbon::parse($visitedDataData->start_date)->format('d-m-Y') : null"
                                class="w-full flatpickr" />

                            <x-team.forms.datepicker
                                label="End Date"
                                name="end_date"
                                id="end_date"
                                placeholder="Select visited end date"
                                required="true"
                                dateFormat="Y-m-d"
                                maxDate="today"
                                :value="!empty($visitedDataData->end_date) ? \Carbon\Carbon::parse($visitedDataData->end_date)->format('d-m-Y') : null"
                                class="w-full flatpickr" />

                        </div>
                    </div>

                @endforeach
            @else
                <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                    <button type="button" class="absolute top-0 right-0 text-red-500 remove-visited" data-repeater-delete>
                        <i class="ki-filled ki-trash text-lg text-destructive"></i>
                    </button>
                    <div class="visited-visa-fields grid grid-cols-1 lg:grid-cols-4 gap-5 mt-5">
                        <x-team.forms.select
                            name="visited_country"
                            label="Visited Country"
                            :options="$visitedCountry"
                            placeholder="Select country"
                            required
                        />

                        <x-team.forms.select
                            name="visited_visa_type"
                            label="Visa Type"
                            :options="$visitedVisaType"
                            placeholder="Select visa type"
                            required
                        />

                        <x-team.forms.datepicker
                            label="Start Date"
                            name="start_date"
                            id="start_date"
                            placeholder="Select visited start date"
                            required="true"
                            dateFormat="Y-m-d"
                            maxDate="today"
                            class="w-full flatpickr" />

                        <x-team.forms.datepicker
                            label="End Date"
                            name="end_date"
                            id="end_date"
                            placeholder="Select visited end date"
                            required="true"
                            dateFormat="Y-m-d"
                            maxDate="today"
                            class="w-full flatpickr" />

                    </div>
                </div>
            @endif
        </div>

    <!-- Add More Button -->
        <div class="mt-4">
            <button type="button" class="kt-btn kt-btn-sm  kt-btn-primary" data-repeater-create>+ Add Visited</button>
        </div>
    </div>
</div>

