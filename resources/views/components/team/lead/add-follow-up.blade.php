@props([
    'id' => 'followUpModal',
    'title' => 'Add Follow-up',
    'formId' => 'followUpForm',
    'followup' => null,
    'action' => ''
])

<div class="kt-modal kt-modal-center" data-kt-modal="true" id="{{ $id }}" data-kt-modal-scroll="true">
    <div class="kt-modal-content max-w-xl w-full">
        <form action="{{ $action }}" method="POST" id="{{ $formId }}">
            @csrf
            @if($followup)
                @method('PUT')
            @endif

            <div class="kt-modal-header">
                <h3 class="kt-modal-title">{{ $title }}</h3>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true" type="button">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>

            <div class="kt-modal-body p-5 space-y-4">
                <x-team.forms.datepicker
                    label="Follow-up Date"
                    name="followup_date"
                    id="followup_date"
                    placeholder="Select follow-up date"
                    required="true"
                    minDate="today"
                    dateFormat="Y-m-d"
                    class="w-full flatpickr"
                    :value="old('followup_date', \Carbon\Carbon::today()->format('d/m/Y'))"
                    class="w-full flatpickr"
                />

                <x-team.forms.textarea label="Follow-up Remark" name="remarks" id="remarks" required/>

            </div>

            <div class="kt-modal-footer flex justify-end gap-2 p-5">
                <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">
                    Cancel
                </button>
                <button type="submit" class="kt-btn kt-btn-primary">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
