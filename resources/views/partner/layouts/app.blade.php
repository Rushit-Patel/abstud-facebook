<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: window.innerWidth >= 1024 }" x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Partner Portal - ' . config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
        <script src="{{ asset('assets/js/app.js') }}"></script>
    </head>
    <body class="font-sans antialiased bg-[#F5F7FA] dark:bg-gray-900 overflow-hidden">
        <div class="flex h-screen">
            <!-- Include Partner Sidebar Component -->
            @include('partner.components.sidebar')

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen && window.innerWidth < 1024" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
                 @click="sidebarOpen = false">
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Include Partner Header Component -->
                @include('partner.components.header')

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto bg-[#F5F7FA] dark:bg-gray-900 p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
