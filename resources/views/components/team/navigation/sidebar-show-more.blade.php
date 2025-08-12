{{-- Sidebar Show More/Less Component --}}
@props([
    'showText' => 'Show more',
    'hideText' => 'Show less',
    'isSubmenuItem' => true
])

<div class="kt-menu-item flex-col-reverse" data-kt-menu-item-toggle="accordion" data-kt-menu-item-trigger="click">
    <div class="kt-menu-link border border-transparent grow cursor-pointer gap-[{{ $isSubmenuItem ? '5px' : '14px' }}] ps-[10px] pe-[10px] py-[8px]" tabindex="0">
        @if($isSubmenuItem)
            <span class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
            </span>
        @endif
        
        <span class="kt-menu-title text-2sm font-normal text-secondary-foreground">
            <span class="hidden kt-menu-item-show:!flex">{{ $hideText }}</span>
            <span class="flex kt-menu-item-show:hidden">{{ $showText }}</span>
        </span>
        
        <span class="kt-menu-arrow text-muted-foreground w-[20px] shrink-0 justify-end ms-1 me-[-10px]">
            <span class="inline-flex kt-menu-item-show:hidden">
                <i class="ki-filled ki-plus text-[11px]"></i>
            </span>
            <span class="hidden kt-menu-item-show:inline-flex">
                <i class="ki-filled ki-minus text-[11px]"></i>
            </span>
        </span>
    </div>
    
    <div class="kt-menu-accordion gap-1">
        {{ $slot }}
    </div>
</div>
