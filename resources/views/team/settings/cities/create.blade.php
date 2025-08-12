@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Cities', 'url' => route('team.settings.cities.index')],
    ['title' => 'Add City']
];
@endphp

<x-team.layout.app title="Add City" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New City
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new city
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.cities.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Cities
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.cities.store') }}" method="POST">
                @csrf
                <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Basic Information -->
                    <div class="col-span-1">
                        <x-team.card title="Basic Information">
                            <div class="grid gap-5">
                                <!-- Country Selection -->
                                 <div class="flex flex-col gap-1">
                                    <x-team.forms.select 
                                        name="country_id" 
                                        label="Country" 
                                        id="country_id"
                                        :options="$countries"
                                        :selected="old('country_id')"
                                        placeholder="Select Country"
                                        required="true"
                                        searchable="true"
                                    />
                                </div>

                                <div class="flex flex-col gap-1">
                                    <x-team.forms.select 
                                        name="state_id" 
                                        label="State/Province" 
                                        :options="$states ?? []"
                                        :selected="old('state_id' )"
                                        placeholder="Select State"
                                        required="true"
                                        searchable="true"
                                    />
                                </div>

                                <!-- City Name -->
                                <div>                                
                                <x-team.forms.input 
                                    name="name" 
                                    label="City Name" 
                                    type="text" 
                                    placeholder="Enter city name" 
                                    :value="old('name')" 
                                    required />
                                </div>
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Status Settings -->
                    <div class="col-span-1">
                        <x-team.card title="Status Settings">
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
                                        Active (Enable this city for selection)
                                    </span>
                                </label>
                            </div>
                        </x-team.card>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-7.5">
                    <a href="{{ route('team.settings.cities.index') }}" class="kt-btn kt-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-check"></i>
                        Create City
                    </button>
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
