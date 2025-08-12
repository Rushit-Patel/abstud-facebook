<x-team.card title="Passport Details" headerClass="">
    @if (isset($passportData) && $passportData->count() > 0)
        <div class="passport-item rounded-lg mb-5 relative bg-secondary-50">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                <!-- Passport Checkbox -->
                <input type="hidden" name="passport_id" value="{{ $passportData->id }}">
                <div class="col-span-4">
                    <div class="flex flex-wrap gap-4">
                        <x-team.forms.checkbox
                            id="passportCheckbox"
                            name="passport"
                            :value="1"
                            label="Do you have a passport?"
                            style="inline"
                            class="passport-checkbox"
                            :checked="old('passport', isset($passportData) && ($passportData->passport_number || $passportData->passport_copy || $passportData->passport_expiry_date))"
                        />
                    </div>
                </div>

                <!-- Passport Fields -->
                <div id="passportFields" class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 {{ old('passport', isset($clientLead) && $clientLead->passport ? '' : 'hidden') }}">
                    <!-- Passport Number -->
                    <div>
                        <x-team.forms.input
                            label="Passport Number"
                            id="passport_number"
                            name="passport_number"
                            type="text"
                            placeholder="Enter passport number"
                            value="{{ old('passport_number', $passportData->passport_number ?? '') }}"
                            required
                        />
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <x-team.forms.datepicker
                            label="Expiry Date"
                            id="passport_expiry_date"
                            name="passport_expiry_date"
                            type="date"
                            value="{{ old('passport_expiry_date', \Carbon\Carbon::parse($passportData->passport_expiry_date)->format('d-m-Y')  ?? '') }}"
                            required
                        />
                    </div>

                    <!-- Passport Letter -->
                    @php
                        $isEdit = isset($passportData) && $passportData->passport_copy;
                    @endphp

                    <div x-data="{ hasOldFile: {{ $isEdit ? 'true' : 'false' }} }">
                        <x-team.forms.input
                            label="Passport Letter"
                            id="passport_copy"
                            name="passport_copy"
                            type="file"
                        />

                        @if ($isEdit)
                            <div class="mt-2">
                                <a
                                    href="{{ asset('storage/' . $passportData->passport_copy) }}"
                                    target="_blank"
                                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                >
                                    View Existing File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="passport-item  rounded-lg mb-5 relative bg-secondary-50">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                <!-- Passport Checkbox -->
                <div class="col-span-4">
                    <div class="flex flex-wrap gap-4">
                        <x-team.forms.checkbox
                            id="passportCheckbox"
                            name="passport"
                            :value="1"
                            label="Do you have a passport?"
                            style="inline"
                            class="passport-checkbox"
                            :checked="old('passport', isset($clientLead) && $clientLead->passport)"
                        />
                    </div>
                </div>

                <!-- Passport Fields -->
                <div id="passportFields" class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 {{ old('passport', isset($clientLead) && $clientLead->passport ? '' : 'hidden') }}">
                    <!-- Passport Number -->
                    <div>
                        <x-team.forms.input
                            label="Passport Number"
                            id="passport_number"
                            name="passport_number"
                            type="text"
                            placeholder="Enter passport number"
                            value="{{ old('passport_number', $clientLead->passport_number ?? '') }}"
                            required
                        />
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <x-team.forms.datepicker
                            label="Expiry Date"
                            id="passport_expiry_date"
                            name="passport_expiry_date"
                            type="date"
                            value="{{ old('passport_expiry_date', $clientLead->passport_expiry_date ?? '') }}"
                            required
                        />
                    </div>
                    <!-- Passport Letter -->
                    <div>
                        <x-team.forms.input
                            label="Passport Copy"
                            id="passport_copy"
                            name="passport_copy"
                            type="file"
                        />
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-team.card>
