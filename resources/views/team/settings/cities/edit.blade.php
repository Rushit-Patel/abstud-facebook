@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Cities', 'url' => route('team.settings.cities.index')],
    ['title' => 'Edit City']
];
@endphp

<x-team.layout.app title="Edit City" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit City
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update city
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.cities.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Cities
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.cities.update' ,$city) }}" method="POST">
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
                                        :selected="old('country_id' ,$city?->state?->country_id)"
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
                                        :selected="old('state_id' ,$city->state_id)"
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
                                    :value="old('name',$city->name)" 
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
                                        {{ old('is_active', $city?->is_active) ? 'checked' : '' }}
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
                        Update City
                    </button>
                </div>
            </form>
        </div>
    </x-slot>

     @push('scripts')
    <script src="{{ asset('assets/js/team/location-ajax.js') }}"></script>

    
    <script>
        $(document).ready(function() {
            // If editing existing company with location data, set the values
            @if($city && $city->state_id)
                // Set initial values for edit mode
                setTimeout(function() {
                    LocationAjax.setSelectedValues({
                        country_id: '{{ $city?->state?->country_id }}',
                        state_id: '{{ $city->state->id }}',
                        city_id: '{{ $city->id }}'
                    });
                }, 100);
            @endif
        });
    </script>
    @endpush
</x-team.layout.app>
