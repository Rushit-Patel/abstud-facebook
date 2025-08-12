@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'size' => 'max-w-[400px]',
    'position' => 'top-[15%]',
    'showHeader' => true,
    'showCloseButton' => true,
    'formId' => 'deleteForm',
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.'
])
<div class="kt-modal kt-modal-center" data-kt-modal="true" id="{{ $id }}" data-kt-modal-scroll="true">
    <div class="kt-modal-content {{ $size }}">
        <form action="" method="post" id="{{ $formId }}">
            @csrf
            @method('DELETE')
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">{{ $title }}</h3>
                @if($showCloseButton)
                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true" type="button">
                        <i class="ki-filled ki-cross">
                        </i>
                    </button>
                @endif
            </div>
            <div class="kt-modal-body p-4 pb-5">
                <p class="text-sm text-gray-600">
                    {{ $message ?? 'Are you sure you want to delete this item? This action cannot be undone.' }}
                </p>
            </div>
            <div class="kt-modal-footer">
                <div class="flex justify-end gap-2">
                    <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">
                        Cancel
                    </button>
                    <button type="submit" class="kt-btn kt-btn-destructive">
                        Delete
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>