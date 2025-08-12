<?php

namespace App\DataTables\Team\Automation;

use App\Models\WhatsappCampaign;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WhatsappCampaignDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('campaign_name', fn($row) => $this->renderCampaignName($row))
            ->addColumn('campaign_type', fn($row) => $this->renderCampaignType($row))
            ->addColumn('execution_type', fn($row) => $this->renderExecutionType($row))
            ->addColumn('status', fn($row) => $this->renderStatus($row))
            ->addColumn('rules_count', fn($row) => $this->renderRulesCount($row))
            ->addColumn('recipients_count', fn($row) => $this->renderRecipientsCount($row))
            ->addColumn('success_rate', fn($row) => $this->renderSuccessRate($row))
            ->addColumn('created_at', fn($row) => $this->renderCreatedAt($row))
            ->filterColumn('campaign_name', function($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->filterColumn('campaign_type', function($query, $keyword) {
                $query->where('campaign_type', 'like', "%{$keyword}%");
            })
            ->filterColumn('execution_type', function($query, $keyword) {
                $query->where('execution_type', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'campaign_name', 'campaign_type', 'execution_type', 'status', 'rules_count', 'recipients_count', 'success_rate', 'created_at'])
            ->setRowId('id');
    }

    public function query(WhatsappCampaign $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['rules'])
            ->select('whatsapp_campaigns.*')
            ->orderBy('created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('whatsapp-campaign-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(8, 'desc') // Order by created_at desc
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('campaign_name')
                ->title('Campaign Name')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('campaign_type')
                ->title('Type')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(true),
            Column::computed('execution_type')
                ->title('Execution')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(true),
            Column::computed('status')
                ->title('Status')
                ->exportable(false)
                ->printable(true)
                ->width(100)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('rules_count')
                ->title('Rules')
                ->exportable(false)
                ->printable(true)
                ->width(80)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('recipients_count')
                ->title('Recipients')
                ->exportable(false)
                ->printable(true)
                ->width(100)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('success_rate')
                ->title('Success Rate')
                ->exportable(false)
                ->printable(true)
                ->width(120)
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
                ->width(150)
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
                'emptyTable' => 'No WhatsApp campaigns available',
                'paginate' => [
                    'first' => 'First',
                    'last' => 'Last',
                    'next' => 'Next',
                    'previous' => 'Previous',
                ],
            ],
            'pageLength' => 10,
            'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
            'order' => [[8, 'desc']],
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
        $actions .= '<a href="' . route('team.automation.whatsapp.campaigns.show', $row->id) . '" 
                         class="kt-btn kt-btn-sm kt-btn-light kt-btn-icon" 
                         title="View">
                         <i class="ki-filled ki-eye"></i>
                     </a>';

        // Edit button
        $actions .= '<a href="' . route('team.automation.whatsapp.campaigns.edit', $row->id) . '" 
                         class="kt-btn kt-btn-sm kt-btn-primary kt-btn-icon" 
                         title="Edit">
                         <i class="ki-filled ki-pencil"></i>
                     </a>';

        // Delete button
        $actions .= '<button data-kt-modal-toggle="#delete_modal" data-form_action="'.route('team.automation.whatsapp.campaigns.destroy', $row->id).'"
                    class="kt-btn kt-btn-sm kt-btn-destructive kt-btn-icon" 
                    title="Delete">
                    <i class="ki-filled ki-trash"></i>
                </button>';
        
        $actions .= '</div>';
        
        return $actions;
    }

    private function renderCampaignName($row): string
    {
        $html = '<div class="flex flex-col">';
        $html .= '<div class="font-medium text-mono">' . e($row->name) . '</div>';
        if ($row->description) {
            $html .= '<div class="text-xs text-muted-foreground mt-1">' . e(Str::limit($row->description, 60)) . '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }

    private function renderCampaignType($row): string
    {
        $colors = [
            'bulk' => 'kt-badge-primary',
            'automation' => 'kt-badge-success',
            'follow_up' => 'kt-badge-warning',
            'reminder' => 'kt-badge-info',
        ];
        
        $color = $colors[$row->campaign_type] ?? 'kt-badge-secondary';
        $displayType = ucwords(str_replace('_', ' ', $row->campaign_type));
        
        return '<span class="kt-badge kt-badge-outline ' . $color . '">' . e($displayType) . '</span>';
    }

    private function renderExecutionType($row): string
    {
        $colors = [
            'one_time' => 'kt-badge-info',
            'automation' => 'kt-badge-success',
        ];
        
        $color = $colors[$row->execution_type] ?? 'kt-badge-secondary';
        $displayType = ucwords(str_replace('_', ' ', $row->execution_type));
        
        return '<span class="kt-badge kt-badge-outline ' . $color . '">' . e($displayType) . '</span>';
    }

    private function renderStatus($row): string
    {
        if ($row->is_active) {
            return '<span class="kt-badge kt-badge-success kt-badge-light">Active</span>';
        }
        
        return '<span class="kt-badge kt-badge-secondary kt-badge-light">Paused</span>';
    }

    private function renderRulesCount($row): string
    {
        $count = $row->rules->count();
        
        if ($count === 0) {
            return '<span class="text-muted-foreground">0</span>';
        }
        
        return '<span class="kt-badge kt-badge-outline kt-badge-primary">' . $count . '</span>';
    }

    private function renderRecipientsCount($row): string
    {
        $count = $row->total_recipients ?? 0;
        $sent = $row->messages_sent ?? 0;
        
        if ($count === 0) {
            return '<span class="text-muted-foreground">0</span>';
        }
        
        $html = '<div class="flex flex-col">';
        $html .= '<span class="font-medium">' . number_format($count) . '</span>';
        if ($sent > 0) {
            $html .= '<div class="text-xs text-muted-foreground">' . number_format($sent) . ' sent</div>';
        }
        $html .= '</div>';
        
        return $html;
    }

    private function renderSuccessRate($row): string
    {
        $stats = $row->getStats();
        $successRate = $stats['success_rate'] ?? 0;
        
        $html = '<div class="flex items-center gap-2">';
        $html .= '<div class="flex-1 bg-gray-200 rounded-full h-2 w-16">';
        $html .= '<div class="bg-success h-2 rounded-full" style="width: ' . $successRate . '%"></div>';
        $html .= '</div>';
        $html .= '<span class="text-sm font-medium">' . $successRate . '%</span>';
        $html .= '</div>';
        
        return $html;
    }

    private function renderCreatedAt($row): string
    {
        return '<div class="text-sm text-secondary-foreground">' . $row->created_at->format('M d, Y') . '</div>';
    }
}
