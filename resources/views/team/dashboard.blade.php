@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Dashboard']
    ];
@endphp

<x-team.layout.app title="Dashboard" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="grid gap-2 lg:gap-2">
                @haspermission('lead:*')
                    <x-team.dashboard.facebook.facebook-card />
                @endhaspermission
                @haspermission('lead:*')
                <div class="grid lg:grid-cols-2 gap-7.5 pb-5">
                    <x-team.card title="Leads Analysis" headerClass="" titleClass="text-xl text-gray-700">
                        <x-slot name="header">
                            @php
                                $leadDateRangeOption = array(
                                    'yesterday' => 'Yesterday',
                                    'last_7_days' => 'Last 7 Days',
                                    'last_30_days' => 'Last 30 Days',
                                    'last_month' => 'Last Month',
                                    'this_year' => 'This Year',
                                    'last_year' => 'Last Year',
                                    'custom' => 'Custom Range',
                                );
                            @endphp
                            <div class="flex flex-row gap-3">
                                <x-team.forms.select name="lead_filter_date_range" id="leadDateRange"
                                    class="lead_filter_date_range"
                                    :options="$leadDateRangeOption" :selected="old('lead_filter_date')"
                                    placeholder="Select Date Range" searchable="true" />

                                    <div class="flex flex-col gap-3 px-5"  id="custom_date_range" style="display: none;">
                                        <x-team.forms.range-datepicker
                                            label=""
                                            name="lead_filter_date"
                                            id="lead_filter_date"
                                            placeholder="Select lead date"
                                            dateFormat="Y-m-d"
                                            class="w-full range-flatpickr"
                                        />
                                    </div>

                                @haspermission('lead:show-all')
                                    <x-team.forms.select name="leadBranch" id="leadBranch" class="w-full"
                                        :options="$branches" placeholder="Select Branch" />
                                @endhaspermission
                            </div>
                        </x-slot>
                        <div class="grid gap-y-5">
                        <div class="lg:col-span-1" id="lead-statistics-wrapper">
                            <x-team.dashboard.lead.lead-statistics
                                :totalLeads="$totalLeads" :openLeads="$openLeads"
                                :closeLeads="$closeLeads" :registerLeads="$registerLeads"
                                :demoCount="$demoCount"/>
                        </div>
                        </div>
                    </x-team.card>

                    <x-team.card headerClass="text-xl" titleClass="text-xl text-gray-700">
                        <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-4">
                            <div class="flex flex-col justify-center gap-2">
                                <h5 class="text-xl font-medium leading-none ">
                                    Performance Matrix
                                </h5>
                                <p class="flex items-center gap-2 text-xs font-normal text-secondary-foreground">
                                    Current Month <b>VS</b> Last Month
                                </p>
                            </div>
                            @haspermission('lead:show-all')
                                <x-team.forms.select name="pLeadBranch" id="pLeadBranch" class="w-full" :options="$branches"
                                    placeholder="Select Branch" />
                            @endhaspermission
                        </div>
                        <div id="performanceMatrixWrapper" class="grid gap-y-5 mt-5">

                            <x-team.dashboard.lead.lead-performance-matrix
                                :thisMonthData="$monthCounts['this_month']"
                                :previousMonthData="$monthCounts['previous_month']"
                                :percentage="$monthCounts['percentage']"
                                />
                        </div>
                    </x-team.card>
                </div>
                @endhaspermission

                {{-- Student Visa Dashboard Start --}}

                <div class="grid lg:grid-cols-3 gap-7.5 pb-5">
                    <div class="col-span-1">
                        <x-team.card headerClass="" bodyClass="text-bg-primary" cardClass="text-bg-primary h-full rounded-lg"
                            titleClass="text-xl">
                            <div class="flex flex-col h-full gap-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center rounded-xl text-muted">
                                        <i class="ki-filled ki-graph-up text-4xl"></i>
                                        {{-- <i class="ki-filled ki-users w-5 mt-3 ms-3.5 text-2xl text-white"></i> --}}
                                    </div>
                                    <h5 class="text-white text-xl whitespace-nowrap">Top Performer - June 25</h5>
                                </div>
                                <!-- Body -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-6 mt-4">
                                    <div class="pr-4">
                                        <h6 class="text-light text-lg font-medium">Ms. Marry<br><span
                                                class="font-normal">25 Registration</span></h6>
                                    </div>
                                    <div class="pl-4 border-l border-white/50">
                                        <h6 class="text-light text-lg font-medium">Ms. Jiyali<br><span
                                                class="font-normal">22 Registration</span></h6>
                                    </div>

                                    <div class="pr-4">
                                        <h6 class="text-light text-lg font-medium">Mr. Nishant<br><span
                                                class="font-normal">20 Registration</span></h6>
                                    </div>
                                    <div class="pl-4 border-l border-white/50">
                                        <h6 class="text-light text-lg font-medium">Mr. Jayant<br><span
                                                class="font-normal">15 Registration</span></h6>
                                    </div>

                                    <div class="pr-4">
                                        <h6 class="text-light text-lg font-medium">Ms. Chandni<br><span
                                                class="font-normal">10 Registration</span></h6>
                                    </div>
                                    <div class="pl-4 border-l border-white/50">
                                        <h6 class="text-light text-lg font-medium">Mr. Jayant<br><span
                                                class="font-normal">13 Registration</span></h6>
                                    </div>
                                </div>
                            </div>
                        </x-team.card>
                    </div>
                    <div class="col-span-2">
                        <x-team.card title="Important Details" headerClass="text-xl" titleClass="text-xl text-gray-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                                <!-- Pending Followups -->
                                <div class="kt-card text-white text-bg-primary">
                                    <div class="kt-card-body p-6">
                                        <span class="inline-block">
                                            <i class="ki-filled ki-category text-2xl "></i>
                                        </span>
                                        <h4 class="text-2xl font-semibold mt-4 mb-1">450</h4>
                                        <p class="text-sm opacity-80">Pending Followups</p>
                                    </div>
                                </div>

                                <!-- Inactive Leads -->
                                <div class="kt-card text-white text-bg-success">
                                    <div class="kt-card-body p-6">
                                        <span class="inline-block">
                                            <i class="ki-filled ki-archive text-2xl"></i>
                                        </span>
                                        <h4 class="text-2xl font-semibold mt-4 mb-1">50</h4>
                                        <p class="text-sm opacity-80">Inactive Leads</p>
                                    </div>
                                </div>

                                <!-- Pending Fees -->
                                <div class="kt-card text-white text-bg-warning">
                                    <div class="kt-card-body p-6">
                                        <span class="inline-block">
                                            <i class="ki-filled ki-users text-2xl"></i>
                                        </span>
                                        <h4 class="text-2xl font-semibold mt-4 mb-1">80</h4>
                                        <p class="text-sm opacity-80">Pending Fees</p>
                                    </div>
                                </div>

                                <!-- Upcoming Interview -->
                                <div class="kt-card text-white text-bg-danger">
                                    <div class="kt-card-body p-6">
                                        <span class="inline-block">
                                            <i class="ki-filled ki-gift text-2xl"></i>
                                        </span>
                                        <h4 class="text-2xl font-semibold mt-4 mb-1">15</h4>
                                        <p class="text-sm opacity-80">Upcoming Interview</p>
                                    </div>
                                </div>

                                <!-- Pending Payment -->
                                <div class="kt-card text-white text-bg-info">
                                    <div class="kt-card-body p-6">
                                        <span class="inline-block">
                                            <i class="ki-filled ki-credit-cart text-2xl"></i>
                                        </span>
                                        <h4 class="text-2xl font-semibold mt-4 mb-1">180000</h4>
                                        <p class="text-sm opacity-80">Pending Payment</p>
                                    </div>
                                </div>

                                <!-- Task To Do -->
                                <div class="kt-card text-white text-bg-secondary">
                                    <div class="kt-card-body p-6">
                                        <span class="inline-block">
                                            <i class="ki-filled ki-message-question text-2xl"></i>
                                        </span>
                                        <h4 class="text-2xl font-semibold mt-4 mb-1">9</h4>
                                        <p class="text-sm opacity-80">Task To Do</p>
                                    </div>
                                </div>

                            </div>
                        </x-team.card>
                    </div>
                </div>


                {{-- Student Visa Dashboard End --}}

                {{-- Coaching Statistics Start --}}


                <div class="grid lg:grid-cols-2 gap-7.5 pb-5">
                    <x-team.card title="Visitor Visa" headerClass="" titleClass="text-xl text-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                            <div class="flex flex-col gap-7.5">
                                <!-- Total Leads -->
                                <a href="{{ route('team.lead.index',['purpose' => base64_encode('3')]) }}" >
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-primary">
                                            <i class="ki-filled ki-users text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-semibold text-gray-800">{{$visitorVisaCount->total}}</h6>
                                            <span class="text-sm text-muted">Total Leads</span>
                                        </div>
                                    </div>
                                </a>

                                <!-- Open Leads -->
                                <a href="{{ route('team.lead.index',['status' => base64_encode('1') ,'purpose' => base64_encode('3')]) }}" >
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-danger">
                                            <i class="ki-filled ki-message-question text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-semibold text-gray-800">{{$visitorVisaCount->open ?? 0}}</h6>
                                            <span class="text-sm text-muted">Open Leads</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="flex flex-col gap-7.5 ">
                                <!-- Close Leads -->

                                <a href="{{ route('team.lead.index',['status' => base64_encode('2') ,'purpose' => base64_encode('3')]) }}" >
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-secondary">
                                            <i class="ki-filled ki-information-3 text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-semibold text-gray-800">{{$visitorVisaCount->close ?? 0}}</h6>
                                            <span class="text-sm text-muted">Close Leads</span>
                                        </div>
                                    </div>
                                </a>

                                <!-- Granted Visa -->
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-success">
                                        <i class="ki-filled ki-message-question text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-lg font-semibold text-gray-800">490</h6>
                                        <span class="text-sm text-muted">Granted Visa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-team.card>

                    <x-team.card title="Dependent Visa" headerClass="" titleClass="text-xl text-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                            <div class="flex flex-col gap-7.5">
                                <!-- Total Leads -->
                                <a href="{{ route('team.lead.index',['purpose' => base64_encode('4')]) }}" >
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-primary">
                                            <i class="ki-filled ki-users text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-semibold text-gray-800">{{$DependentVisaCount->total}}</h6>
                                            <span class="text-sm text-muted">Total Leads</span>
                                        </div>
                                    </div>
                                </a>

                                <!-- Open Leads -->
                                <a href="{{ route('team.lead.index',['status' => base64_encode('1') ,'purpose' => base64_encode('4')]) }}" >
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-danger">
                                            <i class="ki-filled ki-information-3 text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-semibold text-gray-800">{{$DependentVisaCount->open ?? 0}}</h6>
                                            <span class="text-sm text-muted">Open Leads</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="flex flex-col gap-7.5 ">
                                <!-- Close Leads -->
                                <a href="{{ route('team.lead.index',['status' => base64_encode('2') ,'purpose' => base64_encode('4')]) }}" >
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-secondary">
                                            <i class="ki-filled ki-message-question text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-semibold text-gray-800">{{$DependentVisaCount->close ?? 0}}</h6>
                                            <span class="text-sm text-muted">Close Leads</span>
                                        </div>
                                    </div>
                                </a>

                                <!-- Granted Visa -->
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl text-bg-success">
                                        <i class="ki-filled ki-user-tick text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-lg font-semibold text-gray-800">490</h6>
                                        <span class="text-sm text-muted">Granted Visa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-team.card>
                </div>

                {{-- Coaching Statistics End --}}

                {{-- critical data Dashboard Start --}}

                <div class="grid lg:grid-cols-2 gap-7.5 pb-5">
                    <x-team.card title="Application Statastics" headerClass="" titleClass="text-xl text-gray-700">
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between" data-kt-tabs="true">
                                <button class="kt-btn kt-btn-outline active" data-kt-tab-toggle="#tabCanada">
                                    <img src="{{ asset('default/images/flags/canada.svg') }}" alt="Canada Flag" class="w-4 h-4">
                                    Canada
                                </button>
                                <button class="kt-btn kt-btn-outline" data-kt-tab-toggle="#tabUk">
                                    <img src="{{ asset('default/images/flags/united-kingdom.svg') }}" alt="UK Flag" class="w-4 h-4">
                                    UK
                                </button>
                                <button class="kt-btn kt-btn-outline" data-kt-tab-toggle="#tabUsa">
                                    <img src="{{ asset('default/images/flags/united-states.svg') }}" alt="USA Flag" class="w-4 h-4">
                                    USA
                                </button>
                                <button class="kt-btn kt-btn-outline" data-kt-tab-toggle="#tabDubai">
                                    <img src="{{ asset('default/images/flags/united-arab-emirates.svg') }}" alt="Dubai" class="w-4 h-4">
                                    Dubai
                                </button>

                                <button class="kt-btn kt-btn-outline" data-kt-tab-toggle="#tabAustralia">
                                    <img src="{{ asset('default/images/flags/australia.svg') }}" alt="Australia" class="w-4 h-4">
                                    Australia
                                </button>
                            </div>
                            <div class="text-sm">
                                <div class="" id="tabCanada">
                                    <div class="kt-table-wrapper">
                                        <table class="kt-table align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-semibold text-muted">
                                                    <th class="ps-0 min-w-200px">Type</th>
                                                    <th class="min-w-100px">Numbers</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Total Registration</h6>
                                                    </td>
                                                    <td><span>250</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Application Processed</h6>
                                                    </td>
                                                    <td><span>240</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Offer Letter Received</h6>
                                                    </td>
                                                    <td><span>180</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Tuition Fees Paid</h6>
                                                    </td>
                                                    <td><span>170</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File Under Process</h6>
                                                    </td>
                                                    <td><span>20</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File In HC</h6>
                                                    </td>
                                                    <td><span>30</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Granted</h6>
                                                    </td>
                                                    <td><span>120</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Rejected</h6>
                                                    </td>
                                                    <td><span>12</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="hidden" id="tabUk">
                                    <div class="kt-table-wrapper">
                                        <table class="kt-table align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-semibold text-muted">
                                                    <th class="ps-0 min-w-200px">Type</th>
                                                    <th class="min-w-100px">Numbers</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Total Registration</h6>
                                                    </td>
                                                    <td><span>250</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Application Processed</h6>
                                                    </td>
                                                    <td><span>240</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Offer Letter Received</h6>
                                                    </td>
                                                    <td><span>180</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Tuition Fees Paid</h6>
                                                    </td>
                                                    <td><span>170</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File Under Process</h6>
                                                    </td>
                                                    <td><span>20</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File In HC</h6>
                                                    </td>
                                                    <td><span>30</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Granted</h6>
                                                    </td>
                                                    <td><span>120</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Rejected</h6>
                                                    </td>
                                                    <td><span>12</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="hidden" id="tabUsa">
                                    <div class="kt-table-wrapper">
                                        <table class="kt-table align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-semibold text-muted">
                                                    <th class="ps-0 min-w-200px">Type</th>
                                                    <th class="min-w-100px">Numbers</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Total Registration</h6>
                                                    </td>
                                                    <td><span>250</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Application Processed</h6>
                                                    </td>
                                                    <td><span>240</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Offer Letter Received</h6>
                                                    </td>
                                                    <td><span>180</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Tuition Fees Paid</h6>
                                                    </td>
                                                    <td><span>170</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File Under Process</h6>
                                                    </td>
                                                    <td><span>20</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File In HC</h6>
                                                    </td>
                                                    <td><span>30</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Granted</h6>
                                                    </td>
                                                    <td><span>120</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Rejected</h6>
                                                    </td>
                                                    <td><span>12</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="hidden" id="tabDubai">
                                    <div class="kt-table-wrapper">
                                        <table class="kt-table align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-semibold text-muted">
                                                    <th class="ps-0 min-w-200px">Type</th>
                                                    <th class="min-w-100px">Numbers</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Total Registration</h6>
                                                    </td>
                                                    <td><span>250</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Application Processed</h6>
                                                    </td>
                                                    <td><span>240</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Offer Letter Received</h6>
                                                    </td>
                                                    <td><span>180</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Tuition Fees Paid</h6>
                                                    </td>
                                                    <td><span>170</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File Under Process</h6>
                                                    </td>
                                                    <td><span>20</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File In HC</h6>
                                                    </td>
                                                    <td><span>30</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Granted</h6>
                                                    </td>
                                                    <td><span>120</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Rejected</h6>
                                                    </td>
                                                    <td><span>12</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="hidden" id="tabAustralia">
                                    <div class="kt-table-wrapper">
                                        <table class="kt-table align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-semibold text-muted">
                                                    <th class="ps-0 min-w-200px">Type</th>
                                                    <th class="min-w-100px">Numbers</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Total Registration</h6>
                                                    </td>
                                                    <td><span>250</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Application Processed</h6>
                                                    </td>
                                                    <td><span>240</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Offer Letter Received</h6>
                                                    </td>
                                                    <td><span>180</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Tuition Fees Paid</h6>
                                                    </td>
                                                    <td><span>170</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File Under Process</h6>
                                                    </td>
                                                    <td><span>20</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">File In HC</h6>
                                                    </td>
                                                    <td><span>30</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Granted</h6>
                                                    </td>
                                                    <td><span>120</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0">
                                                        <h6 class="mb-0">Visa Rejected</h6>
                                                    </td>
                                                    <td><span>12</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-team.card>

                    <x-team.card title="Application Progress" headerClass="" titleClass="text-xl text-gray-700">
                        <div id="application_chart" style=""></div>
                    </x-team.card>
                </div>
                <div class="grid lg:grid-cols-3 gap-7.5 pb-5">

                    @foreach ($batchesStrengthCoachigWise as $batchesStrengthCoachigOne)
                        <x-team.card headerClass="" titleClass="text-xl text-gray-700">
                            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-4">
                                <div class="flex flex-col justify-center gap-2">
                                    <h5 class="text-xl font-medium leading-none ">
                                        {{ $batchesStrengthCoachigOne['coaching'] }}
                                    </h5>
                                    <p class="flex items-center gap-2 text-xs font-normal text-secondary-foreground">
                                        Batch Strength
                                    </p>
                                </div>
                            </div>
                            <div class="vstack gap-6">

                                @foreach ($batchesStrengthCoachigOne['data'] as $batchData)
                                    @php
                                        $batch_capacity = $batchData->getBatch?->capacity;
                                        if($batchData->counts > 0){
                                            $percentage = ($batchData->counts *100)/$batch_capacity;
                                        }else{
                                            $percentage = 0;
                                        }
                                    @endphp

                                    <div class="flex items-center gap-4">
                                        <h6 class="text-gray-700 text-base w-32 shrink-0">{{ $batchData->getBatch?->name }}</h6>
                                        <div class="w-full h-2.5 rounded bg-gray-100">
                                            <div class="h-full rounded text-bg-primary" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">{{ $batchData->counts }}</h6>
                                    </div>
                                @endforeach


                            </div>
                        </x-team.card>
                    @endforeach


                    {{-- <x-team.card headerClass="" titleClass="text-xl text-gray-700">
                        <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-4">
                            <div class="flex flex-col justify-center gap-2">
                                <h5 class="text-xl font-medium leading-none ">
                                    PTE
                                </h5>
                                <p class="flex items-center gap-2 text-xs font-normal text-secondary-foreground">
                                    Batch Strength
                                </p>
                            </div>
                        </div>
                        <div class="vstack gap-6">
                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Morning 8 to 10</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-primary" style="width: 25%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">25</h6>
                            </div>

                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Morning 10 to 12</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-secondary" style="width: 21%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">21</h6>
                            </div>

                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Extra Practice</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-warning" style="width: 58%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">58</h6>
                            </div>

                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Evening 5 to 7</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-danger" style="width: 32%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">32</h6>
                            </div>
                        </div>
                    </x-team.card>

                    <x-team.card headerClass="" titleClass="text-xl text-gray-700">
                        <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-4">
                            <div class="flex flex-col justify-center gap-2">
                                <h5 class="text-xl font-medium leading-none ">
                                    Duolingo
                                </h5>
                                <p class="flex items-center gap-2 text-xs font-normal text-secondary-foreground">
                                    Batch Strength
                                </p>
                            </div>
                        </div>
                        <div class="vstack gap-6">
                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Morning 8 to 10</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-primary" style="width: 25%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">25</h6>
                            </div>

                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Morning 10 to 12</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-secondary" style="width: 21%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">21</h6>
                            </div>

                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Extra Practice</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-warning" style="width: 58%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">58</h6>
                            </div>

                            <div class="flex items-center gap-4">
                                <h6 class="text-gray-700 text-base w-32 shrink-0">Evening 5 to 7</h6>
                                <div class="w-full h-2.5 rounded bg-gray-100">
                                    <div class="h-full rounded text-bg-danger" style="width: 32%"></div>
                                </div>
                                <h6 class="text-gray-500 text-base w-12 shrink-0 text-end">32</h6>
                            </div>
                        </div>
                    </x-team.card> --}}
                </div>


                {{-- Student Visa Dashboard End --}}
            </div>
        </div>

        <!-- Lead Filter Modal start -->
        <x-team.drawer.drawer id="leadFilterModal" title="Filter Leads">
            <x-slot name="body">
                <form id="leadFilterForm">
                    @csrf
                    <div class="grid gap-4">
                        <!-- Lead Status Filter -->
                        @php
                            $leadDateRangeOption = array(
                                'yesterday' => 'Yesterday',
                                'last_7_days' => 'Last 7 Days',
                                'last_30_days' => 'Last 30 Days',
                                'last_month' => 'Last Month',
                                'this_year' => 'This Year',
                                'last_year' => 'Last Year',
                                'custom' => 'Custom Range',
                            );
                        @endphp
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select name="lead_filter_date_range" id="lead_filter_date_range"
                                label="Date Range" :options="$leadDateRangeOption" :selected="old('lead_filter_date')"
                                placeholder="Select Date Range" searchable="true" />
                        </div>

                        <!-- Custom Date Range Picker -->
                        <div class="flex flex-col gap-3 px-5" id="custom_date_range" style="display: none;">
                            <x-team.forms.range-datepicker label="Lead Date" name="lead_filter_date"
                                id="lead_filter_date" placeholder="Select lead date" dateFormat="Y-m-d"
                                class="w-full range-flatpickr" />
                        </div>
                        @haspermission('lead:show-all')
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select name="lead_filter_branch[]" id="lead_filter_branch" label="Branch"
                                :options="$branches" :selected="old('lead_filter_branch')" placeholder="Select Branch"
                                searchable="true" multiple="true" />
                        </div>

                        @endhaspermission

                        @cannot('lead:show-all')
                        <input type="hidden" name="lead_filter_branch[]" id="lead_filter_branch"
                            value="{{ auth()->user()->branch_id }}">
                        @endcannot

                        @haspermission('lead:show-branch')
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select name="lead_filter_users[]" id="lead_filter_users" label="User"
                                :options="[]" :selected="old('lead_filter_users')" placeholder="Select user"
                                searchable="true" multiple="true" />
                        </div>
                        @endhaspermission
                    </div>
                    <div class="kt-card-footer grid grid-cols-2 gap-2.5 mt-5">
                        <button type="button" class="kt-btn kt-btn-outline" onclick="clearFilters()">
                            Clear Filters
                        </button>
                        <button type="submit" class="kt-btn kt-btn-primary" id="applyFiltersBtn">
                            <i class="ki-filled ki-check"></i>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </x-slot>
        </x-team.drawer.drawer>
        <!-- Lead Filter Modal End -->

    </x-slot>

    @push('scripts')

        <script>
            $(document).ready(function () {

                $(document).on('change','.lead_filter_date_range', function() {
                    if ($(this).val() == 'custom') {
                        $('#custom_date_range').show();
                    } else {
                        $('#custom_date_range').hide();
                    }
                });

                function fetchUsersByBranch(branchIds) {
                    $('#lead_filter_users').html('<option>Loading...</option>');

                    $.ajax({
                        url: '{{ route("team.get.users.by.branch") }}',
                        type: 'GET',
                        data: { 'branch_ids[]': branchIds },
                        success: function (response) {
                            let options = '<option value="">Select Users</option>';
                            response.forEach(function (user) {
                                options += `<option value="${user.id}">${user.name}</option>`;
                            });
                            $('#lead_filter_users').html(options);
                        },
                        error: function () {
                            $('#lead_filter_users').html('<option>Error loading users</option>');
                        }
                    });
                }

                const selectedBranchIds = $('#lead_filter_branch').val();

                // If there's a predefined branch (from hidden input), load its users
                if (selectedBranchIds && selectedBranchIds.length > 0) {
                    fetchUsersByBranch(selectedBranchIds);
                }

                // Attach event for dynamic branch change
                $('#lead_filter_branch').on('change', function () {
                    const selected = $(this).val();
                    if (selected && selected.length > 0) {
                        fetchUsersByBranch(selected);
                    } else {
                        $('#lead_filter_users').html('<option value="">Select Branch First</option>');
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function () {
                // Show/hide custom date range
                $('#leadDateRange').on('change', function () {
                    let value = $(this).val();
                    if (value === 'custom') {
                        $('#custom_date_range').show();
                    } else {
                        $('#custom_date_range').hide();
                        fetchLeads(); // fetch on preset ranges
                    }
                });

                // Fetch on branch change
                $('#leadBranch').on('change', function () {
                    fetchLeads();
                });

                // Fetch when custom date range is selected (you may need to tweak based on your datepicker)
                $(document).on('change', '#lead_filter_date', function () {
                    if ($('#leadDateRange').val() === 'custom') {
                        fetchLeads();
                    }
                });

                function fetchLeads() {
                    let dateRange = $('#leadDateRange').val();
                    let date = $('#lead_filter_date').val(); // only for custom
                    let branch = $('#leadBranch').val();

                    $.ajax({
                        url: "{{ route('team.dashboard.filter-leads-analysis') }}",
                        method: "GET",
                        data: {
                            date_range: dateRange,
                            custom_date: date,
                            branch: branch
                        },
                        beforeSend: function () {
                            // Show loading indicator if needed
                        },
                        success: function (response) {
                            if (response.success) {
                                $('#lead-statistics-wrapper').html(response.data.statistics);

                                if (typeof KTToast !== 'undefined') {
                                    KTToast.show({
                                        message: "Filters applied successfully.",
                                        pauseOnHover: true,
                                        variant: "success"
                                    });
                                }

                            } else {
                                alert('Failed to fetch data.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            if (typeof KTToast !== 'undefined') {
                                KTToast.show({
                                    message: "Error applying filters.",
                                    pauseOnHover: true,
                                    variant: "error"
                                });
                            }
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                }

                //Performance Matrix Ajax Call
                $(document).on('change', '#pLeadBranch', function () {
                    let branchId = $(this).val();
                    $.ajax({
                        url: "{{ route('team.dashboard.performance-matrix') }}",
                        type: "GET",
                        data: { branch_id: branchId },
                        beforeSend: function() {
                            $("#performanceMatrixWrapper").html(`
                                <div class="flex justify-center items-center h-40">
                                    <i class="ki-filled ki-loading animate-spin text-3xl text-primary"></i>
                                    Please wait data Filtering...
                                </div>
                            `);
                        },
                        success: function (response) {
                            $("#performanceMatrixWrapper").html(response.html);
                        },
                        error: function () {
                            alert('Error loading performance matrix');
                        }
                    });
                });

            });
        </script>


        <script>
            document.querySelectorAll("pre.code-view > code").forEach((codeBlock) => {
                codeBlock.textContent = codeBlock.innerHTML;
            });
        </script>

        <!-- <script src="../assets/js/apex-chart/apex.bar.init.js"></script> -->
        <script>
            const leadCounsellerCount = @json($leadCounsellerCount);
            document.addEventListener("DOMContentLoaded", function () {
                // Basic Bar Chart -------> BAR CHART
                var options_basic = {
                    series: [
                        {
                            data: [250, 240, 180, 170, 20, 30, 120, 12],
                        },
                    ],
                    chart: {
                        fontFamily: "inherit",
                        type: "bar",
                        height: 500,
                        toolbar: {
                            show: false,
                        },
                    },
                    grid: {
                        borderColor: "transparent",
                    },
                    colors: ["var(--bs-primary)"],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    xaxis: {
                        categories: [
                            "Total Registration",
                            "Application Processed",
                            "Offer Letter Received",
                            "Tuition Fees Paid",
                            "File Under Process",
                            "File In HC",
                            "Visa Grated",
                            "Visa Rejected",
                        ],
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    tooltip: {
                        theme: "dark",
                    },
                };

                var chart_bar_basic = new ApexCharts(
                    document.querySelector("#chart-bar-basic"),
                    options_basic
                );
                chart_bar_basic.render();

                // Stacked Bar Chart -------> BAR CHART
                var options_stacked = {
                    series: [
                        {
                            name: "Marine Sprite",
                            data: [44, 55, 41, 37, 22, 43, 21],
                        },
                        {
                            name: "Striking Calf",
                            data: [53, 32, 33, 52, 13, 43, 32],
                        },
                        {
                            name: "Tank Picture",
                            data: [12, 17, 11, 9, 15, 11, 20],
                        },
                        {
                            name: "Bucket Slope",
                            data: [9, 7, 5, 8, 6, 9, 4],
                        },
                        {
                            name: "Reborn Kid",
                            data: [25, 12, 19, 32, 25, 24, 10],
                        },
                    ],
                    chart: {
                        fontFamily: "inherit",
                        type: "bar",
                        height: 350,
                        stacked: true,
                        toolbar: {
                            show: false,
                        },
                    },
                    grid: {
                        borderColor: "transparent",
                    },
                    colors: [
                        "var(--bs-primary)",
                        "var(--bs-secondary)",
                        "#ffae1f",
                        "#fa896b",
                        "#39b69a",
                    ],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                        },
                    },
                    stroke: {
                        width: 1,
                        colors: ["#fff"],
                    },
                    xaxis: {
                        categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
                        labels: {
                            formatter: function (val) {
                                return val + "K";
                            },
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    yaxis: {
                        title: {
                            text: undefined,
                        },
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + "K";
                            },
                        },
                        theme: "dark",
                    },
                    fill: {
                        opacity: 1,
                    },
                    legend: {
                        position: "top",
                        horizontalAlign: "left",
                        offsetX: 40,
                        labels: {
                            colors: ["#a1aab2"],
                        },
                    },
                };

                var chart_bar_stacked = new ApexCharts(
                    document.querySelector("#chart-bar-stacked"),
                    options_stacked
                );
                chart_bar_stacked.render();

                // Reversed Bar Chart -------> BAR CHART
                var options_reversed = {
                    series: [
                        {
                            data: [400, 430, 448, 470, 540, 580, 690],
                        },
                    ],
                    chart: {
                        fontFamily: "inherit",
                        type: "bar",
                        height: 350,
                        toolbar: {
                            show: false,
                        },
                    },
                    grid: {
                        borderColor: "transparent",
                    },
                    colors: ["var(--bs-primary)"],
                    annotations: {
                        xaxis: [
                            {
                                x: 500,
                                borderColor: "var(--bs-primary)",
                                label: {
                                    borderColor: "var(--bs-primary)",
                                    style: {
                                        color: "#fff",
                                        background: "var(--bs-primary)",
                                    },
                                    text: "X annotation",
                                },
                            },
                        ],
                        yaxis: [
                            {
                                y: "July",
                                y2: "September",
                                label: {
                                    text: "Y annotation",
                                },
                            },
                        ],
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                        },
                    },
                    dataLabels: {
                        enabled: true,
                    },
                    xaxis: {
                        categories: [
                            "June",
                            "July",
                            "August",
                            "September",
                            "October",
                            "November",
                            "December",
                        ],
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    grid: {
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                        borderColor: "transparent",
                    },
                    yaxis: {
                        reversed: true,
                        axisTicks: {
                            show: true,
                        },
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    tooltip: {
                        theme: "dark",
                    },
                };

                var chart_bar_reversed = new ApexCharts(
                    document.querySelector("#chart-bar-reversed"),
                    options_reversed
                );
                chart_bar_reversed.render();

                // Patterned Bar Chart -------> BAR CHART
                var options_patterned = {
                    series: [
                        {
                            name: "Marine Sprite",
                            data: [44, 55, 41, 37, 22, 43, 21],
                        },
                        {
                            name: "Striking Calf",
                            data: [53, 32, 33, 52, 13, 43, 32],
                        },
                        {
                            name: "Tank Picture",
                            data: [12, 17, 11, 9, 15, 11, 20],
                        },
                        {
                            name: "Bucket Slope",
                            data: [9, 7, 5, 8, 6, 9, 4],
                        },
                    ],
                    chart: {
                        fontFamily: "inherit",
                        type: "bar",
                        height: 350,
                        colors: "#a1aab2",
                        stacked: true,
                        dropShadow: {
                            enabled: true,
                            blur: 1,
                            opacity: 0.25,
                        },
                        toolbar: {
                            show: false,
                        },
                    },
                    grid: {
                        borderColor: "transparent",
                    },
                    colors: ["var(--bs-primary)", "var(--bs-secondary)", "#ffae1f", "#fa896b"],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: "60%",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        width: 2,
                    },
                    xaxis: {
                        categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    yaxis: {
                        title: {
                            text: undefined,
                        },
                        labels: {
                            style: {
                                colors: "#a1aab2",
                            },
                        },
                    },
                    tooltip: {
                        shared: false,
                        y: {
                            formatter: function (val) {
                                return val + "K";
                            },
                        },
                        theme: "dark",
                    },
                    fill: {
                        type: "pattern",
                        opacity: 1,
                        pattern: {
                            style: ["circles", "slantedLines", "verticalLines", "horizontalLines"], // string or array of strings
                        },
                    },
                    states: {
                        hover: {
                            filter: "none",
                        },
                    },
                    legend: {
                        position: "right",
                        offsetY: 40,
                        labels: {
                            colors: "#a1aab2",
                        },
                    },
                };

                var chart_bar_patterned = new ApexCharts(
                    document.querySelector("#chart-bar-patterned"),
                    options_patterned
                );
                chart_bar_patterned.render();

                var colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'];

                // Application Chart
                var options = {
                    chart: { type: 'bar', height: 400, toolbar: { show: false } },
                    series: [{ name: 'Cases', data: [343, 250, 123, 98, 47, 5] }],
                    xaxis: { categories: ['Applications', 'Offers', 'Tuition Fees', 'Visa Applied', 'Approved', 'Rejected'] },
                    colors: ['#343c7c'],
                    dataLabels: { enabled: true },
                    plotOptions: {
                        bar: { horizontal: true, borderRadius: 4, columnWidth: '50%' }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#application_chart"), options);
                chart.render();
            });
        </script>
    @endpush
</x-team.layout.app>
