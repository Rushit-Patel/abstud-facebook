<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Repositories\Team\FollowRepository;
use App\Repositories\Team\LeadRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FollowUpDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $filters;

    public function __construct(FollowRepository $repository,$filters = [])
    {
        $this->repository = $repository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = $this->repository->getFollowUp();
        $filters = $this->filters;

        // Date range
        $date = !empty($filters['date']) ? $filters['date'] : null;
        if (!empty($date)) {
            if (str_contains($date, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $date);
                $query->whereBetween('followup_date', [
                    Helpers::parseToYmd($startDate),
                    Helpers::parseToYmd($endDate),
                ]);
            } else {
                // Single date
                $query->whereDate('followup_date', Helpers::parseToYmd($date));
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
            $query->whereIn('created_by', $ownerArray);
        }
        if(!empty($filters['follow_name'])){
            $today = Carbon::today()->format('Y-m-d');
            if($filters['follow_name'] == "Pending"){
                $query->where('status', '0')
                    ->where(function ($q) use ($today) {
                        $q->where('followup_date', $today)
                        ->orWhere('followup_date', '<', $today);
                    });
            }elseif($filters['follow_name'] == "Upcoming"){
                $query->where('status', '0')
                    ->where(function ($q) use ($today) {
                        $q->where('followup_date', '>', $today);
                    });
            }elseif($filters['follow_name'] == "Completed"){
                $query->where('status', '1');
            }else{

            }
        }

       $followUps = $query->get();

        return $followUps->map(function ($followUp) {
            return [
                'Date'          => $followUp->followup_date,
                'Client Name'   => $followUp?->clientLead->client->first_name .''. $followUp?->clientLead?->client->last_name?? '',
                'Client Code'   => $followUp->clientLead->client->client_code ?? '',
                'Client Email'  => $followUp->clientLead->client->email_id ?? '',
                'Mobile'  => '+'.$followUp->clientLead->client->country_code .' '. $followUp->clientLead->client->mobile_no?? '',
                'Branch'        => $followUp->clientLead->getBranch->branch_name ?? '',
                'Purpose'       => $followUp->clientLead->getPurpose->name ?? '',
                'Country'       => $followUp->clientLead->getForeignCountry->name ?? '',
                'Coaching'      => $followUp->clientLead->getCoaching->name ?? '',
                'Created Owner'   => $followUp->createdByUser->name ?? '',
                'Remarks'   => $followUp->remarks ?? '',
                'Communication'   => $followUp->communication ?? '',

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
            'Purpose',
            'Country',
            'Coaching',
            'Created Owner',
            'Remarks',
            'Communication',
        ];
    }
}
