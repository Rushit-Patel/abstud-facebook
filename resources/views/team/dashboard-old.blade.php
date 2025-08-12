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
                    {{-- Lead Dashboard Start --}}
                    <x-team.card title="Leads Statistics" headerClass="">
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <div class="kt-menu" data-kt-menu="true">
                                    <button class="kt-btn kt-btn-icon kt-btn-outline" data-kt-drawer-toggle="#leadFilterModal">
                                        <i class="ki-filled ki-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </x-slot>
                        <div class="grid lg:grid-cols-3 gap-y-5 lg:gap-7.5 items-stretch  pb-5">
                            <div class="lg:col-span-1" id="lead-statistics-container">
                                <x-team.dashboard.lead.lead-statistics-old
                                    :totalLeads="$totalLeads"
                                    :openLeads="$openLeads"
                                    :closeLeads="$closeLeads"
                                    :registerLeads="$registerLeads" />
                            </div>
                            <div class="lg:col-span-1" id="lead-by-service-container">
                                <x-team.dashboard.lead.lead-by-service />
                            </div>
                            <div class="lg:col-span-1" id="lead-by-team-container">
                                <x-team.dashboard.lead.lead-by-team />
                            </div>
                        </div>
                    </x-team.card>
                    {{-- Lead Dashboard End --}}
                @endhaspermission
                {{-- Student Visa Dashboard Start --}}
                <x-team.card title="Student Visa Statistics" headerClass="">
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="kt-menu" data-kt-menu="true">
                                    <button class="kt-btn kt-btn-icon kt-btn-outline"
                                        data-kt-modal-toggle="#studentVisaFilterModal">
                                        <i class="ki-filled ki-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <div class="grid lg:grid-cols-2 gap-y-5 lg:gap-7.5 items-stretch mb-5">
                        <div class="lg:col-span-1">
                            <div class="grid lg:grid-cols-2 gap-y-5 lg:gap-4 items-stretch mb-5">
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-bank text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Applications
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                343
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i
                                                            class="ki-filled ki-questionnaire-tablet text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Offers
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                250
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-dollar text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Tution Fees
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                123
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-address-book text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Visa Applied
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                98
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-airplane text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Visa Approved
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                47
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-question text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Visa Rejected
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                5
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="kt-card h-full ">
                                <div class="kt-card-content p-0">
                                    <div id="application_chart"></div>
                                </div>
                                <div class="kt-card-footer justify-center">
                                    <a class="kt-link kt-link-underlined kt-link-dashed" href="javascript:void(0);">
                                        Stage-wise Case Comparison
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-team.card>
                {{-- Student Visa Dashboard End --}}

                {{-- Coaching Statistics Start --}}
                <x-team.card title="Coaching Statistics" headerClass="">
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="kt-menu" data-kt-menu="true">
                                    <button class="kt-btn kt-btn-icon kt-btn-outline"
                                        data-kt-modal-toggle="#studentVisaFilterModal">
                                        <i class="ki-filled ki-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <div class="grid lg:grid-cols-3 gap-y-5 lg:gap-7.5 items-stretch mb-5">
                        <div class="lg:col-span-2">
                            <div class="grid lg:grid-cols-3 gap-y-5 lg:gap-4 items-stretch mb-5">
                                <div class="lg:col-span-3">
                                    <div class="kt-card p-5">
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5">
                                            <div class="flex flex-col items-center justify-center text-center gap-1.5">
                                                <div
                                                    class="flex justify-center items-center size-14 rounded-full ring-1 ring-input bg-violet-50">
                                                    343
                                                </div>
                                                <div class="mt-1">
                                                    <span class="leading-none font-medium text-base text-primary">
                                                        Demo Students
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center text-center gap-1.5">
                                                <div
                                                    class="flex justify-center items-center size-14 rounded-full ring-1 ring-input bg-violet-50">
                                                    34
                                                </div>
                                                <div class="mt-1">
                                                    <span class="leading-none font-medium text-base text-primary">
                                                        Not Attended
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center text-center gap-1.5">
                                                <div
                                                    class="flex justify-center items-center size-14 rounded-full ring-1 ring-input bg-violet-50">
                                                    300
                                                </div>
                                                <div class="mt-1">
                                                    <span class="leading-none font-medium text-base text-primary">
                                                        Attended
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-center justify-center text-center gap-1.5">
                                                <div
                                                    class="flex justify-center items-center size-14 rounded-full ring-1 ring-input bg-violet-50">
                                                    200
                                                </div>
                                                <div class="mt-1">
                                                    <span class="leading-none font-medium text-base text-primary">
                                                        Registered
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-center justify-center text-center gap-1.5">
                                                <div
                                                    class="flex justify-center items-center size-14 rounded-full ring-1 ring-input bg-violet-50">
                                                    100
                                                </div>
                                                <div class="mt-1">
                                                    <span class="leading-none font-medium text-base text-primary">
                                                        Close
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid lg:grid-cols-3 gap-y-5 lg:gap-4 items-stretch mb-5">
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-archive-tick text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Registered
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                343
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-chart-line text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Active
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                250
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-double-check-circle text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Completed
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                123
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-calendar-tick text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Exam Book
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                98
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-like-tag text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Result Received
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                47
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="relative size-[30px] shrink-0">
                                                    <div
                                                        class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-airplane text-2xl text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-base text-mono">
                                                        Visa File
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="font-semibold text-1xl text-foreground">
                                                5
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="kt-card h-full">
                                {{-- <div class="kt-card-header">
                                    <h3 class="kt-card-title">
                                        Highlights
                                    </h3>
                                </div> --}}
                                <div class="kt-card-content flex flex-col gap-4 p-5 lg:p-7.5 lg:pt-4">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-sm font-normal text-secondary-foreground">
                                            Active Coaching Students
                                        </span>
                                        <div class="flex items-center gap-2.5">
                                            <span class="text-3xl font-semibold text-mono">
                                                305
                                            </span>
                                            <span class="kt-badge kt-badge-outline kt-badge-success kt-badge-sm">
                                                +2.7%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 mb-1.5">
                                        <div class="bg-green-500 h-2 w-full max-w-[60%] rounded-xs">
                                        </div>
                                        <div class="bg-destructive h-2 w-full max-w-[25%] rounded-xs">
                                        </div>
                                        <div class="bg-violet-500 h-2 w-full max-w-[15%] rounded-xs">
                                        </div>
                                    </div>
                                    <div class="flex items-center flex-wrap gap-4 mb-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="size-2 rounded-full kt-badge-success">
                                            </span>
                                            <span class="text-sm font-normal text-foreground">
                                                IELTS
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="size-2 rounded-full kt-badge-destructive">
                                            </span>
                                            <span class="text-sm font-normal text-foreground">
                                                PTE
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="size-2 rounded-full kt-badge-info">
                                            </span>
                                            <span class="text-sm font-normal text-foreground">
                                                TOEFL
                                            </span>
                                        </div>
                                    </div>
                                    <div class="border-b border-input">
                                    </div>
                                    <div class="grid gap-3">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <i class="ki-filled ki-shop text-base text-muted-foreground">
                                                </i>
                                                <span class="text-sm font-normal text-mono">
                                                    IELTS
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-medium text-foreground gap-6">
                                                <span class="lg:text-right">
                                                    200
                                                </span>
                                                <span class="lg:text-right">
                                                    3.9%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <i class="ki-filled ki-facebook text-base text-muted-foreground">
                                                </i>
                                                <span class="text-sm font-normal text-mono">
                                                    PTE
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-medium text-foreground gap-6">
                                                <span class="lg:text-right">
                                                    87
                                                </span>
                                                <span class="lg:text-right">
                                                    0.7%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <i class="ki-filled ki-instagram text-base text-muted-foreground">
                                                </i>
                                                <span class="text-sm font-normal text-mono">
                                                    TOEFL
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-medium text-foreground gap-6">
                                                <span class="lg:text-right">
                                                    36
                                                </span>
                                                <span class="lg:text-right">

                                                    8.2%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-team.card>
                {{-- Coaching Statistics End --}}

                {{-- critical data Dashboard Start --}}
                <x-team.card title="Critical Data" headerClass="" cardClass="mb-10">

                    <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch ">
                        <div class="lg:col-span-1">
                            <div class="grid lg:grid-cols-4 gap-y-5 lg:gap-4 items-stretch ">
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                                    343
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Pending Follow Up
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground truncate w-full max-w-[10rem]"  data-kt-tooltip="#inactive_tooltip"
                                                    data-kt-tooltip-placement="bottom-start">
                                                        Count pending follow-ups (not completed) for the given date, linked to inquiries that are not closed.
                                                        <span data-kt-tooltip-content="true" class="kt-tooltip kt-tooltip-light">
                                                            Count pending follow-ups (not completed) for the given date, linked to inquiries that are not closed.
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-destructive/5 rounded-lg border border-input">
                                                    20
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Inactive Inquiry
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground truncate w-full max-w-[10rem]"  data-kt-tooltip="#inactive_tooltip"
                                                    data-kt-tooltip-placement="bottom-start">
                                                        counts inactive inquiries where the not marked as demo, not closed, not registered, inquiry date is 5 or more days old, and either the last follow-up was on or before 5 days ago or no follow-up exists.
                                                        <span data-kt-tooltip-content="true" class="kt-tooltip kt-tooltip-light">
                                                            counts inactive inquiries where the not marked as demo, not closed, not registered, inquiry date is 5 or more days old, and either the last follow-up was on or before 5 days ago or no follow-up exists.
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-destructive/5 rounded-lg border border-input">
                                                    4
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Inactive Demo
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground">
                                                        Description
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                                    20
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Pending Payment
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground">
                                                        Description
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                                    34
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Expiring Offer
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground">
                                                        Description
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                                    5
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Upcoming Interview
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground">
                                                        Description
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                                    0
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        Bio Matric
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground">
                                                        Description
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-1">
                                    <div class="kt-card p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-5">
                                            <div class="flex align-start gap-3.5">
                                                <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                                    8
                                                </div>
                                                <div class="flex flex-col justify-start gap-1">
                                                    <span class="text-sm font-medium text-mono">
                                                        VFS
                                                    </span>
                                                    <span class="text-xs text-secondary-foreground">
                                                        Description
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-team.card>
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
                            <x-team.forms.select
                                name="lead_filter_date_range"
                                id="lead_filter_date_range"
                                label="Date Range"
                                :options="$leadDateRangeOption"
                                :selected="old('lead_filter_date')"
                                placeholder="Select Date Range"
                                searchable="true"
                            />
                        </div>

                        <!-- Custom Date Range Picker -->
                        <div class="flex flex-col gap-3 px-5"  id="custom_date_range" style="display: none;">
                            <x-team.forms.range-datepicker
                                label="Lead Date"
                                name="lead_filter_date"
                                id="lead_filter_date"
                                placeholder="Select lead date"
                                dateFormat="Y-m-d"
                                class="w-full range-flatpickr"
                            />
                        </div>
                        @haspermission('lead:show-all')
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select
                                name="lead_filter_branch[]"
                                id="lead_filter_branch"
                                label="Branch"
                                :options="$branches"
                                :selected="old('lead_filter_branch')"
                                placeholder="Select Branch"
                                searchable="true"
                                multiple="true"
                            />
                        </div>

                        @endhaspermission

                        @cannot('lead:show-all')
                            <input type="hidden" name="lead_filter_branch[]" id="lead_filter_branch" value="{{ auth()->user()->branch_id }}">
                        @endcannot

                        @haspermission('lead:show-branch')
                        <div class="flex flex-col gap-3 px-5">
                            <x-team.forms.select
                                    name="lead_filter_users[]"
                                    id="lead_filter_users"
                                    label="User"
                                    :options="[]"
                                    :selected="old('lead_filter_users')"
                                    placeholder="Select user"
                                    searchable="true"
                                    multiple="true"
                                />
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
            const leadLabels = @json($leadLabels);
            const leadSeries = @json($leadSeries);
            const leadCounsellerCount = @json($leadCounsellerCount);
            const leadCounsellerName = @json($leadCounsellerName);

            $(document).ready(function () {

                initializeCharts();


                $(document).on('submit','#leadFilterForm', function(e) {
                    e.preventDefault();
                    applyLeadFilters();
                });

                $(document).on('change','#lead_filter_date_range', function() {
                    if ($(this).val() == 'custom') {
                        $('#custom_date_range').show();
                    } else {
                        $('#custom_date_range').hide();
                    }
                });
            });

            function initializeCharts() {
                // Lead Service Chart
                var colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'];
                var leadServiceChartOptions = {
                    series: leadSeries,
                    chart: { type: 'pie', height: 250 },
                    colors: colors,
                    labels: leadLabels,
                    legend: { position: 'bottom' }
                };
                var leadServiceChart = new ApexCharts(document.querySelector("#lead_by_service_chart"), leadServiceChartOptions);
                leadServiceChart.render();

                // Team Chart
                var salesTeamChartOptions = {
                    series: [{ data: leadCounsellerCount }],
                    chart: { type: 'bar', height: 270, toolbar: { show: false } },
                    colors: colors,
                    xaxis: { categories: leadCounsellerName },
                    plotOptions: {
                        bar: { horizontal: false, distributed: true, columnWidth: '50%' }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false }
                };
                var salesTeamChart = new ApexCharts(document.querySelector("#top_performing_sales_team_chart"), salesTeamChartOptions);
                salesTeamChart.render();

                // Application Chart
                var options = {
                    chart: { type: 'bar', height: 240, toolbar: { show: false } },
                    series: [{ name: 'Cases', data: [343, 250, 123, 98, 47, 5] }],
                    xaxis: { categories: ['Applications', 'Offers', 'Tuition Fees', 'Visa Applied', 'Approved', 'Rejected'] },
                    colors: ['#343c7c'],
                    dataLabels: { enabled: true },
                    plotOptions: {
                        bar: { horizontal: false, borderRadius: 4, columnWidth: '50%' }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#application_chart"), options);
                chart.render();
            }

            function applyLeadFilters() {
                const $btn = $('#applyFiltersBtn');
                const originalText = $btn.html();

                $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Loading...');

                $.ajax({
                    url: '{{ route("team.dashboard.filter-leads") }}',
                    type: 'POST',
                    data: $('#leadFilterForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update statistics
                            $('#lead-statistics-container').html(response.data.statistics);

                            // Update charts
                            updateChart("#lead_by_service_chart", response.data.leadByService.labels, response.data.leadByService.series, 'pie');
                            updateChart("#top_performing_sales_team_chart", response.data.leadByTeam.names, response.data.leadByTeam.counts, 'bar');

                            var $drawer = $('#leadFilterModal');
                            if ($drawer.length && typeof KTDrawer !== 'undefined') {
                                var drawerInstance = KTDrawer.getInstance($drawer[0]);
                                if (drawerInstance) {
                                    drawerInstance.hide();
                                }
                            }

                            if (typeof KTToast !== 'undefined') {
                                KTToast.show({
                                    message: "Filters applied successfully!",
                                    icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                                    pauseOnHover: true,
                                    variant: "success"
                                });
                            }
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

            function updateChart(selector, labels, data, type) {
                const chartElement = document.querySelector(selector);
                if (chartElement) {
                    chartElement.innerHTML = '';
                    var colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'];

                    var options = {
                        colors: colors,
                        chart: {
                            type: type,
                            height: type === 'pie' ? 250 : 270,
                            toolbar: { show: false }
                        }
                    };

                    if (type === 'pie') {
                        options.series = data;
                        options.labels = labels;
                        options.legend = { position: 'bottom' };
                    } else {
                        options.series = [{ data: data }];
                        options.xaxis = { categories: labels };
                        options.plotOptions = {
                            bar: { horizontal: false, distributed: true, columnWidth: '50%' }
                        };
                        options.dataLabels = { enabled: false };
                        options.legend = { show: false };
                    }

                    var chart = new ApexCharts(chartElement, options);
                    chart.render();
                }
            }

            function clearFilters() {
                $('#leadFilterForm')[0].reset();
                $('#custom_date_range').hide();
                location.reload();
            }
        </script>

        {{-- <script>
            $(document).ready(function () {
                $('#lead_filter_branch').on('change', function () {
                    const selectedBranchIds = $(this).val();

                    // Show loading text
                    $('#lead_filter_users').html('<option>Loading...</option>');

                    if (selectedBranchIds && selectedBranchIds.length > 0) {
                        $.ajax({
                            url: '{{ route("team.get.users.by.branch") }}',
                            type: 'GET',
                            data: { branch_ids: selectedBranchIds },
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
                    } else {
                        $('#lead_filter_users').html('<option value="">Select Branch First</option>');
                    }
                });
            });
            </script> --}}


            <script>
                $(document).ready(function () {
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



    @endpush
</x-team.layout.app>
