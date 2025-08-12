<x-team.card title="English Proficiency Test" headerClass="">
    @if (isset($clientLead?->client?->examData) && $clientLead?->client?->examData->count() > 0)
        <div class="education-item rounded-lg mb-5 relative bg-secondary-50">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                <!-- Checkboxes -->
                <div class="col-span-4">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $checkedTests = old('exam_data', $clientLead?->client?->examData?->pluck('exam_id')->toArray());
                            $existingTests = $clientLead?->client?->examData?->keyBy('exam_id');
                        @endphp

                        @foreach($englishProficiencyTest as $test)
                            <x-team.forms.checkbox
                                name="exam_data[]"
                                :value="$test->id"
                                :label="$test->name"
                                style="inline"
                                class="test-checkbox"
                                data-target="#modules-{{ $test->id }}"
                                :checked="in_array($test->id, $checkedTests)"
                            />

                            {{-- Hidden input for tracking initially selected tests --}}
                            @if(in_array($test->id, $checkedTests) && isset($existingTests[$test->id]))
                                <input type="hidden" name="exam_data[{{ $test->id }}]" value="{{ $test->id }}">
                            @endif
                        @endforeach

                    </div>
                </div>

                <!-- Score Fields -->
                <div class="col-span-4">
                    @foreach($englishProficiencyTest as $test)
                        @php
                            $isVisible = in_array($test->id, old('exam_data', $clientLead?->client?->examData->pluck('exam_id')->toArray()));
                            $clientTest = $clientLead?->client->examData->where('exam_id', $test->id)->first();
                            $scores = $clientTest ? $clientTest?->exam_dataScore->keyBy('exam_modual_id') : collect();
                        @endphp

                        <div id="modules-{{ $test->id }}" class="test-modules border-b border-gray-200 rounded bg-secondary mt-4" style="{{ $isVisible ? '' : 'display: none;' }}">
                            <h4 class="font-bold mb-2 text-indigo-700">{{ $test->name }} TEST</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Exam Date -->
                                <div class="relative">
                                    <x-team.forms.datepicker
                                        label="Exam Date"
                                        name="exam_date[{{ $test->id }}]"
                                        id="exam_date_{{ $test->id }}"
                                        placeholder="Select exam date"
                                        required="true"
                                        dateFormat="Y-m-d"
                                        :value="old('exam_date.' . $test->id, \Carbon\Carbon::parse(optional($clientTest)->exam_date)->format('d/m/Y'))"
                                        class="w-full flatpickr"
                                    />
                                </div>

                                @foreach($test?->moduals as $modual)
                                    @php
                                        $oldScore = old("exam_score.{$test->id}.{$modual->id}.score");
                                        $savedScore = $scores[$modual->id]->score ?? '';
                                    @endphp
                                    @if($scores->has($modual->id))
                                        <input type="hidden" name="exam_score[{{$test->id}}][id][{{$modual->id}}]" value="{{ $scores[$modual->id]->id }}">
                                    @endif

                                    <div class="relative">
                                        <x-team.forms.input
                                            type="text"
                                            :name="'exam_score[' . $test->id . '][' . $modual->id . '][score]'"
                                            :label="$modual->name"
                                            :value="$oldScore ?? $savedScore"
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
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="education-item rounded-lg mb-5 relative bg-secondary-50">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                <!-- Test Checkboxes in a Row -->
                <div class="col-span-4">
                    <label class="block font-semibold mb-2">Select Test:</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach($englishProficiencyTest as $test)
                            <x-team.forms.checkbox
                                name="exam_data[]"
                                :value="$test->id"
                                :label="$test->name"
                                style="inline"
                                class="test-checkbox"
                                data-target="#modules-{{ $test->id }}"
                                :checked="in_array($test->id, old('exam_data', []))"
                            />
                        @endforeach
                    </div>
                </div>

                <!-- Modules Input Fields -->
                <div class="col-span-4">
                    @foreach($englishProficiencyTest as $test)
                        <div id="modules-{{ $test->id }}" class="test-modules border-b border-gray-200 rounded pb-3 bg-secondary mt-4" style="display: none;">
                            <h4 class="font-bold mb-2 text-indigo-700">{{ $test->name }} TEST</h4>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div class="relative">
                                    <div class="relative">
                                        <x-team.forms.datepicker
                                            label="Exam Date"
                                            name="exam_date[{{ $test->id }}]"
                                            id="exam_date_{{ $test->id }}"
                                            placeholder="Select exam date"
                                            required="true"
                                            dateFormat="Y-m-d"
                                            :value="old('exam_date.' . $test->id)"
                                            class="w-full flatpickr"
                                        />
                                    </div>
                                </div>

                                @foreach($test->moduals as $modual)
                                    <div class="relative">
                                        <x-team.forms.input
                                            type="text"
                                            :name="'exam_score[' . $test->id . '][' . $modual->id . '][score]'"
                                            :label="$modual->name"
                                            :value="old('exam_score.' . $test->id . '.' . $modual->id . '.score')"
                                            :placeholder="$modual->range_score"
                                            {{-- data-range="{{ $modual->range_score }}" --}}

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
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-team.card>
