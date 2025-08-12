<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Repositories\Team\CoachingRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $filters;

    public function __construct(CoachingRepository $repository ,$filters = [])
    {
        $this->repository = $repository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;
        $query = $this->repository->getCoaching();

        // Branch
        $branch = !empty($filters['branch']) ? $filters['branch'] : ($filters['branch_dashboard'] ?? null);
        if (!empty($branch)) {
            $branchArray = explode(',', $branch);
            $query->whereIn('branch', $branchArray);
        }

        // Coaching
        $coaching = !empty($filters['coaching']) ? $filters['coaching'] : null;
        if (!empty($coaching)) {
            $coachingArray = explode(',', $coaching);
            $query->whereIn('coaching_id', $coachingArray);
        }

        // Batch
        $batch_id = !empty($filters['batch_id']) ? $filters['batch_id'] : null;
        if (!empty($batch_id)) {
            $batch_idArray = explode(',', $batch_id);
            $query->whereIn('batch_id', $batch_idArray);
        }

       $coachings = $query->with('getAttendances')->get();

        return $coachings->map(function ($coaching)use ($filters) {

            $attendanceCounts = $coaching->getAttendances
                ->groupBy('status')
                ->map(fn($items) => $items->count());

            return [
                'Date'          => $coaching->joining_date,
                'Client Name'   => $coaching?->clientLead->client->first_name .''. $coaching?->clientLead?->client->last_name?? '',
                'Client Code'   => $coaching->clientLead->client->client_code ?? '',
                'Client Email'  => $coaching->clientLead->client->email_id ?? '',
                'Mobile'  => '+'.$coaching->clientLead->client->country_code .' '. $coaching->clientLead->client->mobile_no?? '',
                'Branch'        => $coaching->clientLead->getBranch->branch_name ?? '',
                'Purpose'       => $coaching->clientLeadRegistration->getPurpose->name ?? '',
                'Coaching'       => $coaching->getCoaching->name ?? '',
                'Batch'       => $coaching->getBatch->name ?? '',
                'Faculty'       => $coaching->getFaculty->name ?? '',
                // Attendance counts
                'Present'       => $attendanceCounts['present'] ?? 0,
                'Absent'        => $attendanceCounts['absent'] ?? 0,
                'Nothing'        => $attendanceCounts['nothing'] ?? 0,


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
            'Present',
            'Absent',
            'Nothing',
        ];
    }
}
