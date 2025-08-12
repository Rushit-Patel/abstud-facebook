<?php

namespace Database\Seeders;

use App\Models\ForeignCountry;
use Illuminate\Database\Seeder;

class ForeignCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foreignCountries = [
            [
                'name' => 'Canada',
                'status' => true,
                'priority' => 1,
            ],
            [
                'name' => 'UK',
                'status' => true,
                'priority' => 2,
            ],
            [
                'name' => 'USA',
                'status' => true,
                'priority' => 3,
            ],
            [
                'name' => 'Australia',
                'status' => true,
                'priority' => 4,
            ],
            [
                'name' => 'Germany',
                'status' => true,
                'priority' => 5,
            ],
            [
                'name' => 'UAE',
                'status' => true,
                'priority' => 6,
            ],
            [
                'name' => 'New Zealand',
                'status' => true,
                'priority' => 7,
            ],
        ];

        foreach ($foreignCountries as $foreignCountry) {
            ForeignCountry::updateOrCreate(
                ['name' => $foreignCountry['name']],
                $foreignCountry
            );
        }
    }
}
