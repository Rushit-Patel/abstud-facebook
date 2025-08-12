@props([
    'id' => 'followUpModal',
    'title' => 'Edit Follow-up',
    'formId' => 'followUpForm',
])

<div
    class="kt-modal kt-modal-center hidden fixed inset-0 items-center "
    id="{{ $id }}" data-kt-modal="true"
    data-kt-modal-scroll="true"
>
    <div class="kt-modal-content max-w-xl w-full">
        <form action="{{route('team.lead-follow-up.update',$id)}}" method="POST" id="{{ $formId }}">
            @csrf
            @method('PUT')

            <div class="kt-modal-header ">
                <h3 class="kt-modal-title ">{{ $title }}</h3>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true" type="button">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>

            <div class="kt-modal-body max-h-[400px]">
                <input type="hidden" name="follow_id" id="follow_up_id">
                <input type="hidden" name="client_lead_id" id="client_lead_id">
                <div class="flex flex-col gap-2">
                    {{-- <x-team.forms.textarea label="Remarks" name="remarks" id="remarks" required="true" readonly="true" :value="''" /> --}}
                    <x-team.forms.textarea label="Remarks" name="remarks" id="remarks" required="true" readonly="true"  :value="old('remarks')" />


                    <x-team.forms.textarea label="Communication" name="communication" id="communication" required="true" class=""/>

                    <div class="kt-alert kt-alert-outline kt-alert-primary kt-alert-sm">
                        <x-team.forms.switch
                            label="Need Next Follow-up?"
                            name="check"
                            id="toggleNextFollowUp"
                            icon="ki-filled ki-coffee"
                            style="menu"
                            size="sm"
                        />
                    </div>
                    <div id="nextFollowUpFields" class="hidden space-y-4">
                        <x-team.forms.datepicker
                            label="Next Follow-up Date"
                            name="next_follow_up_date"
                            id="next_follow_up_date"
                            placeholder="Select follow-up date"
                            required="true"
                            minDate="today"
                            dateFormat="Y-m-d"
                            :value="old('next_follow_up_date', \Carbon\Carbon::today()->format('d/m/Y'))"
                            class="w-full flatpickr"
                        />
                        <x-team.forms.textarea label="Next Follow-up Remark" name="next_remarks" id="next_remarks" />
                    </div>
                </div>
            </div>

            <div class="kt-modal-footer flex justify-end gap-2 p-5">
                <button type="button" class="kt-btn kt-btn-secondary " data-kt-modal-dismiss="true" >
                    Cancel
                </button>
                <button type="submit" class="kt-btn kt-btn-primary">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
