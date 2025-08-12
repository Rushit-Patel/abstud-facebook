<?php

namespace Database\Seeders;

use App\Models\Purpose;
use Illuminate\Database\Seeder;

class PurposesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purposes = [
            [
                'name' => 'Student Visa',
                'status' => true,
                'priority' => 1,
            ],
            [
                'name' => 'Coaching',
                'status' => true,
                'priority' => 2,
            ],
            [
                'name' => 'Visitor Visa',
                'status' => true,
                'priority' => 3,
            ],
            [
                'name' => 'Dependent Visa',
                'status' => true,
                'priority' => 4,
            ],
        ];

        foreach ($purposes as $purpose) {
            Purpose::updateOrCreate(
                ['name' => $purpose['name']],
                $purpose
            );
        }
    }
}
