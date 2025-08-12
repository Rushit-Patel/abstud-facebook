<x-team.card title="Payment Details" headerClass="">

@if (isset($invoiceData?->getPayments) && $invoiceData?->getPayments->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Lead Purpose</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Service</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Amount</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Billing Company</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Created By</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Due Date</th>
                    @if(auth()->user()->can('invoice:edit') || auth()->user()->can('invoice:delete'))
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($invoiceData?->getPayments as $index => $invoice)
                    <tr>
                        <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $invoice->clientLead->getPurpose->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $invoiceData->getService?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $invoice->amount ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $invoiceData->getBillingcompany->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $invoice->CreatedByOwner->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $invoiceData->due_date ? date('d M Y', strtotime($invoiceData->due_date)) : '-' }}</td>

                        @if(auth()->user()->can('invoice:edit') || auth()->user()->can('invoice:delete') || auth()->user()->can('invoice:view'))
                            <td class="px-4 py-2 text-gray-800">
                                @haspermission('invoice:edit')
                                    <a href="{{ route('team.payment.Edit', $invoice->id) }}" class="kt-btn kt-btn-sm"><i class="ki-filled ki-pencil text-md"></i></a>
                                @endhaspermission
                                @haspermission('invoice:view')
                                    <a href="{{ route('team.payment.print', base64_encode($invoice->id)) }}" class="kt-btn kt-btn-sm"><i class="ki-filled ki-notepad-bookmark text-md"></i></a>
                                @endhaspermission
                                @haspermission('invoice:delete')
                                    <button type="delete" class="kt-btn-sm kt-btn-destructive" data-kt-modal-toggle="#payment_delete_modal" data-form_action="{{route('team.payment.destroy', $invoice->id)}}">
                                        <i class="ki-filled ki-trash text-md"></i>
                                    </button>
                                @endhaspermission
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


<hr>
@endif

    @php
        // Already paid total
        $totalPayments = $invoiceData?->getPayments?->sum('amount') ?? 0;

        // Remaining = total payable - paid
        $remainingAmount = ($invoiceData->payable_amount ?? 0) - $totalPayments;

        // Agar remaining 0 se kam ho gaya to 0 set kar do
        if ($remainingAmount < 0) {
            $remainingAmount = 0;
        }
    @endphp

    <div class="demo-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">
                <div>
                    <x-team.forms.input
                        id="amount"
                        type="text"
                        name="amount"
                        label="Amount"
                        :value="old('amount')"
                        placeholder="Enter amount"
                        required="true"
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="payment_mode"
                        label="Payment Mode"
                        :options="$paymentMode"
                        :selected="old('payment_mode')"
                        placeholder="Select payment mode"
                        searchable="true"
                        required="true"
                        id="payment_mode"
                    />
                </div>
                <div>
                    <x-team.forms.select
                        name="created_by"
                        label="Created By"
                        :options="$createdBy"
                        :selected="old('created_by')"
                        placeholder="Select created by"
                        searchable="true"
                        required="true"
                        id="created_by"
                    />
                </div>

                <div>
                    <x-team.forms.input
                        id="paybleAmount"
                        type="text"
                        name="payble_amount"
                        label="Payble Amount"
                        :value="old('payble_amount' ,$remainingAmount)"
                        placeholder="Enter remark"
                        readonly="true"/>
                </div>

                <div>
                    <x-team.forms.input
                        label="Payment Receipt"
                        id="payment_receipt"
                        name="payment_receipt"
                        type="file"
                    />
                </div>

                @php
                    $currentBranches = is_array(auth()->user()->branch_id)
                        ? auth()->user()->branch_id
                        : explode(',', auth()->user()->branch_id ?? '');

                    $companyBranches = explode(',', $billingCompany->branch ?? '');

                    $getOption = [
                        'included' => 'Included',
                        'excluded' => 'Excluded'
                    ];
                @endphp

                @if (!empty(array_intersect($currentBranches, $companyBranches)) && $billingCompany->is_gst == 1)
                    <div class="col-span-1">
                        <div class="grid gap-5">
                            <x-team.forms.select
                                name="gst"
                                label="GST"
                                :options="$getOption"
                                :selected="old('gst')" placeholder="Select GST" searchable="true"
                                required="true"
                                id="gst"
                            />
                        </div>
                    </div>
                @endif

                 <div id="due-fields" class="hidden lg:col-span-3 grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <!-- Due Date -->
                        <div>
                            <x-team.forms.datepicker
                                label="Due Date"
                                id="due_date"
                                name="due_date"
                                type="date"
                                value="{{ old('due_date') }}"
                                required
                            />
                        </div>
                        <!-- Due Amount -->
                        <div>
                            <x-team.forms.input
                                id="due_amount"
                                type="text"
                                name="due_amount"
                                label="Due Amount"
                                :value="old('due_amount')"
                                readonly
                            />
                        </div>
                    </div>
            </div>

            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-1 gap-5">
                <div class="col-span-1">
                    <div class="grid gap-5">
                        <x-team.forms.textarea
                            id="remarks"
                            name="remarks"
                            label="Remarks"
                            :value="old('remarks')" placeholder="Enter remarks"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
    @include('team.lead.lead-js')






    <script>
        $(document).ready(function () {
            let payableAmount = parseFloat("{{ $remainingAmount ?? 0 }}") || 0;

            $("#amount").on("input", function () {
                let amount = parseFloat($(this).val()) || 0;

                // ❌ Negative value ko 0 set karo
                if (amount < 0) {
                    amount = 0;
                    $(this).val(amount.toFixed(2));
                }

                // ❌ Payable se zyada value ko payable pe fix karo
                if (amount > payableAmount) {
                    amount = payableAmount;
                    $(this).val(amount.toFixed(2));
                }

                // If amount entered is less than payable amount → show due fields
                if (amount < payableAmount) {
                    let dueAmount = payableAmount - amount;
                    $("#due_amount").val(dueAmount.toFixed(2));
                    $("#due-fields").removeClass("hidden");
                    $("#due_date").attr("required", true);
                }
                // If amount entered is equal → hide due fields
                else {
                    $("#due-fields").addClass("hidden");
                    $("#due_amount").val('');
                    $("#due_date").val('');
                    $("#due_date").removeAttr("required");
                }
            });

            // Extra: browser level validation ke liye min/max attributes
            $("#amount").attr({
                min: 0,
                max: payableAmount
            });
        });
    </script>

@endpush
</x-team.card>


