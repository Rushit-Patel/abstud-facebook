<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions following the module:action format
        $permissions = [
            // Branch permissions
            'branch:create',
            'branch:edit',
            'branch:delete',
            'branch:view',

            // User permissions
            'user:create',
            'user:edit',
            'user:delete',
            'user:view',
            'user:show-branch',
            'user:show-all',

            // Lead permissions
            'lead:create',
            'lead:edit',
            'lead:delete',
            'lead:show-branch',
            'lead:purpose',
            'lead:country',
            'lead:coaching',
            'lead:show-all',
            'lead:show',
            'lead:export',

            // Follow-up permissions
            'follow-up:create',
            'follow-up:edit',
            'follow-up:delete',
            'follow-up:show-branch',
            'follow-up:show-all',
            'follow-up:show',
            'follow-up:export',

            // Demo permissions
            'demo:create',
            'demo:edit',
            'demo:delete',
            'demo:show-branch',
            'demo:show-all',
            'demo:show',
            'demo:export',

            // Invoice permissions
            'invoice:create',
            'invoice:edit',
            'invoice:delete',
            'invoice:show-branch',
            'invoice:show-all',
            'invoice:show',
            'invoice:export',

            // Coaching permissions
            'coaching:create',
            'coaching:edit',
            'coaching:delete',
            'coaching:show-branch',
            'coaching:show-all',
            'coaching:show',
            'coaching:export',

            // Coaching material permissions
            'coaching-material:create',
            'coaching-material:edit',
            'coaching-material:delete',
            'coaching-material:show-branch',
            'coaching-material:show-all',
            'coaching-material:show',
            'coaching-material:export',

            // Application permissions
            'application:create',
            'application:edit',
            'application:delete',
            'application:show-branch',
            'application:show-all',
            'application:show',
            'application:export',

            // Master module permissions
            'master-module:country',
            'master-module:state',
            'master-module:city',
            'master-module:lead-type',
            'master-module:purpose',
            'master-module:source',
            'master-module:lead-status',
            'master-module:lead-sub-status',
            'master-module:destination-country',
            'master-module:coaching',
            'master-module:email-template',
            'master-module:english-proficiency-test',
            'master-module:education-level',
            'master-module:education-stream',
            'master-module:education-board',

            'automation:create',
            'automation:edit',
            'automation:delete',
            'automation:view',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'Super Admin',
            'Admin',
            'Frontdesk Executive',
            'Visa Advisor'
        ];

        foreach ($roles as $roleName) {
            Role::updateOrCreate(['name' => $roleName]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles based on their responsibilities
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $branchAdmin = Role::findByName('Admin');
        $branchAdmin->givePermissionTo([
            'user:create',
            'user:edit',
            'user:delete',
            'user:view',
            'user:show-branch',
            'lead:create',
            'lead:edit',
            'lead:delete',
            'lead:show-branch',
            'lead:show',
            'lead:export',
            'follow-up:create',
            'follow-up:edit',
            'follow-up:delete',
            'follow-up:show-branch',
            'follow-up:show',
            'follow-up:export',
            'demo:delete',
            'demo:show-branch',
            'demo:show',
            'demo:export',
            'invoice:delete',
            'invoice:show-branch',
            'invoice:show',
            'invoice:export',
            'coaching:edit',
            'coaching:delete',
            'coaching:show-branch',
            'coaching:show',
            'coaching:export',
            'automation:create',
            'automation:edit',
            'automation:delete',
            'automation:view',
        ]);
        $frontdeskExecutive = Role::findByName('Frontdesk Executive');
        $frontdeskExecutive->givePermissionTo([
            'lead:create',
            'lead:edit',
            'lead:show-branch',
            'lead:show',
            'follow-up:create',
            'follow-up:edit',
            'follow-up:show',
        ]);

        $visaAdvisor = Role::findByName('Visa Advisor');
        $visaAdvisor->givePermissionTo([
            'lead:create',
            'lead:edit',
            'lead:show',
            'follow-up:create',
            'follow-up:edit',
            'follow-up:show',
            'demo:create',
            'demo:edit',
            'demo:show',
            'invoice:create',
            'invoice:edit',
            'invoice:show',
            'coaching:show'
        ]);
    }
}
