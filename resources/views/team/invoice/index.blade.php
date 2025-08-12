@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Invoice']
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
                        {{$invoiceName}} Invoice Management
                    </h1>
                    <p class="text-sm text-gray-600">Manage {{$invoiceName}} Invoice for today and overdue Invoice</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <!-- Add any Invoice specific actions here -->
                </div>
            </div>
            <div class="grid gap-2 lg:gap-2">
                {{-- Lead Dashboard Start --}}
                <x-team.card title="{{$invoiceName}} Invoice" headerClass="">
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                        </div>
                        <!-- Right: Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('team.invoice.pending') }}" class="kt-btn kt-btn-outline rounded">
                                Pending Invoices
                            </a>
                            <a href="{{ route('team.invoice.complete') }}" class="kt-btn kt-btn-outline rounded">
                                Completed Invoice
                            </a>
                            <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-drawer-toggle="#invoice_filter_drawer">
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
        @endphp

        {{-- Filter Drawer Component --}}
        <x-team.lead.invoice-filter
            id="invoice_filter_drawer"
            title="Invoice Filters"
            :branches="$branches"
        />
    </x-slot>

    @push('scripts')

        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @include('team.invoice.datatables.invoice-filter-js')
        @include('team.lead.lead-js')

        <script>
            function updateInvoiceFiltersFromStorage() {
                var filters = JSON.parse(localStorage.getItem('invoice_filters'));

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
                updateInvoiceFiltersFromStorage();

                // Watch for storage changes from other tabs (not same tab)
                window.addEventListener('storage', function (e) {
                    if (e.key === 'invoice_filters') {
                        updateInvoiceFiltersFromStorage();
                    }
                });

                // Optional: re-sync filters if manually updated in same tab
                // Example: after setting or clearing localStorage
                window.refreshFiltersFromStorage = updateInvoiceFiltersFromStorage;
            });
            </script>

    @endpush
</x-team.layout.app>

<x-team.modals.delete-modal
    id="delete_modal"
    title="Delete Lead"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this Lead? This action cannot be undone."
/>
