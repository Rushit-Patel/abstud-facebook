<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maritalStatuses = [
            [
                'name' => 'Single',
                'status' => true,
            ],
            [
                'name' => 'Married',
                'status' => true,
            ],
            [
                'name' => 'Divorced',
                'status' => true,
            ],
        ];

        foreach ($maritalStatuses as $maritalStatus) {
            MaritalStatus::updateOrCreate(
                ['name' => $maritalStatus['name']],
                $maritalStatus
            );
        }
    }
}
