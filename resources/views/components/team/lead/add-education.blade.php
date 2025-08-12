<x-team.card title="Education Information" headerClass="">
    <div id="education-repeater">
        <div data-repeater-list="education">
            <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                <button type="button" class="absolute top-0 right-0 m-2 text-red-500 remove-education" data-repeater-delete>
                    <i class="ki-filled ki-trash text-lg text-destructive"></i>
                </button>
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 py-5">
                    <!-- Education Level -->
                    <div class="col-span-1">
                        <x-team.forms.select name="education_level" label="Education Level"
                            :options="$educationLevel" class="education_level"  placeholder="Select Education Level" searchable="true"
                            required />
                    </div>
                    <div class="col-span-1 field-board">
                        <x-team.forms.select name="education_board" label="Education Board"
                            :options="$educationBoard" class="education_board" required />
                    </div>

                    <div class="col-span-1 field-language">
                        <x-team.forms.input name="language" label="Language" type="text"
                            placeholder="Enter language" required />
                    </div>

                    <div class="col-span-1 field-stream">
                        <x-team.forms.select name="education_stream" label="Education Stream"
                            :options="[]" class="education_stream" placeholder="Select Stream" required />
                    </div>

                    <div class="col-span-1 field-passing_year">
                        <x-team.forms.input name="passing_year" label="Passing Year" type="text"
                            placeholder="Enter passing year" required />
                    </div>

                    <div class="col-span-1 field-result">
                        <x-team.forms.input name="result" label="Result" type="text"
                            placeholder="Enter result" required />
                    </div>

                    <div class="col-span-1 field-no_of_backlog">
                        <x-team.forms.input name="no_of_backlog" label="No of Backlog" type="text"
                            placeholder="Enter no of backlog" required />
                    </div>

                    <div class="col-span-1 field-institute">
                        <x-team.forms.input name="institute" label="Institute" type="text"
                            placeholder="Enter institute" required />
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button type="button" class="kt-btn kt-btn-sm  kt-btn-primary" data-repeater-create>+ Add Education</button>
        </div>
    </div>
</x-team.card>
<!-- Add More Button -->
