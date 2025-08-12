<?php

namespace App\DataTables\Team\Setting;

use App\Models\EducationStream;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EducationStreamDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
   public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('education_stream', fn($row) => $this->renderEducationStream($row))
            ->filterColumn('education_stream', function($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->addColumn('education_levels', fn($row) => $this->renderEducationLevels($row))
            ->addColumn('status', fn($row) => $this->renderStatus($row))
            ->rawColumns(['action', 'education_stream', 'education_levels', 'status'])
            ->setRowId('id');
    }

    public function query(EducationStream $model): QueryBuilder
    {
        return $model->newQuery(); // You can add ->limit(22) if needed
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('education-stream-table')
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
            Column::computed('education_stream')->title('Education Stream')->exportable(false)->printable(true)->width(200)->addClass('text-start')->searchable(true),
            Column::computed('education_levels')->title('Education Levels')->exportable(false)->printable(true)->width(250)->addClass('text-start')->searchable(false),
            Column::make('status')->width(130),
            Column::computed('action')
                // ->title('Actions')
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
            'dom' => '<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-2"<"dt-length flex items-center gap-2 order-2 md:order-1"><"dt-search ml-auto"f>>rt<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-4 border-t border-gray-200"<"kt-datatable-length text-secondary-foreground text-sm font-medium"l><"dt-paging flex items-center space-x-1 text-secondary-foreground text-sm font-medium"ip>>',
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
        ];
    }

    protected function filename(): string
    {
        return 'EducationStream_' . date('YmdHis');
    }

    // Dummy render methods for customization
    protected function renderAction($row): string
    {
        $button = '<a href="'.route('team.settings.education-stream.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="ki-filled ki-notepad-edit text-2xl me-2"></i></a>';
        $deleteBtn = '
            <button type="delete" data-kt-modal-toggle="#delete_modal" data-form_action="' . route('team.settings.education-stream.destroy', $row->id) . '">
                <i class="ki-filled ki-trash text-2xl"></i>
            </button>
        ';
        return $button. ' ' .$deleteBtn;
    }

    protected function renderEducationStream($row): string
    {
        return  e($row->name);
    }

    protected function renderEducationLevels($row): string
    {
        if (empty($row->education_level_id)) {
            return '<span class="text-gray-400">No levels assigned</span>';
        }

        $levelIds = explode(',', $row->education_level_id);
        $educationLevels = \App\Models\EducationLevel::whereIn('id', $levelIds)->pluck('name')->toArray();
        
        if (empty($educationLevels)) {
            return '<span class="text-gray-400">No levels found</span>';
        }

        $badges = '';
        foreach ($educationLevels as $level) {
            $badges .= '<span class="kt-badge kt-badge-secondary me-1 mb-1">' . e($level) . '</span>';
        }
        
        return $badges;
    }

    protected function renderStatus($row): string
    {
        $class = $this->getStatusBadgeClass($row->status);
        $label = $row->status == '1' ? 'Active' : 'Inactive';
        return '<span class="badge '.$class.'">'.$label.'</span>';
    }

    private function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            '1' => 'bg-green-100 text-white-800',
            '0' => 'bg-red-100 text-white-800',
            default => 'bg-gray-200 text-gray-900'
        };
    }
}
