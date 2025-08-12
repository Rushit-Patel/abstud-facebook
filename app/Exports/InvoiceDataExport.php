<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Repositories\Team\InvoiceRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $filters;

    public function __construct(InvoiceRepository $repository,$filters = [])
    {
        $this->repository = $repository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = $this->repository->getInvoice()->withSum('getPayments as total_paid', 'amount');
        $filters = $this->filters;

        // Date range
        $date = !empty($filters['date']) ? $filters['date'] : null;
        if (!empty($date)) {
            if (str_contains($date, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $date);
                $query->whereBetween('invoice_date', [
                    Helpers::parseToYmd($startDate),
                    Helpers::parseToYmd($endDate),
                ]);
            } else {
                // Single date
                $query->whereDate('invoice_date', Helpers::parseToYmd($date));
            }
        }

        // Branch
        $branch = !empty($filters['branch']) ? $filters['branch'] : ($filters['branch_dashboard'] ?? null);
        if (!empty($branch)) {
            $branchArray = explode(',', $branch);

            $query->whereHas('clientLead', function ($q) use ($branchArray) {
                $q->whereHas('getBranch', function ($q2) use ($branchArray) {
                    $q2->whereIn('branch', $branchArray);
                });
            });
        }
        // Owner
        if (!empty($filters['owner'])) {
            $ownerArray = explode(',', $filters['owner']);
            $query->whereIn('created_by', $ownerArray);
        }
        if(!empty($filters['invoice_name'])){
            $today = Carbon::today()->format('Y-m-d');
            if($filters['invoice_name'] == "Pending"){
                $query->havingRaw('payable_amount <> total_paid OR total_paid IS NULL');
            }elseif($filters['invoice_name'] == "Complete"){
                $query->havingRaw('payable_amount = total_paid');
            }else{

            }
        }

       $invoices = $query->get();

        return $invoices->map(function ($invoice) {
            return [
                'Invoice Date'          => $invoice->invoice_date,
                'Client Name'   => $invoice?->clientLead->client->first_name .''. $invoice?->clientLead?->client->last_name?? '',
                'Client Code'   => $invoice->clientLead->client->client_code ?? '',
                'Client Email'  => $invoice->clientLead->client->email_id ?? '',
                'Mobile'  => '+'.$invoice->clientLead->client->country_code .' '. $invoice->clientLead->client->mobile_no?? '',
                'Branch'        => $invoice->clientLead->getBranch->branch_name ?? '',
                'Purpose'       => $invoice->clientLead->getPurpose->name ?? '',
                'Billing Company'       => $invoice->getBillingcompany->name ?? '',
                'Service'       => $invoice->getService->name ?? '',
                'Total Amount'       => $invoice->total_amount ?? '',
                'Discount'       => $invoice->discount ?? '',
                'Payable Amount'       => $invoice->payable_amount ?? '',
                'Paid Amount'       => $invoice->total_paid ?? '0',
                'Due Amount' => ($invoice->payable_amount ?? 0) - ($invoice->total_paid ?? 0),

            ];
        });

    }

    public function headings(): array
    {
        return [
            'Invoice Date',
            'Client Name',
            'Client Code',
            'Client Email',
            'Mobile',
            'Branch',
            'Purpose',
            'Billing Company',
            'Service',
            'Total Amount',
            'Discount',
            'Payable Amount',
            'Paid Amount',
            'Due Amount',
        ];
    }
}
