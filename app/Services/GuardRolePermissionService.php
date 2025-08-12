<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GuardRolePermissionService
{
    /**
     * Get all available guards
     */
    public function getAvailableGuards(): array
    {
        return ['web', 'student', 'partner'];
    }

    /**
     * Get roles for a specific guard
     */
    public function getRolesForGuard(string $guard): \Illuminate\Database\Eloquent\Collection
    {
        return Role::where('guard_name', $guard)->get();
    }

    /**
     * Get permissions for a specific guard
     */
    public function getPermissionsForGuard(string $guard): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::where('guard_name', $guard)->get();
    }

    /**
     * Create role for specific guard
     */
    public function createRoleForGuard(string $roleName, string $guard): Role
    {
        return Role::create([
            'name' => $roleName,
            'guard_name' => $guard
        ]);
    }

    /**
     * Create permission for specific guard
     */
    public function createPermissionForGuard(string $permissionName, string $guard): Permission
    {
        return Permission::create([
            'name' => $permissionName,
            'guard_name' => $guard
        ]);
    }

    /**
     * Assign role to user with guard context
     */
    public function assignRoleToUser($user, string $roleName): bool
    {
        $guard = $user->getGuardName();
        $role = Role::where('name', $roleName)
            ->where('guard_name', $guard)
            ->first();

        if ($role) {
            $user->assignRole($role);
            return true;
        }

        return false;
    }

    /**
     * Give permission to user with guard context
     */
    public function givePermissionToUser($user, string $permissionName): bool
    {
        $guard = $user->getGuardName();
        $permission = Permission::where('name', $permissionName)
            ->where('guard_name', $guard)
            ->first();

        if ($permission) {
            $user->givePermissionTo($permission);
            return true;
        }

        return false;
    }

    /**
     * Get user model by guard
     */
    public function getUserModelByGuard(string $guard): string
    {        return match($guard) {
            'student' => 'App\\Models\\Student',
            'partner' => 'App\\Models\\Partner',
            'web' => 'App\\Models\\User',
            default => 'App\\Models\\User',
        };
    }

    /**
     * Get authenticated user with guard context
     */
    public function getAuthenticatedUser(string $guard = null)
    {
        if ($guard) {
            return Auth::guard($guard)->user();
        }

        // Try to detect guard from current user
        foreach ($this->getAvailableGuards() as $guardName) {
            if (Auth::guard($guardName)->check()) {
                return Auth::guard($guardName)->user();
            }
        }

        return null;
    }

    /**
     * Check if user has specific role in their guard
     */
    public function userHasRole($user, string $roleName): bool
    {
        if (!$user) return false;
        
        return $user->hasRole($roleName, $user->getGuardName());
    }

    /**
     * Check if user has specific permission in their guard
     */
    public function userHasPermission($user, string $permissionName): bool
    {
        if (!$user) return false;
        
        return $user->hasPermissionTo($permissionName, $user->getGuardName());
    }

    /**
     * Get dashboard route for authenticated user based on guard and role
     */
    public function getDashboardRouteForUser($user): string
    {
        if (!$user) {
            return '/login';
        }

        return $user->getDashboardRoute();
    }

    /**
     * Sync permissions to role for specific guard
     */
    public function syncPermissionsToRole(string $roleName, string $guard, array $permissions): bool
    {
        $role = Role::where('name', $roleName)
            ->where('guard_name', $guard)
            ->first();

        if (!$role) {
            return false;
        }

        $permissionModels = Permission::whereIn('name', $permissions)
            ->where('guard_name', $guard)
            ->get();

        $role->syncPermissions($permissionModels);
        return true;
    }

    /**
     * Get all users with specific role in guard
     */
    public function getUsersWithRole(string $roleName, string $guard)
    {
        $userModel = $this->getUserModelByGuard($guard);
        $role = Role::where('name', $roleName)
            ->where('guard_name', $guard)
            ->first();

        if (!$role) {
            return collect();
        }

        return $userModel::role($role)->get();
    }

    /**
     * Get permissions summary for user
     */
    public function getUserPermissionsSummary($user): array
    {
        if (!$user) {
            return [];
        }

        $guard = $user->getGuardName();
        
        return [
            'guard' => $guard,
            'roles' => $user->getRoleNames()->toArray(),
            'direct_permissions' => $user->getDirectPermissions()->pluck('name')->toArray(),
            'all_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ];
    }
}
