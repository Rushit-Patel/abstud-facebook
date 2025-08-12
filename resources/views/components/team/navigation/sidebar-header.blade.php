{{-- Sidebar Header Component --}}
@props([
    'appData' => [],
    'showToggle' => true
])

<div class="kt-sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0" id="sidebar_header">
    {{-- Logo --}}
    <a class="dark:hidden" href="{{ route('team.dashboard') }}">
        <img class="default-logo h-16" src="{{ $appData['companyLogo'] }}" alt="{{ $appData['companyName'] ?? 'Company Logo' }}" />
        <img class="small-logo w-24 " src="{{ $appData['companyFavicon'] }}" alt="{{ $appData['companyName'] ?? 'Company Logo' }}" />
    </a>
    <a class="hidden dark:block" href="{{ route('team.dashboard') }}">
        <img class="default-logo h-16" src="{{ $appData['companyLogo'] }}" alt="{{ $appData['companyName'] ?? 'Company Logo' }}" />
        <img class="small-logo w-24 " src="{{ $appData['companyFavicon'] }}" alt="{{ $appData['companyName'] ?? 'Company Logo' }}" />
    </a>
    
    @if($showToggle)
        {{-- Sidebar Toggle Button --}}
        <button class="kt-btn kt-btn-outline kt-btn-icon size-[30px] absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
                data-kt-toggle="body" data-kt-toggle-class="kt-sidebar-collapse" id="sidebar_toggle">
            <i class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 transition-all duration-300 rtl:translate rtl:rotate-180 rtl:kt-toggle-active:rotate-0"></i>
        </button>
    @endif
</div>
