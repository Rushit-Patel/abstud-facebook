<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payable Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            color: #555;
        }
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .invoice-header h2 {
            margin: 0;
        }
        .invoice-details, .customer-details {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .invoice-details td, .customer-details td {
            padding: 5px;
            vertical-align: top;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.items th, table.items td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table.items th {
            background: #f2f2f2;
        }
        .total-section {
            margin-top: 20px;
            float: right;
        }
        .note {
            margin-top: 40px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="invoice-header">
        <h2>INVOICE OF PROFESSIONAL SERVICES</h2>
        <p>{{$companyData->company_name}}<br>
        {{$companyData->company_address}}<br>
        Email: {{$invoice->getInvoice->getBillingcompany->email_id}} @if ($invoice->getInvoice->getBillingcompany->is_gst == 1)
|           GSTIN: {{$invoice->getInvoice->getBillingcompany->gst_number}}
        @endif </p>
    </div>

    <table class="invoice-details">
        <tr>
            <td><strong>Invoice No:</strong> {{$invoice->clientLead->getBranch->branch_code}}-INV_{{$invoice->id}}</td>
            <td><strong>Invoice Date:</strong> {{ $invoice->getInvoice->invoice_date ? date('d M Y', strtotime($invoice->getInvoice->invoice_date)) : '-' }}</td>
        </tr>
    </table>

    <table class="customer-details">
        <tr>
            <td>
                <strong>Student Name:</strong> {{$invoice->clientLead->client->first_name}} {{$invoice->clientLead->client->last_name}}<br>
                <strong>Student Mobile:</strong> +{{$invoice->clientLead->client->country_code}} {{$invoice->clientLead->client->mobile_no}}<br>
                <strong>Billing Address:</strong> {{$invoice->getInvoice->getBillingcompany->address}}.<br>
                <strong>Tel No:</strong> +91 {{$invoice->getInvoice->getBillingcompany->mobile_no}}<br>
                <strong>Email:</strong> {{$invoice->getInvoice->getBillingcompany->email_id}}
            </td>
            <td>
                <strong>Recived By:</strong> {{$invoice->CreatedByOwner->name}}<br>
                <strong>Remarks:</strong> {{$invoice->remarks}}<br>
                <strong>Payment Mode:</strong> {{$invoice->getPaymentMode->name}}
            </td>
        </tr>
    </table>

    <table class="items">
        <tr>
            <th>Sr.No</th>
            <th>Service</th>
            <th>Total Amount</th>
            <th>Discount</th>
            <th>Payable Amount</th>
            <th>Paid Amount (INR)</th>
            <th>Due Amount (INR)</th>
        </tr>
        <tr>
            <td>1</td>
            <td>{{$invoice->getInvoice->getService->name}}</td>
            <td>{{$invoice->getInvoice->total_amount}}</td>
            <td>{{$invoice->getInvoice->discount}}</td>
            <td>{{$invoice->getInvoice->payable_amount}}</td>
            <td>{{$invoice->amount}}</td>
            @php
                // Already paid total
                $totalPayments = $invoice?->getInvoice->getPayments?->sum('amount') ?? 0;
                $remainingAmount = ($invoice?->getInvoice->payable_amount ?? 0) - $totalPayments;
                if ($remainingAmount < 0) {
                    $remainingAmount = 0;
                }
            @endphp
            <td>{{$remainingAmount}}</td>
        </tr>
    </table>

    <div class="total-section">
        {{-- <p><strong>Actual Package:</strong> ₹30,000</p>
        <p><strong>GST (18%):</strong> ₹5,400</p>
        <p><strong>Total Payable:</strong> ₹35,400</p> --}}
    </div>

    <div class="note">
        <p><strong>Note:</strong> Following are our bank details:</p>
        {{-- <p>
            Beneficiary Name: Amratpal A Vision Private Limited<br>
            Account No: 777705480101<br>
            IFSC: ICICINBBCTS<br>
            Bank: ICICI Bank, Subhash Chowk, Ahmedabad
        </p> --}}
        <p>This is an electronically generated invoice and does not require a signature.</p>
    </div>
</div>

</body>
</html>
