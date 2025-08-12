<x-team.card title="Mock Test Result Information" headerClass="">

@if (isset($clientCoaching->getMockTestStudent) && $clientCoaching->getMockTestStudent->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">

    {{-- Result Date --}}
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
                :value="old('result_date', \Carbon\Carbon::parse($clientCoaching->getMockTestStudent->result_date)->format('d-m-Y'))"
                class="w-full flatpickr"
            />
        </div>
    </div>

    @foreach ($englishProficiencyTest as $test)
        @php
            $moduals = $test->moduals ?? [];
        @endphp

        @foreach ($moduals as $modual)
            @php
                // Get saved score from old input or existing result if any
                $savedScore = old(
                    "result_score.{$test->id}.{$modual->id}.score",
                    optional($modual->MockTestResult)->score ?? ''
                );
            @endphp

            <div class="relative">
                <x-team.forms.input
                    type="text"
                    :name="'result_score[' . $test->id . '][' . $modual->id . '][score]'"
                    :label="$modual->name"
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
    @endforeach

</div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5">

    {{-- Result Date --}}
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
                :value="old('result_date', now()->format('d-m-Y'))"
                class="w-full flatpickr"
            />
        </div>
    </div>

    @foreach ($englishProficiencyTest as $test)
        @php
            $moduals = $test->moduals ?? [];
        @endphp

        @foreach ($moduals as $modual)
            <div class="relative">
                <x-team.forms.input
                    type="text"
                    :name="'result_score[' . $test->id . '][' . $modual->id . '][score]'"
                    :label="$modual->name"
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
    @endforeach

</div>
@endif

</x-team.card>
