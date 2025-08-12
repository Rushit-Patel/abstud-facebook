<?php
namespace App\Repositories\Team\Dashboard;

use App\Models\ClientCoaching;
use App\Repositories\Team\DemoRepository;
use App\Repositories\Team\LeadRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardRepository
{
    protected $leadRepository;
    protected $demoRepository;

    public function __construct(LeadRepository $leadRepository, DemoRepository $demoRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->demoRepository = $demoRepository;
    }

    public function getCoaching()
    {
        // Your logic here for coaching data
    }

    public function getPerformanceMatrix($branchId = null)
    {
        $query = $this->leadRepository->getLead();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $previousMonth = Carbon::now()->subMonth()->month;
        $previousYear = Carbon::now()->subMonth()->year;

        if ($branchId) {
            $query->where('branch', $branchId);
        }

        $getCounts = function ($month, $year) use ($query) {
            return (clone $query)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as open,
                    SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as close,
                    SUM(CASE WHEN status = '3' AND purpose = '1' THEN 1 ELSE 0 END) as register_application,
                    SUM(CASE WHEN status = '3' AND purpose = '2' THEN 1 ELSE 0 END) as register_coaching
                ")
                ->whereMonth('client_date', $month)
                ->whereYear('client_date', $year)
                ->first();
        };

        $calcPercent = function ($current, $previous) {
            // Both months zero → 0%
            if ($previous == 0 && $current == 0) {
                return 0;
            }

            // Previous zero but current > 0 → +100%
            if ($previous == 0 && $current > 0) {
                return 100;
            }

            // Current zero but previous > 0 → -100%
            if ($current == 0 && $previous > 0) {
                return -100;
            }

            // Normal calculation
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $thisMonth = $getCounts($currentMonth, $currentYear);
        $prevMonth = $getCounts($previousMonth, $previousYear);

        $monthCounts = [
            'this_month' => [
                'label' => Carbon::create($currentYear, $currentMonth)->format('F Y'),
                'this_month_date' => Carbon::create($currentYear, $currentMonth, 1)->toDateString() .' to '.Carbon::create($currentYear, $currentMonth)->endOfMonth()->toDateString(),
                'data'  => $thisMonth
            ],
            'previous_month' => [
                'label' => Carbon::create($previousYear, $previousMonth)->format('F Y'),
                'previous_month_date' => Carbon::create($previousYear, $previousMonth, 1)->toDateString() .' to '.Carbon::create($previousYear, $previousMonth)->endOfMonth()->toDateString(),
                'data'  => $prevMonth
            ],
            'percentage' => [
                'total'                => $calcPercent($thisMonth->total ?? 0, $prevMonth->total ?? 0),
                'close'                => $calcPercent($thisMonth->close ?? 0, $prevMonth->close ?? 0),
                'register_coaching'    => $calcPercent($thisMonth->register_coaching ?? 0, $prevMonth->register_coaching ?? 0),
                'register_application' => $calcPercent($thisMonth->register_application ?? 0, $prevMonth->register_application ?? 0),
            ]
        ];

        return $monthCounts;
    }

    public function getImportantDetails()
    {
        $query = $this->leadRepository->getLead();
        //
        return ;
    }

    public function getVisitorVisa()
    {
        $query = $this->leadRepository->getLead();

        $getCountsVisitorVisa = (clone $query)
            ->where('purpose', '3')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as close
            ")
            ->first();

        return $getCountsVisitorVisa;
    }

    public function getDependentVisa()
    {
        $query = $this->leadRepository->getLead();

        $getCountsDependentVisa = (clone $query)
            ->where('purpose', '4')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as close
            ")
            ->first();

        return $getCountsDependentVisa;
    }

}
