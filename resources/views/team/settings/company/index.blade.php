@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Settings', 'url' => route('team.settings.index')],
        ['title' => 'Company Settings']
    ];
@endphp

<x-team.layout.app title="Company Settings" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Page Header -->
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Company Settings
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage your company information and system settings
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.company.edit') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-notepad-edit"></i>
                        Edit Settings
                    </a>
                </div>
            </div>
            <!-- begin: grid -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 lg:gap-7.5">
                <div class="col-span-1">
                    <div class="grid gap-5 lg:gap-7.5">
                        <!-- Company Information Card -->
                        <div class="kt-card min-w-full">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">
                                    Company Information
                                </h3>
                            </div>
                            <div class="kt-card-table kt-scrollable-x-auto pb-3">
                                <table class="kt-table align-middle text-sm text-muted-foreground">
                                    <tbody>
                                        <tr>
                                            <td class="py-2 min-w-28 text-secondary-foreground font-normal">
                                                Logo
                                            </td>
                                            <td class="py-2 text-secondary-foreground font-normal min-w-60 text-sm">
                                                Company Logo (150x150px)
                                            </td>
                                            <td class="py-2 text-center">
                                                <div class="flex justify-center items-center">
                                                    @if($company && $company->company_logo)
                                                        <div class="rounded-lg overflow-hidden border border-input">
                                                            <img src="{{ Storage::url($company->company_logo) }}"
                                                                alt="{{ $company->company_name }}"
                                                                class="w-full h-full object-cover">
                                                        </div>
                                                    @else
                                                        <div
                                                            class="size-16 rounded-lg border-2 border-dashed border-input flex items-center justify-center bg-background">
                                                            <i
                                                                class="ki-filled ki-picture text-xl text-muted-foreground"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 text-secondary-foreground font-normal">
                                                Name
                                            </td>
                                            <td class="py-2 text-foreground font-normal text-sm">
                                                {{ $company->company_name ?? 'Not set' }}
                                            </td>
                                            <td class="py-2 text-center">
                                                <a class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost kt-btn-primary"
                                                    href="{{ route('team.settings.company.edit') }}">
                                                    <i class="ki-filled ki-notepad-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Email
                                            </td>
                                            <td class="py-3 text-foreground font-normal">
                                                {{ $company->email ?? 'Not set' }}
                                            </td>
                                            <td class="py-3 text-center">
                                                <a class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost kt-btn-primary"
                                                    href="{{ route('team.settings.company.edit') }}">
                                                    <i class="ki-filled ki-notepad-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Phone
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                {{ $company->phone ?? 'Not set' }}
                                            </td>
                                            <td class="py-3 text-center">
                                                <a class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost kt-btn-primary"
                                                    href="{{ route('team.settings.company.edit') }}">
                                                    <i class="ki-filled ki-notepad-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Website
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                @if($company->website_url)
                                                    <a href="{{ $company->website_url }}" target="_blank"
                                                        class="text-primary hover:underline">
                                                        {{ $company->website_url }}
                                                    </a>
                                                @else
                                                    Not set
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                <a class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost kt-btn-primary"
                                                    href="{{ route('team.settings.company.edit') }}">
                                                    <i class="ki-filled ki-notepad-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3">
                                                Address
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                @if($company && $company->company_address)
                                                    {{ $company->company_address }}
                                                    @if($company->city || $company->state || $company->country)
                                                        <br>
                                                        {{ collect([$company->city, $company->state, $company->country])->filter()->implode(', ') }}
                                                        @if($company->postal_code)
                                                            {{ $company->postal_code }}
                                                        @endif
                                                    @endif
                                                @else
                                                    No address set
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                @if($company && $company->company_address)
                                                    <a class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost kt-btn-primary"
                                                        href="{{ route('team.settings.company.edit') }}">
                                                        <i class="ki-filled ki-notepad-edit"></i>
                                                    </a>
                                                @else
                                                    <a class="kt-link kt-link-underlined kt-link-dashed"
                                                        href="{{ route('team.settings.company.edit') }}">
                                                        Add
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SMTP Configuration Card -->
                        <div class="kt-card min-w-full">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">
                                    SMTP Configuration
                                </h3>
                            </div>
                            <style>
                                .user-access-bg {
                                    background-image: url('/default/images/2600x1600/bg-3.png');
                                }
                                .dark .user-access-bg {
                                    background-image: url('/default/images/2600x1600/bg-3-dark.png');
                                }
                            </style>
                            <div class="flex items-center flex-wrap sm:flex-nowrap justify-between grow border border-border rounded-xl gap-2 p-5 rtl:[background-position:-195px_-85px] [background-position:195px_-85px] bg-no-repeat bg-[length:450px] user-access-bg">
                                <div class="flex items-center gap-4">
                                    <div class="relative size-[50px] shrink-0">
                                        <div class="flex items-center justify-center w-[2.875rem] h-[2.875rem] bg-accent/60 rounded-lg border border-input">
                                            <i class="ki-filled ki-security-user text-xl text-primary">
                                            </i>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center flex-wrap gap-2.5">
                                            <a class="text-base font-medium text-mono hover:text-primary" href="javascript:void(0);" data-kt-modal-toggle="#smtpModal">
                                                Test SMTP Configuration
                                            </a>
                                            <span class="kt-badge kt-badge-sm kt-badge-outline shrink-0">
                                                Now
                                            </span>
                                        </div>
                                        <div class="kt-form-description text-2sm">
                                            Ensure your SMTP settings are correct to send emails
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button class="kt-btn kt-btn-mono" data-kt-modal-toggle="#smtpModal">
                                        Test SMTP
                                    </button>
                                </div>
                            </div>
                            <div class="kt-card-table kt-scrollable-x-auto pb-3">
                                <table class="kt-table align-middle text-sm text-muted-foreground">
                                    <tbody>
                                        <tr>
                                            <td class="py-2 min-w-28 text-secondary-foreground font-normal">
                                                Mail Driver
                                            </td>
                                            <td class="py-2 text-foreground font-normal text-sm">
                                                {{ env('MAIL_MAILER', 'Not set') }}
                                            </td>
                                            <td class="py-2 text-center">
                                                @if(env('MAIL_MAILER') === 'log')
                                                    <span class="kt-badge kt-badge-warning kt-badge-sm">Dev Mode</span>
                                                @elseif(env('MAIL_MAILER') === 'smtp')
                                                    <span class="kt-badge kt-badge-success kt-badge-sm">SMTP</span>
                                                @else
                                                    <span
                                                        class="kt-badge kt-badge-secondary kt-badge-sm">{{ strtoupper(env('MAIL_MAILER', 'Unknown')) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Host
                                            </td>
                                            <td class="py-3 text-foreground font-normal">
                                                {{ env('MAIL_HOST', 'Not set') }}
                                            </td>
                                            <td class="py-3 text-center">
                                                @if(env('MAIL_HOST'))
                                                    <i class="ki-filled ki-check text-success"></i>
                                                @else
                                                    <i class="ki-filled ki-close text-destructive"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Port
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                {{ env('MAIL_PORT', 'Not set') }}
                                            </td>
                                            <td class="py-3 text-center">
                                                @if(env('MAIL_PORT'))
                                                    <i class="ki-filled ki-check text-success"></i>
                                                @else
                                                    <i class="ki-filled ki-close text-destructive"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Username
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                @if(env('MAIL_USERNAME'))
                                                    {{ env('MAIL_USERNAME') }}
                                                @else
                                                    Not set
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                @if(env('MAIL_USERNAME'))
                                                    <i class="ki-filled ki-check text-success"></i>
                                                @else
                                                    <i class="ki-filled ki-close text-destructive"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                Password
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                @if(env('MAIL_PASSWORD'))
                                                    {{ env('MAIL_PASSWORD') }}
                                                @else
                                                    Not set
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                @if(env('MAIL_PASSWORD'))
                                                    <i class="ki-filled ki-check text-success"></i>
                                                @else
                                                    <i class="ki-filled ki-close text-destructive"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                From Address
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                {{ env('MAIL_FROM_ADDRESS', 'Not set') }}
                                            </td>
                                            <td class="py-3 text-center">
                                                @if(env('MAIL_FROM_ADDRESS') && env('MAIL_FROM_ADDRESS') !== 'hello@example.com')
                                                    <i class="ki-filled ki-check text-success"></i>
                                                @else
                                                    <i class="ki-filled ki-close text-destructive"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-secondary-foreground font-normal">
                                                From Name
                                            </td>
                                            <td class="py-3 text-secondary-foreground text-sm font-normal">
                                                {{ env('MAIL_FROM_NAME', env('APP_NAME')) }}
                                            </td>
                                            <td class="py-3 text-center">
                                                @if(env('MAIL_FROM_NAME'))
                                                    <i class="ki-filled ki-check text-success"></i>
                                                @else
                                                    <i class="ki-filled ki-close text-destructive"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="grid gap-5 lg:gap-7.5">
                        <!-- Setup Progress Card -->
                        <div
                            class="kt-card flex-col gap-5 justify-between bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20 pt-5 lg:pt-10 px-5">
                            <div class="text-center">
                                <div class="mb-4">
                                    <div
                                        class="size-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="ki-filled ki-setting-3 text-2xl text-primary"></i>
                                    </div>
                                </div>
                                <h3 class="text-mono text-lg font-semibold leading-6 mb-1.5">
                                    Company Setup
                                    @if($company && $company->is_setup_completed)
                                        <span class="kt-badge kt-badge-success kt-badge-sm ml-2">Completed</span>
                                    @else
                                        <span class="kt-badge kt-badge-warning kt-badge-sm ml-2">Incomplete</span>
                                    @endif
                                </h3>
                                <span class="text-secondary-foreground text-sm block mb-5">
                                    @if($company && $company->is_setup_completed)
                                        Your company profile is fully configured and ready.
                                    @else
                                        Complete your company profile to get started.
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">
                                    Quick Actions
                                </h3>
                            </div>
                            <div class="kt-card-content">
                                <div class="grid gap-2.5">
                                    <div
                                        class="flex items-center justify-between flex-wrap border border-border rounded-xl gap-2 px-3.5 py-2.5 hover:bg-accent/50 transition-colors">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div
                                                class="size-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                                <i class="ki-filled ki-geolocation text-primary"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <a class="text-sm font-medium text-mono hover:text-primary mb-px"
                                                    href="{{ route('team.settings.branches.index') }}">
                                                    Manage Branches
                                                </a>
                                                <span class="text-xs text-secondary-foreground">
                                                    Add and configure branch locations
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('team.settings.branches.index') }}"
                                            class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                            <i class="ki-filled ki-right"></i>
                                        </a>
                                    </div>

                                    <div
                                        class="flex items-center justify-between flex-wrap border border-border rounded-xl gap-2 px-3.5 py-2.5 hover:bg-accent/50 transition-colors">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div
                                                class="size-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                                <i class="ki-filled ki-profile-circle text-green-600"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <a class="text-sm font-medium text-mono hover:text-primary mb-px"
                                                    href="{{ route('team.settings.users.index') }}">
                                                    User Management
                                                </a>
                                                <span class="text-xs text-secondary-foreground">
                                                    Manage user accounts and permissions
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('team.settings.users.index') }}"
                                            class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                            <i class="ki-filled ki-right"></i>
                                        </a>
                                    </div>

                                    <div
                                        class="flex items-center justify-between flex-wrap border border-border rounded-xl gap-2 px-3.5 py-2.5 hover:bg-accent/50 transition-colors">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div
                                                class="size-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                <i class="ki-filled ki-setting-2 text-blue-600"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <a class="text-sm font-medium text-mono hover:text-primary mb-px"
                                                    href="{{ route('team.settings.index') }}">
                                                    System Settings
                                                </a>
                                                <span class="text-xs text-secondary-foreground">
                                                    Configure system preferences
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('team.settings.index') }}"
                                            class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                            <i class="ki-filled ki-right"></i>
                                        </a>
                                    </div>

                                    <div
                                        class="flex items-center justify-between flex-wrap border border-border rounded-xl gap-2 px-3.5 py-2.5 hover:bg-accent/50 transition-colors">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div
                                                class="size-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                                <i class="ki-filled ki-message-text-2 text-orange-600"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <button type="button"
                                                    class="text-sm font-medium text-mono hover:text-primary mb-px text-left"
                                                    data-kt-modal-toggle="#smtpModal">
                                                    Test SMTP Configuration
                                                </button>
                                                <span class="text-xs text-secondary-foreground">
                                                    Verify email sending functionality
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost"
                                            data-kt-modal-toggle="#smtpModal">
                                            <i class="ki-filled ki-message-text-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end: grid -->
        </div>

        <!-- SMTP Test Modal using x-team.modal component -->
        <x-team.modal id="smtpModal" title="Test SMTP Configuration" size="max-w-2xl">
            <form id="smtpTestForm" action="{{ route('team.settings.company.test-smtp') }}" method="POST">
                @csrf
                @method('POST')
                <div class="flex flex-col gap-5">
                    <!-- Modal Header Icon -->
                    <div class="flex items-center gap-2 text-primary">
                        <i class="ki-filled ki-message-text-2 text-lg"></i>
                        <span class="text-sm font-medium">Verify your email configuration</span>
                    </div>
                    <!-- Status/Logs Display Area -->
                    <div id="troubleshootingArea" class="hidden">
                        <div id="statusResults" class="hidden mb-4">
                            <h4 class="text-sm font-semibold mb-2">SMTP Configuration Status:</h4>
                            <div id="statusContent" class="text-xs bg-gray-50 p-3 rounded border max-h-40 overflow-y-auto"></div>
                        </div>
                        <div id="logResults" class="hidden mb-4">
                            <h4 class="text-sm font-semibold mb-2">Recent Email Logs:</h4>
                            <div id="logContent" class="text-xs bg-gray-50 p-3 rounded border max-h-40 overflow-y-auto font-mono"></div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div
                        class="flex items-start gap-2.5 px-3.5 py-2.5 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
                        <i class="ki-filled ki-information-2 text-blue-600 dark:text-blue-400 mt-0.5"></i>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Current SMTP Settings:</strong><br>
                            Host: {{ env('MAIL_HOST', 'Not configured') }}<br>
                            Port: {{ env('MAIL_PORT', 'Not configured') }}<br>
                            From: {{ env('MAIL_FROM_ADDRESS', 'Not configured') }}
                        </div>
                    </div>

                    <!-- Test Email Input -->
                    <div class="flex flex-col gap-1">
                        <label class="kt-label">
                            Test Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="test_email" id="test_email" class="kt-input"
                            placeholder="Enter email address to send test email"
                            value="{{ auth()->user()->email ?? '' }}" required>
                        <span class="text-xs text-secondary-foreground">
                            A test email will be sent to this address to verify SMTP configuration. Check logs after sending for detailed information including Message ID.
                        </span>
                    </div>

                    <!-- Warning for Production -->
                    @if(env('APP_ENV') === 'production')
                        <div
                            class="flex items-start gap-2.5 px-3.5 py-2.5 border border-orange-200 rounded-lg bg-orange-50 dark:bg-orange-900/20 dark:border-orange-800">
                            <i class="ki-filled ki-warning text-orange-600 dark:text-orange-400 mt-0.5"></i>
                            <span class="text-sm text-orange-800 dark:text-orange-200">
                                <strong>Production Environment:</strong> This will send a real email using your live SMTP
                                settings.
                            </span>
                        </div>
                    @endif

                    <!-- Troubleshooting Tips -->
                    <div class="flex items-start gap-2.5 px-3.5 py-2.5 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-900/20 dark:border-gray-800">
                        <i class="ki-filled ki-lightbulb text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                        <div class="text-xs text-gray-700 dark:text-gray-300">
                            <strong>If emails aren't being received:</strong><br>
                            • Check spam/junk folders<br>
                            • Verify SMTP credentials and settings<br>
                            • Check server firewall/port access<br>
                            • View email logs for error details<br>
                            • Contact your email provider if issues persist
                        </div>
                    </div>
                </div>
                <x-slot name="footer">
                    <div class="flex justify-end gap-4 pb-5">
                        <button type="submit" form="smtpTestForm" class="kt-btn kt-btn-primary" id="smtpTestBtn">
                            <i class="ki-filled ki-message-text-2"></i>
                            Send Test Email
                        </button>
                        <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">
                            <i class="ki-filled ki-cross"></i>
                            Cancel
                        </button>
                    </div>
                </x-slot>
            </form>
        </x-team.modal>
    </x-slot>
</x-team.layout.app>