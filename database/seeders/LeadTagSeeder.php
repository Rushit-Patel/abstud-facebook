<?php

namespace Database\Seeders;

use App\Models\LeadTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leadTags = [
            [
                'name' => '🔥 Hot',
                'status' => true,
            ],
            [
                'name' => '🌤️ Warm',
                'status' => true,
            ],
            [
                'name' => '❄️ Cold',
                'status' => true,
            ],
        ];

        foreach ($leadTags as $tag) {
            LeadTag::updateOrCreate(
                ['name' => $tag['name']],
                $tag
            );
        }
    }
}
