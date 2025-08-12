<!DOCTYPE html>
<html class="h-full md:h-full h-[min-content] light" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en" data-kt-theme-swtich-initialized="true" data-kt-theme-switch-mode="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Client Guest - ' . config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/styles.bundle.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/team/styles.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
        @stack('styles')
    </head>
    <style>
    .page-bg {
        background-image: url('/default/images/2600x1200/bg-10.png');
    }
    .dark .page-bg {
        background-image: url('/default/images/2600x1200/bg-10-dark.png');
    }
    .iti{
        width: 100%;
    }
    </style>
    <body class="antialiased  flex text-base text-foreground bg-background">
        <div class="flex items-center min-h-screen justify-center grow bg-center bg-no-repeat page-bg">
            <div class="kt-card @yield('card-width', 'max-w-[370px]')  w-full">
                <div class="flex items-center justify-center mb-2.5 mt-2.5 border-b-2 border-dashed border-gray-200 ">
                    <x-shared.logo :companyLogo="$appData['companyLogo']" :companyName="$appData['companySetting']->companyName" />
                </div>
                @yield('content')
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/team/vendors/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/team/vendors/flatpickr.js') }}"></script>
        <script src="{{ asset('assets/js/app.js') }}"></script>
        @stack('scripts')
    </body>
</html>
