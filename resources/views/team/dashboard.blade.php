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
