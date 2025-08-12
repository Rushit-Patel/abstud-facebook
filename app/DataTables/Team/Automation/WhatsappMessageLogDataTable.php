<?php

namespace App\DataTables\Team\Automation;

use App\Models\WhatsappMessage;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WhatsappMessageLogDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('recipient_details', fn($row) => $this->renderRecipientDetails($row))
            ->addColumn('provider_info', fn($row) => $this->renderProviderInfo($row))
            ->addColumn('message_content', fn($row) => $this->renderMessageContent($row))
            ->addColumn('status', fn($row) => $this->renderStatus($row))
            ->addColumn('sent_at', fn($row) => $this->renderSentAt($row))
            ->addColumn('delivered_at', fn($row) => $this->renderDeliveredAt($row))
            ->addColumn('retry_count', fn($row) => $this->renderRetryCount($row))
            ->filterColumn('recipient_details', function($query, $keyword) {
                $query->where('phone_number', 'like', "%{$keyword}%");
            })
            ->filterColumn('provider_info', function($query, $keyword) {
                // Since we removed provider relationship, this filter does nothing
                // but we keep it to prevent breaking the UI
            })
            ->filterColumn('message_content', function($query, $keyword) {
                $query->where('message_content', 'like', "%{$keyword}%");
            })
            ->filterColumn('status', function($query, $keyword) {
                $query->where('status', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'recipient_details', 'provider_info', 'message_content', 'status', 'sent_at', 'delivered_at', 'retry_count'])
            ->setRowId('id');
    }

    public function query(WhatsappMessage $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('whatsapp_messages.*')
            ->orderBy('created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('whatsapp-message-log-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(5, 'desc') // Order by sent_at desc
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('recipient_details')
                ->title('Recipient')
                ->exportable(false)
                ->printable(true)
                ->width(150)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('provider_info')
                ->title('Provider')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(true),
            Column::computed('message_content')
                ->title('Message')
                ->exportable(false)
                ->printable(true)
                ->width(250)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('status')
                ->title('Status')
                ->exportable(false)
                ->printable(true)
                ->width(100)
                ->addClass('text-center')
                ->searchable(true),
            Column::computed('sent_at')
                ->title('Sent')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('delivered_at')
                ->title('Delivered')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('retry_count')
                ->title('Retries')
                ->exportable(false)
                ->printable(true)
                ->width(80)
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
                'emptyTable' => 'No WhatsApp message logs available',
                'paginate' => [
                    'first' => 'First',
                    'last' => 'Last',
                    'next' => 'Next',
                    'previous' => 'Previous',
                ],
            ],
            'pageLength' => 25,
            'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
            'order' => [[4, 'desc']],
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
        $actions .= '<button onclick="viewMessageDetails(' . $row->id . ')" 
                         class="kt-btn kt-btn-sm kt-btn-light kt-btn-icon" 
                         title="View Details">
                         <i class="ki-filled ki-eye"></i>
                     </button>';

        // Retry button (only for failed messages)
        if ($row->status === 'failed') {
            $actions .= '<button onclick="retryMessage(' . $row->id . ')" 
                             class="kt-btn kt-btn-sm kt-btn-warning kt-btn-icon" 
                             title="Retry Message">
                             <i class="ki-filled ki-refresh"></i>
                         </button>';
        }

        // Resend button (for sent messages that need to be resent)
        if (in_array($row->status, ['sent', 'delivered', 'read'])) {
            $actions .= '<button onclick="resendMessage(' . $row->id . ')" 
                             class="kt-btn kt-btn-sm kt-btn-info kt-btn-icon" 
                             title="Resend Message">
                             <i class="ki-filled ki-share"></i>
                         </button>';
        }
        
        $actions .= '</div>';
        
        return $actions;
    }

    private function renderRecipientDetails($row): string
    {
        $html = '<div class="flex flex-col">';
        $html .= '<div class="font-medium text-mono">' . e($row->phone_number) . '</div>';
        
        if ($row->message_id) {
            $html .= '<div class="text-xs text-muted-foreground">ID: ' . e(Str::limit($row->message_id, 15)) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderProviderInfo($row): string
    {
        return '<span class="text-muted-foreground">Auto Provider</span>';
    }

    private function renderMessageContent($row): string
    {
        $html = '<div class="flex flex-col">';
        
        // Message type badge
        $typeColors = [
            'text' => 'primary',
            'template' => 'success',
            'media' => 'info',
            'document' => 'warning',
        ];
        
        $typeColor = $typeColors[$row->message_type] ?? 'secondary';
        $html .= '<div class="mb-1">';
        $html .= '<span class="kt-badge kt-badge-light kt-badge-' . $typeColor . ' kt-badge-sm">' . ucfirst($row->message_type) . '</span>';
        $html .= '</div>';
        
        // Message content (truncated)
        if ($row->message_content) {
            $html .= '<div class="text-sm">' . e(Str::limit($row->message_content, 80)) . '</div>';
        } else {
            $html .= '<div class="text-sm text-muted-foreground">No content</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderStatus($row): string
    {
        $statusConfig = [
            'pending' => ['color' => 'warning', 'icon' => 'time', 'text' => 'Pending'],
            'sending' => ['color' => 'info', 'icon' => 'loading', 'text' => 'Sending'],
            'sent' => ['color' => 'success', 'icon' => 'check', 'text' => 'Sent'],
            'delivered' => ['color' => 'success', 'icon' => 'double-check', 'text' => 'Delivered'],
            'read' => ['color' => 'primary', 'icon' => 'eye', 'text' => 'Read'],
            'failed' => ['color' => 'danger', 'icon' => 'cross', 'text' => 'Failed'],
            'cancelled' => ['color' => 'secondary', 'icon' => 'stop', 'text' => 'Cancelled'],
        ];

        $config = $statusConfig[$row->status] ?? ['color' => 'secondary', 'icon' => 'question', 'text' => ucfirst($row->status)];
        
        $html = '<span class="kt-badge kt-badge-' . $config['color'] . ' kt-badge-light">';
        $html .= '<i class="ki-filled ki-' . $config['icon'] . ' me-1"></i>';
        $html .= $config['text'];
        $html .= '</span>';

        if ($row->error_message && $row->status === 'failed') {
            $html .= '<div class="text-xs text-danger mt-1" title="' . e($row->error_message) . '">';
            $html .= e(Str::limit($row->error_message, 25));
            $html .= '</div>';
        }
        
        return $html;
    }

    private function renderSentAt($row): string
    {
        if (!$row->sent_at) {
            return '<span class="text-muted-foreground">-</span>';
        }

        return '<div class="text-sm text-secondary-foreground">' . $row->sent_at->format('M d, Y H:i') . '</div>';
    }

    private function renderDeliveredAt($row): string
    {
        if (!$row->delivered_at) {
            return '<span class="text-muted-foreground">-</span>';
        }

        return '<div class="text-sm text-secondary-foreground">' . $row->delivered_at->format('M d, Y H:i') . '</div>';
    }

    private function renderRetryCount($row): string
    {
        if ($row->retry_count === 0) {
            return '<span class="text-muted-foreground">0</span>';
        }
        
        $color = $row->retry_count > 3 ? 'danger' : ($row->retry_count > 1 ? 'warning' : 'info');
        
        return '<span class="kt-badge kt-badge-outline kt-badge-' . $color . '">' . $row->retry_count . '</span>';
    }
}
