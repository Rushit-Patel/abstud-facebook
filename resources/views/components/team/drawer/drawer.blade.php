@props([
    'id' => 'drawer',
    'title' => 'Drawer',
    'showFooter' => true
])

<div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[320px] top-5 bottom-5 end-5 rounded-xl border border-border" data-kt-drawer="true" data-kt-drawer-container="body" id="{{ $id }}">
    {{-- Header --}}
    <div class="kt-card-header ps-5 pr-2">
        <h3 class="kt-card-title">
            {{ $title }}
        </h3>
        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost shrink-0" data-kt-drawer-dismiss="true">
            <i class="ki-filled ki-cross text-base"></i>
        </button>
    </div>

    {{-- Content --}}
    <div class="kt-card-content px-0 pt-3.5 kt-scrollable-y-auto">
        @if(isset($body))
            {{ $body }}
        @else
            {{ $slot }}
        @endif
    </div>
    {{-- Footer --}}
    @if($showFooter)
        <div class="kt-card-footer grid grid-cols-2 gap-2.5">
            @if(isset($footer))
                {{ $footer }}
            @endif
        </div>
    @endif
</div>
