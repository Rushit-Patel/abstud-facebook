<?php

namespace App\DataTables\Team\Automation;

use App\Models\EmailAutomationLog;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EmailAutomationLogDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('recipient_details', fn($row) => $this->renderRecipientDetails($row))
            ->addColumn('campaign_info', fn($row) => $this->renderCampaignInfo($row))
            ->addColumn('email_subject', fn($row) => $this->renderEmailSubject($row))
            ->addColumn('status', fn($row) => $this->renderStatus($row))
            ->addColumn('scheduled_at', fn($row) => $this->renderScheduledAt($row))
            ->addColumn('sent_at', fn($row) => $this->renderSentAt($row))
            ->addColumn('retry_count', fn($row) => $this->renderRetryCount($row))
            ->filterColumn('recipient_details', function($query, $keyword) {
                $query->where('recipient_email', 'like', "%{$keyword}%")
                      ->orWhereHas('clientLead', function($q) use ($keyword) {
                          $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                      });
            })
            ->filterColumn('campaign_info', function($query, $keyword) {
                $query->whereHas('campaign', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('email_subject', function($query, $keyword) {
                $query->where('subject', 'like', "%{$keyword}%");
            })
            ->filterColumn('status', function($query, $keyword) {
                $query->where('status', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'recipient_details', 'campaign_info', 'email_subject', 'status', 'scheduled_at', 'sent_at', 'retry_count'])
            ->setRowId('id');
    }

    public function query(EmailAutomationLog $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['clientLead.client', 'campaign', 'emailTemplate'])
            ->select('email_automation_logs.*')
            ->orderBy('created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('email-automation-log-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(5, 'desc') // Order by scheduled_at desc
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('recipient_details')
                ->title('Recipient')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('campaign_info')
                ->title('Campaign')
                ->exportable(false)
                ->printable(true)
                ->width(180)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('email_subject')
                ->title('Subject')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('status')
                ->title('Status')
                ->exportable(false)
                ->printable(true)
                ->width(100)
                ->addClass('text-center')
                ->searchable(true),
            Column::computed('scheduled_at')
                ->title('Scheduled')
                ->exportable(false)
                ->printable(true)
                ->width(120)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('sent_at')
                ->title('Sent')
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
                'emptyTable' => 'No email logs available',
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
        $actions .= '<button onclick="viewLogDetails(' . $row->id . ')" 
                         class="kt-btn kt-btn-sm kt-btn-light kt-btn-icon" 
                         title="View Details">
                         <i class="ki-filled ki-eye"></i>
                     </button>';

        // Retry button (only for failed emails)
        if ($row->status === 'failed') {
            $actions .= '<button onclick="retryEmail(' . $row->id . ')" 
                             class="kt-btn kt-btn-sm kt-btn-warning kt-btn-icon" 
                             title="Retry Email">
                             <i class="ki-filled ki-refresh"></i>
                         </button>';
        }

        // Performance button (only for sent emails)
        if ($row->status === 'sent') {
            $actions .= '<button onclick="viewPerformance(' . $row->id . ')" 
                             class="kt-btn kt-btn-sm kt-btn-info kt-btn-icon" 
                             title="View Performance">
                             <i class="ki-filled ki-chart-pie-simple"></i>
                         </button>';
        }
        
        $actions .= '</div>';
        
        return $actions;
    }

    private function renderRecipientDetails($row): string
    {
        $html = '<div class="flex flex-col">';
        
        if ($row->clientLead) {
            $html .= '<div class="font-medium text-mono">' . e($row->clientLead->name) . '</div>';
            $html .= '<div class="text-sm text-muted-foreground">' . e($row->recipient_email) . '</div>';
            if ($row->clientLead->phone) {
                $html .= '<div class="text-xs text-muted-foreground">' . e($row->clientLead->phone) . '</div>';
            }
        } else {
            $html .= '<div class="font-medium text-mono">' . e($row->recipient_email) . '</div>';
            $html .= '<div class="text-xs text-muted-foreground">Lead not found</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderCampaignInfo($row): string
    {
        $html = '<div class="flex flex-col">';
        
        if ($row->campaign) {
            $html .= '<div class="font-medium text-primary">' . e($row->campaign->name) . '</div>';
            if ($row->campaign->description) {
                $html .= '<div class="text-xs text-muted-foreground mt-1">' . e(Str::limit($row->campaign->description, 40)) . '</div>';
            }
        } else {
            $html .= '<div class="text-muted-foreground">No Campaign</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderEmailSubject($row): string
    {
        $html = '<div class="flex flex-col">';
        $html .= '<div class="font-medium">' . e(Str::limit($row->subject, 50)) . '</div>';
        
        if ($row->emailTemplate) {
            $html .= '<div class="text-xs text-muted-foreground mt-1">Template: ' . e($row->emailTemplate->name) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderStatus($row): string
    {
        $statusConfig = [
            'pending' => ['color' => 'warning', 'icon' => 'time', 'text' => 'Pending'],
            'processing' => ['color' => 'info', 'icon' => 'loading', 'text' => 'Processing'],
            'sent' => ['color' => 'success', 'icon' => 'check', 'text' => 'Sent'],
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
            $html .= e(Str::limit($row->error_message, 30));
            $html .= '</div>';
        }
        
        return $html;
    }

    private function renderScheduledAt($row): string
    {
        if (!$row->scheduled_at) {
            return '<span class="text-muted-foreground">-</span>';
        }

        return '<div class="text-sm text-secondary-foreground">' . $row->scheduled_at->format('M d, Y H:i') . '</div>';
    }

    private function renderSentAt($row): string
    {
        if (!$row->sent_at) {
            return '<span class="text-muted-foreground">-</span>';
        }

        return '<div class="text-sm text-secondary-foreground">' . $row->sent_at->format('M d, Y H:i') . '</div>';
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
