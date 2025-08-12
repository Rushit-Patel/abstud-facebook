@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Campus', 'url' => route('team.settings.campus.index')],
    ['title' => 'Create Campus']
];
@endphp

<x-team.layout.app title="Create Campus" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Campus
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new campus to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.campus.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Campus Information" headerClass="">
                <form action="{{ route('team.settings.campus.store') }}" method="POST" class="form">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid grid-cols-2 gap-5">
                                    <!-- Campus Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="Campus Name"
                                        type="text"
                                        placeholder="Enter campus name"
                                        :value="old('name')"
                                        required />

                                    <!-- Country -->
                                    <x-team.forms.select
                                        name="country_id"
                                        label="Country"
                                        :options="$countries"
                                        placeholder="Select Country"
                                        searchable="true"
                                        required />

                                    <x-team.forms.select
                                        name="state_id"
                                        label="State"
                                        :options="[]"
                                        :selected="null"
                                        placeholder="Select State"
                                        searchable="true"
                                        required />

                                    <x-team.forms.select
                                        name="city_id"
                                        label="City"
                                        :options="[]"
                                        :selected="null"
                                        placeholder="Select City"
                                        searchable="true"
                                        required />
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
                                                {{ old('status', true) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this campus inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.campus.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Campus
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

                // Country change → State update + City blank
                $('select[name="country_id"]').on('change', function () {
                    let countryId = $(this).val();

                    // Clear state & city
                    $('select[name="state_id"]').html('<option value="">Select State</option>');
                    $('select[name="city_id"]').html('<option value="">Select City</option>');

                    if (countryId) {
                        $.ajax({
                            url: "{{ route('team.get.foreign.states', ['country_id' => '___']) }}".replace('___', countryId),
                            type: "GET",
                            success: function (data) {
                                $.each(data, function (key, value) {
                                    $('select[name="state_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                                });

                                // Agar state ka value pehle se set hai to usko select karo
                                let selectedState = $('select[name="state_id"]').data('selected');
                                if (selectedState) {
                                    $('select[name="state_id"]').val(selectedState);
                                }

                                // Auto trigger state change
                                $('select[name="state_id"]').trigger('change');
                            }
                        });
                    }
                });

                // State change → City update
                $('select[name="state_id"]').on('change', function () {
                    let stateId = $(this).val();

                    // Clear city
                    $('select[name="city_id"]').html('<option value="">Select City</option>');

                    if (stateId) {
                        $.ajax({
                            url: "{{ route('team.get.foreign.cities', ['state_id' => '___']) }}".replace('___', stateId),
                            type: "GET",
                            success: function (data) {
                                $.each(data, function (key, value) {
                                    $('select[name="city_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                                });

                                // Agar city ka value pehle se set hai to usko select karo
                                let selectedCity = $('select[name="city_id"]').data('selected');
                                if (selectedCity) {
                                    $('select[name="city_id"]').val(selectedCity);
                                }
                            }
                        });
                    }
                });

                // Page load pe agar country already selected hai to trigger kar do
                if ($('select[name="country_id"]').val()) {
                    $('select[name="country_id"]').trigger('change');
                }
            });
        </script>

    @endpush
</x-team.layout.app>
