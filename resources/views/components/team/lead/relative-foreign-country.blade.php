@if (isset($relativeData) && $relativeData->count() > 0)
    <div class="relation-item rounded-lg mb-2 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Relative Checkbox -->
            <input type="hidden" name="client_relation_id" value="{{ $relativeData->id }}">
            <div class="col-span-4">
                <div class="flex flex-wrap gap-4">
                    <x-team.forms.checkbox
                        id="is_relativeCheckbox"
                        name="is_relative"
                        :value="1"
                        label="Relative In Foreign Country?"
                        style="inline"
                        class="kt-switch kt-switch-lg"
                        :checked="old('is_relative' , isset($relativeData) && ($relativeData->relative_relationship || $relativeData->relative_country || $relativeData->visa_type))"
                    />
                </div>
            </div>
        </div>

        <!-- Relative Details: Hidden initially -->
        <div id="relative-fields" class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5 hidden">

            <!-- Relationship -->
            <x-team.forms.select
                name="relative_relationship"
                id="relative_relationship"
                label="Relationship"
                :options="$typeOfRelations"
                :selected="old('relative_relationship' ,$relativeData->relative_relationship)"
                placeholder="Select relationship"
                required
            />

            <!-- Country -->
            <x-team.forms.select
                name="relative_country"
                id="relative_country"
                label="Relative Country"
                :options="$countrys"
                :selected="old('relative_country',$relativeData->relative_country)"
                placeholder="Select country"
                required
            />

            <!-- Residency Status -->
            <x-team.forms.select
                name="visa_type"
                label="Visa Type"
                id="visa_type"
                :options="$otherVisaTypes"
                :selected="old('visa_type',$relativeData->visa_type)"
                placeholder="Select visa type"
                required
            />
        </div>
    </div>
@else
    <div class="relation-item rounded-lg mb-2 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Relative Checkbox -->
            <div class="col-span-4">
                <div class="flex flex-wrap gap-4">
                    <x-team.forms.checkbox
                        id="is_relativeCheckbox"
                        name="is_relative"
                        :value="1"
                        label="Relative In Foreign Country?"
                        style="inline"
                        class="kt-switch kt-switch-lg"
                        :checked="old('is_relative')"
                    />
                </div>
            </div>
        </div>

        <!-- Relative Details: Hidden initially -->
        <div id="relative-fields" class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5 hidden">

            <!-- Relationship -->
            <x-team.forms.select
                name="relative_relationship"
                id="relative_relationship"
                label="Relationship"
                :options="$typeOfRelations"
                :selected="old('relative_relationship')"
                placeholder="Select relationship"
                required
            />

            <!-- Country -->
            <x-team.forms.select
                name="relative_country"
                label="Relative Country"
                id="relative_country"
                :options="$countrys"
                :selected="old('relative_country')"
                placeholder="Select country"
                required
            />

            <!-- Residency Status -->
            <x-team.forms.select
                name="visa_type"
                label="Visa Type"
                id="visa_type"
                :options="$otherVisaTypes"
                :selected="old('visa_type')"
                placeholder="Select visa type"
                required
            />
        </div>
    </div>
@endif
