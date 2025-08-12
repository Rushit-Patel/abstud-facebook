<?php

namespace Database\Seeders;

use App\Models\EducationBoard;
use Illuminate\Database\Seeder;

class EducationBoardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationBoards = [
            // Central Boards
            ['name' => 'CBSE', 'status' => true],
            ['name' => 'ICSE', 'status' => true],
            ['name' => 'ISC', 'status' => true],
            ['name' => 'NIOS', 'status' => true],
            ['name' => 'GSEB', 'status' => true],
            // Others
            ['name' => 'Other Board', 'status' => true]
        ];

        foreach ($educationBoards as $board) {
            EducationBoard::updateOrCreate(
                ['name' => $board['name']],
                $board
            );
        }
    }
}
