@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Application']
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
                        {{$application}} Application Management
                    </h1>
                    <p class="text-sm text-gray-600">Manage {{$application}} Application for today and overdue Application</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <!-- Add any Application specific actions here -->
                </div>
            </div>
            <div class="grid gap-2 lg:gap-2">
                {{-- Lead Dashboard Start --}}
                <x-team.card title="{{$application}} Application" headerClass="">
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                        </div>
                        <!-- Right: Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('team.application.pending') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.pending')) active @endif">
                                Pending Applications
                            </a>
                            {{-- <a href="{{ route('team.coaching.running') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.running')) active @endif">
                                Running Applications
                            </a>
                            <a href="{{ route('team.coaching.completed') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.completed')) active @endif">
                                Completed Applications
                            </a>
                            <a href="{{ route('team.coaching.drop') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.drop')) active @endif">
                                Drop Applications
                            </a> --}}
                            {{-- <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-drawer-toggle="#coaching_filter_drawer">
                                <i class="ki-filled ki-filter"> </i> Filter
                            </button> --}}
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
        <x-team.lead.coaching-filter
            id="coaching_filter_drawer"
            title="Application Filters"
            :branches="$branches"
        />
    </x-slot>

    @push('scripts')

        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @include('team.coaching.datatables.coaching-filter-js')
        @include('team.lead.lead-js')

        <script>
            function updateApplicationFiltersFromStorage() {
                var filters = JSON.parse(localStorage.getItem('coaching_filters'));

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
                updateApplicationFiltersFromStorage();

                // Watch for storage changes from other tabs (not same tab)
                window.addEventListener('storage', function (e) {
                    if (e.key === 'coaching_filters') {
                        updateApplicationFiltersFromStorage();
                    }
                });

                // Optional: re-sync filters if manually updated in same tab
                // Example: after setting or clearing localStorage
                window.refreshFiltersFromStorage = updateApplicationFiltersFromStorage;
            });
            </script>

    @endpush
</x-team.layout.app>

<x-team.modals.delete-modal
    id="delete_modal"
    title="Delete {{$application}} Application"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this {{$application}} Application? This action cannot be undone."
/>
