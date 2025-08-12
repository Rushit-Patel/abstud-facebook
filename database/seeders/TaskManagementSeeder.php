<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\TaskCategory;

class TaskManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed task priorities
        $priorities = [
            ['name' => 'Low', 'slug' => 'low', 'level' => 1, 'color' => '#10B981', 'is_active' => true],
            ['name' => 'Medium', 'slug' => 'medium', 'level' => 2, 'color' => '#F59E0B', 'is_active' => true],
            ['name' => 'High', 'slug' => 'high', 'level' => 3, 'color' => '#EF4444', 'is_active' => true],
            ['name' => 'Critical', 'slug' => 'critical', 'level' => 4, 'color' => '#DC2626', 'is_active' => true],
        ];

        foreach ($priorities as $priority) {
            TaskPriority::firstOrCreate(
                ['slug' => $priority['slug']], 
                $priority
            );
        }

        // Seed task statuses
        $statuses = [
            ['name' => 'Pending', 'slug' => 'pending', 'order' => 1, 'color' => '#6B7280', 'is_completed' => false, 'is_active' => true],
            ['name' => 'In Progress', 'slug' => 'in-progress', 'order' => 2, 'color' => '#3B82F6', 'is_completed' => false, 'is_active' => true],
            ['name' => 'Review', 'slug' => 'review', 'order' => 3, 'color' => '#8B5CF6', 'is_completed' => false, 'is_active' => true],
            ['name' => 'Completed', 'slug' => 'completed', 'order' => 4, 'color' => '#10B981', 'is_completed' => true, 'is_active' => true],
            ['name' => 'Cancelled', 'slug' => 'cancelled', 'order' => 5, 'color' => '#EF4444', 'is_completed' => true, 'is_active' => true],
        ];

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate(
                ['slug' => $status['slug']], 
                $status
            );
        }

        // Seed default categories for student visa industry
        $categories = [
            ['name' => 'Application Processing', 'slug' => 'application-processing', 'description' => 'Student visa application processing tasks', 'color' => '#3B82F6', 'is_active' => true],
            ['name' => 'Document Verification', 'slug' => 'document-verification', 'description' => 'Document verification and validation tasks', 'color' => '#8B5CF6', 'is_active' => true],
            ['name' => 'Client Consultation', 'slug' => 'client-consultation', 'description' => 'Student consultation and counseling tasks', 'color' => '#10B981', 'is_active' => true],
            ['name' => 'University Coordination', 'slug' => 'university-coordination', 'description' => 'University admission and coordination tasks', 'color' => '#F59E0B', 'is_active' => true],
            ['name' => 'Visa Interview Prep', 'slug' => 'visa-interview-prep', 'description' => 'Visa interview preparation and training tasks', 'color' => '#EF4444', 'is_active' => true],
            ['name' => 'Financial Documentation', 'slug' => 'financial-documentation', 'description' => 'Financial document preparation and verification tasks', 'color' => '#06B6D4', 'is_active' => true],
            ['name' => 'English Proficiency', 'slug' => 'english-proficiency', 'description' => 'IELTS/TOEFL preparation and testing tasks', 'color' => '#84CC16', 'is_active' => true],
            ['name' => 'Pre-Departure', 'slug' => 'pre-departure', 'description' => 'Pre-departure briefing and orientation tasks', 'color' => '#F97316', 'is_active' => true],
            ['name' => 'Follow-up', 'slug' => 'follow-up', 'description' => 'Student follow-up and support tasks', 'color' => '#EC4899', 'is_active' => true],
            ['name' => 'Administrative', 'slug' => 'administrative', 'description' => 'General administrative and operational tasks', 'color' => '#6B7280', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            TaskCategory::firstOrCreate(
                ['slug' => $category['slug']], 
                $category
            );
        }
    }
}
