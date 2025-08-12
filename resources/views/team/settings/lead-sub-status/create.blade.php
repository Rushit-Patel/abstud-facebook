@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Lead Sub Status', 'url' => route('team.settings.lead-sub-status.index')],
    ['title' => 'Add Lead Sub Status']
];
@endphp

<x-team.layout.app title="Add Lead Sub Status" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New Lead Sub Status
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new state or province
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.lead-sub-status.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Lead Sub Status
                    </a>
                </div>
            </div>

            <form action="{{ route('team.settings.lead-sub-status.store') }}" method="POST">
                @csrf
                <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Basic Information -->
                    <div class="col-span-1">
                        <x-team.card title="Basic Information">
                            <div class="grid gap-5">
                                <!-- Country Selection -->
                                <div class="flex flex-col gap-1">
                                    <label class="kt-form-label font-normal text-mono required"><b>Lead Status <span style="color: #E7004A;">*</span></b></label>
                                    <select name="lead_status_id" class="kt-select" required>
                                        <option value="">Select status</option>
                                        @foreach($leadStatues as $leadStatus)
                                            <option value="{{ $leadStatus->id }}" {{ old('leas_status_id') == $leadStatus->id ? 'selected' : '' }}>
                                                {{ $leadStatus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leas_status_id')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Lead Sub Status Name -->
                                <x-team.forms.input
                                    name="name"
                                    label="Sub Status Name"
                                    type="text"
                                    placeholder="Enter sub status name"
                                    :value="old('name')"
                                    required />
                            </div>
                        </x-team.card>
                    </div>

                    <!-- Status Settings -->
                    <div class="col-span-1">
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
                                        Active (Enable this status for selection)
                                    </span>
                                </label>
                            </div>
                        </x-team.card>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-7.5">
                    <a href="{{ route('team.settings.lead-sub-status.index') }}" class="kt-btn kt-btn-secondary">
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
