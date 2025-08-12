<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationLevels = [
            [
                'name' => '10TH GRADE',
                'status' => true,
                'required_details' => json_encode([
                    "board",
                    "language",
                    "passing_year",
                    "result",
                ])
            ],
            [
                'name' => '12TH GRADE',
                'status' => true,
                'required_details' => json_encode([
                    "board",
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                ])
            ],
            [
                'name' => 'BACHELOR DEGREE',
                'status' => true,
                'required_details' => json_encode([
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                    "no_of_backlog",
                    "institute"
                ])
            ],
            [
                'name' => 'MASTER DEGREE',
                'status' => true,
                'required_details' => json_encode([
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                    "no_of_backlog",
                    "institute"
                ])
            ],
            [
                'name' => 'DIPLOMA CERTIFICATE',
                'status' => true,
                'required_details' => json_encode([
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                    "no_of_backlog",
                    "institute"
                ])
            ],
            [
                'name' => 'BACHELOR OF ENGINEERING (4 YEARS)',
                'status' => true,
                'required_details' => json_encode([
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                    "no_of_backlog",
                    "institute"
                ])
            ],
            [
                'name' => 'MASTER OF ENGINEERING (2 YEARS)',
                'status' => true,
                'required_details' => json_encode([
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                    "no_of_backlog",
                    "institute"
                ])
            ],
            [
                'name' => 'Post Graduate Diploma',
                'status' => true,
                'required_details' => json_encode([
                    "language",
                    "stream",
                    "passing_year",
                    "result",
                    "no_of_backlog",
                    "institute"
                ])
            ]
        ];

        foreach ($educationLevels as $level) {
            EducationLevel::updateOrCreate(
                ['name' => $level['name']],
                $level
            );
        }
    }
}
