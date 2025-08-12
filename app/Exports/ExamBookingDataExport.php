<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Models\Client_E_P_T_Booking_Result;
use App\Models\EnglishProficiencyTestModual;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\ExamBookingRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamBookingDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $filters;

    public function __construct(ExamBookingRepository $repository ,$filters = [])
    {
        $this->repository = $repository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;
        $query = $this->repository->getExamBooking();

        // Date range
        $examDate = !empty($filters['exam_date']) ? $filters['exam_date'] : null;
        if (!empty($examDate)) {
            if (str_contains($examDate, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $examDate);
                $query->whereBetween('exam_date', [
                    Helpers::parseToYmd($startDate),
                    Helpers::parseToYmd($endDate),
                ]);
            } else {
                // Single date
                $query->whereDate('exam_date', Helpers::parseToYmd($examDate));
            }
        }

        // Date range
        $resultDate = !empty($filters['result_date']) ? $filters['result_date'] : null;
        if (!empty($resultDate)) {
            if (str_contains($resultDate, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $resultDate);
                $query->whereBetween('result_date', [
                    Helpers::parseToYmd($startDate),
                    Helpers::parseToYmd($endDate),
                ]);
            } else {
                // Single date
                $query->whereDate('result_date', Helpers::parseToYmd($resultDate));
            }
        }

        // Branch
        $branch = !empty($filters['branch']) ? $filters['branch'] : ($filters['branch_dashboard'] ?? null);
        if (!empty($branch)) {
            $branchArray = explode(',', $branch);
            $query->whereHas('clientLead', function ($q) use ($branchArray) {
                    $q->whereIn('branch', $branchArray);
                });
        }



        // Coaching
        $coaching = !empty($filters['coaching']) ? $filters['coaching'] : null;
        if (!empty($coaching)) {
            $coachingArray = explode(',', $coaching);
            $query->whereHas('clientCoaching', function ($q) use ($coachingArray) {
                    $q->whereIn('coaching_id', $coachingArray);
                });
        }

        // Batch
        $batch_id = !empty($filters['batch_id']) ? $filters['batch_id'] : null;
        if (!empty($batch_id)) {
            $batch_idArray = explode(',', $batch_id);
            $query->whereHas('clientCoaching', function ($q) use ($batch_idArray) {
                    $q->whereIn('batch_id', $batch_idArray);
                });
        }

        $moduleList = EnglishProficiencyTestModual::whereHas('results.booking', function($q) use ($filters, $query) {
        })->pluck('name', 'id')->toArray();

        $coachings = $query->with(['results.modual', 'clientLead.client', 'clientLead.getBranch', 'clientLead.getPurpose', 'clientCoaching.getCoaching', 'clientCoaching.getBatch'])->get();

        return $coachings->map(function ($coaching) use ($moduleList) {
            $row = [
                'Exam Date'   => $coaching->exam_date,
                'Result Date' => $coaching->result_date,
                'Client Name' => trim(($coaching->clientLead->client->first_name ?? '') . ' ' . ($coaching->clientLead->client->last_name ?? '')),
                'Client Code' => $coaching->clientLead->client->client_code ?? '',
                'Client Email'=> $coaching->clientLead->client->email_id ?? '',
                'Mobile'      => '+' . ($coaching->clientLead->client->country_code ?? '') . ' ' . ($coaching->clientLead->client->mobile_no ?? ''),
                'Branch'      => $coaching->clientLead->getBranch->branch_name ?? '',
                'Purpose'     => $coaching->clientCoaching->clientLeadRegistration->getPurpose->name ?? '',
                'Coaching'    => $coaching->clientCoaching->getCoaching->name ?? '',
                'Batch'       => $coaching->clientCoaching->getBatch->name ?? '',
            ];

            // Add dynamic module scores
            foreach ($moduleList as $moduleId => $moduleName) {
                $score = $coaching->results->firstWhere('exam_modual_id', $moduleId)->score ?? '';
                $row[$moduleName] = $score;
            }

            return $row;
        });


    }

    public function headings(): array
    {
        $staticHeadings = [
            'Exam Date',
            'Result Date',
            'Client Name',
            'Client Code',
            'Client Email',
            'Mobile',
            'Branch',
            'Purpose',
            'Coaching',
            'Batch',
        ];

        $usedModuleIds = Client_E_P_T_Booking_Result::query()
            ->whereHas('booking', function ($q) {
                // Yaha wahi filter lagao jo aap apne main query me lagate ho
            })
            ->distinct()
            ->pluck('exam_modual_id');

        $moduleList = EnglishProficiencyTestModual::whereIn('id', $usedModuleIds)
            ->pluck('name')
            ->toArray();

        return array_merge($staticHeadings, $moduleList);
    }
}
