<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait GuardHelpers
{
    /**
     * Get the guard name for the current model
     */
    public function getGuardName(): string
    {
        return $this->guard_name ?? 'web';
    }

    /**
     * Check if user has role with guard context
     */
    public function hasGuardRole(string $role): bool
    {
        return $this->hasRole($role, $this->getGuardName());
    }

    /**
     * Check if user has permission with guard context
     */
    public function hasGuardPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission, $this->getGuardName());
    }

    /**
     * Assign role with guard context
     */
    public function assignGuardRole(string $role): self
    {
        $roleModel = Role::where('name', $role)
            ->where('guard_name', $this->getGuardName())
            ->first();

        if ($roleModel) {
            $this->assignRole($roleModel);
        }

        return $this;
    }

    /**
     * Give permission with guard context
     */
    public function giveGuardPermission(string $permission): self
    {
        $permissionModel = Permission::where('name', $permission)
            ->where('guard_name', $this->getGuardName())
            ->first();

        if ($permissionModel) {
            $this->givePermissionTo($permissionModel);
        }

        return $this;
    }

    /**
     * Get all roles for the guard
     */
    public function getGuardRoles()
    {
        return $this->roles()->where('guard_name', $this->getGuardName())->get();
    }

    /**
     * Get all permissions for the guard
     */
    public function getGuardPermissions()
    {
        return $this->permissions()->where('guard_name', $this->getGuardName())->get();
    }

    /**
     * Get all permissions through roles for the guard
     */
    public function getAllGuardPermissions()
    {
        return $this->getAllPermissions()->where('guard_name', $this->getGuardName());
    }

    /**
     * Check if user can perform action on resource with guard context
     */
    public function canWithGuard(string $permission, $resource = null): bool
    {
        return $this->can($permission) || $this->hasGuardPermission($permission);
    }

    /**
     * Get user's dashboard route based on guard and role
     */
    public function getDashboardRoute(): string
    {
        $guard = $this->getGuardName();
        
        switch ($guard) {
            case 'student':
                return 'student.dashboard';
            case 'partner':
                return 'partner.dashboard';
            case 'web':
            default:
                if ($this->hasGuardRole('Super Admin')) {
                    return 'team.dashboard';
                } elseif ($this->hasGuardRole('Admin') || $this->hasGuardRole('Manager')) {
                    return 'team.dashboard';
                }
                return 'dashboard';
        }
    }

    /**
     * Get user's login route based on guard
     */
    public function getLoginRoute(): string
    {
        $guard = $this->getGuardName();
        
        switch ($guard) {
            case 'student':
                return 'student.login';
            case 'partner':
                return 'partner.login';
            case 'web':
            default:
                return 'team.login';
        }
    }
}
