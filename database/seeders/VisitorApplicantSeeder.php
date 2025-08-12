<?php

namespace Database\Seeders;

use App\Models\VisitorApplicant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitorApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visitorApplicants = [
            [
                'name' => 'FOR SINGLE',
                'no_of_applicant' => 1,
                'status' => true,
            ],
            [
                'name' => 'COUPLE ( HUSBAND & WIFE )',
                'no_of_applicant' => 2,
                'status' => true,
            ],
            [
                'name' => 'COUPLE WITH ONE CHILD',
                'no_of_applicant' => 3,
                'status' => true,
            ],
            [
                'name' => 'COUPLE WITH TWO CHILDREN',
                'no_of_applicant' => 4,
                'status' => true,
            ],
            [
                'name' => 'FULL FAMILY',
                'no_of_applicant' => 5,
                'status' => true,
            ],
        ];

        foreach ($visitorApplicants as $visitorApplicant) {
            VisitorApplicant::updateOrCreate(
                ['name' => $visitorApplicant['name']],
                $visitorApplicant
            );
        }
    }
}
