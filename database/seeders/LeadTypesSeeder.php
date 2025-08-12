<?php

namespace Database\Seeders;

use App\Models\LeadType;
use Illuminate\Database\Seeder;

class LeadTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leadTypes = [
            [
                'name' => 'Direct Walk-In',
                'status' => true,
            ],
            [
                'name' => 'Virtual',
                'status' => true,
            ],
        ];

        foreach ($leadTypes as $leadType) {
            LeadType::updateOrCreate(
                ['name' => $leadType['name']],
                $leadType
            );
        }
    }
}
