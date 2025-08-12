<x-team.card title="Exam Book Information" headerClass="">
@if (isset($bookingData) && $bookingData->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="english_proficiency_test_id"
                    id="english_proficiency_test_id"
                    label="English Proficiency Test"
                    :options="$englishProficiencyTest"
                    :selected="old('english_proficiency_test_id',$bookingData->english_proficiency_test_id)"
                    placeholder="Select english proficiency test"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="exam_mode_id"
                    id="exam_mode_id"
                    label="Exam Mode"
                    :options="[]"
                    :selected="old('exam_mode_id',$bookingData->exam_mode_id)"
                    placeholder="Select exam mode"
                    required="true"
                    searchable="true"
                    data-selected="{{ old('exam_mode_id', $bookingData->exam_mode_id) }}" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="exam_center_id"
                    label="Exam Center"
                    :options="$examCenter"
                    :selected="old('exam_center_id',$bookingData->exam_center)"
                    placeholder="Select exam center"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.datepicker
                    label="Exam Date"
                    name="exam_date"
                    id="exam_date"
                    placeholder="Select exam date"
                    required="true"
                    dateFormat="Y-m-d"
                    :value="old('exam_date', \Carbon\Carbon::parse($bookingData->exam_date)->format('d-m-Y'))"
                    class="w-full flatpickr" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="exam_way_id"
                    label="Exam Way"
                    :options="$examWay"
                    :selected="old('exam_way_id',$bookingData->exam_way)"
                    placeholder="Select exam way"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <div class="col-span-1" id="result_days_wrapper" style="display: none;">
            <div class="grid gap-5">
                <x-team.forms.input
                    name="result_days"
                    id="result_days"
                    label="Result Day"
                    readonly="true"
                    placeholder="Result days"
                    :value="old('result_days')" />
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.datepicker
                    label="Result Date"
                    name="result_date"
                    id="result_date"
                    placeholder="Select result_date"
                    required="true"
                    readonly="true"
                    dateFormat="Y-m-d"
                    :value="old('result_date', \Carbon\Carbon::parse($bookingData->result_date)->format('d-m-Y'))"
                    class="w-full flatpickr" />
            </div>
        </div>
            {{-- Modules from selected English Proficiency Test --}}
            @php
                $moduals = $bookingData->englishProficiencyTest?->moduals ?? [];
            @endphp

            @foreach ($moduals as $modual)

                @php
                    // Get saved score from old() or from relation if exists
                    $savedScore = old(
                        'exam_score.' . $bookingData->english_proficiency_test_id . '.' . $modual->id . '.score',
                        optional($modual->bookingResult)->score ?? ''
                    );
                @endphp

                <div class="relative">
                    <x-team.forms.input
                        type="text"
                        :name="'exam_score[' . $bookingData->english_proficiency_test_id . '][' . $modual->id . '][score]'"
                        {{-- :name='moduals[{{ $modual->id }}]' --}}
                        :label="$modual->name"
                        {{-- :value="old('moduals.' . $bookingData->english_proficiency_test_id . '.' . $modual->id . '.score', $modual->saved_score ?? '')" --}}
                        :value="$savedScore"
                        :placeholder="$modual->range_score"
                        data-min="{{ $modual->minimum_score }}"
                        data-max="{{ $modual->maximum_score }}"
                        data-step="{{ $modual->range_score }}"
                        class="score-input"
                        required
                    />
                    <p class="text-red-600 text-sm mt-1 error-message hidden"></p>
                </div>

            @endforeach



    </div>

@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">
        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="english_proficiency_test_id"
                    id="english_proficiency_test_id"
                    label="English Proficiency Test"
                    :options="$englishProficiencyTest"
                    :selected="old('english_proficiency_test_id')"
                    placeholder="Select english proficiency test"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="exam_mode_id"
                    id="exam_mode_id"
                    label="Exam Mode"
                    :options="[]"
                    :selected="old('exam_mode_id')"
                    placeholder="Select exam mode"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="exam_center_id"
                    label="Exam Center"
                    :options="$examCenter"
                    :selected="old('exam_center_id')"
                    placeholder="Select exam center"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.datepicker
                    label="Exam Date"
                    name="exam_date"
                    id="exam_date"
                    placeholder="Select exam date"
                    required="true"
                    dateFormat="Y-m-d"
                    :value="old('exam_date', \Carbon\Carbon::today()->format('d/m/Y'))"
                    class="w-full flatpickr" />
            </div>
        </div>

        <div class="col-span-1">
            <div class="grid gap-5">
                <x-team.forms.select
                    name="exam_way_id"
                    label="Exam Way"
                    :options="$examWay"
                    :selected="old('exam_way_id')"
                    placeholder="Select exam way"
                    required="true"
                    searchable="true" />
            </div>
        </div>

        <!-- Result Days (Hidden Initially) -->
        <div class="col-span-1" id="result_days_wrapper" style="display: none;">
            <div class="grid gap-5">
                <x-team.forms.input
                    name="result_days"
                    id="result_days"
                    label="Result Day"
                    readonly="true"
                    placeholder="Result days"
                    :value="old('result_days')" />
            </div>
        </div>


    </div>
@endif

</x-team.card>
