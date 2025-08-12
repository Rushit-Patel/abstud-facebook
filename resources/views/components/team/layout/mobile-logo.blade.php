{{-- Mobile Logo Component --}}
@props(['appData' => []])

@php
    $logoUrl = $appData['companyLogo'] ?? asset('images/default-logo.png');
@endphp

<!-- Mobile Logo -->
<div class="flex gap-2.5 lg:hidden items-center -ms-1">
     <a class="shrink-0" href="{{ route('team.dashboard') }}">
          <img class="max-h-[25px] w-full" src="{{ $logoUrl }}" alt="{{ $appData['companyName'] ?? 'AbstudERP' }}" />
     </a>
     <div class="flex items-center">
          <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#sidebar">
               <i class="ki-filled ki-menu"></i>
          </button>
     </div>
</div>
