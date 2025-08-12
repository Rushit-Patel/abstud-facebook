{{-- Team Auth Branding Component --}}
@props(['title' => 'Admin Login', 'subtitle' => 'Sign in to your admin account'])
<div class="lg:rounded-xl lg:border lg:border-border lg:m-5 order-1 lg:order-2 bg-top xxl:bg-center xl:bg-cover bg-no-repeat branded-bg">
    <div class="flex flex-col p-8 lg:p-16 gap-4">
        <x-team.auth.logo companyLogo="{{ $appData['companyLogo'] }}" companyName="{{ $appData['companyName'] }}" />
        <div class="flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-mono">
                {{ $title }}
            </h3>
            <div class="text-base font-medium text-secondary-foreground">
                {{ $subtitle }}
                <br />
                Dashboard interface.
            </div>
        </div>
    </div>
</div>