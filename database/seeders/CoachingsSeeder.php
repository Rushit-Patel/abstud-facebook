<?php

namespace Database\Seeders;

use App\Models\Coaching;
use Illuminate\Database\Seeder;

class CoachingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coachings = [
            [
                'name' => 'IELTS',
                'status' => true,
                'priority' => 1,
            ],
            [
                'name' => 'PTE',
                'status' => true,
                'priority' => 2,
            ],
            [
                'name' => 'DUOLINGO',
                'status' => true,
                'priority' => 3,
            ],
            [
                'name' => 'TOEFL',
                'status' => true,
                'priority' => 4,
            ],
        ];

        foreach ($coachings as $coaching) {
            Coaching::updateOrCreate(
                ['name' => $coaching['name']],
                $coaching
            );
        }
    }
}
