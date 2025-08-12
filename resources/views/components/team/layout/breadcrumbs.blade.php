{{-- Team Breadcrumbs Component --}}
@props([
    'breadcrumbs' => []
])

@if($breadcrumbs && count($breadcrumbs) > 0)
<div class="flex [.kt-header_&]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0 [--kt-reparent-target:#contentContainer] lg:[--kt-reparent-target:#headerContainer] [--kt-reparent-mode:prepend] lg:[--kt-reparent-mode:prepend]"
     data-kt-reparent="true">
     @foreach($breadcrumbs as $index => $breadcrumb)
          @if($index > 0)
               <i class="ki-filled ki-right text-muted-foreground text-[10px]"></i>
          @endif
          
          @if(isset($breadcrumb['url']) && $breadcrumb['url'])
               <a href="{{ $breadcrumb['url'] }}" class="text-secondary-foreground hover:text-primary transition-colors">
                    {{ $breadcrumb['title'] }}
               </a>
          @else
               <span class="{{ $index === count($breadcrumbs) - 1 ? 'text-mono font-medium' : 'text-secondary-foreground' }}">
                    {{ $breadcrumb['title'] }}
               </span>
          @endif
     @endforeach
</div>
@endif
