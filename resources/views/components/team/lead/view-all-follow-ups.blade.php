@props([
    'id' => 'viewAllFollowUpsModal',
    'title' => 'Follow-up History',
])

<div
    class="kt-modal kt-modal-center hidden fixed inset-0 items-center"
    id="{{ $id }}" data-kt-modal="true"
    data-kt-modal-scroll="true"
>
    <div class="kt-modal-content max-w-4xl w-full">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">{{ $title }}</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true" type="button">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>

        <div class="kt-modal-body max-h-[70vh] overflow-y-auto">
            {{-- Client Info Card --}}
            <div class="kt-card mb-6">
                <div class="kt-card-header">
                    <h4 class="kt-card-title text-sm">Client Information</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                        <div class="flex items-center">
                            <i class="ki-filled ki-user text-gray-400 text-xs mr-2"></i>
                            <div>
                                <span class="text-gray-600 block text-xs">Name</span>
                                <span id="client-name" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="ki-filled ki-phone text-gray-400 text-xs mr-2"></i>
                            <div>
                                <span class="text-gray-600 block text-xs">Mobile</span>
                                <span id="client-mobile" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="ki-filled ki-sms text-gray-400 text-xs mr-2"></i>
                            <div>
                                <span class="text-gray-600 block text-xs">Email</span>
                                <span id="client-email" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="ki-filled ki-geolocation text-gray-400 text-xs mr-2"></i>
                            <div>
                                <span class="text-gray-600 block text-xs">Branch</span>
                                <span id="client-branch" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Follow-ups Container --}}
            <div id="followups-container">
                {{-- Loading State --}}
                <div class="kt-card">
                    <div class="kt-card-body text-center py-8">
                        <i class="ki-filled ki-loading text-2xl text-gray-400 animate-spin"></i>
                        <p class="text-gray-500 mt-2">Loading follow-ups...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-modal-footer">
            <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross text-xs mr-1"></i>
                Close
            </button>
        </div>
    </div>
</div>
