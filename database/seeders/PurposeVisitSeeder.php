<?php

namespace Database\Seeders;

use App\Models\PurposeVisit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurposeVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purposeVisits = [
            [
                'name' => 'Business Visit',
                'status' => true,
            ],
            [
                'name' => 'Family Visit',
                'status' => true,
            ],
            [
                'name' => 'Tourist Visit',
                'status' => true,
            ],
            [
                'name' => 'Super Visa',
                'status' => true,
            ],
            [
                'name' => 'Convocation',
                'status' => true,
            ],
        ];

        foreach ($purposeVisits as $purposeVisit) {
            PurposeVisit::updateOrCreate(
                ['name' => $purposeVisit['name']],
                $purposeVisit
            );
        }
    }
}
