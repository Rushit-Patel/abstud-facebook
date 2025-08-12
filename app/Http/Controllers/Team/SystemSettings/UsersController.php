<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Coaching;
use App\Models\ForeignCountry;
use App\Models\Purpose;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use App\Mail\TeamAccountCreatedMail;

class UsersController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['branch', 'roles'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->when($request->role, function ($q) use ($request) {
                $q->whereHas('roles', function ($roleQuery) use ($request) {
                    $roleQuery->where('name', $request->role);
                });
            })
            ->when($request->status !== null, function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            })
            ->latest();

        $users = $query->paginate(15)->withQueryString();
        
        // Get filter options
        $branches = Branch::active()->get();
        $roles = Role::where('guard_name', 'web')->get();

        return view('team.settings.users.index', compact('users', 'branches', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $branches = Branch::active()->get();
        $roles = Role::where('guard_name', 'web')->get();
        $countries = ForeignCountry::active()->get();
        $purposes = Purpose::active()->get();
        $coaching = Coaching::active()->get();
        
        return view('team.settings.users.create', compact('branches', 'roles', 'countries', 'purposes', 'coaching'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'username' => 'required|string|unique:users,username|max:255|alpha_dash',
                'phone' => 'nullable|string|max:20',
                'password' => ['required'],
                'branch_id' => 'required|exists:branches,id',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,id',
                'is_active' => 'boolean',
                // Role-specific configurations
                'role_branch_configurations' => 'nullable|array',
                'role_country_configurations' => 'nullable|array',
                'role_purpose_configurations' => 'nullable|array',
                'role_coaching_configurations' => 'nullable|array',
            ], [
                'name.required' => 'Full name is required.',
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already registered.',
                'username.required' => 'Username is required.',
                'username.unique' => 'This username is already taken.',
                'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
                'password.required' => 'Password is required.',
                'password.confirmed' => 'Password confirmation does not match.',
                'branch_id.required' => 'Branch selection is required.',
                'branch_id.exists' => 'Selected branch does not exist.',
                'roles.required' => 'At least one role is required.',
                'roles.*.exists' => 'One or more selected roles are invalid.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['password'] = Hash::make($validated['password']);
            $validated['base_password'] = base64_encode($request->password);

            // Remove roles from user data before creating user
            $userRoles = $validated['roles'];
            unset($validated['roles'], $validated['role_branch_configurations'], 
                  $validated['role_country_configurations'], $validated['role_purpose_configurations'], 
                  $validated['role_coaching_configurations']);

            // Create user
            $user = User::create($validated);

            // Assign roles and their configurations
            foreach ($userRoles as $roleId) {
                $role = Role::findById($roleId);
                $user->assignRole($role);

                // Check if role has specific permission requirements and store configurations
                $permissions = $role->permissions->pluck('name');
                
                // Handle show-all branch configuration (specific check for lead:show-all and followup:show-all)
                if ($permissions->contains('lead:show-all') || $permissions->contains('followup:show-all')) {
                    $branchIds = $request->input("role_branch_configurations.{$roleId}", []);
                    if (!empty($branchIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'show-all', $branchIds
                        );
                    }
                }

                // Handle country configuration (specific check for lead:country)
                if ($permissions->contains('lead:country')) {
                    $countryIds = $request->input("role_country_configurations.{$roleId}", []);
                    if (!empty($countryIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'country', $countryIds
                        );
                    }
                }

                // Handle purpose configuration (specific check for lead:purpose)
                if ($permissions->contains('lead:purpose')) {
                    $purposeIds = $request->input("role_purpose_configurations.{$roleId}", []);
                    if (!empty($purposeIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'purpose', $purposeIds
                        );
                    }
                }

                // Handle coaching configuration (specific check for lead:coaching)
                if ($permissions->contains('lead:coaching')) {
                    $coachingIds = $request->input("role_coaching_configurations.{$roleId}", []);
                    if (!empty($coachingIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'coaching', $coachingIds
                        );
                    }
                }
            }

            // Send welcome email with account details
            try {
                $subject = 'Welcome to ' . config('app.name', 'Our Platform') . ' - Your Account Has Been Created';
                
                $content = [
                    "name" => $user->name,
                    "username" => $user->username,
                    "app_url" => config('app.url'),
                    'password' => base64_decode($validated['base_password'])
                ];
                
                Mail::to($user->email)->send(
                    new TeamAccountCreatedMail($subject, $content, null)
                );
            } catch (\Exception $mailException) {
                // Log the error but don't fail the user creation
                Log::error('Failed to send welcome email to user: ' . $user->email, [
                    'error' => $mailException->getMessage(),
                    'user_id' => $user->id
                ]);
            }

            return redirect()->route('team.settings.users.index')
                ->with('success', "User '{$validated['name']}' has been created successfully with multiple roles and configurations.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the user. Please try again.');
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['branch', 'roles.permissions', 'roleConfigurations']);
        return view('team.settings.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $branches = Branch::active()->get();
        $roles = Role::where('guard_name', 'web')->get();
        $countries = ForeignCountry::active()->get();
        $purposes = Purpose::active()->get();
        $coaching = Coaching::active()->get();
        $user->load(['branch', 'roles', 'roleConfigurations']);
        
        return view('team.settings.users.edit', compact('user', 'branches', 'roles', 'countries', 'purposes', 'coaching'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id . '|max:255',
                'username' => 'required|string|unique:users,username,' . $user->id . '|max:255|alpha_dash',
                'phone' => 'nullable|string|max:20',
                'password' => ['nullable', 'confirmed', Password::defaults()],
                'branch_id' => 'required|exists:branches,id',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,id',
                'is_active' => 'boolean',
                // Role-specific configurations
                'role_branch_configurations' => 'nullable|array',
                'role_country_configurations' => 'nullable|array',
                'role_purpose_configurations' => 'nullable|array',
                'role_coaching_configurations' => 'nullable|array',
            ], [
                'name.required' => 'Full name is required.',
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already registered.',
                'username.required' => 'Username is required.',
                'username.unique' => 'This username is already taken.',
                'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
                'password.confirmed' => 'Password confirmation does not match.',
                'branch_id.required' => 'Branch selection is required.',
                'branch_id.exists' => 'Selected branch does not exist.',
                'roles.required' => 'At least one role is required.',
                'roles.*.exists' => 'One or more selected roles are invalid.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');

            // Update password only if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
                $validated['base_password'] = base64_encode($request->password);
            } else {
                unset($validated['password']);
            }

            // Remove roles and configurations from user data before updating user
            $userRoles = $validated['roles'];
            unset($validated['roles'], $validated['role_branch_configurations'], 
                  $validated['role_country_configurations'], $validated['role_purpose_configurations'], 
                  $validated['role_coaching_configurations']);

            // Update user
            $user->update($validated);

            // Clear existing role configurations
            $user->roleConfigurations()->delete();

            // Sync roles and their configurations
            $roleModels = Role::whereIn('id', $userRoles)->get();
            $user->syncRoles($roleModels);

            foreach ($userRoles as $roleId) {
                $role = Role::findById($roleId);
                $permissions = $role->permissions->pluck('name');
                
                // Handle show-all branch configuration
                if ($permissions->contains('lead:show-all') || $permissions->contains('followup:show-all')) {
                    $branchIds = $request->input("role_branch_configurations.{$roleId}", []);
                    if (!empty($branchIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'show-all', $branchIds
                        );
                    }
                }

                // Handle country configuration
                if ($permissions->contains('lead:country')) {
                    $countryIds = $request->input("role_country_configurations.{$roleId}", []);
                    if (!empty($countryIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'country', $countryIds
                        );
                    }
                }

                // Handle purpose configuration
                if ($permissions->contains('lead:purpose')) {
                    $purposeIds = $request->input("role_purpose_configurations.{$roleId}", []);
                    if (!empty($purposeIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'purpose', $purposeIds
                        );
                    }
                }

                // Handle coaching configuration
                if ($permissions->contains('lead:coaching')) {
                    $coachingIds = $request->input("role_coaching_configurations.{$roleId}", []);
                    if (!empty($coachingIds)) {
                        \App\Models\UserRoleConfiguration::addConfiguration(
                            $user->id, $roleId, 'coaching', $coachingIds
                        );
                    }
                }
            }

            return redirect()->route('team.settings.users.index')
                ->with('success', "User '{$validated['name']}' has been updated successfully with multiple roles and configurations.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the user. Please try again.');
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deletion of current user
            if ($user->id === auth()->id()) {
                return redirect()->route('team.settings.users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            // Prevent deletion of super admin if only one exists
            if ($user->isSuperAdmin()) {
                $superAdminCount = User::whereHas('roles', function ($q) {
                    $q->where('name', 'Super Admin');
                })->count();

                if ($superAdminCount <= 1) {
                    return redirect()->route('team.settings.users.index')
                        ->with('error', 'Cannot delete the last Super Admin user.');
                }
            }

            $userName = $user->name;
            $user->delete();

            return redirect()->route('team.settings.users.index')
                ->with('success', "User '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->route('team.settings.users.index')
                ->with('error', 'An error occurred while deleting the user. Please try again.');
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent deactivating current user
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update(['is_active' => !$user->is_active]);
            
            $status = $user->is_active ? 'activated' : 'deactivated';
            
            return redirect()->back()
                ->with('success', "User '{$user->name}' has been {$status} successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating user status.');
        }
    }

    /**
     * Get role permissions (AJAX endpoint)
     */
    public function getRolePermissions(Request $request)
    {
        $roleId = $request->input('role_id');
        $role = Role::with('permissions')->findOrFail($roleId);
        
        $permissions = $role->permissions->pluck('name');
        
        return response()->json([
            'role_name' => $role->name,
            'permissions' => $permissions,
            'has_show_all' => $permissions->contains(fn($perm) => str_contains($perm, ':show-all')),
            'has_country' => $permissions->contains(fn($perm) => str_contains($perm, ':country')),
            'has_purpose' => $permissions->contains(fn($perm) => str_contains($perm, ':purpose')),
            'has_coaching' => $permissions->contains(fn($perm) => str_contains($perm, ':coaching')),
        ]);
    }
}
