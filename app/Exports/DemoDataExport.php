<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Repositories\Team\DemoRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DemoDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $filters;

    public function __construct(DemoRepository $repository,$filters = [])
    {
        $this->repository = $repository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = $this->repository->getDemo();
        $filters = $this->filters;

        // Date range
        $date = !empty($filters['date']) ? $filters['date'] : ($filters['date_dashboard'] ?? null);
        if (!empty($date)) {
            if (str_contains($date, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $date);
                $query->whereBetween('demo_date', [
                    Helpers::parseToYmd($startDate),
                    Helpers::parseToYmd($endDate),
                ]);
            } else {
                // Single date
                $query->whereDate('demo_date', Helpers::parseToYmd($date));
            }
        }

        // Branch
        $branch = !empty($filters['branch']) ? $filters['branch'] : ($filters['branch_dashboard'] ?? null);
        if (!empty($branch)) {
            $branchArray = explode(',', $branch);

            $query->whereHas('clientLead', function ($q) use ($branchArray) {
                $q->whereHas('getBranch', function ($q2) use ($branchArray) {
                    $q2->whereIn('branch', $branchArray);
                });
            });
        }
        // Owner
        if (!empty($filters['owner'])) {
            $ownerArray = explode(',', $filters['owner']);
            $query->whereIn('assign_owner', $ownerArray);
        }
        if(!empty($filters['demo_name'])){
            if($filters['demo_name'] == "Pending"){
                $query->where('status', '0');
            }elseif($filters['demo_name'] == "Attended"){
                $query->where('status', '1');
            }elseif($filters['demo_name'] == "Cancelled"){
                $query->where('status', '2');
            }else{

            }
        }

       $demos = $query->get();

        return $demos->map(function ($demo) {
            return [
                'Date'          => $demo->demo_date,
                'Client Name'   => $demo?->clientLeadDetails->first_name .''. $demo?->clientLeadDetails->last_name?? '',
                'Client Code'   => $demo->clientLeadDetails->client_code ?? '',
                'Client Email'  => $demo->clientLeadDetails->email_id ?? '',
                'Mobile'  => '+'.$demo->clientLeadDetails->country_code .' '. $demo->clientLeadDetails->mobile_no?? '',
                'Branch'        => $demo->clientLead->getBranch->branch_name ?? '',
                'Coaching'      => $demo->clientLead->getCoaching->name ?? '',
                'Batch'      => $demo->getDemoBatch->name .' ' .$demo->getDemoBatch->time,
                'Assign Owner'   => $demo->getDemoAssignOwner->name ?? '',
            ];
        });

    }

    public function headings(): array
    {
        return [
            'Date',
            'Client Name',
            'Client Code',
            'Client Email',
            'Mobile',
            'Branch',
            'Coaching',
            'Batch',
            'Assign Owner',
        ];
    }
}
