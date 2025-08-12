<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        $guard = $request->get('guard', 'web');

        $query = Permission::where('guard_name', $guard)
            ->withCount('roles', 'users')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->module, function ($q) use ($request) {
                $q->where('name', 'like', $request->module . ':%');
            })
            ->when($request->action, function ($q) use ($request) {
                $q->where('name', 'like', '%:' . $request->action);
            })
            ->latest();

        $permissions = $query->paginate(20)->withQueryString();

        // Get modules and actions for filter
        $modules = $this->getPermissionModules();
        $actions = $this->getPermissionActions();

        // Available guards
        $guards = [
            'web' => 'Admin/Staff',
            'student' => 'Students',
            'partner' => 'Partners'
        ];

        return view('team.settings.permissions.index', compact('permissions', 'modules', 'actions', 'guards', 'guard'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create(Request $request)
    {
        $guard = $request->get('guard', 'web');
        $modules = $this->getPermissionModules();
        $actions = $this->getPermissionActions();

        // Available guards
        $guards = [
            'web' => 'web',
            'student' => 'student',
            'partner' => 'partner'
        ];

        return view('team.settings.permissions.create', compact('modules', 'actions', 'guards', 'guard'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'module' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'guard_name' => 'required|string|in:web,student,partner',
                'description' => 'nullable|string|max:500',
            ], [
                'module.required' => 'Permission module is required.',
                'action.required' => 'Permission action is required.',
                'guard_name.required' => 'Guard is required.',
                'guard_name.in' => 'Invalid guard selected.',
            ]);

            // Generate permission name based on module and action
            $permissionName = $this->generatePermissionName($validated['module'], $validated['action']);

            // Check if permission already exists
            if (Permission::where('name', $permissionName)->where('guard_name', $validated['guard_name'])->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Permission '{$permissionName}' already exists.");
            }

            // Create permission
            Permission::create([
                'name' => $permissionName,
                'guard_name' => $validated['guard_name']
            ]);

            return redirect()->route('team.settings.permissions.index', ['guard' => $validated['guard_name']])
                ->with('success', "Permission '{$permissionName}' has been created successfully.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the permission. Please try again.');
        }
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission)
    {
        $permission->load('roles.users');
        $permission->loadCount('roles', 'users');

        return view('team.settings.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit(Permission $permission)
    {
        $modules = $this->getPermissionModules();
        $actions = $this->getPermissionActions();

        // Parse existing permission name to get module and action
        $permissionParts = explode(':', $permission->name);
        $currentModule = $permissionParts[0] ?? '';
        $currentAction = $permissionParts[1] ?? '';

        // Available guards
        $guards = [
            'web' => 'Admin/Staff',
            'student' => 'Students',
            'partner' => 'Partners'
        ];

        return view('team.settings.permissions.edit', compact('permission', 'modules', 'actions', 'guards', 'currentModule', 'currentAction'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        try {
            $validated = $request->validate([
                'module' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ], [
                'module.required' => 'Permission module is required.',
                'action.required' => 'Permission action is required.',
            ]);

            // Generate permission name based on module and action
            $permissionName = $this->generatePermissionName($validated['module'], $validated['action']);

            // Check if permission already exists (excluding current permission)
            if (Permission::where('name', $permissionName)
                ->where('guard_name', $permission->guard_name)
                ->where('id', '!=', $permission->id)
                ->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Permission '{$permissionName}' already exists.");
            }

            // Update permission
            $permission->update(['name' => $permissionName]);

            return redirect()->route('team.settings.permissions.index', ['guard' => $permission->guard_name])
                ->with('success', "Permission '{$permissionName}' has been updated successfully.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the permission. Please try again.');
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        try {
            // Check if permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                return redirect()->route('team.settings.permissions.index', ['guard' => $permission->guard_name])
                    ->with('error', "Cannot delete permission '{$permission->name}' because it is assigned to roles. Please remove it from roles first.");
            }

            $permissionName = $permission->name;
            $guardName = $permission->guard_name;
            $permission->delete();

            return redirect()->route('team.settings.permissions.index', ['guard' => $guardName])
                ->with('success', "Permission '{$permissionName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->route('team.settings.permissions.index')
                ->with('error', 'An error occurred while deleting the permission. Please try again.');
        }
    }

    /**
     * Get permission modules
     */
    private function getPermissionModules()
    {
        return [
            'users' => 'User Management',
            'roles' => 'Role & Permission Management',
            'company' => 'Company Settings',
            'branches' => 'Branch Management',
            'students' => 'Student Management',
            'partners' => 'Partner Management',
            'leads' => 'Lead Management',
            'reports' => 'Reports & Analytics',
            'communications' => 'Communications',
            'system' => 'System Administration',
            'dashboard' => 'Dashboard Access',
            'settings' => 'Settings Management',
            'coaching' => 'Coaching Management',
            'education' => 'Education Management',
            'countries' => 'Country Management',
            'english_tests' => 'English Test Management',
            'invoice' => 'Invoice Management',
        ];
    }

    /**
     * Get permission actions
     */
    private function getPermissionActions()
    {
        return [
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'manage' => 'Manage',
            'list' => 'List',
            'show' => 'Show Details',
            'update' => 'Update',
            'destroy' => 'Remove',
            'export' => 'Export',
            'import' => 'Import',
            'approve' => 'Approve',
            'reject' => 'Reject',
            'assign' => 'Assign',
            'unassign' => 'Unassign',
        ];
    }

    /**
     * Generate permission name based on module and action
     */
    private function generatePermissionName($module, $action)
    {
        // Clean the module and action names
        $module = strtolower(trim($module));
        $action = strtolower(trim($action));

        // Remove spaces and special characters
        $module = str_replace([' ', '-'], '_', $module);
        $action = str_replace([' ', '-'], '_', $action);

        return $module . ':' . $action;
    }
}
