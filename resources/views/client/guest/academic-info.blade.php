@extends('client.layouts.guest')

@section('card-width', 'max-w-[800px]')
@section('content')
    <form action="{{ route('client.guest.academic-info.store',[$clientId,$purpose->id]) }}" method="POST" class="form mb-5" enctype="multipart/form-data">
        @csrf
        <div class="flex flex-col gap-5">
            <x-team.card title="Preference" headerClass="">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Education Level -->
                    @if ($purpose->id == '2')
                        <div class="col-span-1" id="coachingDiv">
                            <x-team.forms.select name="coaching" label="Coaching" :options="$coachings"
                                placeholder="Select Coaching" searchable="true" required id="coaching" />
                        </div>
                    @else
                        <div class="col-span-1" id="countryDiv">
                            <x-team.forms.select name="country" label="Country" :options="$foreignCountries"
                                placeholder="Select Country" searchable="true" required id="country" />
                        </div>
                        <div class="col-span-1" id="SecondcountryDiv">
                            <x-team.forms.select name="second_country[]" label="Second Country" :options="$foreignCountries"
                                placeholder="Select Second Country" searchable="true" multiple="true" id="country" />
                        </div>
                    @endif
                </div>
            </x-team.card>
            <x-team.card title="Academic Information" headerClass="">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Education Level -->
                    <div class="col-span-1">
                        <x-team.forms.select name="education_level" label="Education Level" :options="$educationLevel"
                            placeholder="Select Education Level" searchable="true" required id="education_level"
                            :selected="$getDetails?->educationDetailsLast?->education_level"/>
                    </div>

                    <div class="col-span-1 field-board">
                        <x-team.forms.select name="education_board" label="Education Board" :options="$educationBoard" :selected="$getDetails?->educationDetailsLast?->education_board" required />
                    </div>

                    <div class="col-span-1 field-language">
                        <x-team.forms.input name="language" label="Language" type="text" placeholder="Enter language"
                            required
                            :value="$getDetails?->educationDetailsLast?->language"/>
                    </div>

                    <div class="col-span-1 field-stream">
                        <x-team.forms.select name="education_stream" label="Education Stream" :options="[]"
                            placeholder="Select Stream" required id="education_stream"
                            :selected="$getDetails?->educationDetailsLast?->education_stream"/>
                    </div>
                    <div class="col-span-1 field-passing_year">
                        <x-team.forms.input name="passing_year" label="Passing Year" type="text"
                            placeholder="Enter passing year" required
                            :value="$getDetails?->educationDetailsLast?->passing_year"/>
                    </div>

                    <div class="col-span-1 field-result">
                        <x-team.forms.input name="result" label="Result" type="text" placeholder="Enter result"
                        :value="$getDetails?->educationDetailsLast?->result" required />
                    </div>

                    <div class="col-span-1 field-no_of_backlog">
                        <x-team.forms.input name="no_of_backlog" label="No of Backlog" type="text"
                            placeholder="Enter no of backlog"
                            :value="$getDetails?->educationDetailsLast?->no_of_backlog" required />
                    </div>

                    <div class="col-span-1 field-institute">
                        <x-team.forms.input name="institute" label="Institute" type="text" placeholder="Enter institute"
                        :value="$getDetails?->educationDetailsLast?->institute"
                            required />
                    </div>
                </div>
            </x-team.card>

            <x-team.card title="English Proficiency Test" headerClass="">
                <div class="education-item rounded-lg mb-5 relative bg-secondary-50">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                        @php
                            $checkedTests = old('exam_data') ?? ($getDetails?->examData?->pluck('exam_id')->toArray() ?? []);
                            $existingTests = $getDetails?->examData?->keyBy('exam_id') ?? collect();
                        @endphp

                        {{-- Checkboxes --}}
                        <div class="col-span-4">
                            <div class="flex flex-wrap gap-4">
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

                                    {{-- Hidden input for already saved exams --}}
                                    @if(in_array($test->id, $checkedTests) && isset($existingTests[$test->id]))
                                        <input type="hidden" name="exam_data[{{ $test->id }}]" value="{{ $test->id }}">
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Score Fields --}}
                        <div class="col-span-4">
                            @foreach($englishProficiencyTest as $test)
                                @php
                                    $isVisible = in_array($test->id, $checkedTests);
                                    $clientTest = $existingTests[$test->id] ?? null;
                                    $scores = $clientTest?->exam_dataScore?->keyBy('exam_modual_id') ?? collect();
                                    $examDate = old('exam_date.' . $test->id) ?? optional($clientTest)->exam_date;
                                @endphp

                                <div id="modules-{{ $test->id }}" class="test-modules border-b border-gray-200 rounded pb-3 bg-secondary mt-4" style="{{ $isVisible ? '' : 'display: none;' }}">
                                    <h4 class="font-bold mb-2 text-indigo-700">{{ $test->name }}</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        {{-- Exam Date --}}
                                        <div class="relative">
                                            <x-team.forms.datepicker
                                                label="Exam Date"
                                                name="exam_date[{{ $test->id }}]"
                                                id="exam_date_{{ $test->id }}"
                                                placeholder="Select exam date"
                                                dateFormat="Y-m-d"
                                                :value="($examDate ? \Carbon\Carbon::parse($examDate)->format('d-m-Y') : '')"
                                                class="w-full flatpickr"
                                            />
                                        </div>

                                        {{-- Modules --}}
                                        @foreach($test->moduals as $modual)
                                            @php
                                                $oldScore = old("exam_score.{$test->id}.{$modual->id}.score");
                                                $savedScore = $scores[$modual->id]->score ?? '';
                                            @endphp

                                            {{-- Hidden ID for update --}}
                                            @if($scores->has($modual->id))
                                                <input type="hidden" name="exam_score[{{$test->id}}][id][{{$modual->id}}]" value="{{ $scores[$modual->id]->id }}">
                                            @endif

                                            <div class="relative">
                                                <x-team.forms.input
                                                    type="text"
                                                    :name="'exam_score[' . $test->id . '][' . $modual->id . '][score]'"
                                                    :label="$modual->name"
                                                    :value="$oldScore ?? $savedScore"
                                                    :placeholder="'Range: ' . $modual->minimum_score . '-' . $modual->maximum_score . ' (Step: ' . $modual->range_score . ')'"
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
        </div>

        <div class="flex justify-end gap-2.5 pt-5 px-5 border-t border-gray-200">
            <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                Continue
            </button>
        </div>
    </form>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        // Map of levelId => required field keys
        var educationDetailsMap = {!! $educationLevel->keyBy('id')->map->required_details !!};

        var getStreamsRoute = "{{ route('team.education.get-streams', ['levelId' => '__LEVEL_ID__']) }}";

        var allFieldKeys = ['board', 'language', 'stream', 'passing_year', 'result', 'no_of_backlog', 'institute'];

        function updateVisibleFields(levelId) {
            var visibleFields = educationDetailsMap[levelId] || [];

            $.each(allFieldKeys, function (i, key) {
                var $field = $('.field-' + key);
                if ($field.length) {
                    var $input = $field.find("input, select, textarea");
                    if (visibleFields.includes(key)) {
                        $field.show();
                        $input.prop('required', true);
                    } else {
                        $field.hide();
                        $input.prop('required', false);
                    }
                }
            });
        }

        function fetchStreams(levelId, selectedStream = null) {
            var url = getStreamsRoute.replace('__LEVEL_ID__', levelId);
            var $streamSelect = $("#education_stream");

            if (!$streamSelect.length) {
                console.error("Element with ID 'education_stream' not found.");
                return;
            }

            $streamSelect.html('<option value="">Loading...</option>');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $streamSelect.empty().append('<option value="">Select Stream</option>');

                    $.each(data.streams || {}, function (key, value) {
                        const isSelected = selectedStream && selectedStream == key ? 'selected' : '';
                        $streamSelect.append(`<option value="${key}" ${isSelected}>${value}</option>`);
                    });

                    // Reinitialize select2 or other UI components if needed
                    if ($.fn.select2) {
                        $streamSelect.select2();
                    } else if (typeof KTComponents !== 'undefined') {
                        KTComponents.init();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Stream fetch error:", error);
                    $streamSelect.html('<option value="">Error loading streams</option>');
                }
            });
        }

        var $levelSelect = $("#education_level");
        if ($levelSelect.length) {
            $levelSelect.on("change", function () {
                var levelId = $(this).val();
                updateVisibleFields(levelId);
                fetchStreams(levelId);
            });

            // Initial call for edit
            var selectedLevelId = $levelSelect.val();
            @if (!empty($getDetails?->educationDetailsLast?->education_stream))
                var selectedStream = "{{ $getDetails->educationDetailsLast->education_stream }}";
                if (selectedLevelId) {
                    updateVisibleFields(selectedLevelId);
                    fetchStreams(selectedLevelId, selectedStream);
                }
            @else
                if (selectedLevelId) {
                    updateVisibleFields(selectedLevelId);
                    fetchStreams(selectedLevelId);
                }
            @endif
        }

        // Checkbox toggle for modules
        function toggleModules() {
            $('.test-checkbox').each(function () {
                var $checkbox = $(this);
                var $target = $($checkbox.data('target'));

                if ($checkbox.is(':checked')) {
                    $target.show();
                    $target.find('input[type="text"]').prop('required', true);
                } else {
                    $target.hide();
                    $target.find('input[type="text"]').prop('required', false).val('');
                }
            });
        }

        toggleModules();
        $('.test-checkbox').on('change', toggleModules);


        // Score validation
        function validateScore(input) {
            const $input = $(input);
            const $errorMsg = $input.closest('.relative').find('.error-message');
            const value = parseFloat($input.val());
            const min = parseFloat($input.data('min'));
            const max = parseFloat($input.data('max'));
            const step = parseFloat($input.data('step'));

            // Clear previous error
            $errorMsg.addClass('hidden').text('');
            $input.removeClass('border-red-500');

            if (!$input.val()) {
                return true; // Let required validation handle empty values
            }

            if (isNaN(value)) {
                $errorMsg.removeClass('hidden').text('Please enter a valid number');
                $input.addClass('border-red-500');
                return false;
            }

            if (value < min || value > max) {
                $errorMsg.removeClass('hidden').text(`Score must be between ${min} and ${max}`);
                $input.addClass('border-red-500');
                return false;
            }

            // Check if value follows the step increment
            const remainder = (value - min) % step;
            console.log(`Value: ${value}, Min: ${min}, Step: ${step}, Remainder: ${remainder}`);

            if (Math.abs(remainder) > 0.001 && Math.abs(remainder - step) > 0.001) {
                $errorMsg.removeClass('hidden').text(`Score must be in increments of ${step}`);
                $input.addClass('border-red-500');
                return false;
            }

            return true;
        }

        // Attach validation to score inputs
        $(document).on('input blur', '.score-input', function() {
            validateScore(this);
        });
    });
</script>
@endpush

