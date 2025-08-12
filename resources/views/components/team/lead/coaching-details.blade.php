<x-team.card title="Coaching Details" headerClass="">

@if (isset($coachingData) && $coachingData->count() > 0)
    <div class="coaching-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- coaching Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">

                <input type="hidden" name="batch" id="selected_batch" value="{{ $coachingData->batch_id }}">
                <input type="hidden" id="selected_coaching_materials" value="{{ implode(',', $coachingData->getCoachingMaterial->pluck('material_id')->toArray()) }}">

                <!-- Joining Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Joining Date"
                        id="joining_date"
                        name="joining_date"
                        type="date"
                        :value="old('joining_date', \Carbon\Carbon::parse($coachingData->joining_date)->format('d-m-Y'))"
                        required
                    />
                </div>
                <div>
                    <x-team.forms.select
                        name="coaching_id"
                        label="Coaching"
                        id="coaching_select"
                        :options="$coaching"
                        :selected="old('coaching_id' ,$coachingData->coaching_id)"
                        placeholder="Select coaching"
                        searchable="true"
                        required="true"
                    />
                </div>

                <!-- Batch Dropdown -->
                <div>
                    <x-team.forms.select
                        name="batch_id"
                        label="Batch"
                        id="batch_select"
                        :options="[]"
                        :selected="old('batch_id',$coachingData->batch_id)"
                        placeholder="Select batch"
                        searchable="true"
                        required="true"
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="faculty"
                        label="Faculty"
                        :options="$faculty"
                        :selected="old('faculty',$coachingData->faculty)"
                        placeholder="Select faculty"
                        searchable="true"
                        required="true"
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="coaching_length"
                        label="Coaching Length"
                        :options="$coachingLength"
                        :selected="old('coaching_length',$coachingData->coaching_length)"
                        placeholder="Select coaching length"
                        searchable="true"
                        required="true"
                    />
                </div>

                <div class="flex items-center gap-6">
                    <!-- Completed Switch -->
                    <div class="flex items-center gap-2">
                        <input class="kt-switch" type="checkbox" name="is_complete_coaching" id="is_completed"
                            value="1"
                            {{ old('is_complete_coaching', $coachingData->is_complete_coaching) ? 'checked' : '' }}>
                        <label class="kt-label" for="is_completed">Is Completed?</label>
                    </div>

                    <!-- Drop Switch -->
                    <div class="flex items-center gap-2">
                        <input class="kt-switch" type="checkbox" name="is_drop_coaching" id="is_drop"
                            value="1"
                            {{ old('is_drop_coaching', $coachingData->is_drop_coaching) ? 'checked' : '' }}>
                        <label class="kt-label" for="is_drop">Is Drop?</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input class="kt-switch" type="checkbox" name="is_material" id="is_material"
                            value="1"
                            {{ old('is_material', $coachingData->getCoachingMaterial->isNotEmpty() ? 1 : 0) == 1 ? 'checked' : '' }}>
                        <label class="kt-label" for="is_material">Is Material?</label>
                    </div>
                </div>

            </div>
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-1 gap-5 mt-4 ">
                <div id="material-checkboxes" class="mt-4">

                </div>
            </div>
        </div>
    </div>
@else
    <div class="coaching-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Coaching Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">

                <!-- Joining Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Joining Date"
                        id="joining_date"
                        name="joining_date"
                        type="date"
                        value="{{ old('joining_date') }}"
                        required
                    />
                </div>
                <div>
                    <x-team.forms.select
                        name="coaching_id"
                        label="Coaching"
                        id="coaching_select"
                        :options="$coaching"
                        :selected="old('coaching_id' ,$registerData->clientLead->coaching)"
                        placeholder="Select coaching"
                        searchable="true"
                        required="true"
                    />
                </div>

                <!-- Batch Dropdown -->
                <div>
                    <x-team.forms.select
                        name="batch_id"
                        label="Batch"
                        id="batch_select"
                        :options="[]"
                        :selected="old('batch_id')"
                        placeholder="Select batch"
                        searchable="true"
                        required="true"
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="faculty"
                        label="Faculty"
                        :options="$faculty"
                        :selected="old('faculty')"
                        placeholder="Select faculty"
                        searchable="true"
                        required="true"
                    />
                </div>
                <div>
                    <x-team.forms.select
                        name="coaching_length"
                        label="Coaching Length"
                        :options="$coachingLength"
                        :selected="old('coaching_length')"
                        placeholder="Select coaching length"
                        searchable="true"
                        required="true"
                    />
                </div>

                <div class="flex items-center gap-6">
                    <!-- Completed Switch -->
                    <div class="flex items-center gap-2">
                        <input class="kt-switch" type="checkbox" name="is_material" id="is_material"
                            value="1"
                            {{ old('is_material')}}>
                        <label class="kt-label" for="is_material">Is Material?</label>
                    </div>

                </div>
            </div>
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-1 gap-5 mt-4 ">
                <div id="material-checkboxes" class="mt-4">

                </div>
            </div>
        </div>
    </div>
@endif
</x-team.card>
