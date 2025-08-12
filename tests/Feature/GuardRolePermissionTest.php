<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Partner;
use App\Services\GuardRolePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected GuardRolePermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = app(GuardRolePermissionService::class);
        
        // Seed roles and permissions
        $this->artisan('guard:seed-roles-permissions');
    }

    /** @test */
    public function web_guard_user_can_have_admin_role()
    {
        $user = User::factory()->create();
        
        $result = $this->service->assignRoleToUser($user, 'Super Admin');
        
        $this->assertTrue($result);
        $this->assertTrue($user->hasGuardRole('Super Admin'));
        $this->assertTrue($user->hasGuardPermission('manage_users'));
    }

    /** @test */
    public function student_guard_user_can_have_active_student_role()
    {
        $student = Student::factory()->create();
        
        $result = $this->service->assignRoleToUser($student, 'Active Student');
        
        $this->assertTrue($result);
        $this->assertTrue($student->hasGuardRole('Active Student'));
        $this->assertTrue($student->hasGuardPermission('view_profile'));
        $this->assertTrue($student->hasGuardPermission('enroll_courses'));
    }

    /** @test */
    public function partner_guard_user_can_have_corporate_partner_role()
    {
        $partner = Partner::factory()->create();
        
        $result = $this->service->assignRoleToUser($partner, 'Corporate Partner');
        
        $this->assertTrue($result);
        $this->assertTrue($partner->hasGuardRole('Corporate Partner'));
        $this->assertTrue($partner->hasGuardPermission('view_profile'));
        $this->assertTrue($partner->hasGuardPermission('create_job_postings'));
    }

    /** @test */
    public function users_cannot_access_permissions_from_other_guards()
    {
        $user = User::factory()->create();
        $this->service->assignRoleToUser($user, 'Super Admin');
        
        // Web guard user should not have student permissions
        $this->assertFalse($user->hasGuardPermission('enroll_courses'));
        
        // Web guard user should not have partner permissions
        $this->assertFalse($user->hasGuardPermission('create_job_postings'));
    }

    /** @test */
    public function guard_service_can_get_roles_by_guard()
    {
        $webRoles = $this->service->getRolesForGuard('web');
        $studentRoles = $this->service->getRolesForGuard('student');
        
        $this->assertGreaterThan(0, $webRoles->count());
        $this->assertGreaterThan(0, $studentRoles->count());
        
        $this->assertTrue($webRoles->contains('name', 'Super Admin'));
        $this->assertTrue($studentRoles->contains('name', 'Active Student'));
    }

    /** @test */
    public function guard_service_can_get_permissions_by_guard()
    {
        $webPermissions = $this->service->getPermissionsForGuard('web');
        $studentPermissions = $this->service->getPermissionsForGuard('student');
        
        $this->assertGreaterThan(0, $webPermissions->count());
        $this->assertGreaterThan(0, $studentPermissions->count());
        
        $this->assertTrue($webPermissions->contains('name', 'manage_users'));
        $this->assertTrue($studentPermissions->contains('name', 'view_profile'));
    }

    /** @test */
    public function user_dashboard_route_varies_by_guard_and_role()
    {
        // Web guard user
        $admin = User::factory()->create();
        $this->service->assignRoleToUser($admin, 'Super Admin');
        $this->assertEquals('team.dashboard', $admin->getDashboardRoute());

        // Student guard user
        $student = Student::factory()->create();
        $this->service->assignRoleToUser($student, 'Active Student');
        $this->assertEquals('student.dashboard', $student->getDashboardRoute());

        // Partner guard user
        $partner = Partner::factory()->create();
        $this->service->assignRoleToUser($partner, 'Corporate Partner');
        $this->assertEquals('partner.dashboard', $partner->getDashboardRoute());
    }
}
