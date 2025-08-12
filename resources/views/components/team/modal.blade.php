{{-- Team Modal Component --}}
@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'size' => 'max-w-[600px]',
    'position' => 'top-[15%]',
    'showHeader' => true,
    'showCloseButton' => true
])

<div class="kt-modal" data-kt-modal="true" id="{{ $id }}" >
    <div class="kt-modal-content  {{ $size }} {{ $position }}">
        @if($showHeader)
        <div class="kt-modal-header py-4 px-5">
            <h3 class="kt-modal-title">{{ $title }}</h3>
            @if($showCloseButton)
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true">
                    <i class="ki-filled ki-cross">
                    </i>
                </button>
            @endif
        </div>
        @endif
        <div class="kt-modal-body p-4 pb-5">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="kt-modal-footer">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>