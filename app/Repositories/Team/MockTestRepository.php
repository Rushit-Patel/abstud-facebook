<?php
namespace App\Repositories\Team;
use App\Models\ClientCoaching;
use App\Models\MockTest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class MockTestRepository
{
    public static function getMockTest()
    {

        $query = MockTest::query();
        self::applyBranchFilters($query, Auth::user());

        return $query;
    }

    /**
     * Apply branch-specific filtering logic
     */
    private static function applyBranchFilters($query, $user)
    {
        $hasShowAll = false;
        $hasShowBranch = false;
        $accessibleBranches = [];

        // Check all user roles for coaching permissions
        foreach($user->roles as $role) {
            $permissions = $role->permissions->pluck('name');

            if($permissions->contains('coaching:show-all')) {
                $hasShowAll = true;
                $branchIds = self::getRoleConfiguration($user, $role->id, 'show-all');
                if (!empty($branchIds)) {
                    $accessibleBranches = array_merge($accessibleBranches, $branchIds);
                }
            }

            if($permissions->contains('coaching:show-branch')) {
                $hasShowBranch = true;
            }
        }

        // Apply filtering logic
        if ($hasShowAll) {
            if (!empty($accessibleBranches)) {
                $query->whereIn('branch_id', array_unique($accessibleBranches));
            }
        } elseif ($hasShowBranch) {
            $query->where('branch_id', $user->branch_id);
        }
    }

    /**
     * Get role configuration data
     */
    private static function getRoleConfiguration($user, $roleId, $permissionType)
    {
        $config = $user->roleConfigurations()
            ->where('role_id', $roleId)
            ->where('permission_type', $permissionType)
            ->first();

        return ($config && is_array($config->configuration_data)) ? $config->configuration_data : [];
    }
}
