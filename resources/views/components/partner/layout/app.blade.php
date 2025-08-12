{{-- Partner Layout Component --}}
@props(['title' => 'Partner Portal', 'showHeader' => true])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - AbstudERP Partner</title>
    
    {{-- Partner-specific styles --}}
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/partner/layout.css') }}">
</head>
<body class="partner-layout bg-blue-50">
    <div class="min-h-screen">
        @if($showHeader)
            {{-- Partner Header --}}
            <x-partner.layout.header :title="$title" />
        @endif
        
        {{-- Partner Navigation --}}
        <x-partner.navigation.main-nav />
        
        {{-- Main Content --}}
        <main class="container mx-auto px-4 py-8">
            {{ $slot }}
        </main>
        
        {{-- Partner Footer --}}
        <x-partner.layout.footer />
    </div>
    
    {{-- Partner-specific scripts --}}
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/partner/layout.js') }}"></script>
</body>
</html>
