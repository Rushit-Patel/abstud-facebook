@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Invoice', 'url' => route('team.invoice.pending')],
    ['title' => 'Add Payment']
];
@endphp
<x-team.layout.app title="Add Payment" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add Payment
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add Payment to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.invoice.pending') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="Payment Information" headerClass="">
                <form action="{{ route('team.invoice.update',$invoiceData->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-team.lead.payment-details
                        :invoiceData="$invoiceData"
                        :paymentMode="$paymentMode"
                        :createdBy="$createdBy"
                        :billingCompany="$billingCompany"
                    />


                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.invoice.pending') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Payment
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

</x-team.layout.app>

<x-team.modals.delete-modal
    id="payment_delete_modal"
    title="Delete Payment"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this payment? This action cannot be undone."
/>
