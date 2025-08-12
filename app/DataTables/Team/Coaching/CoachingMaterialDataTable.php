<?php

namespace App\DataTables\Team\Coaching;

use App\Models\CoachingMaterial;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CoachingMaterialDataTable extends DataTable
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
            ->addColumn('name', fn($row) => $this->renderCoachingMaterial($row))
            ->filterColumn('name', function($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->addColumn('coaching', fn($row) => $this->renderCoaching($row))
            ->filterColumn('coaching', function($query, $keyword) {
                $query->whereHas('getCoaching', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('total_stock', fn($row) => $this->renderTotalStock($row))
            ->addColumn('available_stock', fn($row) => $this->renderAvailableStock($row))


            ->addColumn('status', fn($row) => $this->renderStatus($row))
            ->addColumn('stock', fn($row) => $this->renderStock($row))
            ->rawColumns(['action','stock', 'name', 'coaching','status','total_stock'])
            ->setRowId('id');
    }

    public function query(CoachingMaterial $model): QueryBuilder
    {
        return $model->newQuery(); // You can add ->limit(22) if needed
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('coaching-material-table')
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
            Column::computed('name')->title('Name')->exportable(false)->printable(true)->width(200)->addClass('text-start')->searchable(true),
            Column::computed('coaching')->title('Coaching')->exportable(false)->printable(true)->width(150)->addClass('text-start')->searchable(true),
            Column::computed('total_stock')->title('Total Stock')->exportable(false)->printable(true)->width(150)->addClass('text-start')->searchable(true),
            Column::computed('available_stock')->title('Available Stock')->exportable(false)->printable(true)->width(150)->addClass('text-start')->searchable(true),
            Column::computed('stock')->title('Stock')->exportable(false)->printable(true)->width(150)->addClass('text-start')->searchable(true),
            Column::make('status')->width(130),
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
        return 'CoachingMaterial_' . date('YmdHis');
    }

    // Dummy render methods for customization
    protected function renderAction($row): string
    {
        $button = '<a href="'.route('team.settings.coaching-material.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="ki-filled ki-notepad-edit text-2xl me-2"></i></a>';
        $deleteBtn = '
            <button type="delete" data-kt-modal-toggle="#delete_modal" data-form_action="' . route('team.settings.coaching-material.destroy', $row->id) . '">
                <i class="ki-filled ki-trash text-2xl"></i>
            </button>
        ';
        return $button. ' ' .$deleteBtn;
    }

    protected function renderStock($row): string
    {
        $stockButtonAdd = '<a href="'.route('team.settings.coaching-material.stock', $row->id).'" class="btn btn-sm btn-primary me-2">
            <i class="ki-filled ki-abstract-10 me-1"></i> Stock
        </a>';

        return $stockButtonAdd;
    }

    protected function renderCoachingMaterial($row): string
    {
        return  e($row->name);
    }

    protected function renderCoaching($row): string
    {
        return  e($row->getCoaching->name);
    }

    protected function renderStatus($row): string
    {
        $class = $this->getStatusBadgeClass($row->status);
        $label = $row->status == '1' ? 'Active' : 'Inactive';
        return '<span class="badge '.$class.'">'.$label.'</span>';
    }

    protected function renderTotalStock($row): string
    {
        $totalStock = $row->TotalStocks->sum('stock');
        return '<span class="text-sm font-medium">' . $totalStock . '</span>';
    }


protected function renderAvailableStock($row): string
{
    // Total stock
    $total = $row->TotalStocks->sum('stock');

    // Used stock (materials issued to students)
    $used = $row->issuedMaterials()->count(); // or ->sum('quantity') if you track quantity

    $available = $total - $used;

    return $available;
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
