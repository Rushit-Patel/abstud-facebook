<?php

namespace App\DataTables\Team\Task;

use App\Models\Task;
use App\Repositories\Team\TaskRepository;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class TaskDataTable extends DataTable
{
    
    protected $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('task', fn($row) => $this->renderTask($row))
                ->filterColumn('task', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhereHas('category', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        });
                    });
                })
            ->addColumn('priority', fn($row) => $this->renderPriority($row))
                ->filterColumn('priority', function ($query, $keyword) {
                    $query->whereHas('priority', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
            ->addColumn('status', fn($row) => $this->renderStatus($row))
                ->filterColumn('status', function ($query, $keyword) {
                    $query->whereHas('status', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
            ->addColumn('progress', fn($row) => $this->renderProgress($row))
            ->addColumn('assignees', fn($row) => $this->renderAssignees($row))
            ->addColumn('due_date', fn($row) => $this->renderDueDate($row))
            ->rawColumns(['action', 'task', 'priority', 'status', 'progress', 'assignees', 'due_date'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.task.partials.actions', ['task' => $row])->render();
    }

    private function renderTask($row): string
    {
        return view('team.task.partials.task', [
            'title' => $row->title,
            'description' => $row->description,
            'category' => $row->category?->name,
            'id' => $row->id,
            'created_at' => $row->created_at,
        ])->render();
    }

    private function renderPriority($row): string
    {
        return view('team.task.partials.priority', ['task' => $row])->render();
    }

    private function renderStatus($row): string
    {
        return view('team.task.partials.status', ['task' => $row])->render();
    }

    private function renderProgress($row): string
    {
        return view('team.task.partials.progress', ['task' => $row])->render();
    }

    private function renderAssignees($row): string
    {
        return view('team.task.partials.assignees', ['task' => $row])->render();
    }

    private function renderDueDate($row): string
    {
        if (!$row->due_date) {
            return '<span class="text-muted-foreground text-sm">-</span>';
        }
        
        $isOverdue = $row->due_date < now() && !$row->status->is_completed;
        $class = $isOverdue ? 'text-red-600 font-medium' : 'text-secondary-foreground';
        
        return '<span class="' . $class . ' text-sm">' . $row->due_date->format('M d, Y') . '</span>';
    }

    /**
     * Get query source of dataTable.
     */
    public function query(): QueryBuilder
    {
        $query = $this->repository->getTasks();

        // Apply filters from request
        $this->applyFilters($query);

        return $query;
    }

    /**
     * Apply filters to the query based on request parameters
     */
    private function applyFilters(QueryBuilder $query): void
    {
        $request = request();

        // Date filter
        if ($request->filled('date')) {
            $date = $request->get('date');
            $query->whereDate('created_at', $date);
        }

        // Due date filter
        if ($request->filled('due_date')) {
            $dueDate = $request->get('due_date');
            $query->whereDate('due_date', $dueDate);
        }

        // Status filter
        if ($request->filled('status')) {
            $statuses = is_array($request->get('status')) ? $request->get('status') : [$request->get('status')];
            $query->whereHas('status', function ($q) use ($statuses) {
                $q->whereIn('id', $statuses);
            });
        }

        // Priority filter
        if ($request->filled('priority')) {
            $priorities = is_array($request->get('priority')) ? $request->get('priority') : [$request->get('priority')];
            $query->whereHas('priority', function ($q) use ($priorities) {
                $q->whereIn('id', $priorities);
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $categories = is_array($request->get('category')) ? $request->get('category') : [$request->get('category')];
            $query->whereHas('category', function ($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        // Assigned to filter
        if ($request->filled('assigned_to')) {
            $assignees = is_array($request->get('assigned_to')) ? $request->get('assigned_to') : [$request->get('assigned_to')];
            $query->whereHas('assignees', function ($q) use ($assignees) {
                $q->whereIn('users.id', $assignees);
            });
        }

        // Owner (Creator) filter
        if ($request->filled('owner')) {
            $owners = is_array($request->get('owner')) ? $request->get('owner') : [$request->get('owner')];
            $query->whereIn('created_by', $owners);
        }
    }
    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('task-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(0)
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [
            Column::make('task')->width(200)->searchable(true),
            Column::make('priority')->width(100)->searchable(true),
            Column::make('status')->width(120)->searchable(true),
            Column::make('progress')->width(100)->searchable(true),
            Column::make('due_date')->title('Due Date')->width(120)->searchable(false),
            Column::make('assignees')->width(150)->searchable(false),
        ];
        
        if (auth()->user()->can('task:edit') || auth()->user()->can('task:delete')) {
            $columns[] = Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center')
                ->orderable(false)
                ->searchable(false);
        }
        
        return $columns;
    }

    private function getTableParameters(): array
    {
        return [
            'dom' => '<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3"<"ml-auto"f>>rt<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-4 border-t border-gray-200"<"text-secondary-foreground text-sm font-medium"l><"dt-paging flex items-center space-x-1 text-secondary-foreground text-sm font-medium"ip>>',
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

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Tasks_' . date('YmdHis');
    }
}
