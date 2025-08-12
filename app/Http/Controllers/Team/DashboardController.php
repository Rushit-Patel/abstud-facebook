<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\ClientLead;
use App\Models\CompanySetting;
use App\Models\Branch;
use App\Models\User;
use App\Models\Student;
use App\Models\Partner;
use App\Repositories\Team\Dashboard\DashboardRepository;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\DemoRepository;
use App\Repositories\Team\LeadRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    protected $repository;
    protected $demoRepository;
    protected $dashboardRepository;

    public function __construct(CoachingRepository $CoachingRepository ,LeadRepository $repository ,DemoRepository $demoRepository ,DashboardRepository $dashboardRepository)
    {
        $this->repository = $repository;
        $this->demoRepository = $demoRepository;
        $this->CoachingRepository = $CoachingRepository;
        $this->dashboardRepository = $dashboardRepository;
    }
    public function index()
    {
        $company = CompanySetting::getSettings();
        $branches = Branch::active()->get();
        $query = $this->repository->getLead();


        $queryDemo = $this->demoRepository->getDemo();

        // Performance Matrix Count
        $monthCounts = $this->dashboardRepository->getPerformanceMatrix();
        $visitorVisaCount = $this->dashboardRepository->getVisitorVisa();
        $DependentVisaCount = $this->dashboardRepository->getDependentVisa();

        $stats = [
            'total_users' => User::where('is_active', true)->count(),
            'total_students' => 152,
            'total_partners' => 4545,
            'total_branches' => Branch::where('is_active', true)->count(),
        ];

        $leadCounts = $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as close,
            SUM(CASE WHEN status = '3' THEN 1 ELSE 0 END) as register
        ")
        ->first();

        $totalLeads = $leadCounts->total;
        $openLeads = $leadCounts->open;
        $closeLeads = $leadCounts->close;
        $registerLeads = $leadCounts->register;

        $demoCount = $queryDemo
                ->where('status', '1')
                ->distinct('client_lead_id')
                ->count('client_lead_id');

        $leadPurposeCounts = $this->repository->getLead()
            ->select('purpose', DB::raw('COUNT(id) as total'))
            ->groupBy('purpose')
            ->with('getPurpose')
            ->get()
            ->mapWithKeys(function ($item) {
                $purposeName = $item->getPurpose->name ?? 'Unknown';
                return [$purposeName => $item->total];
            })->toArray();

        $leadLabels = array_keys($leadPurposeCounts);
        $leadSeries = array_values($leadPurposeCounts);

        $coachings_different = $this->CoachingRepository->getCoaching()->
        select('coaching_id')
        ->groupBy('coaching_id')->get();

        $batchesStrengthCoachigWise = array();
        foreach($coachings_different as $index=>$coachings_one){
            $coaching_batch_data = $this->CoachingRepository->getCoaching()->
            select(
                DB::raw('COUNT(id) as counts'),
                DB::raw('batch_id'),
            )
            ->where('coaching_id',$coachings_one->coaching_id)
            ->with('getBatch')
            ->groupBy('batch_id')->get();

            $batchesStrengthCoachigWise[$index]['data'] = $coaching_batch_data;
            $batchesStrengthCoachigWise[$index]['coaching'] = $coachings_one->getCoaching?->name;
        }

        $leadCounsellerCounts = $this->repository->getLead()->select('users.name', DB::raw('COUNT(client_leads.id) as total'))
            ->join('users', 'client_leads.assign_owner', '=', 'users.id')
            ->groupBy('users.name')
            ->limit(5)
            ->pluck('total', 'name')
            ->toArray();

            $leadCounsellerName = array_map(function ($fullName) {
                return explode(' ', $fullName);
            }, array_keys($leadCounsellerCounts));

            $leadCounsellerCount = array_values($leadCounsellerCounts);


        return view('team.dashboard', compact('company', 'stats' ,'totalLeads' ,'openLeads' ,'closeLeads','registerLeads' ,'leadLabels','leadSeries','leadCounsellerCount','leadCounsellerName', 'branches','demoCount','monthCounts','batchesStrengthCoachigWise','visitorVisaCount','DependentVisaCount'));
    }
    public function index1()
    {
        $company = CompanySetting::getSettings();
        $branches = Branch::active()->get();
        $query = $this->repository->getLead();
        $stats = [
            'total_users' => User::where('is_active', true)->count(),
            'total_students' => 152,
            'total_partners' => 4545,
            'total_branches' => Branch::where('is_active', true)->count(),
        ];

        $leadCounts = $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as close,
            SUM(CASE WHEN status = '3' THEN 1 ELSE 0 END) as register
        ")
        ->first();

        $totalLeads = $leadCounts->total;
        $openLeads = $leadCounts->open;
        $closeLeads = $leadCounts->close;
        $registerLeads = $leadCounts->register;

        $leadPurposeCounts = $this->repository->getLead()
            ->select('purpose', DB::raw('COUNT(id) as total'))
            ->groupBy('purpose')
            ->with('getPurpose')
            ->get()
            ->mapWithKeys(function ($item) {
                $purposeName = $item->getPurpose->name ?? 'Unknown';
                return [$purposeName => $item->total];
            })->toArray();

        $leadLabels = array_keys($leadPurposeCounts);
        $leadSeries = array_values($leadPurposeCounts);


        $leadCounsellerCounts = $this->repository->getLead()->select('users.name', DB::raw('COUNT(client_leads.id) as total'))
            ->join('users', 'client_leads.assign_owner', '=', 'users.id')
            ->groupBy('users.name')
            ->limit(5)
            ->pluck('total', 'name')
            ->toArray();

            $leadCounsellerName = array_map(function ($fullName) {
                return explode(' ', $fullName);
            }, array_keys($leadCounsellerCounts));

            $leadCounsellerCount = array_values($leadCounsellerCounts);


        return view('team.dashboard-old', compact('company', 'stats' ,'totalLeads' ,'openLeads' ,'closeLeads','registerLeads' ,'leadLabels','leadSeries','leadCounsellerCount','leadCounsellerName', 'branches'));
    }

    public function filterLeads(Request $request)
    {
        $query = $this->repository->getLead();
        $date = '';
        $branchFilter ='';
        $userFilter = '';
        $start = null;
        $end = null;

        if ($request->filled('lead_filter_date_range') && $request->lead_filter_date_range !== 'custom') {
            $dateRange = $request->lead_filter_date_range;
            $today = Carbon::today();


            switch ($dateRange) {
                case 'yesterday':
                    $start = $today->copy()->subDay()->format('Y-m-d');
                    $end = $start;
                    $query->whereBetween('client_date', [$start, $end]);
                    $date = $start .'to'. $end;
                    break;
                case 'last_7_days':
                    $start = $today->copy()->subDays(7)->format('Y-m-d');
                    $end = $today->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);

                    $date = $start .'to'. $end;
                    break;
                case 'last_30_days':
                    $start = $today->copy()->subDays(30)->format('Y-m-d');
                    $end = $today->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $date = $start .'to'. $end;
                    break;
                case 'last_month':
                    $lastMonth = $today->copy()->subMonth();
                    $start = $lastMonth->copy()->startOfMonth()->format('Y-m-d');
                    $end = $lastMonth->copy()->endOfMonth()->format('Y-m-d');
                    $date = $start .'to'. $end;
                    $query->whereBetween('client_date', [$start, $end]);
                    break;
                case 'this_year':
                    $start = $today->copy()->startOfYear()->format('Y-m-d');
                    $end = $today->copy()->endOfYear()->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $date = $start .'to'. $end;
                    break;
                case 'last_year':
                    $lastYear = $today->copy()->subYear();
                    $start = $lastYear->copy()->startOfYear()->format('Y-m-d');
                    $end = $lastYear->copy()->endOfYear()->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $date = $start .'to'. $end;
                    break;
                case 'custom':
                    if ($request->filled('lead_filter_date')) {
                        $dateRange = $request->lead_filter_date;
                        // Handle date range format "YYYY-MM-DD to YYYY-MM-DD"
                        if (strpos($dateRange, ' to ') !== false) {
                            $dates = explode(' to ', $dateRange);
                            if (count($dates) === 2) {
                                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                                $query->whereBetween('client_date', [$startDate, $endDate]);
                            }
                        } else {
                            // Single date selected
                            $query->whereDate('client_date', Carbon::parse($dateRange));
                        }
                    }
                    $date = $dateRange;
                    break;
            }
        }
        if($request->filled('lead_filter_date') && $request->lead_filter_date!='') {
            $leadFilterDateArray = explode(' to ', $request->lead_filter_date);
            if (count($leadFilterDateArray) === 2) {
                $start = Carbon::createFromFormat('d/m/Y', trim($leadFilterDateArray[0]))->format('Y-m-d');
                $end = Carbon::createFromFormat('d/m/Y', trim($leadFilterDateArray[1]))->format('Y-m-d');
            } else {
                $start = Carbon::createFromFormat('d/m/Y', trim($leadFilterDateArray[0]))->format('Y-m-d');
                $end = $start;
            }
            $query->whereBetween('client_date', [
                $start,
                $end
            ]);
            $date = $start . 'to' . $end;

        }

        // Apply branch filter
        if ($request->filled('lead_filter_branch') && $request->lead_filter_branch !== 'All') {
            $branches = $request->lead_filter_branch;
            // Handle multiple branch selection
            if (is_array($branches)) {
                $query->whereIn('branch', $branches);
                $branchFilter = implode(',', $branches);
            } else {
                $query->where('branch', $branches);
                $branchFilter = $branches;
            }
        }

        if ($request->filled('lead_filter_users') && $request->lead_filter_users !== 'All') {
            $users = $request->lead_filter_users;
            // Handle multiple branch selection
            if (is_array($users)) {
                $query->whereIn('assign_owner', $users);
                $userFilter = implode(',', $users);
            } else {
                $query->where('assign_owner', $users);
                $userFilter = $users;
            }
        }

        // Get lead counts with filters
        $leadCountQuery = clone $query;
        $leadCounts = $leadCountQuery->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as close,
            SUM(CASE WHEN status = '3' THEN 1 ELSE 0 END) as register
        ");

        $leadCounts = $leadCounts->first();

        $totalLeads = $leadCounts->total ?? 0;
        $openLeads = $leadCounts->open ?? 0;
        $closeLeads = $leadCounts->close ?? 0;
        $registerLeads = $leadCounts->register ?? 0;

        $purposeQuery = clone $query;
        $leadPurposeCounts = $purposeQuery->select('purposes.name', DB::raw('COUNT(client_leads.id) as total'))
            ->join('purposes', 'client_leads.purpose', '=', 'purposes.id')
            ->groupBy('purposes.name')
            ->orderBy('total', 'desc')
            ->pluck('total', 'name')
            ->toArray();

            $leadLabels = array_keys($leadPurposeCounts);
            $leadSeries = array_values($leadPurposeCounts);

        // Get filtered lead counsellor counts
        $queryClone2 = clone $query;
        $leadCounsellerCounts = $queryClone2->select('users.name', DB::raw('COUNT(client_leads.id) as total'))
            ->join('users', 'client_leads.assign_owner', '=', 'users.id')
            ->groupBy('users.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->pluck('total', 'name')
            ->toArray();

        $leadCounsellerName = array_map(function ($fullName) {
            return explode(' ', $fullName);
        }, array_keys($leadCounsellerCounts));

        $leadCounsellerCount = array_values($leadCounsellerCounts);

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => view('components.team.dashboard.lead.lead-statistics', compact('totalLeads', 'openLeads', 'closeLeads', 'registerLeads','branchFilter','userFilter','date'))->render(),
                'leadByService' => [
                    'labels' => $leadLabels,
                    'series' => $leadSeries
                ],
                'leadByTeam' => [
                    'names' => $leadCounsellerName,
                    'counts' => $leadCounsellerCount
                ]
            ]
        ]);
    }

    public function filterLeadsAnalysis(Request $request)
    {
        $dateRange = $request->date_range;
        $customDate = $request->custom_date;
        $branchId = $request->branch;
        $branchFilter = '';

        $query = $this->repository->getLead(); // Base query
        $queryDemo = $this->demoRepository->getDemo();
        $today = Carbon::today();
        $date = null;

        // Date filtering
        if ($request->filled('date_range')) {
            switch ($dateRange) {
                case 'yesterday':
                    $start = $today->copy()->subDay()->format('Y-m-d');
                    $end = $start;
                    $query->whereBetween('client_date', [$start, $end]);
                    $queryDemo->whereBetween('demo_date', [$start, $end]);
                    $date = "$start to $end";
                    break;

                case 'last_7_days':
                    $start = $today->copy()->subDays(7)->format('Y-m-d');
                    $end = $today->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $queryDemo->whereBetween('demo_date', [$start, $end]);
                    $date = "$start to $end";
                    break;

                case 'last_30_days':
                    $start = $today->copy()->subDays(30)->format('Y-m-d');
                    $end = $today->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $queryDemo->whereBetween('demo_date', [$start, $end]);
                    $date = "$start to $end";
                    break;

                case 'last_month':
                    $lastMonth = $today->copy()->subMonth();
                    $start = $lastMonth->startOfMonth()->format('Y-m-d');
                    $end = $lastMonth->endOfMonth()->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $queryDemo->whereBetween('demo_date', [$start, $end]);
                    $date = "$start to $end";
                    break;

                case 'this_year':
                    $start = $today->copy()->startOfYear()->format('Y-m-d');
                    $end = $today->copy()->endOfYear()->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $queryDemo->whereBetween('demo_date', [$start, $end]);
                    $date = "$start to $end";
                    break;

                case 'last_year':
                    $lastYear = $today->copy()->subYear();
                    $start = $lastYear->startOfYear()->format('Y-m-d');
                    $end = $lastYear->endOfYear()->format('Y-m-d');
                    $query->whereBetween('client_date', [$start, $end]);
                    $queryDemo->whereBetween('demo_date', [$start, $end]);
                    $date = "$start to $end";
                    break;

                case 'custom':
                    if ($request->filled('custom_date')) {
                        if (strpos($customDate, ' to ') !== false) {
                            [$startRaw, $endRaw] = explode(' to ', $customDate);
                            $startDate = Carbon::createFromFormat('d/m/Y', trim($startRaw))->startOfDay();
                            $endDate = Carbon::createFromFormat('d/m/Y', trim($endRaw))->endOfDay();
                            $query->whereBetween('client_date', [$startDate, $endDate]);
                            $queryDemo->whereBetween('demo_date', [$startDate, $endDate]);
                            $date = $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d');
                        } else {
                            $singleDate = Carbon::createFromFormat('d/m/Y', $customDate)->format('Y-m-d');
                            $query->whereDate('client_date', $singleDate);
                            $queryDemo->whereDate('demo_date', $singleDate);
                            $date = $singleDate;
                        }
                    }
                    break;
            }
        }


        if ($request->filled('branch') && $request->branch !== 'All') {
            $branches = $request->branch;
            // Handle multiple branch selection
            if (is_array($branches)) {
                $query->whereIn('branch', $branches);
                $queryDemo->whereHas('clientLead', function ($q) use ($branches) {
                    $q->whereIn('branch', $branches);
                });

                $branchFilter = implode(',', $branches);
            } else {
                $query->where('branch', $branches);
                $queryDemo->whereHas('clientLead', function ($q) use ($branches) {
                    $q->where('branch', $branches);
                });
                $branchFilter = $branches;
            }
        }

        $baseQuery = clone $query;

        // Lead counts
        $totalLeads = $baseQuery->count();
        $openLeads = (clone $query)->where('status', '1')->count();
        $closeLeads = (clone $query)->where('status', '2')->count();
        $registerLeads = (clone $query)->where('status', '3')->count();

         $demoCount = $queryDemo
                ->where('status', '1')
                ->distinct('client_lead_id')
                ->count('client_lead_id');



        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => view('components.team.dashboard.lead.lead-statistics', compact('totalLeads', 'openLeads', 'closeLeads', 'registerLeads','branchFilter','date','demoCount'))->render(),
            ]
        ]);
    }

    public function filterLeadsPerformanceMatrix(Request $request){

        $branchId = $request->branch_id;
        $monthCounts = $this->dashboardRepository->getPerformanceMatrix($branchId);

        return response()->json([
            'html' => view('components.team.dashboard.lead.lead-performance-matrix', [
                'thisMonthData'    => $monthCounts['this_month'],
                'previousMonthData'=> $monthCounts['previous_month'],
                'percentage'=> $monthCounts['percentage'],
                'branchFilter' => $branchId
            ])->render()
        ]);
    }
}
