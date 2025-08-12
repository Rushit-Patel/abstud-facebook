<?php
namespace App\Repositories\Team;
use App\Models\ClientLead;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class LeadRepository
{
    public static function getLead()
    {
        $query = ClientLead::with([
            'client',
            'getBranch',
            'assignedOwner',
            'getPurpose',
            'getForeignCountry',
        ]);

        $user = Auth::user();

        // Apply branch restrictions
        self::applyBranchFilters($query, $user);

        // Apply other permission-based filters
        self::applyPermissionFilters($query, $user, 'lead:country', 'foreign_country', 'country');
        self::applyPermissionFilters($query, $user, 'lead:purpose', 'purpose', 'purpose');
        self::applyPermissionFilters($query, $user, 'lead:coaching', 'coaching', 'coaching');

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

        // Check all user roles for branch permissions
        foreach($user->roles as $role) {
            $permissions = $role->permissions->pluck('name');

            if($permissions->contains('lead:show-all')) {
                $hasShowAll = true;
                $branchIds = self::getRoleConfiguration($user, $role->id, 'show-all');
                if (!empty($branchIds)) {
                    $accessibleBranches = array_merge($accessibleBranches, $branchIds);
                }
            }

            if($permissions->contains('lead:show-branch')) {
                $hasShowBranch = true;
            }
        }

        // Apply filtering logic
        if($hasShowAll) {
            if (!empty($accessibleBranches)) {
                $query->whereIn('client_leads.branch', array_unique($accessibleBranches));
            }
            // No restriction if no configuration is set
        } elseif($hasShowBranch) {
            $query->where('client_leads.branch', $user->branch_id);
        } else {
            // Default: own leads only
            $query->where(function($q) use ($user) {
                $q->where("client_leads.assign_owner", $user->id)
                    ->orWhere("client_leads.added_by", $user->id);
            });
        }
    }

    /**
     * Apply permission-based filters (country, purpose, coaching)
     */
    private static function applyPermissionFilters($query, $user, $permission, $column, $configType)
    {
        $accessibleIds = [];

        foreach($user->roles as $role) {
            if($role->permissions->pluck('name')->contains($permission)) {
                $ids = self::getRoleConfiguration($user, $role->id, $configType);
                if (!empty($ids)) {
                    $accessibleIds = array_merge($accessibleIds, $ids);
                }
            }
        }

        if (!empty($accessibleIds)) {
            $query->whereIn("client_leads.{$column}", array_unique($accessibleIds));
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
