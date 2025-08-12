<?php

namespace App\DataTables\Team\Setting;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EmailTemplateDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('template_name', fn($row) => $this->renderTemplateName($row))
            ->addColumn('subject', fn($row) => $this->renderSubject($row))
            ->addColumn('category', fn($row) => $this->renderCategory($row))
            ->addColumn('type', fn($row) => $this->renderType($row))
            ->addColumn('status', fn($row) => $this->renderStatus($row))
            ->addColumn('created_at', fn($row) => $this->renderCreatedAt($row))
            ->filterColumn('template_name', function($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->filterColumn('subject', function($query, $keyword) {
                $query->where('subject', 'like', "%{$keyword}%");
            })
            ->filterColumn('category', function($query, $keyword) {
                $query->where('category', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'template_name', 'subject', 'category', 'type', 'status','created_at'])
            ->setRowId('id');
    }

    public function query(EmailTemplate $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('email_templates.*')
            ->orderBy('created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('email-template-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(6, 'desc') // Order by created_at desc
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('template_name')
                ->title('Template Name')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('subject')
                ->title('Subject')
                ->exportable(false)
                ->printable(true)
                ->width(250)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('category')
                ->title('Category')
                ->exportable(false)
                ->printable(true)
                ->width(150)
                ->addClass('text-center')
                ->searchable(true),
            Column::computed('type')
                ->title('Type')
                ->exportable(false)
                ->printable(true)
                ->width(100)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('status')
                ->title('Status')
                ->exportable(false)
                ->printable(true)
                ->width(100)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('created_at')
                ->title('Created')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(120)
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
                'infoEmpty' => 'No entries found',
                'infoFiltered' => '(filtered from _MAX_ total entries)',
                'zeroRecords' => 'No matching records found',
                'emptyTable' => 'No email templates available',
                'paginate' => [
                    'first' => 'First',
                    'last' => 'Last',
                    'next' => 'Next',
                    'previous' => 'Previous',
                ],
            ],
            'pageLength' => 10,
            'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
            'order' => [[6, 'desc']],
            'responsive' => true,
            'autoWidth' => false,
            'searching' => true,
            'processing' => true,
            'serverSide' => true,
            'stateSave' => true,
        ];
    }

    private function renderAction($row): string
    {
        $actions = '<div class="flex items-center justify-center gap-1">';
        
        // View button
        $actions .= '<a href="' . route('team.settings.email-templates.show', $row->id) . '" 
                         class="kt-btn kt-btn-sm kt-btn-secondary kt-btn-icon" 
                         title="View">
                         <i class="ki-filled ki-eye"></i>
                     </a>';
        
        // Edit button
        $actions .= '<a href="' . route('team.settings.email-templates.edit', $row->id) . '" 
                         class="kt-btn kt-btn-sm kt-btn-primary kt-btn-icon" 
                         title="Edit">
                         <i class="ki-filled ki-pencil"></i>
                     </a>';
        
        // Duplicate button
        $actions .= '<button onclick="duplicateTemplate(' . $row->id . ')" 
                         class="kt-btn kt-btn-sm kt-btn-warning kt-btn-icon" 
                         title="Duplicate">
                         <i class="ki-filled ki-copy"></i>
                     </button>';
        
        // Toggle status button
        $statusIcon = $row->is_active ? 'ki-toggle-on' : 'ki-toggle-off';
        $statusClass = $row->is_active ? 'kt-btn-success' : 'kt-btn-secondary';
        $statusTitle = $row->is_active ? 'Deactivate' : 'Activate';
        
        $actions .= '<button onclick="toggleStatus(' . $row->id . ')" 
                         class="kt-btn kt-btn-sm ' . $statusClass . ' kt-btn-icon" 
                         title="' . $statusTitle . '">
                         <i class="ki-filled ' . $statusIcon . '"></i>
                     </button>';
        
        // Delete button (only for custom templates)
        if ($row->canBeDeleted()) {
            $actions .= '<button onclick="deleteTemplate(' . $row->id . ', \'' . addslashes($row->name) . '\')" 
                             class="kt-btn kt-btn-sm kt-btn-danger kt-btn-icon" 
                             title="Delete">
                             <i class="ki-filled ki-trash"></i>
                         </button>';
        }
        
        $actions .= '</div>';
        
        return $actions;
    }

    private function renderTemplateName($row): string
    {
        $html = '<div class="flex flex-col">';
        $html .= '<div class="font-medium text-mono">' . e($row->name) . '</div>';
        $html .= '<div class="text-sm text-secondary-foreground">' . e($row->slug) . '</div>';
        if ($row->description) {
            $html .= '<div class="text-xs text-muted-foreground mt-1">' . e(Str::limit($row->description, 60)) . '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }

    private function renderSubject($row): string
    {
        return '<span class="text-sm font-medium" title="' . e($row->subject) . '">' . e(Str::limit($row->subject, 40)) . '</span>';
    }

    private function renderCategory($row): string
    {
        if (!$row->category) {
            return '<span class="kt-badge kt-badge-outline kt-badge-secondary">Uncategorized</span>';
        }
        
        $colors = [
            'user' => 'kt-badge-primary',
            'system' => 'kt-badge-info',
            'notification' => 'kt-badge-warning',
            'marketing' => 'kt-badge-success',
            'transactional' => 'kt-badge-danger',
        ];
        
        $color = $colors[strtolower($row->category)] ?? 'kt-badge-secondary';
        
        return '<span class="kt-badge kt-badge-outline ' . $color . '">' . e(ucfirst($row->category)) . '</span>';
    }

    private function renderType($row): string
    {
        if ($row->is_system) {
            return '<span class="kt-badge kt-badge-outline kt-badge-info">System</span>';
        }
        
        return '<span class="kt-badge kt-badge-outline kt-badge-primary">Custom</span>';
    }

    private function renderStatus($row): string
    {
        if ($row->is_active) {
            return '<span class="kt-badge kt-badge-success kt-badge-light">Active</span>';
        }
        
        return '<span class="kt-badge kt-badge-secondary kt-badge-light">Inactive</span>';
    }

    private function renderCreatedAt($row): string
    {
        return '<div class="text-sm text-secondary-foreground">' . $row->created_at->format('M d, Y') . '</div>';
    }
}
