<?php

namespace Database\Seeders;

use App\Models\TypeOfRelative;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeOfRelativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeOfRelatives = [
            [
                'name' => 'Mother',
                'status' => true,
            ],
            [
                'name' => 'Father',
                'status' => true,
            ],
            [
                'name' => 'Sister',
                'status' => true,
            ],
            [
                'name' => 'Brother',
                'status' => true,
            ],
            [
                'name' => 'Grand Father',
                'status' => true,
            ],
            [
                'name' => 'Grand Mother',
                'status' => true,
            ],
            [
                'name' => 'Son',
                'status' => true,
            ],
            [
                'name' => 'Daughter',
                'status' => true,
            ],
            [
                'name' => 'Son/Daughter in Law',
                'status' => true,
            ],
            [
                'name' => 'Father/Mother in Law',
                'status' => true,
            ],
            [
                'name' => 'Spouse',
                'status' => true,
            ],
            [
                'name' => 'Friend / Other',
                'status' => true,
            ],
        ];

        foreach ($typeOfRelatives as $typeOfRelative) {
            TypeOfRelative::updateOrCreate(
                ['name' => $typeOfRelative['name']],
                $typeOfRelative
            );
        }
    }
}
