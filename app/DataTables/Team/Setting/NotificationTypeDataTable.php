<?php

namespace App\DataTables\Team\Setting;

use App\Models\TeamNotificationType;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NotificationTypeDataTable extends DataTable
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
            ->addColumn('title', fn($row) => $this->renderNotificationType($row))
            ->filterColumn('title', function($query, $keyword) {
                $query->where('title', 'like', "%{$keyword}%");
            })
            ->addColumn('is_active', fn($row) => $this->renderStatus($row))
            ->rawColumns(['action', 'title', 'is_active'])
            ->setRowId('id');
    }

    public function query(TeamNotificationType $model): QueryBuilder
    {
        return $model->newQuery(); // You can add ->limit(22) if needed
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('notification-table')
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
            Column::computed('title')->title('NotificationType')->exportable(false)->printable(true)->width(200)->addClass('text-start')->searchable(true),
            Column::make('is_active')->width(130),
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
        return 'NotificationType_' . date('YmdHis');
    }

    // Dummy render methods for customization
    protected function renderAction($row): string
    {
        $button = '<a href="'.route('team.settings.notification-type.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="ki-filled ki-notepad-edit text-2xl me-2"></i></a>';
        $deleteBtn = '
            <button type="delete" data-kt-modal-toggle="#delete_modal" data-form_action="' . route('team.settings.notification-type.destroy', $row->id) . '">
                <i class="ki-filled ki-trash text-2xl"></i>
            </button>
        ';
        return $button. ' ' .$deleteBtn;
    }

    protected function renderNotificationType($row): string
    {
        return  e($row->title);
    }

    protected function renderStatus($row): string
    {
        $class = $this->getStatusBadgeClass($row->is_active);
        $label = $row->is_active == '1' ? 'Active' : 'Inactive';
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
