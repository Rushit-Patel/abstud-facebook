<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Company Settings Data
        $companyData = [
            'id' => 1,
            'company_name' => 'Abstud',
            'company_logo' => 'default/images/logo/logo.png',
            'company_favicon' => 'default/images/logo/fav.png',
            'website_url' => 'https://abstud.io/',
            'company_address' => '1201, The Capital 2, Science City Rd, Sola, Ahmedabad, Gujarat 380060',
            'phone' => '90995 89276',
            'email' => 'projects@abstud.io',
            'country_id' => 101,
            'state_id' => 4030,
            'city_id' => 57606,
            'postal_code' => null,
            'is_setup_completed' => true,
        ];

        // Roles Data
        $roles = [
            'Super Admin',
            'Admin',
        ];

        // Branches Data
        $branchesData = [
            [
                'id' => 1,
                'branch_code' => 'ABHO',
                'branch_name' => 'Main Branch',
                'address' => '1201, The Capital 2, Science City Rd, Sola, Ahmedabad, Gujarat 380060',
                'country_id' => 101,
                'state_id' => 4030,
                'city_id' => 57606,
                'postal_code' => '380060',
                'phone' => '9099589276',
                'email' => 'projects@abstud.io',
                'timezone' => null,
                'is_main_branch' => true,
                'is_active' => true,
            ]
        ];

        // Users Data for each branch with different roles
        $usersData = [
            // Main Branch Users
            [
                'name' => 'Super Admin',
                'email' => 'admin@abstud.io',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9099589276',
                'is_active' => true,
                'branch_code' => 'ABHO',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Admin',
                'email' => 'projects@abstud.io',
                'username' => 'admin1',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9099589276',
                'is_active' => true,
                'branch_code' => 'ABHO',
                'role' => 'Admin'
            ],
        ];

        // Create or update company settings
        CompanySetting::updateOrCreate(
            ['id' => $companyData['id']],
            $companyData
        );

        // Create or update branches
        $createdBranches = [];
        foreach ($branchesData as $branchData) {
            $branch = Branch::updateOrCreate(
                ['branch_code' => $branchData['branch_code']],
                $branchData
            );
            $createdBranches[$branchData['branch_code']] = $branch;
            $this->command->info("Branch '{$branch->branch_name}' created/updated successfully!");
        }

        // Create or update users with roles
        foreach ($usersData as $userData) {
            // Get the branch for this user
            $branch = $createdBranches[$userData['branch_code']];
            
            // Set the correct branch_id
            $userData['branch_id'] = $branch->id;
            
            // Remove the branch_code and role from user data as they are not needed for user creation
            $role = $userData['role'];
            unset($userData['branch_code'], $userData['role']);

            // Create or update user
            $user = User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );

            // Assign role to the user
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }

            $this->command->info("User '{$user->name}' with role '{$role}' created/updated for branch '{$branch->branch_name}'!");
        }

        $this->command->info('Company settings, branches, and users with roles seeded successfully!');
    }
}
