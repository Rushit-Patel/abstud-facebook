@extends('client.layouts.guest')

@section('card-width', 'max-w-[800px]')
@section('content')
    <x-team.card title="Personal Information" headerClass="">
        <form action="{{ route('client.guest.personal.info.store') }}" method="POST" class="form"
            enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="col-span-1">
                    <div class="gap-1 grid gap-5">
                        <x-team.forms.input name="first_name" label="First Name" type="text"
                            placeholder="Enter first name" :value="old('first_name')" required />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="gap-1 grid gap-5">
                        <x-team.forms.input name="middle_name" label="Middle Name" type="text"
                            placeholder="Enter middle name" :value="old('middle_name')" />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="gap-1 grid gap-5">
                        <x-team.forms.input name="last_name" label="Last Name" type="text" placeholder="Enter last name"
                            :value="old('last_name')" required />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="gap-1 grid gap-5">
                        <x-team.forms.mobile-input name="mobile_no" label="Mobile no" type="tel"
                            placeholder="Enter mobile no" :value="old('mobile_no', $mobile_no)" readonly />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="gap-1 grid gap-5 relative">
                        <x-team.forms.mobile-input name="whatsapp_no" label="Whatsapp no" type="tel"
                            placeholder="Enter whatsapp no" :value="old('whatsapp_no')" required />
                        <button type="button" onclick="copyMobileToWhatsapp()"
                            class="absolute py-2  top-7 end-0 text-sm rounded kt-btn kt-btn-sm kt-btn-ghost" style="margin-left: 89px; margin-top: -3px;">
                            <i class="ki-filled ki-copy"></i> 
                        </button>
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="gap-1 grid gap-5">
                        <x-team.forms.input name="email_id" label="Email id" type="text" placeholder="Enter email "
                            :value="old('email_id')" required />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="grid gap-5">
                        @php
                            $gender = [
                                'male' => 'Male',
                                'female' => 'Female',
                            ];
                        @endphp
                        <x-team.forms.select name="gender" label="Gender" :options="$gender" :selected="old('gender')"
                            placeholder="Select gender" searchable="true" />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="grid gap-5">
                        <x-team.forms.select name="country_id" label="Country" :options="$countries" :selected="null"
                            placeholder="Select Country" searchable="true" required />
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="grid gap-5">
                        <x-team.forms.select name="state_id" label="State/Province" :options="[]" :selected="null"
                            placeholder="Select State" searchable="true" required />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="grid gap-5">
                        <x-team.forms.select name="city_id" label="City" :options="[]" :selected="null"
                            placeholder="Select City" required searchable="true" />
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="grid gap-5">
                        <x-team.forms.datepicker label="Date of Birth" name="date_of_birth" id="date_of_birth"
                            placeholder="Select Date of Birth " maxDate="today" dateFormat="Y-m-d"
                            :value="old('date_of_birth')" class="w-full flatpickr" />
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="grid gap-5">
                        <x-team.forms.select name="source" label="Source" :options="$sources"
                            :selected="old('source')" placeholder="Select source" searchable="true" required />
                    </div>
                </div>
            </div>

                <select name="branch" class="form-control" hidden>
                    <option value="{{ $branch->id }}" selected>{{ $branch->branch_name }}</option>
                </select>
                <input type="hidden" name="branch" value="{{ $branch->id }}">
                <input type="hidden" name="mobile_no" value="{{ $mobile_no }}">

                <div class="grid grid-cols-1 md:grid-cols-1 mt-4 lg:grid-cols-1 gap-5">
                    <div class="col-span-1">
                        <div class="grid gap-5">
                            <x-team.forms.textarea id="address" name="address" label="Address" :value="old('address')"
                                placeholder="Enter address" />
                        </div>
                    </div>
                </div>
            <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                    Continue
                </button>
            </div>
        </form>
    </x-team.card>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/team/location-ajax.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Location AJAX for country/state/city dropdowns
            LocationAjax.init({
                countrySelector: '#country_id',
                stateSelector: '#state_id',
                citySelector: '#city_id',
                statesRoute: '{{ route('team.get.states', ':countryId') }}'.replace(':countryId', ''),
                citiesRoute: '{{ route('team.get.cities', ':stateId') }}'.replace(':stateId', '')
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Re-initialize flatpickr on all relevant inputs
            $(".flatpickr").flatpickr({
                dateFormat: "d/m/Y",
                maxDate: "today",
                defaultDate: $("input[name='date_of_birth']").val() || null
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            let branchId = $('select[name="branch"]').val();

            if (branchId) {
                loadLocationData(branchId); // Page load pe call hoga
            }

            // Disable dropdown so user can't change it
            $('select[name="branch"]').prop('disabled', true);

            function loadLocationData(branchId) {
                $.ajax({
                    url: '{{ route('team.ajax.branch.country.state.city') }}',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    success: function(response) {
                        // Populate Country
                        let countrySelect = $('select[name="country_id"]');
                        countrySelect.empty().append(`<option value="">Select Country</option>`);
                        $.each(response.countries, function(id, name) {
                            let selected = (id == response.country_id) ? 'selected' : '';
                            countrySelect.append(
                                `<option value="${id}" ${selected}>${name}</option>`);
                        });

                        // Populate State
                        let stateSelect = $('select[name="state_id"]');
                        stateSelect.empty().append(`<option value="">Select State</option>`);
                        $.each(response.states, function(id, name) {
                            let selected = (id == response.state_id) ? 'selected' : '';
                            stateSelect.append(
                                `<option value="${id}" ${selected}>${name}</option>`);
                        });

                        // Populate City
                        let citySelect = $('select[name="city_id"]');
                        citySelect.empty().append(`<option value="">Select City</option>`);
                        $.each(response.cities, function(id, name) {
                            let selected = (id == response.city_id) ? 'selected' : '';
                            citySelect.append(
                                `<option value="${id}" ${selected}>${name}</option>`);
                        });
                    }
                });
            }
        });
    </script>
    {{-- Whatsapp Copy --}}
    <script>
        function copyMobileToWhatsapp() {
            const mobileInput = document.querySelector('input[name="mobile_no"]');
            const whatsappInput = document.querySelector('input[name="whatsapp_no"]');
            if (mobileInput && whatsappInput) {
                whatsappInput.value = mobileInput.value;
            }
        }
    </script>
@endpush
