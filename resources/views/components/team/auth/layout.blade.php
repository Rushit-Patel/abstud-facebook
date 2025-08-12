{{-- Team Auth Layout Component --}}
@props(['title' => 'Team Portal', 'showBranding' => true])

<!DOCTYPE html>
<html lang="en" class="h-full light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - AbstudERP</title>
    
    <link rel="stylesheet" href="{{ asset('assets/css/team/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/styles.bundle.css') }}">
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background">
    <!-- Page -->
    <style>
        .branded-bg {
            background-image: url('/default/images/illustrations/21.png');
        }
    </style>
    <div class="grid lg:grid-cols-2 grow">
        {{ $slot }}
        @if($showBranding)
            <x-team.auth.branding />
        @endif
    </div>
    {{ $footer ?? '' }}
    <script src="{{ asset('assets/js/team/core.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/abstud.min.js') }}"></script>
    {{-- Optional scripts --}}
</body>
</html>
