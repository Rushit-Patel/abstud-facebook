<?php

namespace Database\Seeders;

use App\Models\OtherVisaType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OtherVisaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visaTypes = [
            [
                'name' => 'Student Visa',
                'status' => true,
            ],
            [
                'name' => 'Visitor Visa',
                'status' => true,
            ],
            [
                'name' => 'Dependent Visa',
                'status' => true,
            ],
            [
                'name' => 'Business Visa',
                'status' => true,
            ],
            [
                'name' => 'PR Visa',
                'status' => true,
            ],
            [
                'name' => 'Other Visa',
                'status' => true,
            ],
        ];

        foreach ($visaTypes as $visaType) {
            OtherVisaType::updateOrCreate(
                ['name' => $visaType['name']],
                $visaType
            );
        }
    }
}
