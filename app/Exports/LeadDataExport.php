<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Repositories\Team\LeadRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadDataExport implements FromCollection, WithHeadings
{
    protected $repository;
    protected $filters;

    public function __construct(LeadRepository $repository,$filters = [])
    {
        $this->repository = $repository;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = $this->repository->getLead();
        $filters = $this->filters;

        // Date range
        $date = !empty($filters['date']) ? $filters['date'] : ($filters['date_dashboard'] ?? null);
        if (!empty($date)) {
            if (str_contains($date, ' to ')) {
                // Date range
                [$startDate, $endDate] = explode(' to ', $date);
                $query->whereBetween('client_date', [
                    Helpers::parseToYmd($startDate),
                    Helpers::parseToYmd($endDate),
                ]);
            } else {
                // Single date
                $query->whereDate('client_date', Helpers::parseToYmd($date));
            }
        }

        // Status
        $status = !empty($filters['status']) ? $filters['status'] : ($filters['status_dashboard'] ?? null);
        if (!empty($status)) {
            $statusArray = explode(',', $status);
            $query->whereIn('status', $statusArray);
        }

        $purpose = !empty($filters['purpose']) ? $filters['purpose'] : ($filters['purpose_dashboard'] ?? null);
        if (!empty($purpose)) {
            $purposeArray = explode(',', $purpose);
            $query->whereIn('purpose', $purposeArray);
        }

        // Branch
        $branch = !empty($filters['branch']) ? $filters['branch'] : ($filters['branch_dashboard'] ?? null);
        if (!empty($branch)) {
            $branchArray = explode(',', $branch);
            $query->whereIn('branch', $branchArray);
        }
        // Owner
        if (!empty($filters['owner'])) {
            $ownerArray = explode(',', $filters['owner']);
            $query->whereIn('assign_owner', $ownerArray);
        }

        // Source
        if (!empty($filters['source'])) {
            $sourceArray = explode(',', $filters['source']);
            $query->whereIn('source', $sourceArray);
        }

        // Lead Type
        if (!empty($filters['lead_type'])) {
            $typeArray = explode(',', $filters['lead_type']);
            $query->whereIn('lead_type', $typeArray);
        }

       $leads = $query->get();

        return $leads->map(function ($lead) {

            return [
                'Date'          => Helpers::parseToDmy($lead->client_date),
                'Client Name'   => $lead->client->first_name .''. $lead->client->last_name?? '',
                'Client Code'   => $lead->client->client_code ?? '',
                'Client Email'  => $lead->client->email_id ?? '',
                'Mobile'  => '+'.$lead->client->country_code .' '. $lead->client->mobile_no?? '',
                'Branch'        => $lead->getBranch->branch_name ?? '',
                'Purpose'       => $lead->getPurpose->name ?? '',
                'Country'       => $lead->getForeignCountry->name ?? '',
                'Coaching'       => $lead->getCoaching->name ?? '',
                'Assigned To'   => $lead->assignedOwner->name ?? '',
                'Lead Type'   => $lead->getLeadType->name ?? '',
                'Source' => optional($lead->getSource)->name ?? optional($lead->client?->getSource)->name ?? '',
                'Remark'   => $lead->remark ?? '',
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
            'Assigned To',
            'Lead Type',
            'Source',
            'Remark',
        ];
    }
}
