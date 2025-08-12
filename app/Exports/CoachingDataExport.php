<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\RegistrationRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoachingDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $registrationRepository;
    protected $filters;

    public function __construct(CoachingRepository $repository ,RegistrationRepository $registrationRepository,$filters = [])
    {
        $this->repository = $repository;
        $this->registrationRepository = $registrationRepository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;

        if($filters['coaching_name'] == "Pending"){
            $query = $this->registrationRepository->getRegistration();
        }else{
            $query = $this->repository->getCoaching();
        }

        // Date range
        $date = !empty($filters['date']) ? $filters['date'] : null;
        if (!empty($date)) {
            if (str_contains($date, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $date);

                if($filters['coaching_name'] == "Pending"){
                    $query->whereBetween('reg_date', [
                        Helpers::parseToYmd($startDate),
                        Helpers::parseToYmd($endDate),
                    ]);
                }else{
                    $query->whereBetween('joining_date', [
                        Helpers::parseToYmd($startDate),
                        Helpers::parseToYmd($endDate),
                    ]);
                }
            } else {
                // Single date
                if($filters['coaching_name'] == "Pending"){
                    $query->whereDate('reg_date', Helpers::parseToYmd($date));
                }else{
                    $query->whereDate('joining_date', Helpers::parseToYmd($date));
                }
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
            if($filters['coaching_name'] == "Pending"){
                $query->whereIn('assign_owner', $ownerArray);
            }else{
                $query->whereIn('faculty', $ownerArray);
            }
        }

        if(!empty($filters['coaching_name'])){
            $today = Carbon::today()->format('Y-m-d');
            if($filters['coaching_name'] == "Pending"){
                $query->where('purpose' ,'2');
            }elseif($filters['coaching_name'] == "Running"){
                $query->where('is_complete_coaching',0)->where('is_drop_coaching',0);
            }elseif($filters['coaching_name'] == "Completed"){
                $query->where('is_complete_coaching',1)->where('is_drop_coaching',0);
            }elseif($filters['coaching_name'] == "Drop"){
                $query->where('is_drop_coaching',1);
            }else{

            }
        }

       $coachings = $query->get();

        return $coachings->map(function ($coaching)use ($filters) {
            if($filters['coaching_name'] == "Pending"){
                $date = $coaching->reg_date;
                $coachingData = $coaching->clientLead->getCoaching->name;
                $purposeData = $coaching->clientLead->getPurpose->name;
            }else{
                $date = $coaching->joining_date;
                $coachingData = $coaching->getCoaching->name;
                $purposeData = $coaching->clientLeadRegistration->getPurpose->name;
            }
            return [
                'Date'          => $date,
                'Client Name'   => $coaching?->clientLead->client->first_name .''. $coaching?->clientLead?->client->last_name?? '',
                'Client Code'   => $coaching->clientLead->client->client_code ?? '',
                'Client Email'  => $coaching->clientLead->client->email_id ?? '',
                'Mobile'  => '+'.$coaching->clientLead->client->country_code .' '. $coaching->clientLead->client->mobile_no?? '',
                'Branch'        => $coaching->clientLead->getBranch->branch_name ?? '',
                'Purpose'       => $purposeData ?? '',
                'Coaching'       => $coachingData ?? '',
                'Batch'       => $coaching->getBatch->name ?? '',
                'Faculty'       => $coaching->getFaculty->name ?? '',
                'Coaching Lenth'       => $coaching->coaching_length ?? '',
                'Is Complete' => ($coaching->is_complete_coaching ?? 0) == 1 ? 'Yes' : 'No',
                'Is Drop'     => ($coaching->is_drop_coaching ?? 0) == 1 ? 'Yes' : 'No',

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
            'Coaching',
            'Batch',
            'Faculty',
            'Coaching Lenth',
            'Is Complete',
            'Is Drop',
        ];
    }
}
