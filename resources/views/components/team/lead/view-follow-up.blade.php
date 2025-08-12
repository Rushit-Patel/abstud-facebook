@props([
    'id' => 'viewFollowUpModal',
    'title' => 'Follow-up Details',
])

<div
    class="kt-modal kt-modal-center hidden fixed inset-0 items-center"
    id="{{ $id }}" data-kt-modal="true"
    data-kt-modal-scroll="true"
>
    <div class="kt-modal-content max-w-lg w-full">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">{{ $title }}</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true" type="button">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>

        <div class="kt-modal-body max-h-[400px] p-6">
            <div class="space-y-4">
                <div>
                    <label class="kt-form-label font-medium text-sm text-gray-700">Follow-up Date:</label>
                    <div id="view-followup-date" class="text-sm text-gray-900 mt-1">-</div>
                </div>

                <div>
                    <label class="kt-form-label font-medium text-sm text-gray-700">Status:</label>
                    <div id="view-followup-status" class="text-sm mt-1">-</div>
                </div>

                <div>
                    <label class="kt-form-label font-medium text-sm text-gray-700">Remarks:</label>
                    <div id="view-followup-remarks" class="text-sm text-gray-900 mt-1 p-3 bg-gray-50 rounded border min-h-[60px]">-</div>
                </div>

                <div>
                    <label class="kt-form-label font-medium text-sm text-gray-700">Communication:</label>
                    <div id="view-followup-communication" class="text-sm text-gray-900 mt-1 p-3 bg-gray-50 rounded border min-h-[60px]">-</div>
                </div>
            </div>
        </div>

        <div class="kt-modal-footer flex justify-end gap-2 p-5">
            <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">
                Close
            </button>
        </div>
    </div>
</div>
