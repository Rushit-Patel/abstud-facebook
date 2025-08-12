<x-team.card title="Payment Details" headerClass="">

@if (isset($invoiceData?->getPayments) && $invoiceData?->getPayments->count() > 0)

@endif

    @php
        // Example: current editing payment ID
        $excludePaymentId = $paymentData->id ?? null;

        // Get total payments excluding the current one
        $totalPayments = $invoiceData?->getPayments
            ?->where('id', '!=', $excludePaymentId)
            ->sum('amount') ?? 0;

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
                        :value="old('amount',$paymentData->amount)"
                        placeholder="Enter amount"
                        required="true"
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="payment_mode"
                        label="Payment Mode"
                        :options="$paymentMode"
                        :selected="old('payment_mode',$paymentData->payment_mode)"
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
                        :selected="old('created_by',$paymentData->created_by)"
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

                {{-- <div>
                    <x-team.forms.input
                        label="Payment Receipt"
                        id="payment_receipt"
                        name="payment_receipt"
                        type="file"
                    />
                </div> --}}



                @php
                    $isEdit = isset($paymentData->payment_receipt) && $paymentData->payment_receipt;
                @endphp
                <div x-data="{ hasOldFile: {{ $isEdit ? 'true' : 'false' }} }">
                    <x-team.forms.input
                        label="Payment Receipt"
                        id="payment_receipt"
                        name="payment_receipt"
                        type="file"
                    />

                    {{-- Existing File Link --}}
                    @if ($isEdit)
                        <div class="mt-2">
                            <a
                                href="{{ asset('storage/' . $paymentData->payment_receipt) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition"
                            >
                                📄 View Existing File
                            </a>
                        </div>
                    @endif
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
                                :selected="old('gst',$paymentData->gst)" placeholder="Select GST" searchable="true"
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
                                value="{{ old('due_date', \Carbon\Carbon::parse($invoiceData->due_date)->format('d-m-Y')  ?? '') }}"
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
                            :value="old('remarks' ,$paymentData->remarks)" placeholder="Enter remarks"
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

        function handleAmountChange() {
            let amount = parseFloat($("#amount").val()) || 0;

            // ❌ Negative value ko 0 set karo
            if (amount < 0) {
                amount = 0;
                $("#amount").val(amount.toFixed(2));
            }

            // ❌ Payable se zyada value ko payable pe fix karo
            if (amount > payableAmount) {
                amount = payableAmount;
                $("#amount").val(amount.toFixed(2));
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
        }

        // Event bind
        $("#amount").on("input", handleAmountChange);

        // Browser level validation ke liye min/max attributes
        $("#amount").attr({
            min: 0,
            max: payableAmount
        });

        // Page load pe bhi run kare
        $("#amount").trigger("input"); // <-- input trigger
    });
</script>


@endpush
</x-team.card>


