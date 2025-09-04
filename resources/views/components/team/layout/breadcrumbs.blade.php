{{-- Team Breadcrumbs Component --}}
@props([
    'breadcrumbs' => []
])

@if($breadcrumbs && count($breadcrumbs) > 0)
<div class="">
    <!-- Container -->
    <div class="kt-container-fixed">
        <div class="border-t border-border dark:border-coal-100">
        </div>
        <div class="flex items-center justify-between flex-wrap gap-2 la:gap-5 my-5">
            <div class="flex flex-col gap-1">
                <h1 class="font-medium text-lg text-mono">
                    Dashboard
                </h1>
                <div class="flex items-center gap-1 text-sm">
                    @foreach($breadcrumbs as $index => $breadcrumb)
                         @if($index > 0)
                              <span class="text-muted-foreground text-sm">
                              /
                              </span>
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
            </div>
        </div>
        <div class="border-b border-border mb-5 lg:mb-7.5">
        </div>
    </div>
    <!-- End of Container -->
</div>
@endif
