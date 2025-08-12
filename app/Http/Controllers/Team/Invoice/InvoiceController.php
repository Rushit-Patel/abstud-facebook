<?php

namespace App\Http\Controllers\Team\Invoice;

use App\DataTables\Team\Invoice\CompleteInvoiceDataTable;
use App\DataTables\Team\Invoice\PendingInvoiceDataTable;
use App\Exports\FollowUpDataExport;
use App\Exports\InvoiceDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BillingCompany;
use App\Models\ClientInvoice;
use App\Models\ClientPayment;
use App\Models\CompanySetting;
use App\Models\PaymentMode;
use App\Models\User;
use App\Repositories\Team\InvoiceRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function InvoicePending(PendingInvoiceDataTable $PendingInvoiceDataTable)
    {
        $invoiceName = "Pending";
        return $PendingInvoiceDataTable->render('team.invoice.index',compact('invoiceName'));
    }

    public function InvoiceComplete(CompleteInvoiceDataTable $CompleteInvoiceDataTable)
    {
        $invoiceName = "Complete";
        return $CompleteInvoiceDataTable->render('team.invoice.index',compact('invoiceName'));
    }

    /**
     * Show the form for creating a new Source
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created Source
     */
    public function store(Request $request)
    {
        //
    }

    // Invoice Create
    public function edit($id)
    {
        $invoiceData = ClientInvoice::findOrFail($id);
        $paymentMode = PaymentMode::active()->get();
        $createdBy = User::active()->get();
        $billingCompany = BillingCompany::find($invoiceData->billing_company_id);

        return view('team.invoice.edit', compact('invoiceData','paymentMode','createdBy','billingCompany'));
    }
    // Invoice Create
    public function update(Request $request, $id)
    {
        $invoiceData = ClientInvoice::find($id);
        try {

            $data = [
                'client_id'        => $invoiceData->client_id,
                'invoice_id'        => $id,
                'client_lead_id'   => $invoiceData->client_lead_id,
                'amount'     => $request->input('amount'),
                'payment_mode'     => $request->input('payment_mode'),
                'created_by'     => $request->input('created_by'),
                'gst'     => $request->input('gst'),
                'remarks'     => $request->input('remarks'),
            ];

            if ($request->hasFile('payment_receipt') && $request->file('payment_receipt')->isValid()) {
                $clientDetails = $invoiceData->clientLeadDetails;
                $clientName = Str::slug($clientDetails->first_name . ' ' . $clientDetails->last_name);
                $clientId = $clientDetails->id;

                $folderPath = "payment_receipts/{$clientName}_{$clientId}";
                $file = $request->file('payment_receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();

                if ($request->filled('client_payment_id')) {
                    $existingPayment = ClientPayment::find($request->client_payment_id);
                    if ($existingPayment && $existingPayment->payment_receipt) {
                        Storage::disk('public')->delete($existingPayment->payment_receipt);
                    }
                }

                $storedFilePath = $file->storeAs($folderPath, $fileName, 'public');
                $data['payment_receipt'] = $storedFilePath;
            }


            if ($request->filled('client_payment_id')) {
                ClientPayment::find($request->client_payment_id)->update($data);
            } else {
                $data['added_by'] = auth()->user()->id;
                ClientPayment::create($data);
            }

            if (!empty($request->due_date) && !empty($request->due_amount)) {
                $invoiceData->due_date = Helpers::parseToYmd($request->due_date);
                $invoiceData->due_amount = $request->input('due_amount');
                $invoiceData->save();
            }

            return redirect()->route('team.invoice.pending')
                ->with('success', "Client Payment Received successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error updating Client Payment Received: ' . $e->getMessage());
        }
    }

    public function editPayment($id){

        $paymentData = ClientPayment::findOrFail($id);
        $invoiceData = $paymentData->getInvoice;
        $paymentMode = PaymentMode::active()->get();
        $createdBy = User::active()->get();
        $billingCompany = BillingCompany::find($invoiceData->billing_company_id);

        return view('team.payment.edit', compact('paymentData','invoiceData','paymentMode','createdBy','billingCompany'));
    }

    public function updatePayment(Request $request, $id)
    {
        // Fetch existing payment record
        $payment = ClientPayment::findOrFail($id);

        try {
            $data = [
                'client_id'      => $payment->client_id,
                'client_lead_id' => $payment->client_lead_id,
                'invoice_id'     => $payment->invoice_id, // keep original invoice ID
                'amount'         => $request->input('amount'),
                'payment_mode'   => $request->input('payment_mode'),
                'created_by'     => $request->input('created_by'),
                'gst'            => $request->input('gst'),
                'remarks'        => $request->input('remarks'),
            ];

            // Handle file upload
            if ($request->hasFile('payment_receipt') && $request->file('payment_receipt')->isValid()) {
                $clientDetails = $payment->clientLeadDetails;
                $clientName = Str::slug($clientDetails->first_name . ' ' . $clientDetails->last_name);
                $clientId = $clientDetails->id;

                $folderPath = "payment_receipts/{$clientName}_{$clientId}";
                $file = $request->file('payment_receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Delete old file
                if ($payment->payment_receipt) {
                    Storage::disk('public')->delete($payment->payment_receipt);
                }

                $storedFilePath = $file->storeAs($folderPath, $fileName, 'public');
                $data['payment_receipt'] = $storedFilePath;
            }

            // Update payment record
            $payment->update($data);

            // ✅ Update Invoice due details if provided
            if (!empty($request->due_date) && !empty($request->due_amount)) {
                $invoice = ClientInvoice::find($payment->invoice_id);
                if ($invoice) {
                    $invoice->due_date = Helpers::parseToYmd($request->due_date);
                    $invoice->due_amount = $request->input('due_amount');
                    $invoice->save();
                }
            }


            return redirect()->route('team.invoice.pending')
                ->with('success', "Client Payment Received successfully.");

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function destroyPayment($id){
        try {
            $clientLead = ClientPayment::findOrFail($id);
            $clientName = $clientLead->clientLeadDetails->first_name . ' ' . $clientLead->clientLeadDetails->last_name;
            $clientLead->delete();

            return redirect()->route('team.invoice.pending')
                ->with('success', "Payment for client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client: ' . $e->getMessage());
        }
    }

    public function printPayment($id)
    {
        $id = base64_decode($id);
        // Invoice data fetch
        $invoice = ClientPayment::with(['clientLead.client', 'clientLead.getBranch', 'getInvoice'])
                    ->findOrFail($id);

        $companyData = CompanySetting::first();

        // PDF view load
        $pdf = Pdf::loadView('team.payment.print-pdf', compact('invoice','companyData'));

        // Direct download
        // return $pdf->download('invoice-'.$invoice->id.'.pdf');
        return $pdf->stream('invoice-' . $invoice->id . '.pdf');


    }


    public function destroy($id)
    {
        //
    }

    /**
     * Get all follow-ups for a specific lead
     */

    public function exportInvoice(Request $request,InvoiceRepository $invoiceRepository){

        $export = new InvoiceDataExport($invoiceRepository,$request->all());
        return Excel::download($export, 'invoice.xlsx');

    }
}
