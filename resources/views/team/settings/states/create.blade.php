@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'States', 'url' => route('team.settings.states.index')],
    ['title' => 'Add State']
];
@endphp

<x-team.layout.app title="Add State" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New State
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new state or province
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.states.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to States
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.states.store') }}" method="POST">
                @csrf
                <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Basic Information -->
                    <div class="col-span-1">
                        <x-team.card title="Basic Information">
                            <div class="grid gap-5">
                                <!-- Country Selection -->
                                <div class="flex flex-col gap-1">
                                    <label class="kt-form-label font-normal text-mono required"><b>Country <span style="color: #E7004A;">*</span></b></label>
                                    <select name="country_id" class="kt-select" required>
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- State Name -->
                                <x-team.forms.input
                                    name="name"
                                    label="State/Province Name"
                                    type="text"
                                    placeholder="Enter state or province name"
                                    :value="old('name')"
                                    required />

                                <!-- State Code -->
                                <x-team.forms.input
                                    name="state_code"
                                    label="State Code"
                                    type="text"
                                    placeholder="e.g. CA, NY, TX"
                                    maxlength="10"
                                    :value="old('state_code')" />
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
                                        Active (Enable this state for selection)
                                    </span>
                                </label>
                            </div>
                        </x-team.card>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-7.5">
                    <a href="{{ route('team.settings.states.index') }}" class="kt-btn kt-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-check"></i>
                        Create State
                    </button>
                </div>
            </form>
        </div>
    </x-slot>
</x-team.layout.app>
