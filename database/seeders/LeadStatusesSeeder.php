<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use Illuminate\Database\Seeder;

class LeadStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leadStatuses = [
            [
                'name' => 'Open',
                'status' => true,
                'sub_statuses' => [
                    'Follow Up Required',
                    'In Progress',
                    'Pending Response',
                    'Waiting for Documents',
                    'Under Review'
                ]
            ],
            [
                'name' => 'Close',
                'status' => true,
                'sub_statuses' => [
                    'Not Interested',
                    'Budget Constraints',
                    'Wrong Contact',
                    'Duplicate Lead',
                    'Lost to Competitor'
                ]
            ],
            [
                'name' => 'Register',
                'status' => true,
                'sub_statuses' => [
                    'Converted to Student',
                    'Payment Completed',
                    'Documentation Complete',
                    'Application Submitted',
                    'Enrolled Successfully'
                ]
            ],
        ];

        foreach ($leadStatuses as $statusData) {
            $leadStatus = LeadStatus::updateOrCreate(
                ['name' => $statusData['name']],
                [
                    'name' => $statusData['name'],
                    'status' => $statusData['status']
                ]
            );

            // Create sub-statuses for this lead status
            foreach ($statusData['sub_statuses'] as $subStatusName) {
                LeadSubStatus::updateOrCreate(
                    [
                        'lead_status_id' => $leadStatus->id,
                        'name' => $subStatusName
                    ],
                    [
                        'lead_status_id' => $leadStatus->id,
                        'name' => $subStatusName,
                        'status' => true
                    ]
                );
            }
        }
    }
}
