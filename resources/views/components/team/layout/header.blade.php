{{-- Team Header Component --}}
@props([
    'title' => '',
    'appData' => [],
    'showLogo' => true,
    'showUserMenu' => true,
    'showNotifications' => true,
    'showChat' => false,
    'showApps' => true,
    'showDarkMode' => true,
    'showMobileMenu' => true,
    'fixed' => true,
    'breadcrumbs' => []
])

@php
    $headerClasses = $fixed
        ? 'fixed top-0 left-0 right-0 z-50'
        : 'relative';
@endphp
<header class="flex items-center transition-[height] shrink-0 bg-background py-4 lg:py-0 lg:h-(--header-height)"
    data-kt-sticky="true"
    data-kt-sticky-class="transition-[height] fixed z-10 top-0 left-0 right-0 shadow-xs backdrop-blur-md bg-background/70"
    data-kt-sticky-name="header" data-kt-sticky-offset="200px" id="header">
    <!-- Container -->
    <div class="kt-container-fixed flex flex-wrap gap-2 items-center lg:gap-4" id="header_container">
        <!-- Logo -->
        <div class="flex items-stretch gap-10 grow">
            <div class="flex items-center gap-2.5">
                @if($showLogo)
                    <!-- Mobile Logo -->
                    <x-team.layout.mobile-logo :appData="$appData" />
                    <!-- End of Mobile Logo -->
                @endif
                <button class="lg:hidden kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#mega_menu_container">
                    <i class="ki-filled ki-burger-menu-2">
                    </i>
                </button>
            </div>
            <!-- Mega Menu -->
            <div class="flex items-stretch" id="megaMenuWrapper">
                <div class="flex items-stretch [--kt-reparent-mode:prepend] lg:[--kt-reparent-mode:prepend] [--kt-reparent-target:body] lg:[--kt-reparent-target:#megaMenuWrapper]"
                    data-kt-reparent="true">
                    <div class="hidden lg:flex lg:items-stretch [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
                        data-kt-drawer="true"
                        data-kt-drawer-class="kt-drawer kt-drawer-start fixed z-10 top-0 bottom-0 w-full mr-5 max-w-[250px] p-5 lg:p-0 overflow-auto"
                        id="mega_menu_container">
                        <div class="kt-menu flex-col lg:flex-row gap-5 lg:gap-7.5" data-kt-menu="true" id="mega_menu">
                            <div class="kt-menu-item @if(request()->routeIs('team.dashboard')) active @endif">
                                <a class="kt-menu-link border-b border-b-transparent kt-menu-item-active:border-b-gray-400 kt-menu-item-here:border-b-gray-400"
                                    href="{{ route('team.dashboard') }}">
                                    <span
                                        class="kt-menu-title kt-menu-link-hover:text-mono text-sm text-foreground kt-menu-item-show:text-mono kt-menu-item-here:text-mono kt-menu-item-active:font-medium kt-menu-item-here:font-medium">
                                        Dashboard
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Mega Men -->
        </div>
        <!-- End of Logo -->
        <!-- Topbar -->
        <div class="flex items-center flex-wrap gap-3">
            @if($showApps)
                <!-- Apps -->
                <x-team.layout.apps-menu />
                <!-- End of Apps -->
            @endif
            <div class="border-e border-border h-5 mx-1.5 lg:mx-3">
            </div>
            <!-- User -->
            @if($showUserMenu)
                <x-team.layout.user-menu :appData="$appData" />
            @endif
            <!-- End of User -->
        </div>
        <!-- End of Topbar -->
    </div>
    <!-- End of Container -->
</header>
@if($breadcrumbs)
    <x-team.layout.breadcrumbs :breadcrumbs="$breadcrumbs" />
@endif
