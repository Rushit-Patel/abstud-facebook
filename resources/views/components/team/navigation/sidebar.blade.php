{{-- Team Sidebar Component --}}
@props([
    'appData' => []
])
{{-- Main Sidebar Container --}}
<div class="kt-sidebar bg-background border-e border-e-border fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]" data-kt-drawer="true" data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0" id="sidebar">
    {{-- Sidebar Header --}}
    <x-team.navigation.sidebar-header :appData="$appData" />

    {{-- Sidebar Content --}}
    <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
        <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3"
            data-kt-scrollable="true" data-kt-scrollable-dependencies="#sidebar_header"
            data-kt-scrollable-height="auto" data-kt-scrollable-offset="0px"
            data-kt-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">

            {{-- Sidebar Menu --}}
            <div class="kt-menu flex flex-col grow gap-1" data-kt-menu="true" data-kt-menu-accordion-expand-all="false" id="sidebar_menu">

                {{-- Dashboards Section --}}
                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-element-11"
                    label="Dashboards"
                    route="team.dashboard"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-heading label="Lead Management" />

                @haspermission('lead:*')
                    @php
                        $tooltipText = '';
                        if (auth()->user()->can('lead:show-branch')) {
                            $tooltipText = 'These are unassigned leads (owner not set) for your branch.';
                        } else {
                            $tooltipText = 'These leads are unassigned and have no follow-ups yet.';
                        }
                    @endphp
                    <x-team.navigation.sidebar-menu-item
                        icon="ki-filled ki-people"
                        label="Leads"
                        route="team.lead.index"
                        :badge="$appData['leadCounts']['unassignedLeads'] > 0 ? $appData['leadCounts']['unassignedLeads'] : null"
                        :tooltip="$tooltipText"
                        :hasSubmenu="false">
                    </x-team.navigation.sidebar-menu-item>
                @endhaspermission

                @haspermission('follow-up:*')
                    <x-team.navigation.sidebar-menu-item
                        icon="ki-filled ki-message-question"
                        label="Follow Up"
                        route="team.lead-follow-up.pending"
                        :badge="$appData['pendingCounts']['followUps'] > 0 ? $appData['pendingCounts']['followUps'] : null"
                        tooltip="Follow-ups pending as of today or earlier."
                        :hasSubmenu="false">
                    </x-team.navigation.sidebar-menu-item>
                @endhaspermission

                @haspermission('invoice:*')
                    <x-team.navigation.sidebar-menu-item
                        icon="ki-solid ki-dollar"
                        label="Invoice"
                        route="team.invoice.pending"
                        {{-- :badge="$appData['pendingCounts']['followUps'] > 0 ? $appData['pendingCounts']['followUps'] : null" --}}
                        tooltip="Follow-ups pending as of today or earlier."
                        :hasSubmenu="false">
                    </x-team.navigation.sidebar-menu-item>
                @endhaspermission

                <x-team.navigation.sidebar-heading label="Coaching Management" />

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-abstract-37"
                    label="Demo"
                    route="team.demo.pending"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-book-open"
                    label="Coachings"
                    route="team.coaching.running"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>


                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-user-tick"
                    label="Attendance"
                    route="team.attendance.index"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-feather"
                    label="Mock Test"
                    route="team.mock-test.index"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-calendar-tick"
                    label="Exam Date Booking"
                    route="team.exam-booking.index"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-classmates"
                    label="Coaching Material"
                    route="team.coaching-material.index"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-heading label="Application"/>

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-classmates"
                    label="Application"
                    route="team.application.pending"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>


                <x-team.navigation.sidebar-heading label="Visa Filing" />

                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-bank"
                    label="Student Visa"
                    route="team.student-visa.index"
                    :hasSubmenu="false">
                </x-team.navigation.sidebar-menu-item>

                <x-team.navigation.sidebar-heading label="Automation" />

                {{-- @haspermission('automation:*') --}}
                    <x-team.navigation.sidebar-menu-item
                        icon="ki-filled ki-rocket"
                        label="Automation"
                        route="team.automation.index"
                        :hasSubmenu="false">
                    </x-team.navigation.sidebar-menu-item>
                {{-- @endhaspermission --}}

                {{-- Settings Section --}}
                @haspermission('master-module:*')
                    <x-team.navigation.sidebar-heading label="Settings" />

                    <x-team.navigation.sidebar-menu-item
                        icon="ki-filled ki-setting-2"
                        label="Company Settings"
                        route="team.settings.index"
                        :hasSubmenu="false">
                    </x-team.navigation.sidebar-menu-item>

                @endhaspermission

            {{--
                <x-team.navigation.sidebar-menu-item
                    icon="ki-filled ki-profile-circle"
                    label="Public Profile"
                    :hasSubmenu="true"
                    :isExpanded="true">

                <x-team.navigation.sidebar-menu-item
                    label="Profiles"
                    :hasSubmenu="true"
                    :isSubmenuItem="true">

                    <x-team.navigation.sidebar-menu-item
                        label="Creator"
                        route="team.profile.creator"
                        :isSubmenuItem="true" />

                    <x-team.navigation.sidebar-menu-item
                        label="Company"
                        route="team.profile.company"
                        :isSubmenuItem="true" />

                    <x-team.navigation.sidebar-menu-item
                        label="NFT"
                        route="team.profile.nft"
                        :isSubmenuItem="true" />

                    <x-team.navigation.sidebar-menu-item
                        label="Blogger"
                        route="team.profile.blogger"
                        :isSubmenuItem="true" />

                    <x-team.navigation.sidebar-menu-item
                        label="CRM"
                        route="team.profile.crm"
                        :isSubmenuItem="true" />

                        <x-team.navigation.sidebar-show-more showText="Show 4 more">
                            <x-team.navigation.sidebar-menu-item
                                label="Gamer"
                                route="team.profile.gamer"
                                :isSubmenuItem="true" />

                            <x-team.navigation.sidebar-menu-item
                                label="Feeds"
                                route="team.profile.feeds"
                                :isSubmenuItem="true" />

                            <x-team.navigation.sidebar-menu-item
                                label="Plain"
                                route="team.profile.plain"
                                :isSubmenuItem="true" />

                            <x-team.navigation.sidebar-menu-item
                                label="Modal"
                                route="team.profile.modal"
                                :isSubmenuItem="true" />
                        </x-team.navigation.sidebar-show-more>
                    </x-team.navigation.sidebar-menu-item>
                </x-team.navigation.sidebar-menu-item> --}}

                {{ $slot }}
            </div>
        </div>
    </div>
</div>
