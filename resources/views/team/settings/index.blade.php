@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Company Settings']
    ];
@endphp
<x-team.layout.app title="Company Settings" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Company Settings
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Central Hub for System Customization
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-container-fixed">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5">
                {{-- Company Info Card --}}
                <x-team.cards.setting-card icon="ki-office-bag" title="Company Info"
                    description="Manage your company's basic information, contact details, and business profile settings."
                    link="{{ route('team.settings.company.index') ?? '#' }}" />

                {{-- Manage Branch Card --}}
                <x-team.cards.setting-card icon="ki-geolocation" title="Manage Branch"
                    description="Add, edit, and organize branch locations, assign managers, and configure branch-specific settings."
                    link="{{ route('team.settings.branches.index') ?? '#' }}" />

                {{-- Manage User Account Card --}}
                <x-team.cards.setting-card icon="ki-profile-circle" title="Manage User Account"
                    description="Control user permissions, roles, account settings, and access management for team members."
                    link="{{ route('team.settings.users.index') ?? '#' }}" />
            </div>

            <!-- Master Settings Section -->
            <div class="mt-10">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-mono">Master Settings</h2>
                    <p class="text-sm text-secondary-foreground">Configure system-wide master data and templates</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-5">
                    <div class="lg:col-span-3 xl:col-span-3">
                        <div class="kt-scrollable overflow-y-auto flex flex-col h-[600px] gap-5" id="settings-scrollable">
                            <!-- Geographic Settings -->
                            <div class="col-span-1" id="geographic-settings">
                                <x-team.card title="Geographic Settings"
                                    headerClass="justify-start bg-muted/70 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Country" icon="ki-outline ki-flag"
                                            link="{{ route('team.settings.countries.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="State" icon="ki-outline ki-map"
                                            link="{{ route('team.settings.states.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="City" icon="ki-outline ki-geolocation"
                                            link="{{ route('team.settings.cities.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Lead Management -->
                            <div class="col-span-1" id="lead-management">
                                <x-team.card title="Lead Management"
                                    headerClass="justify-start bg-green-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Lead Type" icon="ki-outline ki-user"
                                            link="{{ route('team.settings.lead-types.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Lead Status" icon="ki-outline ki-chart-line-up"
                                            link="{{ route('team.settings.lead-status.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Lead Sub Status" icon="ki-outline ki-chart-line"
                                            link="{{ route('team.settings.lead-sub-status.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Source" icon="ki-outline ki-share"
                                            link="{{ route('team.settings.source.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Purpose" icon="ki-outline ki-target"
                                            link="{{ route('team.settings.purpose.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Lead Tag" icon="ki-outline ki-tag"
                                            link="{{ route('team.settings.tags.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Destination Country" icon="ki-outline ki-airplane"
                                            link="{{ route('team.settings.foreign-country.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Destination State" icon="ki-outline ki-airplane"
                                            link="{{ route('team.settings.foreign-state.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Destination City" icon="ki-outline ki-airplane"
                                            link="{{ route('team.settings.foreign-city.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Marital Status" icon="ki-outline ki-heart"
                                            link="{{ route('team.settings.marital-status.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Relative Type" icon="ki-outline ki-people"
                                            link="{{ route('team.settings.type-of-relative.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Education & Testing -->
                            <div class="col-span-1" id="education-testing">
                                <x-team.card title="Education & Testing"
                                    headerClass="justify-start bg-purple-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Education Level" icon="ki-outline ki-book"
                                            link="{{ route('team.settings.education-level.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Education Stream" icon="ki-outline ki-book-open"
                                            link="{{ route('team.settings.education-stream.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Education Board" icon="ki-outline ki-book-square"
                                            link="{{ route('team.settings.education-board.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Coaching & Training -->
                            <div class="col-span-1" id="coaching-training">
                                <x-team.card title="Coaching & Training"
                                    headerClass="justify-start bg-orange-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Coaching" icon="ki-outline ki-teacher"
                                            link="{{ route('team.settings.coaching.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Batch" icon="ki-outline ki-people"
                                            link="{{ route('team.settings.batch.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Coaching Length" icon="ki-outline ki-time"
                                            link="{{ route('team.settings.coaching-length.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Coaching Material" icon="ki-outline ki-notepad"
                                            link="{{ route('team.settings.coaching-material.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="English Proficiency Test" icon="ki-outline ki-medal-star"
                                            link="{{ route('team.settings.english-proficiency-test.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Exam Way" icon="ki-outline ki-award"
                                            link="{{ route('team.settings.exam-way.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Exam Mode" icon="ki-outline ki-badge"
                                            link="{{ route('team.settings.exam-mode.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Exam Center" icon="ki-outline ki-compass"
                                            link="{{ route('team.settings.exam-center.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Communication Templates -->
                            <div class="col-span-1" id="communication-templates">
                                <x-team.card title="Communication Templates"
                                    headerClass="justify-start bg-indigo-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Email Template" icon="ki-outline ki-sms"
                                            link="{{ route('team.settings.email-templates.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="WhatsApp Templates" icon="ki-outline ki-whatsapp"
                                            link="{{ route('team.settings.whatsapp-templates.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Notification Type" icon="ki-outline ki-notification"
                                            link="{{ route('team.settings.notification-type.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Notification Config" icon="ki-outline ki-setting"
                                            link="{{ route('team.settings.notification-config.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Task Management -->
                            <div class="col-span-1" id="task-management">
                                <x-team.card title="Task Management"
                                    headerClass="justify-start bg-yellow-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Task Category" icon="ki-outline ki-category"
                                            link="{{ route('team.settings.task-category.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Task Priority" icon="ki-outline ki-ranking"
                                            link="{{ route('team.settings.task-priority.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Task Status" icon="ki-outline ki-status"
                                            link="{{ route('team.settings.task-status.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Visa & Immigration -->
                            <div class="col-span-1" id="visa-immigration">
                                <x-team.card title="Visa & Immigration"
                                    headerClass="justify-start bg-red-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Other Visa Type" icon="ki-outline ki-bookmark"
                                            link="{{ route('team.settings.other-visa-type.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Purpose Visit" icon="ki-outline ki-airplane"
                                            link="{{ route('team.settings.purpose-visit.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Visitor Applicant" icon="ki-outline ki-user"
                                            link="{{ route('team.settings.visitor-applicant.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Document Management -->
                            <div class="col-span-1" id="document-management">
                                <x-team.card title="Document Management"
                                    headerClass="justify-start bg-teal-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Document Category" icon="ki-outline ki-document"
                                            link="{{ route('team.settings.document-category.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Document Checklist" icon="ki-outline ki-check-square"
                                            link="{{ route('team.settings.document-check-list.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <!-- Financial Settings -->
                            <div class="col-span-1" id="financial-settings">
                                <x-team.card title="Financial Settings"
                                    headerClass="justify-start bg-emerald-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Billing Company" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.billing-company.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Payment Mode" icon="ki-outline ki-dollar"
                                            link="{{ route('team.settings.payment-mode.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Service" icon="ki-outline ki-setting-3"
                                            link="{{ route('team.settings.service.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>

                            <div class="col-span-1" id="cource-univercity-settings">
                                <x-team.card title="Course & university Settings"
                                    headerClass="justify-start bg-emerald-50 gap-9 h-auto py-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <x-team.cards.setting-sm-card title="Course" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.course.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Course Type" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.course-type.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Campus" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.campus.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Associate" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.associate.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="University" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.university.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="University Course" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.university-course.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="University Course Key" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.university-course-key.index') ?? '#' }}" />
                                        <x-team.cards.setting-sm-card title="Application Type" icon="ki-outline ki-office-bag"
                                            link="{{ route('team.settings.application-type.index') ?? '#' }}" />
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1 xl:col-span-1">
                        <div class="sticky top-5">
                            <div class="flex gap-8 w-full rounded-md p-6 border border-border">
                                <div
                                    data-kt-scrollspy="true"
                                    data-kt-scrollspy-target="#settings-scrollable"
                                    data-kt-scrollspy-offset="100px"
                                    class="flex flex-col relative gap-2 shrink-0 w-full"
                                >
                                    <h4 class="text-sm font-semibold text-mono mb-3">Quick Navigation</h4>
                                    <a
                                        href="#geographic-settings"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-map text-sm me-2"></i>
                                        Geographic
                                    </a>
                                    <a
                                        href="#lead-management"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-user-tick text-sm me-2"></i>
                                        Lead Management
                                    </a>
                                    <a
                                        href="#education-testing"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-book text-sm me-2"></i>
                                        Education & Testing
                                    </a>
                                    <a
                                        href="#coaching-training"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-teacher text-sm me-2"></i>
                                        Coaching & Training
                                    </a>
                                    <a
                                        href="#communication-templates"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-messages text-sm me-2"></i>
                                        Communication
                                    </a>
                                    <a
                                        href="#task-management"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-notepad-edit text-sm me-2"></i>
                                        Task Management
                                    </a>
                                    <a
                                        href="#visa-immigration"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-airplane text-sm me-2"></i>
                                        Visa & Immigration
                                    </a>
                                    <a
                                        href="#document-management"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-document text-sm me-2"></i>
                                        Document Management
                                    </a>
                                    <a
                                        href="#financial-settings"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-dollar text-sm me-2"></i>
                                        Financial Settings
                                    </a>

                                    <a
                                        href="#cource-univercity-settings"
                                        data-kt-scrollspy-anchor="true"
                                        class="kt-btn kt-btn-outline kt-btn-sm justify-start"
                                    >
                                        <i class="ki-outline ki-dollar text-sm me-2"></i>
                                        Course & university Settings
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>
