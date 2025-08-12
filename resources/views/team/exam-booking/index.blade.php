@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Exam Date Booking']
    ];
@endphp
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush
<x-team.layout.app title="Dashboard" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Exam Date Booking Management
                    </h1>
                    <p class="text-sm text-gray-600">Manage Exam Date Booking for today and overdue Exam Date Booking</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <!-- Add any Exam Date Booking specific actions here -->
                </div>
            </div>
            <div class="grid gap-2 lg:gap-2">
                {{-- Lead Dashboard Start --}}
                <x-team.card title="Exam Date Booking" headerClass="">
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                        </div>
                        <!-- Right: Buttons -->
                        <div class="flex items-center gap-2">

                            <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-drawer-toggle="#exam_booking_filter_drawer">
                                <i class="ki-filled ki-filter"> </i> Filter
                            </button>
                        </div>
                    </x-slot>
                    <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch  pb-5">

                        <div class="lg:col-span-1">
                            {{ $dataTable->table() }}
                        </div>

                    </div>
                </x-team.card>

                {{-- Lead Dashboard End --}}
            </div>
        </div>

        @php
            $branches = \App\Models\Branch::active()->get();
            $coachings = \App\Models\EnglishProficiencyTest::active()->get();
        @endphp

        {{-- Filter Drawer Component --}}
        <x-team.lead.exam-booking.exam-booking-filter
            id="exam_booking_filter_drawer"
            title="Exam Booking Filters"
            :branches="$branches"
            :coachings="$coachings"
        />
    </x-slot>

    @push('scripts')

        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @include('team.exam-booking.datatables.exam-booking-filter-js')
        @include('team.lead.lead-js')

        <script>
            function updateExamBookingFiltersFromStorage() {
                var filters = JSON.parse(localStorage.getItem('exam_booking_filters'));

                if (filters) {
                    $('#filter_exam_date').val(filters.exam_date || '');
                    $('#filter_result_date').val(filters.result_date || '');
                    $('#filter_status').val((filters.status || []).join(','));
                    $('#filter_branch').val((filters.branch || []).join(','));
                    $('#filter_owner').val((filters.owner || []).join(','));
                    $('#filter_source').val((filters.source || []).join(','));
                    $('#filter_lead_type').val((filters.lead_type || []).join(','));
                    $('#filter_coaching').val((filters.coaching || []).join(','));
                    $('#filter_batch').val((filters.batch || []).join(','));
                } else {
                    // Clear inputs if filters are missing
                    $('#filter_exam_date').val('');
                    $('#filter_result_date').val('');
                    $('#filter_status').val('');
                    $('#filter_branch').val('');
                    $('#filter_owner').val('');
                    $('#filter_source').val('');
                    $('#filter_lead_type').val('');
                    $('#filter_coaching').val('');
                    $('#filter_batch').val('');
                }
            }

            $(document).ready(function () {
                updateExamBookingFiltersFromStorage();

                // Watch for storage changes from other tabs (not same tab)
                window.addEventListener('storage', function (e) {
                    if (e.key === 'exam_booking_filters') {
                        updateExamBookingFiltersFromStorage();
                    }
                });

                // Optional: re-sync filters if manually updated in same tab
                // Example: after setting or clearing localStorage
                window.refreshFiltersFromStorage = updateExamBookingFiltersFromStorage;
            });
            </script>

    @endpush
</x-team.layout.app>

<x-team.modals.delete-modal
    id="delete_modal"
    title="Delete Exam Booking Result"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this exam booking result? This action cannot be undone."
/>
