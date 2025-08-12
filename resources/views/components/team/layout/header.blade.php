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

<header class="kt-header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-background" data-kt-sticky="true" data-kt-sticky-class="border-b border-border" data-kt-sticky-name="header" id="header">
    <!-- Container -->
    <div class="kt-container-fixed flex justify-between items-stretch lg:gap-4" id="headerContainer">      
        @if($showLogo)
            <!-- Mobile Logo -->
            <x-team.layout.mobile-logo :appData="$appData" />
            <!-- End of Mobile Logo -->
        @endif

        @if($breadcrumbs)
            <!-- Breadcrumbs -->
            <x-team.layout.breadcrumbs :breadcrumbs="$breadcrumbs" />
            <!-- End of Breadcrumbs -->
        @endif

        <!-- Topbar -->
        <div class="flex items-center gap-2.5">
            <!-- Search -->
            <x-team.layout.search-button />
            <!-- End of Search -->
            
            @if($showNotifications)
                <!-- Notifications -->
                <x-team.layout.notifications :appData="$appData" />
                <!-- End of Notifications -->
            @endif

            @if($showChat)
                <!-- Chat -->
                {{-- <x-team.layout.chat /> --}}
                <!-- End of Chat -->
            @endif

            @if($showApps)
                <!-- Apps -->
                <x-team.layout.apps-menu />
                <!-- End of Apps -->
            @endif

            @if($showUserMenu)
                <!-- User Menu -->
                <x-team.layout.user-menu :appData="$appData" />
                <!-- End of User Menu -->
            @endif
        </div>
          <!-- End of Topbar -->
    </div>
    <!-- End of Container -->
</header>
