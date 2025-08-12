<?php

namespace App\DataTables\Team\Coaching;

use App\Helpers\Helpers;
use App\Repositories\Team\ExamBookingRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Auth;

class ExamBookingDataTable extends DataTable
{
    protected $repository;

    public function __construct(ExamBookingRepository $repository)
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
            ->addColumn('exam_date', fn($row) => $this->renderExamDate($row))
                ->filterColumn('exam_date', function ($query, $keyword) {
                    $query->where('exam_date', 'like', "%{$keyword}%");
                })

            ->addColumn('result_data', fn($row) => $this->renderResualtData($row))
                ->filterColumn('result_data', function ($query, $keyword) {
                    $query->where('result_date', 'like', "%{$keyword}%");
                })

            ->rawColumns(['action', 'client', 'exam_date', 'result_data'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.exam-booking.datatables.action', ['id' => $row->id])->render();
    }

    private function renderClient($row): string
    {
        $client = $row->clientLead->client;
        return view('team.exam-booking.datatables.client-details', [
            'name' => $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name,
            'mobile_no' => $client->country_code . "" . $client->mobile_no,
            'email_id' => $client->email_id,
            'branch' => $row->clientLead->getBranch->branch_name,
        ])->render();
    }

    private function renderExamDate($row): string
    {
        return view('team.exam-booking.datatables.date', [
            'date' => Carbon::parse($row->exam_date)->format('d/m/Y'),
            'is_overdue' => Carbon::parse($row->exam_date)->isPast(),
        ])->render();
    }

    private function renderResualtData($row): string
    {
        $resultsData = $row->results->map(function ($result) {
            return optional($result->modual)->name . ' - ' . $result->score;
        });

        return view('team.exam-booking.datatables.result-score', [
            'results_data' => $resultsData,
            'result_date' => Helpers::parseToDmy($row->result_date)
        ])->render();
    }



    public function query(): QueryBuilder
    {
        $Invoicequery = $this->repository->getExamBooking();

        $query = $Invoicequery
            ->with(['clientLead.client', 'clientLead.getBranch']);

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

        if (request()->has('coaching')) {
            $coachings = request()->get('coaching');

            // Handle comma-separated string or array
            if (!is_array($coachings)) {
                $coachings = array_map('trim', explode(',', $coachings));
            }

            if (!empty($coachings)) {
                $query->whereHas('clientCoaching', function ($q) use ($coachings) {
                    $q->whereIn('coaching_id', $coachings);
                });
            }else{
                $coaching = trim($coachings);
                $query->whereHas('clientCoaching', function ($q) use ($coaching) {
                    $q->where('coaching_id', $coaching);
                });
            }
        }

        if (request()->has('batch')) {
            $batchs = request()->get('batch');

            // Handle comma-separated string or array
            if (!is_array($batchs)) {
                $batchs = array_map('trim', explode(',', $batchs));
            }

            if (!empty($batchs)) {
                $query->whereHas('clientCoaching', function ($q) use ($batchs) {
                    $q->whereIn('batch_id', $batchs);
                });
            }else{
                $batch = trim($batchs);
                $query->whereHas('clientCoaching', function ($q) use ($batch) {
                    $q->where('batch_id', $batch);
                });
            }
        }


        if (request()->has('exam_date')) {
            $exam_dateRange = request()->get('exam_date');
            $exam_dateParts = explode('to', $exam_dateRange);
            // change date formate d/m/Y to Y-m-d
            if (count($exam_dateParts) == 2) {
                $startDateExam = Helpers::parseToYmd(trim($exam_dateParts[0]));
                $endDateExam = Helpers::parseToYmd(trim($exam_dateParts[1]));
                $query->whereBetween('exam_date', [$startDateExam, $endDateExam]);
            }else{
                if (!empty($exam_dateParts[0]) && Carbon::hasFormat(trim($exam_dateParts[0]), 'd/m/Y')) {
                    $startDateExam = Helpers::parseToYmd(trim($exam_dateParts[0]));
                    $query->whereDate('exam_date', $startDateExam);
                }
            }
        }

        if (request()->has('result_date')) {
            $result_dateRange = request()->get('result_date');
            $result_dateParts = explode('to', $result_dateRange);
            // change date formate d/m/Y to Y-m-d
            if (count($result_dateParts) == 2) {
                $startDateResult = Helpers::parseToYmd(trim($result_dateParts[0]));
                $endDateResult = Helpers::parseToYmd(trim($result_dateParts[1]));
                $query->whereBetween('result_date', [$startDateResult, $endDateResult]);
            }else{
                if (!empty($result_dateParts[0]) && Carbon::hasFormat(trim($result_dateParts[0]), 'd/m/Y')) {
                    $startDateResult = Helpers::parseToYmd(trim($result_dateParts[0]));
                    $query->whereDate('result_date', $startDateResult);
                }
            }
        }

        $query = $query->orderBy('exam_date', 'desc')
                    ->orderBy('id', 'desc');
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('exam-booking-table')
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
            Column::make('exam_date')->title('Exam Date')->width(120)->searchable(true),
            Column::make('result_data')->title('Result Data')->width(120)->searchable(true),
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
        return 'ExamBooking_' . date('YmdHis');
    }

    /**
     * Generate the export form HTML.
     */
    private function getExportButton(): string
    {
        if(Auth::user()->can('coaching:export')) {
            return view('team.exam-booking.datatables.export-button')->render();
        }else{
            return '';
        }
    }
}
