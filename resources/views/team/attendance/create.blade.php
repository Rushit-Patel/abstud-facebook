@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Coaching Attendance', 'url' => route('team.coaching.pending')],
    ['title' => 'All Coaching Student']
];
@endphp
<x-team.layout.app title="All Coaching Student" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        All Coaching Student
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        All Coaching Student to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.attendance.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="All Coaching Student Information" headerClass="">

                    <x-team.lead.attendance-details
                        :coaching="$coaching"
                    />
            </x-team.card>
        </div>

        {{-- Attendance Modal --}}
        <div class="kt-modal" data-kt-modal="true" id="attendance_data">
            <div class="kt-modal-content max-w-[786px] top-[10%]">
                <div class="kt-modal-header">
                    <h3 class="kt-modal-title">Attendance Summary</h3>
                    <button type="button" class="kt-modal-close" aria-label="Close modal" data-kt-modal-dismiss="#add_visited_country">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-x" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="kt-modal-body">
                    <form action="{{ route('team.attendance.store') }}" method="POST" class="form" enctype="multipart/form-data">
                        @csrf
                        <div id="attendance_hidden_inputs"></div>
                        <div id="AttendanceModalContent" class="rounded-lg bg-muted w-full grow min-h-[22px] p-4">
                            Loading...
                        </div>
                        <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                            <a href="#" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#attendance_data">Cancel</a>
                            <button type="submit" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-check"></i> Save Change
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </x-slot>
@push('scripts')
    @include('team.lead.lead-js')

<script>
    $(document).ready(function () {
        // Function to update modal content and hidden inputs
        function updateModalContent() {
            // Clear existing hidden inputs
            $('#attendance_hidden_inputs').empty();
            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let yyyy = today.getFullYear();
            let formattedToday = `${dd}/${mm}/${yyyy}`;

            // Capture search form data
            let joiningDate = $('#joining_date').val() || formattedToday;
            let coachingIds = $('#coaching_select_multiple').val() || [];
            let batchIds = $('#batch_select_multiple').val() || [];

            // Append search form data as hidden inputs
            $('#attendance_hidden_inputs').append(
                `<input type="hidden" name="joining_date" value="${joiningDate}">`
            );
            coachingIds.forEach(function(coachingId, index) {
                $('#attendance_hidden_inputs').append(
                    `<input type="hidden" name="coaching_id[${index}]" value="${coachingId}">`
                );
            });
            batchIds.forEach(function(batchId, index) {
                $('#attendance_hidden_inputs').append(
                    `<input type="hidden" name="batch_id[${index}]" value="${batchId}">`
                );
            });

            // Initialize counters
            let presentCount = 0;
            let absentCount = 0;
            let nothingCount = 0;

            // Iterate through table rows to collect attendance data
            $('tbody tr').each(function(index) {
                let clientId = $(this).find('input[name="client_coaching_id[]"]').val() || '';
                let status = $(this).find(`input[name="attendance_${index}"]:checked`).val() || '';

                // Update counters based on status
                if (status === 'present') presentCount++;
                else if (status === 'absent') absentCount++;
                else if (status === 'nothing') nothingCount++;
                else ;

                // Append attendance hidden inputs
                $('#attendance_hidden_inputs').append(
                    `<input type="hidden" name="attendance[${index}][client_coaching_id]" value="${clientId}">`
                );
                $('#attendance_hidden_inputs').append(
                    `<input type="hidden" name="attendance[${index}][status]" value="${status}">`
                );
            });

            // Update modal summary

            let searchDate = joiningDate || formattedToday;

            let html = `
                <div class="space-y-2">
                    <p><b>Submit Date:</b> ${searchDate}</p>
                    <p><b>Present:</b> ${presentCount}</p>
                    <p><b>Absent:</b> ${absentCount}</p>
                    <p><b>Nothing:</b> ${nothingCount}</p>
                </div>
            `;
            $('#AttendanceModalContent').html(html);
        }

        // Trigger when modal toggle button is clicked
        $(document).on('click', '[data-kt-modal-toggle="#attendance_data"]', function (e) {
            e.preventDefault();
            updateModalContent();
            // Manually show the modal if using a custom modal library
            $('#attendance_data').show(); // Adjust based on your modal library
        });

        // Update modal content when the modal is shown (for Bootstrap or similar)
        $('#attendance_data').on('shown.bs.modal', function () {
            updateModalContent();
        });
    });
</script>


<script>
$(document).ready(function () {
    function toggleSubmitButton() {
        // Agar ek bhi attendance select hua hai to enable karo
        if ($('.attendance-radio:checked').length > 0) {
            $('#submitBtnmodel').prop('disabled', false);
        } else {
            $('#submitBtnmodel').prop('disabled', true);
        }
    }

    // Apply All functionality
    $(document).on('change', '.apply-all', function() {
        let value = $(this).val();
        $('.attendance-radio[value="' + value + '"]').prop('checked', true);
        toggleSubmitButton(); // button ka status update
    });

    // Individual attendance select hone par check
    $(document).on('change', '.attendance-radio', function() {
        toggleSubmitButton();
    });

    // Page load par bhi initial check
    toggleSubmitButton();
});
</script>


<script>
$(document).ready(function () {
    function toggleSearchButton() {
        let dateVal = $('#joining_date').val().trim();
        let coachingVal = $('#coaching_select_multiple').val();
        let batchVal = $('#batch_select_multiple').val();

        // Agar tino fields empty hai to disable kare
        if (!dateVal && (!coachingVal || coachingVal.length === 0) && (!batchVal || batchVal.length === 0)) {
            $('#searchBtn').prop('disabled', true);
        } else {
            $('#searchBtn').prop('disabled', false);
        }
    }

    // Pehle load pe check karo
    toggleSearchButton();

    // Har change pe check karo
    $('#joining_date, #coaching_select_multiple, #batch_select_multiple').on('change keyup', function () {
        toggleSearchButton();
    });
});
</script>

@endpush

</x-team.layout.app>

{{-- <x-team.modals.delete-modal
    id="payment_delete_modal"
    title="Delete Coaching"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this coaching? This action cannot be undone."
/> --}}
