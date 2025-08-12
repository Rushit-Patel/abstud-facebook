@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Intake', 'url' => route('team.settings.intake.index')],
    ['title' => 'Edit Intake']
];
@endphp

<x-team.layout.app title="Edit Intake" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Intake: {{ $intake->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update intake information
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.intake.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Intake Information" headerClass="">
                <form action="{{ route('team.settings.intake.update', $intake) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')



                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                            <div class="grid grid-cols-3 gap-5"><!-- Yahan grid-cols-2 kar diya -->
                                <!-- Country Name -->
                                <x-team.forms.input
                                name="name"
                                label="Name"
                                type="text"
                                placeholder="Enter associate name"
                                :value="old('name',$intake->name)"
                                required
                                />

                                @php
                                $month = [
                                    'january' => 'January',
                                    'february' => 'February',
                                    'march' => 'March',
                                    'april' => 'April',
                                    'may' => 'May',
                                    'june' => 'June',
                                    'july' => 'July',
                                    'august' => 'August',
                                    'september' => 'September',
                                    'october' => 'October',
                                    'november' => 'November',
                                    'december' => 'December',
                                ];
                                @endphp
                                <x-team.forms.select
                                name="month[]"
                                label="Month"
                                :options="$month"
                                :selected="old('month', explode(',', $intake->month))"
                                placeholder="Select month"
                                searchable="true"
                                multiple="true"
                                />

                               <x-team.forms.input
                                    name="year"
                                    label="Year"
                                    type="number"
                                    placeholder="Enter year"
                                    :value="old('year',$intake->year)"
                                    :min="1000"
                                    :max="9999"
                                    required
                                />
                            </div>
                            </x-team.card>
                        </div>

                        <!-- Status -->
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
                                            {{ old('status', $intake->status) ? 'checked' : '' }}
                                        />
                                        <span class="kt-checkbox-label">
                                            Uncheck to make this intake inactive
                                        </span>
                                    </label>
                                </div>
                            </x-team.card>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5 border-t border-gray-200">
                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Created At</label>
                            <div class="text-sm text-gray-700">
                                {{ $intake->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Last Updated</label>
                            <div class="text-sm text-gray-700">
                                {{ $intake->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.intake.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Intake
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
    @endpush
</x-team.layout.app>
