<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Direct',
                'status' => true,
            ],
            [
                'name' => 'Events',
                'status' => true,
            ],
            [
                'name' => 'Facebook',
                'status' => true,
            ],
            [
                'name' => 'Instagram',
                'status' => true,
            ],
            [
                'name' => 'Google Ads',
                'status' => true,
            ],
            [
                'name' => 'Youtube',
                'status' => true,
            ],
        ];

        foreach ($sources as $source) {
            Source::updateOrCreate(
                ['name' => $source['name']],
                $source
            );
        }
    }
}
