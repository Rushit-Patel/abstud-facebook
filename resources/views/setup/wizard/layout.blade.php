<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Setup Wizard - ' . config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/apexcharts.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/styles.bundle.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/team/styles.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="min-h-screen">
            <!-- Header -->
            <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">AbstudERP</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Setup Wizard</p>
                            </div>
                        </div>
                          <!-- Progress Indicator -->
                        <div class="hidden sm:flex items-center space-x-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 3; $i++)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                            {{ $step >= $i 
                                                ? 'bg-blue-600 text-white' 
                                                : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                            @if($step > $i)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                {{ $i }}                                            @endif
                                        </div>
                                        @if($i < 3)
                                            <div class="w-12 h-0.5 {{ $step > $i ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="py-12">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} AbstudERP. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>

        <script src="{{ asset('assets/js/team/core.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/team/vendors/abstud.min.js') }}"></script>
        <script src="{{ asset('assets/js/team/vendors/general.js') }}"></script>
        <script src="{{ asset('assets/js/team/vendors/demo.js') }}"></script>
        
        @stack('scripts')
    </body>
</html>
