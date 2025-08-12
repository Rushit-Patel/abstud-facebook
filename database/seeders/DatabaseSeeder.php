<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            LeadTypesSeeder::class,
            LeadTagSeeder::class,
            OtherVisaTypeSeeder::class,
            MaritalStatusSeeder::class,
            PurposeVisitSeeder::class,
            VisitorApplicantSeeder::class,
            TypeOfRelativeSeeder::class,
            PurposesSeeder::class,
            SourcesSeeder::class,
            LeadStatusesSeeder::class,
            ForeignCountriesSeeder::class,
            CoachingsSeeder::class,
            EnglishProficiencyTestsSeeder::class,
            EducationLevelsSeeder::class,
            EducationStreamsSeeder::class,
            EducationBoardsSeeder::class,
            CompanySetupSeeder::class,
            WhatsappProviderSeeder::class,
            TaskManagementSeeder::class,
            TeamNotificationTypeSeeder::class,
            EmailTemplateSeeder::class,
            NotificationConfigSeeder::class,
            
        ]);
    }
}
