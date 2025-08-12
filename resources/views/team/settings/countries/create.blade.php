@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Countries', 'url' => route('team.settings.countries.index')],
    ['title' => 'Add Country']
];
@endphp

<x-team.layout.app title="Add Country" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New Country
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new country with currency and timezone information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.countries.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Countries
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.countries.store') }}" method="POST">
                @csrf
                <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Basic Information -->
                    <div class="col-span-1">
                        <x-team.card title="Basic Information">
                            <div class="grid gap-5">
                                <!-- Country Name -->
                                <x-team.forms.input 
                                    name="name" 
                                    label="Country Name" 
                                    type="text" 
                                    placeholder="Enter country name" 
                                    :value="old('name')" 
                                    required />

                                <!-- Phone Code -->
                                <x-team.forms.input 
                                    name="phone_code" 
                                    label="Phone Code" 
                                    type="text" 
                                    placeholder="e.g. 1, 44, 91" 
                                    :value="old('phone_code')" />

                                <!-- Icon/Flag -->
                                <x-team.forms.input 
                                    name="icon" 
                                    label="Icon/Flag" 
                                    type="text" 
                                    placeholder="e.g. 🇺🇸, 🇬🇧, 🇮🇳" 
                                    :value="old('icon')" />
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Currency & Regional -->
                    <div class="col-span-1">
                        <div class="grid gap-5 lg:gap-7.5">
                            <!-- Currency Information -->
                            <x-team.card title="Currency Information">
                                <div class="grid gap-5">
                                    <!-- Currency Code -->
                                    <x-team.forms.input 
                                        name="currency" 
                                        label="Currency Code" 
                                        type="text" 
                                        placeholder="e.g. USD, GBP, INR" 
                                        maxlength="3"
                                        :value="old('currency')" />

                                    <!-- Currency Symbol -->
                                    <x-team.forms.input 
                                        name="currency_symbol" 
                                        label="Currency Symbol" 
                                        type="text" 
                                        placeholder="e.g. $, £, ₹" 
                                        :value="old('currency_symbol')" />
                                </div>
                            </x-team.card>

                            <!-- Timezone Information -->
                            <x-team.card title="Timezone Settings">
                                <div class="grid gap-5">
                                    <!-- Timezones -->
                                    <div class="flex flex-col gap-1">
                                        <label class="kt-form-label font-normal text-mono">Timezones</label>
                                        <div id="timezone-container">
                                            <div class="timezone-input flex items-center gap-2 mb-2">
                                                <input type="text" 
                                                       name="timezones[]" 
                                                       class="kt-input flex-1" 
                                                       placeholder="e.g. America/New_York, Europe/London"
                                                       value="{{ old('timezones.0') }}">
                                                <button type="button" class="kt-btn kt-btn-sm kt-btn-success" onclick="addTimezone()">
                                                    <i class="ki-filled ki-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="text-xs text-secondary-foreground">
                                            Add multiple timezones for countries spanning multiple zones
                                        </div>
                                    </div>
                                </div>
                            </x-team.card>

                            <!-- Status -->
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
                                            Active (Enable this country for selection)
                                        </span>
                                    </label>
                                </div>
                            </x-team.card>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-7.5">
                    <a href="{{ route('team.settings.countries.index') }}" class="kt-btn kt-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-check"></i>
                        Create Country
                    </button>
                </div>
            </form>
        </div>

        <script>
            function addTimezone() {
                const container = document.getElementById('timezone-container');
                const newInput = document.createElement('div');
                newInput.className = 'timezone-input flex items-center gap-2 mb-2';
                newInput.innerHTML = `
                    <input type="text" 
                           name="timezones[]" 
                           class="kt-input flex-1" 
                           placeholder="e.g. America/New_York, Europe/London">
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-danger" onclick="removeTimezone(this)">
                        <i class="ki-filled ki-minus"></i>
                    </button>
                `;
                container.appendChild(newInput);
            }

            function removeTimezone(button) {
                button.closest('.timezone-input').remove();
            }
        </script>
    </x-slot>
</x-team.layout.app>
