@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Billing Company', 'url' => route('team.settings.billing-company.index')],
    ['title' => 'Edit Billing Company']
];
@endphp

<x-team.layout.app title="Edit Billing Company" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Billing Company: {{ $billingCompany->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update billing-company information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.billing-company.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Billing Company Information" headerClass="">
                <form action="{{ route('team.settings.billing-company.update', $billingCompany) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')


                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="">
                                <div class="grid gap-5">
                                    <!-- Country Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="Name"
                                        type="text"
                                        placeholder="Enter billing-company name"
                                        :value="old('name',$billingCompany->name)"
                                        required />
                                    <x-team.forms.input
                                        name="mobile_no"
                                        label="Mobile No"
                                        type="text"
                                        placeholder="Enter mobile no"
                                        :value="old('mobile_no',$billingCompany->mobile_no)"
                                        required />
                                    <x-team.forms.input
                                        name="email_id"
                                        label="Email"
                                        type="text"
                                        placeholder="Enter email id"
                                        :value="old('email_id',$billingCompany->email_id)"
                                        required />
                                </div>
                            </x-team.card>
                        </div>

                        <div class="col-span-1">
                            <x-team.card title="">
                                <div class="grid gap-5">
                                    @php
                                        $isEdit = isset($billingCompany) && $billingCompany->company_logo;
                                    @endphp
                                    <!-- Country Name -->
                                    <div x-data="{ hasOldFile: {{ $isEdit ? 'true' : 'false' }} }">
                                        <x-team.forms.input
                                            label="Comapny Logo"
                                            id="comapny_logo"
                                            name="comapny_logo"
                                            type="file"
                                        />

                                            @if ($isEdit)
                                                <div class="mt-2">
                                                    <a
                                                        href="{{ asset('storage/' . $billingCompany->company_logo) }}"
                                                        target="_blank"
                                                        class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                    >
                                                        View Existing File
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                    <x-team.forms.select
                                        name="branch[]"
                                        label="Branch"
                                        :options="$branches"
                                        :selected="old('branch', isset($billingCompany->branch) ? explode(',', $billingCompany->branch) : [])"
                                        placeholder="Select Branch"
                                        searchable="true"
                                        required="true"
                                        multiple="true"
                                        id="branch" />

                                    <x-team.forms.textarea
                                        id="addresh"
                                        name="address"
                                        label="Address"
                                        :value="old('address',$billingCompany->address)"
                                        placeholder="Enter Address"
                                        />
                                </div>
                            </x-team.card>
                        </div>

                        <div class="col-span-1">
                            <x-team.card title="GST Details">
                                <div class="gst-item rounded-lg mb-5 relative bg-secondary-50">
                                    <div class="grid grid-cols-1 gap-5">
                                        <!-- GST Checkbox -->
                                        <div>
                                            <x-team.forms.checkbox
                                                id="gstCheckbox"
                                                name="gst"
                                                :value="1"
                                                label="Do you have GST?"
                                                style="inline"
                                                class="gst-checkbox"
                                                :checked="old('gst', $billingCompany->is_gst)"
                                            />
                                        </div>

                                        <!-- GST Fields (Only 2 fields in this card) -->
                                        <div id="gstFields" class="grid grid-cols-1 lg:grid-cols-2 gap-5 {{ old('gst', isset($billingCompany) && $billingCompany->is_gst) ? '' : 'hidden' }}">
                                            <!-- GST form name -->
                                            <div>
                                                <x-team.forms.input
                                                    label="GST form name"
                                                    id="gst_form_name"
                                                    name="gst_form_name"
                                                    type="text"
                                                    placeholder="Enter GST form name"
                                                    :value="old('gst_form_name', $billingCompany->gst_form_name)"
                                                    required
                                                />
                                            </div>

                                            <!-- GST Number -->
                                            <div>
                                                <x-team.forms.input
                                                    label="GST Number"
                                                    id="gst_number"
                                                    name="gst_number"
                                                    type="text"
                                                    placeholder="Enter GST number"
                                                    :value="old('gst_number', $billingCompany->gst_number) "
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-team.card>
                        </div>


                        <!-- Status -->

                        <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">
                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">Status</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="status"
                                                type="checkbox"
                                                value="1"
                                                {{ old('status', $billingCompany->status) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this billing-company inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>


                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5 border-t border-gray-200">
                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Created At</label>
                            <div class="text-sm text-gray-700">
                                {{ $billingCompany->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Last Updated</label>
                            <div class="text-sm text-gray-700">
                                {{ $billingCompany->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.billing-company.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Billing Company
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            // Form validation and enhancement
            $(document).ready(function() {
                // Add any additional form enhancements here

                // Focus on name field
                $('#name').focus();

                // Form submission handling
                $('form').on('submit', function() {
                    // Disable submit button to prevent double submission
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        </script>
        <script>
            $(document).ready(function () {
                const $checkbox = $('#gstCheckbox');
                const $fields = $('#gstFields');
                const $requiredInputs = $fields.find('[required]');

                function toggleGSTFields() {
                    if ($checkbox.is(':checked')) {
                        $fields.removeClass('hidden');
                        $requiredInputs.prop('required', true); // Enable required
                    } else {
                        $fields.addClass('hidden');
                        $requiredInputs.prop('required', false); // Disable required
                    }
                }

                // Initial check on page load
                toggleGSTFields();

                // On checkbox change
                $checkbox.on('change', toggleGSTFields);
            });
        </script>
    @endpush
</x-team.layout.app>
