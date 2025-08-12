@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Coaching']
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
                        {{$coaching_type}} Coaching Management
                    </h1>
                    <p class="text-sm text-gray-600">Manage {{$coaching_type}} Coaching for today and overdue Coaching</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <!-- Add any Coaching specific actions here -->
                </div>
            </div>
            <div class="grid gap-2 lg:gap-2">
                {{-- Lead Dashboard Start --}}
                <x-team.card title="{{$coaching_type}} Coaching" headerClass="">
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                        </div>
                        <!-- Right: Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('team.coaching.pending') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.pending')) active @endif">
                                Pending Coachings
                            </a>
                            <a href="{{ route('team.coaching.running') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.running')) active @endif">
                                Running Coachings
                            </a>
                            <a href="{{ route('team.coaching.completed') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.completed')) active @endif">
                                Completed Coachings
                            </a>
                            <a href="{{ route('team.coaching.drop') }}" class="kt-btn kt-btn-outline rounded @if (request()->routeIs('team.coaching.drop')) active @endif">
                                Drop Coachings
                            </a>
                            <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-drawer-toggle="#coaching_filter_drawer">
                                <i class="ki-filled ki-filter"> </i> Filter
                            </button>
                        </div>
                    </x-slot>
                    <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch  pb-5">

                        @haspermission('invoice:export')
                            {{-- <form method="POST" action="{{ route('team.invoice.export') }}">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="date" id="filter_date">
                                <input type="hidden" name="branch" id="filter_branch">
                                <input type="hidden" name="owner" id="filter_owner">
                                <input type="hidden" name="coaching_name" value="{{ $coaching_type }}">

                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-download"></i> Export
                                </button>
                            </form> --}}
                        @endhaspermission
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
            title="Coaching Filters"
            :branches="$branches"
        />
    </x-slot>

    @push('scripts')

        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        @include('team.coaching.datatables.coaching-filter-js')
        @include('team.lead.lead-js')

        <script>
            function updateCoachingFiltersFromStorage() {
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
                updateCoachingFiltersFromStorage();

                // Watch for storage changes from other tabs (not same tab)
                window.addEventListener('storage', function (e) {
                    if (e.key === 'coaching_filters') {
                        updateCoachingFiltersFromStorage();
                    }
                });

                // Optional: re-sync filters if manually updated in same tab
                // Example: after setting or clearing localStorage
                window.refreshFiltersFromStorage = updateCoachingFiltersFromStorage;
            });
            </script>

    @endpush
</x-team.layout.app>

<x-team.modals.delete-modal
    id="delete_modal"
    title="Delete {{$coaching_type}} Coaching"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this {{$coaching_type}} Coaching? This action cannot be undone."
/>
