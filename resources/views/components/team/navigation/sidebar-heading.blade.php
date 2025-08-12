{{-- Sidebar Menu Heading Component --}}
@props([
    'label' => '',
    'uppercase' => true
])

<div class="kt-menu-item pt-2.25 pb-px">
    <span class="kt-menu-heading {{ $uppercase ? 'uppercase' : '' }} text-xs font-medium text-muted-foreground ps-[10px] pe-[10px]">
        {{ $label }}
    </span>
</div>
