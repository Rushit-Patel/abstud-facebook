@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Branch Management', 'url' => route('team.settings.branches.index')],
    ['title' => 'Add Branch']
];
@endphp

<x-team.layout.app title="Add Branch" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New Branch
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new branch location with detailed information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.branches.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Branches
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('team.settings.branches.store') }}" class="kt-card">
                @csrf
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Branch Information</h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid lg:grid-cols-2 gap-5">
                        <!-- Branch Name -->
                        <x-team.forms.input
                            name="branch_name"
                            label="Branch Name"
                            type="text"
                            :required="true"
                            placeholder="Enter branch name"
                            :value="old('branch_name')" />

                        <!-- Branch Code -->
                        <x-team.forms.input
                            name="branch_code"
                            label="Branch Code"
                            type="text"
                            :required="true"
                            placeholder="Enter unique branch code"
                            :value="old('branch_code')" />

                        <!-- Phone -->
                        <x-team.forms.input
                            name="phone"
                            label="Phone Number"
                            type="tel"
                            placeholder="Enter phone number"
                            :value="old('phone')" />

                        <!-- Email -->
                        <x-team.forms.input
                            name="email"
                            label="Email Address"
                            type="email"
                            placeholder="Enter email address"
                            :value="old('email')" />

                        <!-- Map Link -->
                        <div class="lg:col-span-2">
                            <x-team.forms.input
                                name="map_link"
                                label="Map Link"
                                type="url"
                                placeholder="Enter Google Maps or other map link (e.g., https://maps.google.com/...)"
                                :value="old('map_link')" />
                        </div>

                        <!-- Address -->
                        <div class="lg:col-span-2">
                            <x-team.forms.input
                                name="address"
                                label="Address"
                                type="text"
                                required
                                placeholder="Enter complete address"
                                :value="old('address')" />
                        </div>

                        <!-- Country -->
                        {{-- <x-team.forms.select
                            name="country_id"
                            label="Country"
                            :options="$countries ?? []"
                            :selected="$company->country_id ?? old('country_id')"
                            placeholder="Select Country"
                            required="true"
                            searchable="true"
                        /> --}}

                        <x-team.forms.select
                            name="country_id"
                            id="country_id"
                            label="Country"
                            :options="$countries"
                            :selected="old('country_id')"
                            placeholder="Select Country"
                            searchable="true"
                            required
                        />

                        <!-- State -->
                        {{-- <x-team.forms.select
                            name="state_id"
                            label="State/Province"
                            :options="$states ?? []"
                            :selected="$company->state_id ?? old('state_id')"
                            placeholder="Select State"
                            required="true"
                            searchable="true"
                        /> --}}
                        <x-team.forms.select
                            name="state_id"
                            id="state_id"
                            label="State/Province"
                            :options="[]"
                            :selected="old('state_id')"
                            placeholder="Select State"
                            searchable="true"
                            required
                        />

                        <!-- City -->
                        {{-- <x-team.forms.select
                            name="city_id"
                            label="City"
                            :options="$cities ?? []"
                            :selected="$company->city_id ?? old('city_id')"
                            placeholder="Select City"
                            required="true"
                            searchable="true"
                        /> --}}
                        <x-team.forms.select
                            name="city_id"
                            id="city_id"
                            label="City"
                            :options="[]"
                            :selected="old('city_id')"
                            placeholder="Select City"
                            required
                            searchable="true"
                        />

                        <!-- Postal Code -->
                        <x-team.forms.input
                            name="postal_code"
                            label="Postal Code"
                            type="text"
                            placeholder="Enter postal/zip code"
                            :value="old('postal_code')" />

                        <!-- Status -->
                        <div class="lg:col-span-2">
                            <div class="flex flex-col gap-1">
                                <label class="kt-form-label font-normal text-mono">Status</label>
                                <label class="kt-label">
                                    <input class="kt-checkbox kt-checkbox-sm"
                                        name="is_active"
                                        type="checkbox"
                                        value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                    />
                                    <span class="kt-checkbox-label">
                                        Active (Enable this branch for operations)
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-card-footer">
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('team.settings.branches.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <x-team.forms.button type="submit">
                            <i class="ki-filled ki-check"></i>
                            Create Branch
                        </x-team.forms.button>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>

      @push('scripts')
    <script src="{{ asset('assets/js/team/location-ajax.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Location AJAX for country/state/city dropdowns
            LocationAjax.init({
                countrySelector: '#country_id',
                stateSelector: '#state_id',
                citySelector: '#city_id',
                statesRoute: '{{ route("team.settings.company.states", ":countryId") }}'.replace(':countryId', ''),
                citiesRoute: '{{ route("team.settings.company.cities", ":stateId") }}'.replace(':stateId', '')
            });
        });
    </script>
@endpush

</x-team.layout.app>
