<x-team.card title="Invoice Details" headerClass="">

@if (isset($InvoiceDetails) && $InvoiceDetails->count() > 0)
    <div class="demo-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Demo Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">
                <!-- Demo  -->
                <input type="hidden" name="client_invoice_id" value="{{ $InvoiceDetails->id }}">

                <!-- Register Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Invoice Date"
                        id="invoice_date"
                        name="invoice_date"
                        type="date"
                        :value=" old('invoice_date', \Carbon\Carbon::parse($InvoiceDetails->invoice_date)->format('d-m-Y'))"
                        required
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="client_lead_id"
                        label="Lead"
                        :options="$clientLeadData"
                        :selected="old('client_lead_id',$InvoiceDetails->client_lead_id)"
                        placeholder="Select lead"
                        searchable="true"
                        required="true"
                        id="client_lead_id"
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="service_id"
                        label="Service"
                        :options="[]" {{-- Initially empty, JS se fill hoga --}}
                        :selected="old('service_id',$InvoiceDetails->service_id)"
                        placeholder="Select service"
                        searchable="true"
                        required="true"
                        id="service_id"
                    />
                </div>

                <div id="extraFields" class="contents" style="display: none;">
                    <div>
                        <x-team.forms.input
                            id="total_amount"
                            type="text"
                            name="total_amount"
                            label="Total Amount"
                            :value="old('total_amount',$InvoiceDetails->total_amount)"
                            placeholder="Enter total_amount"
                            readonly="true"
                        />
                    </div>

                    <div>
                        <x-team.forms.input
                            id="discount"
                            type="number"
                            name="discount"
                            label="Discount"
                            :value="old('discount',$InvoiceDetails->discount)"
                            placeholder="Enter discount"
                        />
                    </div>

                    <div>
                        <x-team.forms.input
                            id="payable_amount"
                            type="text"
                            name="payable_amount"
                            label="Payable Amount"
                            :value="old('amount' ,$InvoiceDetails->amount)"
                            placeholder="Enter payable amount"
                            readonly="true"
                        />
                    </div>
                </div>

                <div>
                    <x-team.forms.select
                        name="billing_company_id"
                        label="Billing Company"
                        :options="$billingCompany"
                        :selected="old('billing_company_id',$InvoiceDetails->billing_company_id)"
                        placeholder="Select billing company"
                        searchable="true"
                        required="true"
                    />
                </div>

            </div>
        </div>
    </div>
@else
    <div class="demo-item rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4">

            <!-- Invoice Date -->
            <div>
                <x-team.forms.datepicker
                    label="Invoice Date"
                    id="invoice_date"
                    name="invoice_date"
                    type="date"
                    value="{{ old('invoice_date') }}"
                    required
                />
            </div>
            <!-- Lead -->
            <div>
                <x-team.forms.select
                    name="client_lead_id"
                    label="Lead"
                    :options="$clientLeadData"
                    :selected="old('client_lead_id')"
                    placeholder="Select lead"
                    searchable="true"
                    required="true"
                    id="client_lead_id"
                />
            </div>

            <!-- Service -->
            <div>
                <x-team.forms.select
                    name="service_id"
                    label="Service"
                    :options="[]" {{-- Initially empty, JS se fill hoga --}}
                    :selected="old('service_id')"
                    placeholder="Select service"
                    searchable="true"
                    required="true"
                    id="service_id"
                />
            </div>


            <!-- Extra Fields (hidden initially) -->
            <div id="extraFields" class="contents" style="display: none;">
                <div>
                    <x-team.forms.input
                        id="total_amount"
                        type="text"
                        name="total_amount"
                        label="Total Amount"
                        :value="old('total_amount')"
                        placeholder="Enter total_amount"
                        readonly="true"
                    />
                </div>

                <div>
                    <x-team.forms.input
                        id="discount"
                        type="number"
                        name="discount"
                        label="Discount"
                        :value="old('discount')"
                        placeholder="Enter discount"
                    />
                </div>

                <div>
                    <x-team.forms.input
                        id="payable_amount"
                        type="text"
                        name="payable_amount"
                        label="Payable Amount"
                        :value="old('amount')"
                        placeholder="Enter payable amount"
                        readonly="true"
                    />
                </div>
            </div>

            <!-- Billing Company -->
            <div>
                <x-team.forms.select
                    name="billing_company_id"
                    label="Billing Company"
                    :options="$billingCompany"
                    :selected="old('billing_company_id')"
                    placeholder="Select billing company"
                    searchable="true"
                    required="true"
                />
            </div>
        </div>
    </div>
@endif
</x-team.card>

<script>
$(document).ready(function () {

    let serviceData = {};

    // Function to load services (with optional callback after load)
    function loadServices(leadId, selectedServiceId = null, keepSelection = true, callback = null) {
        if (leadId) {
            $.ajax({
                url: "{{ route('team.get.service.details') }}",
                method: 'GET',
                data: { client_lead_id: leadId },
                success: function (response) {
                    let serviceSelect = $('#service_id');
                    serviceSelect.empty().append('<option value="">Select service</option>');
                    serviceData = response;

                    $.each(response, function (id, data) {
                        serviceSelect.append('<option value="' + id + '">' + data.label + '</option>');
                    });

                    // Only set selection if keepSelection = true (edit mode load)
                    if (keepSelection && selectedServiceId && serviceData[selectedServiceId]) {
                        serviceSelect.val(selectedServiceId).trigger('change');
                    }

                    // Run callback if provided
                    if (typeof callback === 'function') {
                        callback();
                    }
                },
                error: function () {
                    alert('Failed to load services.');
                }
            });
        } else {
            $('#service_id').empty().append('<option value="">Select service</option>');
            serviceData = {};
        }
    }

    // Lead change → fresh service list, no old selection
    $(document).on('change', '#client_lead_id', function () {
        loadServices($(this).val(), null, false);
        // Clear total/payable fields
        $('#total_amount').val('');
        $('#payable_amount').val('');
        $('#extraFields').hide();
    });

    // Service change → update amounts
    $(document).on('change', '#service_id', function () {
        let selectedId = $(this).val();

        if (serviceData[selectedId]) {
            let total = parseFloat(serviceData[selectedId].amount) || 0;
            $('#total_amount').val(total.toFixed(2));

            // Get current discount
            let discount = parseFloat($('#discount').val()) || 0;

            // Validate discount against new total
            let errorEl = $('#discount-error');
            if (!errorEl.length) {
                $('#discount').after('<small id="discount-error" style="color:red;display:none;"></small>');
                errorEl = $('#discount-error');
            }

            if (discount < 0) {
                errorEl.text("Please remove negative value!").show();
                $('#discount').attr('required', true).val('');
                discount = 0;
            }
            else if (discount > total) {
                errorEl.text("Discount cannot be more than total amount!").show();
                $('#discount').attr('required', true).val('');
                discount = 0;
            }
            else {
                errorEl.hide();
                $('#discount').removeAttr('required');
            }

            // Set payable amount based on new total and discount
            let payable = total - discount;
            if (payable < 0) payable = 0;
            $('#payable_amount').val(payable.toFixed(2));

            $('#extraFields').show();
        } else {
            $('#total_amount').val('');
            $('#payable_amount').val('');
            $('#extraFields').hide();
        }
    });


    // Discount change → validate & update payable
    $('#discount').on('input', function () {
        let total = parseFloat($('#total_amount').val()) || 0;
        let discount = parseFloat($(this).val()) || 0;
        let $this = $(this);

        let errorEl = $('#discount-error');
        if (!errorEl.length) {
            $this.after('<small id="discount-error" style="color:red;display:none;"></small>');
            errorEl = $('#discount-error');
        }

        if (discount < 0) {
            errorEl.text("Please remove negative value!").show();
            $this.attr('required', true); // make required
            $this.val('');
            discount = 0;
        }
        else if (discount > total) {
            errorEl.text("Discount cannot be more than total amount!").show();
            $this.attr('required', true); // make required
            $this.val('');
            discount = 0;
        }
        else {
            errorEl.hide();
            $this.removeAttr('required'); // remove required when valid
        }

        let payable = total - discount;
        if (payable < 0) payable = 0;

        $('#payable_amount').val(payable.toFixed(2));
    });

    // ---- EDIT MODE ----
    @if(isset($InvoiceDetails))
        let editLeadId = "{{ $InvoiceDetails->client_lead_id }}";
        let editServiceId = "{{ $InvoiceDetails->service_id }}";
        let editDiscount = "{{ $InvoiceDetails->discount ?? 0 }}";

        if (editLeadId) {
            loadServices(editLeadId, editServiceId, true, function () {
                $('#discount').val(editDiscount).trigger('input');
            });
        }
    @endif

});
</script>
