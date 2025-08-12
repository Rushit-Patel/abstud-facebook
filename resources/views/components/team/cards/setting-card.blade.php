@props([
    'icon' => '',
    'title' => 'Default Title',
    'description' => '',
    'link' => '#',
])

<div class="kt-card p-5 lg:p-7.5 lg:pt-7">
    <div class="flex flex-col gap-4">
        @if($icon!='')
            <div class="flex items-center justify-between gap-2">
                <i class="ki-filled {{ $icon }} text-2xl text-primary">
                </i>
            </div>
        @endif
        <div class="flex flex-col gap-3">
            <a class="text-base font-medium leading-none text-mono hover:text-primary"
                href="{{ $link }}">
                {{ $title }}
            </a>
            @if($description!='')
                <span class="text-sm text-secondary-foreground leading-5">
                    {{ $description }}
                </span>
            @endif
        </div>
    </div>
</div>