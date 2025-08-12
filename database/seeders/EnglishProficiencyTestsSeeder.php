<?php

namespace Database\Seeders;

use App\Models\EnglishProficiencyTest;
use App\Models\EnglishProficiencyTestModual;
use Illuminate\Database\Seeder;

class EnglishProficiencyTestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proficiencyTests = [
            [
                'name' => 'IELTS',
                'status' => true,
                'modules' => [
                    [
                        'name' => 'Listening',
                        'minimum_score' => '0',
                        'maximum_score' => '9',
                        'range_score' => '0.5'
                    ],
                    [
                        'name' => 'Reading',
                        'minimum_score' => '0',
                        'maximum_score' => '9',
                        'range_score' => '0.5'
                    ],
                    [
                        'name' => 'Writing',
                        'minimum_score' => '0',
                        'maximum_score' => '9',
                        'range_score' => '0.5'
                    ],
                    [
                        'name' => 'Speaking',
                        'minimum_score' => '0',
                        'maximum_score' => '9',
                        'range_score' => '0.5'
                    ]
                ]
            ],
            [
                'name' => 'PTE',
                'status' => true,
                'modules' => [
                    [
                        'name' => 'Listening',
                        'minimum_score' => '10',
                        'maximum_score' => '90',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Reading',
                        'minimum_score' => '10',
                        'maximum_score' => '90',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Writing',
                        'minimum_score' => '10',
                        'maximum_score' => '90',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Speaking',
                        'minimum_score' => '10',
                        'maximum_score' => '90',
                        'range_score' => '1'
                    ]
                ]
            ],
            [
                'name' => 'GRE',
                'status' => true,
                'modules' => [
                    [
                        'name' => 'Verbal Reasoning',
                        'minimum_score' => '130',
                        'maximum_score' => '170',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Quantitative Reasoning',
                        'minimum_score' => '130',
                        'maximum_score' => '170',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Analytical Writing',
                        'minimum_score' => '0',
                        'maximum_score' => '6',
                        'range_score' => '0.5'
                    ]
                ]
            ],
            [
                'name' => 'DUOLINGO',
                'status' => true,
                'modules' => [
                    [
                        'name' => 'Overall Score',
                        'minimum_score' => '10',
                        'maximum_score' => '160',
                        'range_score' => '5'
                    ],
                    [
                        'name' => 'Literacy',
                        'minimum_score' => '10',
                        'maximum_score' => '160',
                        'range_score' => '5'
                    ],
                    [
                        'name' => 'Comprehension',
                        'minimum_score' => '10',
                        'maximum_score' => '160',
                        'range_score' => '5'
                    ],
                    [
                        'name' => 'Conversation',
                        'minimum_score' => '10',
                        'maximum_score' => '160',
                        'range_score' => '5'
                    ],
                    [
                        'name' => 'Production',
                        'minimum_score' => '10',
                        'maximum_score' => '160',
                        'range_score' => '5'
                    ]
                ]
            ],
            [
                'name' => 'TOEFL',
                'status' => true,
                'modules' => [
                    [
                        'name' => 'Reading',
                        'minimum_score' => '0',
                        'maximum_score' => '30',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Listening',
                        'minimum_score' => '0',
                        'maximum_score' => '30',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Speaking',
                        'minimum_score' => '0',
                        'maximum_score' => '30',
                        'range_score' => '1'
                    ],
                    [
                        'name' => 'Writing',
                        'minimum_score' => '0',
                        'maximum_score' => '30',
                        'range_score' => '1'
                    ]
                ]
            ]
        ];

        foreach ($proficiencyTests as $testData) {
            $test = EnglishProficiencyTest::updateOrCreate(
                ['name' => $testData['name']],
                [
                    'name' => $testData['name'],
                    'status' => $testData['status']
                ]
            );

            // Create modules for this test
            foreach ($testData['modules'] as $moduleData) {
                EnglishProficiencyTestModual::updateOrCreate(
                    [
                        'english_proficiency_tests_id' => $test->id,
                        'name' => $moduleData['name']
                    ],
                    [
                        'english_proficiency_tests_id' => $test->id,
                        'name' => $moduleData['name'],
                        'minimum_score' => $moduleData['minimum_score'],
                        'maximum_score' => $moduleData['maximum_score'],
                        'range_score' => $moduleData['range_score']
                    ]
                );
            }
        }
    }
}
