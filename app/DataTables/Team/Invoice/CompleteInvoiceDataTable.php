<?php

namespace App\DataTables\Team\Invoice;

use App\Helpers\Helpers;
use App\Repositories\Team\InvoiceRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Auth;

class CompleteInvoiceDataTable extends DataTable
{
    protected $repository;

    public function __construct(InvoiceRepository $repository)
    {
        $this->repository = $repository;
    }
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('client', fn($row) => $this->renderClient($row))
            ->filterColumn('client', function ($query, $keyword) {
                $query->whereHas('clientLead.client', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('invoice_date', fn($row) => $this->renderInvoiceDate($row))
                ->filterColumn('invoice_date', function ($query, $keyword) {
                    $query->where('invoice_date', 'like', "%{$keyword}%");
                })

            ->addColumn('service', fn($row) => $this->renderService($row))
            ->filterColumn('service', function ($query, $keyword) {
                $query->whereHas('getService', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('billing_company', fn($row) => $this->renderBillingCompany($row))
            ->filterColumn('billing_company', function ($query, $keyword) {
                $query->whereHas('getBillingcompany', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->rawColumns(['action', 'client', 'invoice_date', 'service','billing_company'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.invoice.datatables.action', ['id' => $row->id])->render();
    }

    private function renderClient($row): string
    {
        $client = $row->clientLead->client;
        return view('team.invoice.datatables.client-details', [
            'name' => $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name,
            'mobile_no' => $client->country_code . "" . $client->mobile_no,
            'email_id' => $client->email_id,
            'branch' => $row->clientLead->getBranch->branch_name,
        ])->render();
    }

    private function renderInvoiceDate($row): string
    {
        return view('team.invoice.datatables.invoice-date', [
            'invoice_date' => Carbon::parse($row->invoice_date)->format('d/m/Y'),
            'is_overdue' => Carbon::parse($row->invoice_date)->isPast(),
        ])->render();
    }

    private function renderService($row): string
    {
        return view('team.invoice.datatables.service', [
            'service' => $row?->getService?->name,
            'total_amount' => $row?->total_amount,
            'discount' => $row?->discount,
            'payable_amount' => $row?->payable_amount,
        ])->render();
    }

    private function renderBillingCompany($row): string
    {
        return view('team.invoice.datatables.billing-company', [
            'billing_company' => $row?->getBillingcompany?->name,
        ])->render();
    }

    public function query(): QueryBuilder
    {
        $Invoicequery = $this->repository->getInvoice();

        $query = $Invoicequery
            ->with(['clientLead.client', 'clientLead.getBranch'])
            ->withSum('getPayments as total_paid', 'amount');

        if (request()->has('branch')) {
            $branches = request()->get('branch');

            // Handle comma-separated string or array
            if (!is_array($branches)) {
                $branches = array_map('trim', explode(',', $branches));
            }

            if (!empty($branches)) {
                $query->whereHas('clientLead', function ($q) use ($branches) {
                    $q->whereIn('branch', $branches);
                });
            }else{
                $branch = trim($branches);
                $query->whereHas('clientLead', function ($q) use ($branch) {
                    $q->where('branch', $branch);
                });
            }
        }


        if (request()->has('owner')) {
            $ownerParam = request()->get('owner');
            if (is_array($ownerParam)) {
                // New array format from filter form
                $query->whereIn('added_by', $ownerParam);
            } else {
                // Existing single value format
                $owner = trim($ownerParam);
                if (!empty($owner)) {
                    if (str_contains($owner, ',')) {
                        $ownerArray = explode(',', $owner);
                        $query->whereIn('added_by', $ownerArray);
                    } else {
                        $query->where('added_by', $owner);
                    }
                }
            }
        }

        if (request()->has('date')) {
            $dateRange = request()->get('date');
            $dateParts = explode('to', $dateRange);
            // change date formate d/m/Y to Y-m-d
            if (count($dateParts) == 2) {
                $startDate = Helpers::parseToYmd(trim($dateParts[0]));
                $endDate = Helpers::parseToYmd(trim($dateParts[1]));
                $query->whereBetween('invoice_date', [$startDate, $endDate]);
            }else{
                if (!empty($dateParts[0]) && Carbon::hasFormat(trim($dateParts[0]), 'd/m/Y')) {
                    $startDate = Helpers::parseToYmd(trim($dateParts[0]));
                    $query->whereDate('invoice_date', $startDate);
                }
            }
        }

        $query->havingRaw('payable_amount = total_paid');

        $query = $query->orderBy('invoice_date', 'desc')
                    ->orderBy('id', 'desc');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('invoice-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::make('client')->title('Client')->width(150)->searchable(true),
            Column::make('invoice_date')->title('Invoice Date')->width(120)->searchable(true),
            Column::make('service')->title('Service')->width(250)->searchable(true),
            Column::make('billing_company')->title('Billing Company')->width(250)->searchable(true),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center')
                ->orderable(false)
                ->searchable(false),
        ];
    }

    private function getTableParameters(): array
    {
        return [
            'dom' => '<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-2"<"dt-length flex items-center gap-2 order-2 md:order-1"><"datatable-export-form"><"dt-search ml-auto"f>>rt<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-4 border-t border-gray-200"<"kt-datatable-length text-secondary-foreground text-sm font-medium"l><"dt-paging flex items-center space-x-1 text-secondary-foreground text-sm font-medium"ip>>',
            'buttons' => ['export', 'print', 'reset', 'reload'],
            'scrollX' => true,
            'language' => [
                'lengthMenu' => 'Show _MENU_ per page',
                'search' => 'Search: ',
                'info' => '_START_-_END_ of _TOTAL_',
                'paginate' => [
                    'previous' => '←',
                    'next' => '→'
                ]
            ],
            'initComplete' => 'function() {
                var exportFormHtml = ' . json_encode($this->getExportButton()) . ';
                $(".datatable-export-form").html(exportFormHtml);
            }',
        ];
    }

    protected function filename(): string
    {
        return 'CompleteInvoice_' . date('YmdHis');
    }

    /**
     * Generate the export form HTML.
     */
    private function getExportButton(): string
    {
        if(Auth::user()->can('invoice:export')) {
            $invoiceName = "Complete";
            return view('team.invoice.datatables.export-button' ,compact('invoiceName'))->render();
        }else{
            return '';
        }
    }
}
