<?php

namespace App\DataTables\Team\FollowUp;

use App\Helpers\Helpers;
use App\Models\LeadFollowUp;
use App\Repositories\Team\FollowRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Auth;

class UpcomingFollowUpDataTable extends DataTable
{
    protected $repository;

    public function __construct(FollowRepository $repository)
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
            ->addColumn('followup_date', fn($row) => $this->renderFollowUpDate($row))
                ->filterColumn('followup_date', function ($query, $keyword) {
                    $query->where('followup_date', 'like', "%{$keyword}%");
                })

            ->addColumn('followup_remarks', fn($row) => $this->renderFollowUpRemarks($row))
                ->filterColumn('followup_remarks', function ($query, $keyword) {
                    $query->where('remarks', 'like', "%{$keyword}%");
                })

            ->addColumn('created_by', fn($row) => $this->renderCreatedBy($row))
                ->filterColumn('created_by', function ($query, $keyword) {
                    $query->whereHas('createdByUser', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
            ->rawColumns(['action', 'client', 'followup_date', 'followup_remarks', 'created_by'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.follow-up.datatables.action', ['id' => $row->id])->render();
    }

    private function renderClient($row): string
    {
        $client = $row->clientLead->client;
        return view('team.follow-up.datatables.client-details', [
            'name' => $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name,
            'mobile_no' => $client->country_code . "" . $client->mobile_no,
            'email_id' => $client->email_id,
            'branch' => $row->clientLead->getBranch->branch_name,
        ])->render();
    }

    private function renderFollowUpDate($row): string
    {
        return view('team.follow-up.datatables.followup-date', [
            'followup_date' => Carbon::parse($row->followup_date)->format('d/m/Y'),
            'is_overdue' => Carbon::parse($row->followup_date)->isPast(),
        ])->render();
    }

    private function renderFollowUpRemarks($row): string
    {
        return view('team.follow-up.datatables.followup-remarks', [
            'remarks' => $row->remarks,
        ])->render();
    }

    private function renderCreatedBy($row): string
    {
        return view('team.follow-up.datatables.created-by', [
            'created_by' => $row->createdByUser?->name ?? 'Unknown',
        ])->render();
    }

    public function query(LeadFollowUp $model): QueryBuilder
    {
        $today = Carbon::today()->format('Y-m-d');

        $followUpquery = $this->repository->getFollowUp();

        $query = $followUpquery
            ->with(['clientLead.client', 'clientLead.getBranch', 'createdByUser'])
            ->where('status', '0')
            ->where(function ($q) use ($today) {
                $q->where('followup_date', '>', $today);
            });

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
                $query->whereIn('created_by', $ownerParam);
            } else {
                // Existing single value format
                $owner = trim($ownerParam);
                if (!empty($owner)) {
                    if (str_contains($owner, ',')) {
                        $ownerArray = explode(',', $owner);
                        $query->whereIn('created_by', $ownerArray);
                    } else {
                        $query->where('created_by', $owner);
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
                $query->whereBetween('followup_date', [$startDate, $endDate]);
            }else{
                if (!empty($dateParts[0]) && Carbon::hasFormat(trim($dateParts[0]), 'd/m/Y')) {
                    $startDate = Helpers::parseToYmd(trim($dateParts[0]));
                    $query->whereDate('followup_date', $startDate);
                }
            }
        }

        $query = $query->orderBy('followup_date', 'desc')
                    ->orderBy('id', 'desc');
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('lead-table')
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
            Column::make('followup_date')->title('Follow-up Date')->width(120)->searchable(true),
            Column::make('followup_remarks')->title('Follow-up Remarks')->width(250)->searchable(true),
            Column::make('created_by')->title('Follow-up Owner')->width(130)->searchable(true),
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
        return 'PendingFollowUp_' . date('YmdHis');
    }

    /**
     * Generate the export form HTML.
     */
    private function getExportButton(): string
    {
        if(Auth::user()->can('follow-up:export')) {
            $followUpName = "Upcoming";
            return view('team.follow-up.datatables.export-button' ,compact('followUpName'))->render();
        }else{
            return '';
        }
    }
}
