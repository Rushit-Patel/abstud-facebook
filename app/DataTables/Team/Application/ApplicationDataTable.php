<?php

namespace App\DataTables\Team\Application;

use App\Helpers\Helpers;
use App\Repositories\Team\RegistrationRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Auth;

class ApplicationDataTable extends DataTable
{
    protected $repository;

    public function __construct(RegistrationRepository $repository)
    {
        $this->repository = $repository;
    }
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('client', fn($row) => $this->renderClient($row))
            ->filterColumn('name', function ($query, $keyword) {
                $query->whereHas('clientLead.client', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('coaching', fn($row) => $this->renderCoaching($row))
            ->filterColumn('coaching', function ($query, $keyword) {
                $query->whereHas('clientLead', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('assign_owner', fn($row) => $this->renderAssignOwner($row))
                ->filterColumn('assign_owner', function ($query, $keyword) {
                    $query->whereHas('assignedOwner', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })

            ->rawColumns(['action', 'client','coaching','assign_owner'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.application.datatables.action', ['id' => $row->id])->render();
    }

    private function renderClient($row): string
    {
        $client = $row->clientLead->client;
        return view('team.application.datatables.client-details', [
            'name' => $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name,
            'mobile_no' => $client->country_code . "" . $client->mobile_no,
            'email_id' => $client->email_id,
        ])->render();
    }

    private function renderCoaching($row): string
    {
        return view('team.application.datatables.country', [
            'client_code' => $row->clientLeadDetails->client_code,
            'country' => $row?->getForeignCountry?->name,
            'branch' => $row->clientLead->getBranch->branch_name,
        ])->render();
    }
    private function renderAssignOwner($row): string
    {
        return view('team.application.datatables.assign-owner', [
            'assign_owner' => $row->assignedOwner?->name ?? 'Unknown',
        ])->render();
    }

    public function query(): QueryBuilder
    {
        $Invoicequery = $this->repository->getRegistration();

        $query = $Invoicequery
            ->with(['clientLead.client', 'clientLead.getBranch'])
            ->whereDoesntHave('clientCoachingReg')
            ->where('purpose', '!=', '2');

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
                $query->whereIn('reg_owner', $ownerParam);
            } else {
                // Existing single value format
                $owner = trim($ownerParam);
                if (!empty($owner)) {
                    if (str_contains($owner, ',')) {
                        $ownerArray = explode(',', $owner);
                        $query->whereIn('reg_owner', $ownerArray);
                    } else {
                        $query->where('reg_owner', $owner);
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
                $query->whereBetween('reg_date', [$startDate, $endDate]);
            }else{
                if (!empty($dateParts[0]) && Carbon::hasFormat(trim($dateParts[0]), 'd/m/Y')) {
                    $startDate = Helpers::parseToYmd(trim($dateParts[0]));
                    $query->whereDate('reg_date', $startDate);
                }
            }
        }

        $query = $query->orderBy('id', 'desc');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('coaching-table')
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
            Column::make('coaching')->title('Coaching')->width(150)->searchable(true),
            Column::make('assign_owner')->title('Assigned To')->width(150)->searchable(true),
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
        return 'PendingCoaching_' . date('YmdHis');
    }
        /**
     * Generate the export form HTML.
     */
    private function getExportButton(): string
    {
        if(Auth::user()->can('coaching:export')) {
            $coachingName = "Pending";
            return view('team.application.datatables.export-button' ,compact('coachingName'))->render();
        }else{
            return '';
        }
    }
}
