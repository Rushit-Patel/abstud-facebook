<?php

namespace App\DataTables\Team\Coaching;

use App\Helpers\Helpers;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\RegistrationRepository;
use App\Repositories\Team\InvoiceRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Auth;

class AttendanceDataTable extends DataTable
{
    protected $repository;

    public function __construct(CoachingRepository $repository)
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
                $query->whereHas('getCoaching', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('attendance', fn($row) => $this->renderAttendance($row))
            ->addColumn('assign_owner', fn($row) => $this->renderAssignOwner($row))
                ->filterColumn('assign_owner', function ($query, $keyword) {
                    $query->whereHas('assignedOwner', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })

            ->rawColumns(['action', 'client','coaching','attendance','assign_owner'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.attendance.datatables.action', ['id' => $row->id])->render();
    }

    private function renderClient($row): string
    {
        $client = $row->clientLead->client;
        return view('team.coaching.datatables.client-details', [
            'name' => $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name,
            'mobile_no' => $client->country_code . "" . $client->mobile_no,
            'email_id' => $client->email_id,
        ])->render();
    }

    private function renderCoaching($row): string
    {
        return view('team.coaching.datatables.coaching', [
            'client_code' => $row->clientLeadDetails->client_code,
            'coaching' => $row?->getCoaching?->name,
            'branch' => $row->clientLead->getBranch->branch_name,
        ])->render();
    }
    private function renderAssignOwner($row): string
    {
        return view('team.coaching.datatables.assign-owner', [
            'assign_owner' => $row->getFaculty?->name ?? 'Unknown',
        ])->render();
    }

    private function renderAttendance($row): string
    {
        return view('team.attendance.datatables.attendance', [
            'coaching_id' => $row->id ?? 'Unknown',
        ])->render();
    }

    public function query(): QueryBuilder
    {
        $Invoicequery = $this->repository->getCoaching();

        $query = $Invoicequery
            ->with(['clientLead.client', 'clientLead.getBranch'])
            ->where('is_complete_coaching', 0)
            ->where('is_drop_coaching', 0);

        if (request()->has('branch')) {
            $branches = request()->get('branch');

            // Handle comma-separated string or array
            if (!is_array($branches)) {
                $branches = array_map('trim', explode(',', $branches));
            }

            if (!empty($branches)) {
                $query->whereIn('branch_id', $branches);
            }else{
                $branch = trim($branches);
                $query->where('branch_id', $branch);
            }
        }

        if (request()->has('coaching')) {
            $coachings = request()->get('coaching');

            // Handle comma-separated string or array
            if (!is_array($coachings)) {
                $coachings = array_map('trim', explode(',', $coachings));
            }

            if (!empty($coachings)) {
                $query->whereIn('coaching_id', $coachings);
            }else{
                $coaching = trim($coachings);
                $query->where('coaching_id', $coaching);
            }
        }

        if (request()->has('batch_id')) {
            $batch_ids = request()->get('batch_id');

            // Handle comma-separated string or array
            if (!is_array($batch_ids)) {
                $batch_ids = array_map('trim', explode(',', $batch_ids));
            }

            if (!empty($batch_ids)) {
                $query->whereIn('batch_id', $batch_ids);
            }else{
                $batch_id = trim($batch_ids);
                $query->where('batch_id', $batch_id);
            }
        }

        $query = $query->orderBy('id', 'desc');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('attendance-table')
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
            Column::make('assign_owner')->title('Faculty')->width(150)->searchable(true),
            Column::make('attendance')->title('Attendance')->width(150)->searchable(true),
            // Column::computed('action')
            //     ->title('Actions')
            //     ->exportable(false)
            //     ->printable(false)
            //     ->width(80)
            //     ->addClass('text-center')
            //     ->orderable(false)
            //     ->searchable(false),
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
        return 'Attendance_' . date('YmdHis');
    }
    private function getExportButton(): string
    {
        if(Auth::user()->can('coaching:export')) {
            return view('team.attendance.datatables.export-button')->render();
        }else{
            return '';
        }
    }
}
