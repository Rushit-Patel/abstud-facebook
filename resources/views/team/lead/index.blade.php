@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Leads']
    ];
@endphp
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush
<x-team.layout.app title="Dashboard" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex items-center gap-3 w-full">
                <div class="kt-input w-full">
                    <i class="ki-filled ki-magnifier"></i>
                    <div id="leadFilterBadge">
                    </div>
                </div>
                <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-drawer-toggle="#lead_filter_drawer">
                    <i class="ki-filled ki-filter"> </i> Filter
                </button>
                @haspermission('lead:create')
                <a href="{{ route('team.lead.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-plus"></i> Add Lead
                </a>
                @endhaspermission
            </div>
            <div class="grid gap-2 lg:gap-2 mt-2.5">
                {{-- Lead Dashboard Start --}}

                <x-team.card>
                    <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch  pb-5">
                        <div class="lg:col-span-1">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </x-team.card>
                <div id="toggleAssignOwner" class="hidden max-w-[325px] p-4 rounded-lg border border-border text-sm" >
                    Toggling content is a popular technique in web development that enhances
                    user interaction and engagement.
                </div>
                {{-- Lead Dashboard End --}}
            </div>
        </div>
        @php
            $status = \App\Models\LeadStatus::active()->get();
            $branches = \App\Models\Branch::active()->get();
            $leadTypes = \App\Models\LeadType::active()->get();
            $sources = \App\Models\Source::active()->get();
            $purpose = \App\Models\Purpose::active()->get();
            $countries = \App\Models\ForeignCountry::active()->get();
            $coaching = \App\Models\Coaching::active()->get();
        @endphp

        {{-- Filter Drawer Component --}}
        <x-team.lead.lead-filter
            id="lead_filter_drawer"
            title="Lead Filters"
            :status="$status"
            :branches="$branches"
            :leadTypes="$leadTypes"
            :sources="$sources"
            :purpose="$purpose"
            :countries="$countries"
            :coaching="$coaching"
        />

    </x-slot>

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @include('team.lead.datatables.filter-js')
        @include('team.lead.lead-js')




<script>
function updateFiltersFromStorage() {
    var filters = JSON.parse(localStorage.getItem('lead_filters'));

    if (filters) {
        $('#filter_date').val(filters.date || '');
        $('#filter_status').val((filters.status || []).join(','));
        $('#filter_branch').val((filters.branch || []).join(','));
        $('#filter_owner').val((filters.owner || []).join(','));
        $('#filter_source').val((filters.source || []).join(','));
        $('#filter_lead_type').val((filters.lead_type || []).join(','));
    } else {
        // Clear inputs if filters are missing
        $('#filter_date').val('');
        $('#filter_status').val('');
        $('#filter_branch').val('');
        $('#filter_owner').val('');
        $('#filter_source').val('');
        $('#filter_lead_type').val('');
    }
}

$(document).ready(function () {
    updateFiltersFromStorage();

    // Watch for storage changes from other tabs (not same tab)
    window.addEventListener('storage', function (e) {
        if (e.key === 'lead_filters') {
            updateFiltersFromStorage();
        }
    });

    // Optional: re-sync filters if manually updated in same tab
    // Example: after setting or clearing localStorage
    window.refreshFiltersFromStorage = updateFiltersFromStorage;
});
</script>
        <script>
            $(document).ready(function () {

                $('#toggleNextFollowUp').on('change', function () {
                    if ($(this).is(':checked')) {
                        $('#nextFollowUpFields').removeClass('hidden');
                        $('#nextFollowUpFields').find('input, select, textarea').each(function () {
                            $(this).attr('required', true);
                        });
                    } else {
                        $('#nextFollowUpFields').addClass('hidden');
                        $('#nextFollowUpFields').find('input, select, textarea').each(function () {
                            $(this).removeAttr('required');
                        });
                    }
                });
            });

            function viewAllFollowUps(leadId) {
                // Show loading state
                $('#followups-container').html(`
                    <div class="text-center py-8">
                        <i class="ki-filled ki-loading text-2xl text-gray-400 animate-spin"></i>
                        <p class="text-gray-500 mt-2">Loading follow-ups...</p>
                    </div>
                `);

                // Show modal first
                if (typeof window.KTModal !== 'undefined') {
                    const modalElement = document.querySelector('#viewAllFollowUpsModal');
                    const modal = window.KTModal.getOrCreateInstance(modalElement);
                    if (modal) {
                        modal.show();
                    }
                }

                // Fetch data
                $.ajax({
                    url: '{{ url("team/lead-follow-up/all") }}/' + leadId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Update client info
                            $('#client-name').text(response.client.name);
                            $('#client-mobile').text(response.client.mobile);
                            $('#client-email').text(response.client.email);
                            $('#client-branch').text(response.client.branch);

                            // Build follow-ups HTML
                            let followUpsHtml = '';
                            if (response.followUps.length > 0) {
                                response.followUps.forEach(function(followUp, index) {
                                    const statusBadge = followUp.status === '0'
                                        ? '<span class="kt-badge kt-badge-warning">Pending</span>'
                                        : '<span class="kt-badge kt-badge-success">Completed</span>';

                                    const communicationText = followUp.communication || 'No communication recorded';
                                    const updatedInfo = followUp.updated_by
                                        ? `Updated by ${followUp.updated_by} on ${followUp.updated_at}`
                                        : '';

                                    followUpsHtml += `
                                        <div class="border border-gray-200 rounded-lg p-4 ${followUp.status === '0' ? 'bg-yellow-50' : 'bg-gray-50'}">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <h5 class="font-medium text-gray-900">Follow-up #${response.followUps.length - index}</h5>
                                                    <p class="text-sm text-gray-600">Date: ${followUp.followup_date}</p>
                                                </div>
                                                <div class="text-right">
                                                    ${statusBadge}
                                                    <p class="text-xs text-gray-500 mt-1">Created: ${followUp.created_at}</p>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <div>
                                                    <label class="text-xs font-medium text-gray-700 uppercase tracking-wide">Remarks:</label>
                                                    <p class="text-sm text-gray-900 mt-1 p-2 bg-white rounded border">${followUp.remarks || 'No remarks'}</p>
                                                </div>

                                                ${followUp.status === '1' ? `
                                                    <div>
                                                        <label class="text-xs font-medium text-gray-700 uppercase tracking-wide">Communication:</label>
                                                        <p class="text-sm text-gray-900 mt-1 p-2 bg-white rounded border">${communicationText}</p>
                                                    </div>
                                                ` : ''}

                                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                                    <span class="text-xs text-gray-500">Created by: ${followUp.created_by}</span>
                                                    ${updatedInfo ? `<span class="text-xs text-gray-500">${updatedInfo}</span>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                            } else {
                                followUpsHtml = `
                                    <div class="text-center py-8">
                                        <i class="ki-filled ki-information text-3xl text-gray-400"></i>
                                        <p class="text-gray-500 mt-2">No follow-ups found for this lead.</p>
                                    </div>
                                `;
                            }

                            $('#followups-container').html(followUpsHtml);
                        } else {
                            $('#followups-container').html(`
                                <div class="text-center py-8">
                                    <i class="ki-filled ki-cross-circle text-3xl text-red-400"></i>
                                    <p class="text-red-500 mt-2">Error: ${response.message}</p>
                                </div>
                            `);
                        }
                    },
                    error: function () {
                        $('#followups-container').html(`
                            <div class="text-center py-8">
                                <i class="ki-filled ki-cross-circle text-3xl text-red-400"></i>
                                <p class="text-red-500 mt-2">Failed to load follow-ups. Please try again.</p>
                            </div>
                        `);
                    }
                });
            }
            // Function to view all follow-ups for a lead
            function viewFollowUpDetails(leadId) {
                // Show loading state
                $('#followups-container').html(`
                    <div class="text-center py-8">
                        <i class="ki-filled ki-loading text-2xl text-gray-400 animate-spin"></i>
                        <p class="text-gray-500 mt-2">Loading follow-ups...</p>
                    </div>
                `);

                // Show modal first
                if (typeof window.KTModal !== 'undefined') {
                    const modalElement = document.querySelector('#viewAllFollowUpsModal');
                    const modal = window.KTModal.getOrCreateInstance(modalElement);
                    if (modal) {
                        modal.show();
                    }
                }

                // Fetch data from server
                $.ajax({
                    url: '{{ url("team/lead-follow-up/all") }}/' + leadId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Update client info
                            $('#client-name').text(response.client.name);
                            $('#client-mobile').text(response.client.mobile);
                            $('#client-email').text(response.client.email);
                            $('#client-branch').text(response.client.branch);

                            // Simply insert the server-rendered timeline HTML
                            $('#followups-container').html(response.timelineHtml);
                        } else {
                            $('#followups-container').html(`
                                <div class="text-center py-8">
                                    <i class="ki-filled ki-cross-circle text-3xl text-red-400"></i>
                                    <p class="text-red-500 mt-2">Error: ${response.message}</p>
                                </div>
                            `);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });

                        let errorMessage = 'Failed to load follow-ups. Please try again.';
                        if (xhr.status === 404) {
                            errorMessage = 'Lead not found.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'You do not have permission to view these follow-ups.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please try again later.';
                        }

                        $('#followups-container').html(`
                            <div class="text-center py-8">
                                <i class="ki-filled ki-cross-circle text-3xl text-red-400"></i>
                                <p class="text-red-500 mt-2">${errorMessage}</p>
                            </div>
                        `);
                    }
                });
            }
        </script>
    @endpush
</x-team.layout.app>

<x-team.modals.delete-modal
    id="delete_modal"
    title="Delete Lead"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this Lead? This action cannot be undone."
/>

<x-team.lead.add-follow-up
    id="add-followUp"
    title="Add Follow-up"
    formId="followUpForm"
    :client_lead_id="request('client_lead_id')"
/>

<x-team.lead.edit-follow-up
    id="edit-followUp"
    title="Edit Follow-up"
    formId="followUpForm"
    :client_lead_id="request('client_lead_id')"
/>

<x-team.lead.view-follow-up
    id="viewFollowUpModal"
    title="Follow-up Details"
/>

<x-team.lead.view-all-follow-ups
    id="viewAllFollowUpsModal"
    title="All Follow-ups"
/>
