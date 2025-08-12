<?php

namespace App\DataTables\Team\Lead;

use App\Models\ClientLead;
use App\Models\ForeignCountry;
use App\Models\LeadTag;
use App\Repositories\Team\LeadRepository;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class LeadDataTable extends DataTable
{

    protected $repository;

    public function __construct(LeadRepository $repository)
    {
        $this->repository = $repository;
    }
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn($row) => $this->renderAction($row))
            ->addColumn('lead', fn($row) => $this->renderLead($row))
                ->filterColumn('lead', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('getBranch', function ($subQ) use ($keyword) {
                            $subQ->where('branch_name', 'like', "%{$keyword}%");
                        })->orWhere('client_date', 'like', "%{$keyword}%")
                        ->orWhereHas('client.getSource', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        });
                    });
                })

            ->addColumn('client', fn($row) => $this->renderClient($row))
                ->filterColumn('client', function ($query, $keyword) {
                    $query->whereHas('client', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('mobile_no', 'like', "%{$keyword}%")
                        ->orWhere('email_id', 'like', "%{$keyword}%");
                    });
                })

            ->addColumn('service', fn($row) => $this->renderService($row))
                ->filterColumn('service', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('getPurpose', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        })->orWhereHas('getForeignCountry', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        })->orWhereHas('getCoaching', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        });
                    });
                })

            ->addColumn('status', fn($row) => $this->renderStatus($row))
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('getStatus', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        })->orWhereHas('getSubStatus', function ($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        });
                    });
                })
            ->addColumn('follow up', fn($row) => $this->renderFollowUp($row))
            ->rawColumns(['action', 'lead', 'client', 'status', 'follow up', 'service'])
            ->setRowId('id');
    }

    private function renderAction($row): string
    {
        return view('team.lead.datatables.action', ['id' => $row->id])->render();
    }

    private function renderLead($row)
    {
        return view('team.lead.datatables.lead', [
            'date' => Carbon::parse($row->client_date)->format('d/m/Y'),
            'source' => $row?->client?->getSource?->name,
            'client_code' => $row->client->client_code,
        ])->render();
    }

    private function renderClient($row): string
    {
        return view('team.lead.datatables.client', [
            'name' => $row->client->first_name . ' ' . $row->client->last_name,
            'mobile_no' => $row->client->country_code."".$row->client->mobile_no,
            'email_id' => $row->client->email_id,
            'client_id' => $row->client->id,
        ])->render();
    }

    private function renderService($row): string
    {
            $secondCountryNames = '';
            if (!empty($row->second_country)) {
                $secondCountryIds = explode(',', $row->second_country);
                $secondCountryNames = ForeignCountry::whereIn('id', $secondCountryIds)
                    ->pluck('name')
                    ->implode(', ');
            }

        return view('team.lead.datatables.service', [
            'purpose' => $row?->getPurpose?->name,
            'country' => $row?->getForeignCountry?->name .' '.$secondCountryNames,
            'coaching' => $row?->getCoaching?->name,
            'branch' => $row->getBranch->branch_name,
        ])->render();
    }

    private function renderStatus($row): string
    {
        $statusName = $row->getStatus?->name ?? 'Unknown';
        $subStatusName = $row->getSubStatus?->name ?? null;
        $assigned = $row->assignedOwner?->name ?? null;
        $badgeClass = $this->getStatusBadgeClass($statusName);

        return view('team.lead.datatables.status', [
            'status' => $statusName,
            'subStatus' => $subStatusName,
            'badgeClass' => $badgeClass,
            'assigned' => $assigned,
            'id' => $row->id,
        ])->render();
    }

    private function renderFollowUp($row): string
    {
        $tags = LeadTag::active()->pluck('name')->toArray();
        return view('team.lead.datatables.follow-up', [
            'follow_up' => $row?->getFollowUps,
            'lead_data' => $row,
            'tags' => $tags,
        ])->render();
    }

    private function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'Open' => 'bg-blue-100 text-blue-800',
            'Close' => 'bg-yellow-100 text-yellow-800',
            'Completed' => 'bg-green-100 text-green-800',
            'Closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-200 text-gray-900'
        };
    }
    private function parseToYmd($dateString)
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'm/d/Y', 'm-d-Y'];
        $dateString = trim($dateString); // remove whitespace
        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $dateString);
                if ($dt && $dt->format($format) === $dateString) {
                    return $dt->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue; // silently ignore invalid formats
            }
        }
        return null;
    }


    public function query(ClientLead $model): QueryBuilder
    {
        $query = $this->repository->getLead();

        // Handle status filter (support both array and single value formats)
        if (request()->has('status')) {
            $statusParam = request()->get('status');
            if (is_array($statusParam)) {
                // New array format from filter form
                $query->whereIn('status', $statusParam);
            } else {
                // Existing base64 encoded format for backward compatibility
                $decodedStatus = base64_decode($statusParam);
                $query->where('status', $decodedStatus);
            }
        }

        if (request()->has('purpose')) {
            $purposeParam = request()->get('purpose');
            if (is_array($purposeParam)) {
                // New array format from filter form
                $query->whereIn('purpose', $purposeParam);
            } else {
                // Existing base64 encoded format for backward compatibility
                $decodedPurpose = base64_decode($purposeParam);
                $query->where('purpose', $decodedPurpose);
            }
        }

        // Handle branch filter (support both array and single value formats)
        if (request()->has('branch')) {
            $branchParam = request()->get('branch');
            if (is_array($branchParam)) {
                // New array format from filter form
                $query->whereIn('branch', $branchParam);
            } else {
                // Existing single value format
                $branch = trim($branchParam);
                if (!empty($branch)) {
                    if (str_contains($branch, ',')) {
                        $branchArray = explode(',', $branch);
                        $query->whereIn('branch', $branchArray);
                    } else {
                        $query->where('branch', $branch);
                    }
                }
            }
        }
        if (request()->has('owner')) {
            $ownerParam = request()->get('owner');
            if (is_array($ownerParam)) {
                // New array format from filter form
                $query->whereIn('assign_owner', $ownerParam);
            } else {
                // Existing single value format
                $owner = trim($ownerParam);
                if (!empty($owner)) {
                    if (str_contains($owner, ',')) {
                        $ownerArray = explode(',', $owner);
                        $query->whereIn('assign_owner', $ownerArray);
                    } else {
                        $query->where('assign_owner', $owner);
                    }
                }
            }
        }

        // Handle source filter
        if (request()->has('source') && is_array(request()->get('source'))) {
            $query->whereHas('client', function($q) {
                $q->whereIn('source', request()->get('source'));
            });
        }

        // Handle lead type filter
        if (request()->has('lead_type') && is_array(request()->get('lead_type'))) {
            $query->whereHas('client', function($q) {
                $q->whereIn('lead_type', request()->get('lead_type'));
            });
        }

        // Handle date range filter
        if (request()->has('date')) {
            $dateRange = request()->get('date');
            $dateParts = explode('to', $dateRange);
            // change date formate d/m/Y to Y-m-d
            if (count($dateParts) == 2) {
                $startDate = $this->parseToYmd(trim($dateParts[0]));
                $endDate = $this->parseToYmd(trim($dateParts[1]));
                $query->whereBetween('client_date', [$startDate, $endDate]);
            }else{
                if (!empty($dateParts[0]) && Carbon::hasFormat(trim($dateParts[0]), 'd/m/Y')) {
                    $startDate = $this->parseToYmd(trim($dateParts[0]));
                    $query->whereDate('client_date', $startDate);
                }
            }
        }
        $query = $query->orderByRaw('CASE WHEN assign_owner IS NULL OR assign_owner = "" OR assign_owner = 0 THEN 0 ELSE 1 END ASC')->orderBy('id', 'desc');
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('lead-table')
            ->setTableAttribute('class', 'kt-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getTableParameters())
            ->orderBy(1)
            ->selectStyleSingle();
    }

    public function getColumns(): array
    {
        $columns = [
            Column::make('lead')->width(100)->searchable(true),
            Column::make('client')->width(180)->searchable(true),
            Column::make('service')->width(150)->searchable(true),
            Column::make('status')->width(130)->searchable(true),
            Column::make('follow up')->width(200)->searchable(true),
        ];
            if (auth()->user()->can('lead:edit') || auth()->user()->can('lead:delete')) {
                $columns[] = Column::computed('action')
                    ->title('Actions')
                    ->exportable(false)
                    ->printable(false)
                    ->width(80)
                    ->addClass('text-center')
                    ->orderable(false)
                    ->searchable(false);
            }
        return $columns;
    }

    private function getTableParameters(): array
    {
        return [
            'dom' => '<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-2"<"dt-length flex items-center gap-2 order-2 md:order-1"><"datatable-export-form"><"dt-search ml-auto"f>>rt<"kt-datatable-toolbar flex flex-col sm:flex-row items-center justify-between gap-3 py-4 border-t border-gray-200"<"kt-datatable-length text-secondary-foreground text-sm font-medium"l><"dt-paging flex items-center space-x-1 text-secondary-foreground text-sm font-medium"ip>>',
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
        return 'Lead_' . date('YmdHis');
    }

    /**
     * Generate the export form HTML.
     */
    private function getExportButton(): string
    {
        if(Auth::user()->can('lead:export')) {
            return view('team.lead.datatables.export-button')->render();
        }else{
            return '';
        }
    }
}
