<?php

namespace App\DataTables\Team\Setting;

use App\Models\NotificationConfig;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NotificationConfigDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('notification_name', fn($row) => $this->renderNotificationName($row))
            ->addColumn('channels', fn($row) => $this->renderChannels($row))
            ->addColumn('email_template', fn($row) => $this->renderEmailTemplate($row))
            ->addColumn('team_notification', fn($row) => $this->renderTeamNotification($row))
            ->addColumn('whatsapp_template', fn($row) => $this->renderWhatsappTemplate($row))
            ->filterColumn('notification_name', function($query, $keyword) {
                $query->where('slug', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'notification_name', 'channels', 'email_template', 'team_notification', 'whatsapp_template'])
            ->setRowId('id');
    }

    public function query(NotificationConfig $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['emailTemplate', 'teamNotificationType'])
            ->orderBy('slug');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('notification-config-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(1, 'asc')
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        return [
            Column::computed('notification_name')
                ->title('Notification')
                ->exportable(false)
                ->printable(true)
                ->width(250)
                ->addClass('text-start')
                ->searchable(true),
            Column::computed('channels')
                ->title('Active Channels')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-center')
                ->searchable(false),
            Column::computed('email_template')
                ->title('Email Template')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-start')
                ->searchable(false),
            Column::computed('whatsapp_template')
                ->title('WhatsApp Template')
                ->exportable(false)
                ->printable(true)
                ->width(150)
                ->addClass('text-start')
                ->searchable(false),
            Column::computed('team_notification')
                ->title('Team Notification Type')
                ->exportable(false)
                ->printable(true)
                ->width(200)
                ->addClass('text-start')
                ->searchable(false),
            Column::computed('action')
                ->title('')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-end')
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
                'search' => 'Search notifications: ',
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
        return 'NotificationConfig_' . date('YmdHis');
    }

    // Render methods
    protected function renderAction($row): string
    {
        $actions = '';
        
        // View button
        $actions .= '<a href="' . route('team.settings.notification-config.show', $row->id) . '" 
                         class="kt-btn kt-btn-sm kt-btn-secondary kt-btn-icon" 
                         title="View Details">
                         <i class="ki-filled ki-eye"></i>
                     </a>';
        
        // Edit button
        $actions .= '<a href="' . route('team.settings.notification-config.edit', $row->id) . '" 
                         class="kt-btn kt-btn-sm kt-btn-primary kt-btn-icon" 
                         title="Edit Configuration">
                         <i class="ki-filled ki-setting-2"></i>
                     </a>';
        
        return $actions;
    }

    protected function renderNotificationName($row): string
    {
        $title = ucwords(str_replace('_', ' ', $row->slug));
        $description = '<small class="text-muted d-block">' . e($row->class) . '</small>';
        
        return '<div class="d-flex flex-column">
                    <span class="fw-bold">' . e($title) . '</span>
                    ' . $description . '
                </div>';
    }

    protected function renderChannels($row): string
    {
        $channels = [];
        
        if ($row->email_enabled) {
            $channels[] = '<span class="kt-badge kt-badge-light kt-badge-success">Email</span>';
        }
        
        if ($row->whatsapp_enabled) {
            $channels[] = '<span class="kt-badge kt-badge-light kt-badge-info">WhatsApp</span>';
        }
        
        if ($row->system_enabled) {
            $channels[] = '<span class="kt-badge kt-badge-light kt-badge-primary">System</span>';
        }
        
        if (empty($channels)) {
            return '<span class="kt-badge kt-badge-light kt-badge-secondary">None</span>';
        }
        
        return implode(' ', $channels);
    }

    protected function renderEmailTemplate($row): string
    {
        if (!$row->email_enabled) {
            return '<span class="text-muted">-</span>';
        }
        
        if ($row->emailTemplate) {
            return '<span class="text-primary fw-medium">' . e($row->emailTemplate->name) . '</span>';
        }
        
        return '<span class="kt-badge kt-badge-light kt-badge-warning">Not Configured</span>';
    }

    protected function renderWhatsappTemplate($row): string
    {
        if (!$row->whatsapp_enabled) {
            return '<span class="text-muted">-</span>';
        }
        
        if ($row->whatsapp_template) {
            return '<span class="text-primary fw-medium">' . e($row->whatsapp_template) . '</span>';
        }
        
        return '<span class="kt-badge kt-badge-light kt-badge-warning">Not Configured</span>';
    }

    protected function renderTeamNotification($row): string
    {
        if (!$row->system_enabled) {
            return '<span class="text-muted">-</span>';
        }
        
        if ($row->teamNotificationType) {
            return '<span class="text-primary fw-medium">' . e($row->teamNotificationType->title) . '</span>';
        }
        
        return '<span class="kt-badge kt-badge-light kt-badge-warning">Not Configured</span>';
    }
}
