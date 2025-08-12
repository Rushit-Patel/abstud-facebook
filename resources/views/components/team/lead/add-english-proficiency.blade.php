<x-team.card title="English Proficiency Test" headerClass="">
    <div class="education-item rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Test Checkboxes in a Row -->
            <div class="col-span-4">
                <div class="flex flex-wrap flex-row gap-5">
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
                        <h4 class="font-bold mb-2 text-indigo-700">{{ $test->name }}</h4>

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
</x-team.card>
