{{-- Mobile Logo Component --}}
@props(['appData' => []])

@php
    $logoUrl = $appData['companyLogo'] ?? asset('images/default-logo.png');
@endphp

<a class="dark:hidden" href="{{ route('team.dashboard') }}">
     <img class="default-logo h-16" src="{{ $appData['companyLogo'] }}" alt="{{ $appData['companyName'] ?? 'Company Logo' }}" />
</a>
<a class="hidden dark:block" href="{{ route('team.dashboard') }}">
     <img class="default-logo h-16" src="{{ $appData['companyLogo'] }}" alt="{{ $appData['companyName'] ?? 'Company Logo' }}" />
</a>