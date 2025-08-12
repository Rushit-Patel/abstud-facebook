<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RolesAndPermissionsSeeder;

class SeedGuardRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guard:seed-roles-permissions {--fresh : Remove existing roles and permissions before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed guard-specific roles and permissions for all authentication guards';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Seeding guard-based roles and permissions...');

        if ($this->option('fresh')) {
            $this->warn('⚠️  Removing existing roles and permissions...');
            
            // Clear existing roles and permissions
            \Spatie\Permission\Models\Role::truncate();
            \Spatie\Permission\Models\Permission::truncate();
            \DB::table('model_has_roles')->truncate();
            \DB::table('model_has_permissions')->truncate();
            \DB::table('role_has_permissions')->truncate();
            
            $this->info('✅ Existing roles and permissions cleared.');
        }

        // Run the seeder
        $seeder = new RolesAndPermissionsSeeder();
        $seeder->run();

        $this->info('✅ Guard-based roles and permissions seeded successfully!');
        
        // Display summary
        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Display a summary of created roles and permissions
     */
    private function displaySummary(): void
    {
        $this->info("\n📊 Summary:");
        
        $guards = ['web', 'student', 'partner'];
        
        foreach ($guards as $guard) {
            $roles = \Spatie\Permission\Models\Role::where('guard_name', $guard)->get();
            $permissions = \Spatie\Permission\Models\Permission::where('guard_name', $guard)->get();
            
            $this->info("\n🛡️  Guard: " . strtoupper($guard));
            $this->info("   Roles: " . $roles->count());
            $this->info("   Permissions: " . $permissions->count());
            
            if ($roles->count() > 0) {
                $this->line("   📋 Roles: " . $roles->pluck('name')->implode(', '));
            }
        }
        
        $this->info("\n🎯 Next steps:");
        $this->info("   1. Assign roles to users: \$user->assignGuardRole('Admin')");
        $this->info("   2. Check permissions: \$user->hasGuardPermission('manage_users')");
        $this->info("   3. Use middleware: Route::middleware('guard.role:web,Admin')");
    }
}
