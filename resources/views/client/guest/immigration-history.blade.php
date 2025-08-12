@extends('client.layouts.guest')

@section('card-width', 'max-w-[800px]')

@section('content')
    <x-team.card title="Immigration Information" headerClass="">
        <form action="{{ route('client.guest.immigration-history.store', $clientLeadId) }}" method="POST" class="form"
              enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="mb-2">
                    <x-team.forms.checkbox
                        id="is_visa_rejectionCheckbox"
                        name="is_visa_rejection"
                        :value="1"
                        label="Any Visa Rejection?"
                        style="inline"
                        class="kt-switch kt-switch-lg"
                        :checked="old('visa_rejection') || (isset($getDetails->visaRejectionDetailsLatest) && $getDetails?->visaRejectionDetailsLatest->count() > 0)"
                    />
                </div>
            </div>

            <!-- Visa Rejection Section (Single Entry) -->
            <div id="visa-rejection-section" class="hidden">
                <div class="rejection-item rounded-lg mb-3 relative bg-secondary-50 p-4">
                    <div class="visa-rejection-fields grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
                        <x-team.forms.select
                            name="visa_rejection[rejection_country]"
                            label="Rejection Country"
                            :options="$country"
                            placeholder="Select country"
                            :selected="$getDetails?->visaRejectionDetailsLatest?->rejection_country"
                            required
                        />

                        <x-team.forms.input
                            name="visa_rejection[rejection_month_year]"
                            label="Month-Year"
                            type="text"
                            placeholder="Enter JAN-2024"
                            required
                            :value="$getDetails?->visaRejectionDetailsLatest?->rejection_month_year"
                        />

                        <x-team.forms.select
                            name="visa_rejection[rejection_visa_type]"
                            label="Visa Type"
                            :options="$otherVisaType"
                            placeholder="Select visa type"
                            required
                            :selected="$getDetails?->visaRejectionDetailsLatest?->rejection_visa_type"
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                    Continue
                </button>
            </div>
        </form>
    </x-team.card>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        var $isRejectionCheckbox = $('#is_visa_rejectionCheckbox');
        var $rejectionSection = $('#visa-rejection-section');

        function toggleRejectionSection() {
            if ($isRejectionCheckbox.is(':checked')) {
                $rejectionSection.removeClass('hidden');
                $rejectionSection.find('select, input').attr('required', 'required');
            } else {
                $rejectionSection.addClass('hidden');
                $rejectionSection.find('select, input').removeAttr('required');
            }
        }

        $isRejectionCheckbox.on('change', toggleRejectionSection);
        toggleRejectionSection(); // Run on page load
    });
</script>
@endpush
