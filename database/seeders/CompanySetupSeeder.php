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
            'company_name' => 'Global Colliance',
            'company_logo' => 'company/logos/GXW7ZAPRI1nnTlLvQ7lJya0q17vMbroE43kO8p2h.png',
            'company_favicon' => 'company/favicons/dSvM3uLkO2NFuZG0T1C6ioxf2gWJ7d7CazA42Fzn.png',
            'website_url' => 'https://globalcolliance.com',
            'company_address' => 'Panjrapole, 303/304, Addor Aspire - 1, University Rd, near Old Passport Office, Ahmedabad, Gujarat 380015',
            'phone' => null,
            'email' => null,
            'country_id' => 101,
            'state_id' => 4030,
            'city_id' => 57606,
            'postal_code' => null,
            'is_setup_completed' => true,
        ];

        // Roles Data
        $roles = [
            'Super Admin',
            'Branch Admin',
            'Frontdesk Executive',
            'Visa Advisor',
            'Country Head'
        ];

        // Branches Data
        $branchesData = [
            [
                'id' => 1,
                'branch_code' => 'GCHO',
                'branch_name' => 'Main Branch',
                'address' => 'Panjrapole, 303/304, Addor Aspire - 1, University Rd, near Old Passport Office, Ahmedabad, Gujarat 380015',
                'country_id' => 101,
                'state_id' => 4030,
                'city_id' => 57606,
                'postal_code' => '380015',
                'phone' => '7574033366',
                'email' => 'info@globalcolliance.com',
                'timezone' => null,
                'is_main_branch' => true,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'branch_code' => 'GCMB',
                'branch_name' => 'Mumbai Branch',
                'address' => 'Mumbai Office, Bandra Kurla Complex, Mumbai, Maharashtra 400051',
                'country_id' => 101,
                'state_id' => 4008,
                'city_id' => 56949,
                'postal_code' => '400051',
                'phone' => '9876543210',
                'email' => 'mumbai@globalcolliance.com',
                'timezone' => null,
                'is_main_branch' => false,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'branch_code' => 'GCDL',
                'branch_name' => 'Delhi Branch',
                'address' => 'Delhi Office, Connaught Place, New Delhi 110001',
                'country_id' => 101,
                'state_id' => 4012,
                'city_id' => 56968,
                'postal_code' => '110001',
                'phone' => '9876543211',
                'email' => 'delhi@globalcolliance.com',
                'timezone' => null,
                'is_main_branch' => false,
                'is_active' => true,
            ]
        ];

        // Users Data for each branch with different roles
        $usersData = [
            // Main Branch Users
            [
                'name' => 'Super Admin',
                'email' => 'admin@globalcolliance.com',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '7574033366',
                'is_active' => true,
                'branch_code' => 'GCHO',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Branch Admin Ahmedabad',
                'email' => 'branchadmin.ahd@globalcolliance.com',
                'username' => 'branchadmin_ahd',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '7574033367',
                'is_active' => true,
                'branch_code' => 'GCHO',
                'role' => 'Branch Admin'
            ],
            [
                'name' => 'Frontdesk Executive Ahmedabad',
                'email' => 'frontdesk.ahd@globalcolliance.com',
                'username' => 'frontdesk_ahd',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '7574033368',
                'is_active' => true,
                'branch_code' => 'GCHO',
                'role' => 'Frontdesk Executive'
            ],
            [
                'name' => 'Visa Advisor Ahmedabad',
                'email' => 'visaadvisor.ahd@globalcolliance.com',
                'username' => 'visaadvisor_ahd',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '7574033369',
                'is_active' => true,
                'branch_code' => 'GCHO',
                'role' => 'Visa Advisor'
            ],
            [
                'name' => 'Country Head Ahmedabad',
                'email' => 'countryhead.ahd@globalcolliance.com',
                'username' => 'countryhead_ahd',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '7574033370',
                'is_active' => true,
                'branch_code' => 'GCHO',
                'role' => 'Country Head'
            ],

            // Mumbai Branch Users
            [
                'name' => 'Branch Admin Mumbai',
                'email' => 'branchadmin.mumbai@globalcolliance.com',
                'username' => 'branchadmin_mumbai',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543212',
                'is_active' => true,
                'branch_code' => 'GCMB',
                'role' => 'Branch Admin'
            ],
            [
                'name' => 'Frontdesk Executive Mumbai',
                'email' => 'frontdesk.mumbai@globalcolliance.com',
                'username' => 'frontdesk_mumbai',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543213',
                'is_active' => true,
                'branch_code' => 'GCMB',
                'role' => 'Frontdesk Executive'
            ],
            [
                'name' => 'Visa Advisor Mumbai',
                'email' => 'visaadvisor.mumbai@globalcolliance.com',
                'username' => 'visaadvisor_mumbai',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543214',
                'is_active' => true,
                'branch_code' => 'GCMB',
                'role' => 'Visa Advisor'
            ],
            [
                'name' => 'Country Head Mumbai',
                'email' => 'countryhead.mumbai@globalcolliance.com',
                'username' => 'countryhead_mumbai',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543215',
                'is_active' => true,
                'branch_code' => 'GCMB',
                'role' => 'Country Head'
            ],

            // Delhi Branch Users
            [
                'name' => 'Branch Admin Delhi',
                'email' => 'branchadmin.delhi@globalcolliance.com',
                'username' => 'branchadmin_delhi',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543216',
                'is_active' => true,
                'branch_code' => 'GCDL',
                'role' => 'Branch Admin'
            ],
            [
                'name' => 'Frontdesk Executive Delhi',
                'email' => 'frontdesk.delhi@globalcolliance.com',
                'username' => 'frontdesk_delhi',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543217',
                'is_active' => true,
                'branch_code' => 'GCDL',
                'role' => 'Frontdesk Executive'
            ],
            [
                'name' => 'Visa Advisor Delhi',
                'email' => 'visaadvisor.delhi@globalcolliance.com',
                'username' => 'visaadvisor_delhi',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543218',
                'is_active' => true,
                'branch_code' => 'GCDL',
                'role' => 'Visa Advisor'
            ],
            [
                'name' => 'Country Head Delhi',
                'email' => 'countryhead.delhi@globalcolliance.com',
                'username' => 'countryhead_delhi',
                'password' => Hash::make('password'),
                'base_password' => base64_encode('password'),
                'phone' => '9876543219',
                'is_active' => true,
                'branch_code' => 'GCDL',
                'role' => 'Country Head'
            ]
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
