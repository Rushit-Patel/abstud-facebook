<?php

namespace Database\Seeders;

use App\Models\EducationStream;
use Illuminate\Database\Seeder;

class EducationStreamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationStreams = [
            // 12th Grade Streams (Level 2)
            ['name' => 'Arts', 'education_level_id' => '2', 'status' => true],
            ['name' => 'Commerce', 'education_level_id' => '2', 'status' => true],
            ['name' => 'Science A Group', 'education_level_id' => '2', 'status' => true],
            ['name' => 'Science B Group', 'education_level_id' => '2', 'status' => true],
            ['name' => 'Science AB Group', 'education_level_id' => '2', 'status' => true],
            
            // Diploma Streams (Level 5)
            ['name' => 'Pharmacy', 'education_level_id' => '5', 'status' => true],
            ['name' => 'Nursing', 'education_level_id' => '5', 'status' => true],
            ['name' => 'Hotel Mgmt.', 'education_level_id' => '5', 'status' => true],
            ['name' => 'Automobile', 'education_level_id' => '5', 'status' => true],
            ['name' => 'Chemical', 'education_level_id' => '5', 'status' => true],
            
            // Bachelor Degree Streams (Level 3) & Engineering (Level 6)
            ['name' => 'Civil Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Computer Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Electronics & Communication', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Electrical Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Mechanical', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Biomedical', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Industrial Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Nuclear Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Aerospace Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Instrumentation & Control', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Airport Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Design Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Environmental Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Marine Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Mining Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Petroleum Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Textile Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Architecture Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Food Technology', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Forensic Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Genetic Engineering', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Information & Technology', 'education_level_id' => '3,6', 'status' => true],
            ['name' => 'Plastic Engineering', 'education_level_id' => '3,6', 'status' => true],
            
            // Bachelor & Master Streams (Level 3,4,7)
            ['name' => 'Science', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Education', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Psychology', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Physiotherapy', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Computer Application', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Business Administration', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Hospitalisty Management', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Social Work', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Legislative Law', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Homeopathic Medicine and Surgery', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Dental Surgery', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Architecture', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Designing', 'education_level_id' => '3,4', 'status' => true],
            ['name' => 'Computer Science', 'education_level_id' => '3,4,7', 'status' => true],
            ['name' => 'MBBS', 'education_level_id' => '3', 'status' => true],
            ['name' => 'Other', 'education_level_id' => '1,2,3,4,5,6,7,8', 'status' => true]
        ];

        foreach ($educationStreams as $stream) {
            EducationStream::updateOrCreate(
                ['name' => $stream['name']],
                $stream
            );
        }
    }
}
