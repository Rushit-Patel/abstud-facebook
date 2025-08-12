<?php

namespace App\DataTables\Team\Coaching;

use App\Helpers\Helpers;
use App\Models\Batch;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\MockTestRepository;
use App\Repositories\Team\RegistrationRepository;
use App\Repositories\Team\InvoiceRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Auth;

class MockTestDataTable extends DataTable
{
    protected $repository;

    public function __construct(MockTestRepository $repository)
    {
        $this->repository = $repository;
    }
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('mocktest_details', fn($row) => $this->renderMockTest($row))
            ->filterColumn('mocktest_details', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })

           ->addColumn('coaching_branch', fn($row) => $this->renderCoachingBranch($row))
            ->filterColumn('coaching_branch', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    // Coaching name search
                    $q->whereHas('getCoaching', function ($qc) use ($keyword) {
                        $qc->where('name', 'like', "%{$keyword}%");
                    });

                    // Branch name search
                    $q->orWhereHas('getBranch', function ($qb) use ($keyword) {
                        $qb->where('name', 'like', "%{$keyword}%");
                    });
                });
            })
            ->addColumn('batch', fn($row) => $this->renderBatch($row))
                ->filterColumn('batch', function ($query, $keyword) {
                    $query->whereHas('getBatch', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
            ->addColumn('add_client', fn($row) => $this->renderAddClient($row))

            ->rawColumns(['action','mocktest_details','coaching_branch','batch','add_client'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.mock-test.datatables.action', ['id' => $row->id])->render();
    }

    private function renderAddClient($row): string
    {
        return view('team.mock-test.datatables.add-client', ['id' => $row->id])->render();
    }

    private function renderMockTest($row): string
    {
        return view('team.mock-test.datatables.mock-test-details', [
            'name' => $row->name,
            'date_time' => Helpers::parseToDmy($row->mock_test_date)  .'-'. $row->mock_test_time,
            'status' => $row->status,
        ])->render();
    }

    private function renderCoachingBranch($row): string
    {
        return view('team.mock-test.datatables.coaching-branch', [
            'coaching' => $row?->getCoaching?->name,
            'branch' => $row->getBranch->branch_name,
        ])->render();
    }
    private function renderBatch($row): string
    {
        $batchIds = explode(',', $row->batch_id);

        $batches = Batch::whereIn('id', $batchIds)->get();

        return view('team.mock-test.datatables.batch', [
            'batches' => $batches,
        ])->render();
    }


    public function query(): QueryBuilder
    {
        $mockTestquery = $this->repository->getMockTest();

        $query = $mockTestquery;

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
                $query->whereBetween('mock_test_date', [$startDate, $endDate]);
            }else{
                if (!empty($dateParts[0]) && Carbon::hasFormat(trim($dateParts[0]), 'd/m/Y')) {
                    $startDate = Helpers::parseToYmd(trim($dateParts[0]));
                    $query->whereDate('mock_test_date', $startDate);
                }
            }
        }

        $query = $query->orderBy('id', 'desc');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('mock-test-table')
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
            Column::make('mocktest_details')->title('Mock-test Details')->width(150)->searchable(true),
            Column::make('coaching_branch')->title('Coaching & Branch')->width(150)->searchable(true),
            Column::make('batch')->title('Batch')->width(150)->searchable(true),
            Column::make('add_client')->title('View Client')->width(150)->searchable(true),
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
        return 'RunningCoaching_' . date('YmdHis');
    }
    private function getExportButton(): string
    {
        if(Auth::user()->can('coaching:export')) {
            $coachingName = "Running";
            return view('team.coaching.datatables.export-button' ,compact('coachingName'))->render();
        }else{
            return '';
        }
    }
}
