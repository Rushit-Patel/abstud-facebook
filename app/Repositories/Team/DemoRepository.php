<?php
namespace App\Repositories\Team;
use App\Models\ClientCoachingDemoDetails;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class DemoRepository
{
    public static function getDemo()
    {
        $main_query = ClientCoachingDemoDetails::with('clientLead');

        // Exclude status 2 from client leads
        $main_query->whereHas('clientLead', function ($q) {
            $q->where('status', '!=', '2');
        });

        // Apply permission-based filters
        $main_query->whereHas('clientLead', function ($query) {
            self::applyBranchFilters($query, Auth::user());
        });
        return $main_query;
    }

    /**
     * Apply branch-specific filtering logic for demos
     */
    private static function applyBranchFilters($query, $user)
    {
        $hasShowAll = false;
        $hasShowBranch = false;
        $accessibleBranches = [];

        // Check all user roles for demo permissions
        foreach($user->roles as $role) {
            $permissions = $role->permissions->pluck('name');

            if($permissions->contains('demo:show-all')) {
                $hasShowAll = true;
                $branchIds = self::getRoleConfiguration($user, $role->id, 'show-all');
                if (!empty($branchIds)) {
                    $accessibleBranches = array_merge($accessibleBranches, $branchIds);
                }
            }

            if($permissions->contains('demo:show-branch')) {
                $hasShowBranch = true;
            }
        }

        // Apply filtering logic
        if($hasShowAll) {
            if (!empty($accessibleBranches)) {
                $query->whereIn('branch', array_unique($accessibleBranches));
            }
            // No restriction if no configuration is set
        } elseif($hasShowBranch) {
            $query->where('branch', $user->branch_id);
        } else {
            // Default: own demo only
            $query->where(function ($q) use ($user) {
                $q->where('assign_owner', $user->id)
                    ->orWhere('added_by', $user->id);
            });
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
