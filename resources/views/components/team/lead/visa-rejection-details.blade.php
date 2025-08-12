<!-- Single Checkbox outside the repeater -->
<div class="mb-2">
    <x-team.forms.checkbox
        id="is_visa_rejectionCheckbox"
        name="is_visa_rejection"
        :value="1"
        label="Any Visa Rejection?"
        style="inline"
        class="kt-switch kt-switch-lg"
        :checked="old('visa_rejection') || (isset($visaRejectionDatas) && $visaRejectionDatas->count() > 0)"
    />
</div>

<!-- Repeater container -->
<div id="visa-rejection-section" class="hidden">
    <div id="visa-rejection-repeater">
        <div data-repeater-list="visa_rejection">
            @if (isset($visaRejectionDatas) && $visaRejectionDatas->count() > 0)
                @foreach ($visaRejectionDatas as $visaRejectionData)
                    <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                        <button type="button" class="absolute top-0 right-0 text-red-500 remove-rejection" data-repeater-delete>
                            <i class="ki-filled ki-trash text-lg text-destructive"></i>
                        </button>
                        <input type="hidden" name="id" value="{{ $visaRejectionData->id }}">
                        <div class="visa-rejection-fields grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
                            <x-team.forms.select
                                name="rejection_country"
                                label="Rejection Country"
                                :options="$visaRejectionCountry"
                                :selected="$visaRejectionData->rejection_country"
                                placeholder="Select country"
                                required
                            />

                            <x-team.forms.input
                                name="rejection_month_year"
                                label="Month-Year"
                                type="text"
                                placeholder="Enter JAN-2024"
                                :value="$visaRejectionData->rejection_month_year"
                                required
                            />

                            <x-team.forms.select
                                name="rejection_visa_type"
                                label="Visa Type"
                                :options="$visaRejectionVisaType"
                                :selected="$visaRejectionData->rejection_visa_type"
                                placeholder="Select visa type"
                                required
                            />
                        </div>
                    </div>
                @endforeach
            @else
            <div class="rounded-lg mb-5 relative bg-secondary-50 border-b border-gray-200" data-repeater-item>
                <button type="button" class="absolute top-0 right-0 text-red-500 remove-rejection" data-repeater-delete>
                    <i class="ki-filled ki-trash text-lg text-destructive"></i>
                </button>

                <div class="visa-rejection-fields grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
                    <x-team.forms.select
                        name="rejection_country"
                        label="Rejection Country"
                        :options="$visaRejectionCountry"
                        placeholder="Select country"
                        required
                    />

                    <x-team.forms.input
                        name="rejection_month_year"
                        label="Month-Year"
                        type="text"
                        placeholder="Enter JAN-2024"
                        required
                    />

                    <x-team.forms.select
                        name="rejection_visa_type"
                        label="Visa Type"
                        :options="$visaRejectionVisaType"
                        placeholder="Select visa type"
                        required
                    />
                </div>
            </div>
        @endif
    </div>

    <!-- Add More Button -->
    <div class="mt-4">
        <button type="button" class="kt-btn kt-btn-sm  kt-btn-primary" data-repeater-create>+ Add Rejection</button>
    </div>
</div>
</div>
