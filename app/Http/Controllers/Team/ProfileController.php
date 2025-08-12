<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coaching;
use App\Models\ForeignCountry;
use App\Models\Purpose;
use App\Models\User;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $company = CompanySetting::getSettings();
        return view('team.profile.index', compact('user', 'company'));
    }

    public function ProfileEdit(User $user){
         $branches = Branch::active()->get();
        $roles = Role::where('guard_name', 'web')->get();
        $countries = ForeignCountry::active()->get();
        $purposes = Purpose::active()->get();
        $coaching = Coaching::active()->get();
        $user->load(['branch', 'roles', 'roleConfigurations']);

        return view('team.profile.edit', compact('user', 'branches', 'roles', 'countries', 'purposes', 'coaching'));
    }

    public function update(Request $request,User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'username' => 'required|string|max:255|alpha_dash',
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

            return redirect()->route('team.profile')
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

    public function settings()
    {
        $user = Auth::user();
        $company = CompanySetting::getSettings();

        return view('team.settings', compact('user', 'company'));
    }
}
