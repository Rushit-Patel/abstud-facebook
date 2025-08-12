<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\CompanySetting;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupController extends Controller
{
    public function showCompanySetup()
    {
        // Redirect if setup is already completed
        if (CompanySetting::isSetupCompleted()) {
            return redirect()->route('team.dashboard');
        }

        $countries = Country::orderBy('name')->get();
        return view('setup.wizard.step1', compact('countries'));
    }

    public function showBranchSetup()
    {
        // Check if step 1 is completed
        $company = CompanySetting::first();
        if (!$company || !$company->company_name) {
            return redirect()->route('setup.company');
        }

        // Load location data
        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', $company->country_id)->orderBy('name')->get();
        $cities = City::where('state_id', $company->state_id)->orderBy('name')->get();
        
        return view('setup.wizard.step2', compact('company', 'countries', 'states', 'cities'));
    }

    public function storeCompanySetup(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg|max:1024',
            'company_address' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ], [
            'company_name.required' => 'Company name is required.',
            'company_address.required' => 'Company address is required.',
            'website_url.url' => 'Please enter a valid website URL.',
            'company_logo.image' => 'Company logo must be an image.',
            'company_logo.mimes' => 'Company logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'company_logo.max' => 'Company logo may not be greater than 2MB.',
            'company_favicon.image' => 'Company favicon must be an image.',
            'company_favicon.mimes' => 'Company favicon must be a file of type: jpeg, png, jpg, gif, ico, svg.',
            'company_favicon.max' => 'Company favicon may not be greater than 1MB.',
            'country_id.required' => 'Country is required.',
            'country_id.exists' => 'Selected country is invalid.',
            'state_id.required' => 'State is required.',
            'state_id.exists' => 'Selected state is invalid.',
            'city_id.required' => 'City is required.',
            'city_id.exists' => 'Selected city is invalid.',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only([
                'company_name', 'website_url', 'company_address'
            ]);
            $data['country_id'] = $request->country_id;
            $data['state_id'] = $request->state_id;
            $data['city_id'] = $request->city_id;

            // Handle file uploads
            if ($request->hasFile('company_logo')) {
                $data['company_logo'] = $request->file('company_logo')->store('company', 'public');
            }

            if ($request->hasFile('company_favicon')) {
                $data['company_favicon'] = $request->file('company_favicon')->store('company', 'public');
            }

            CompanySetting::updateOrCreate(['id' => 1], $data);
        });

        return redirect()->route('setup.branch');
    }

    public function storeStep2(Request $request)
    {
        $request->validate([
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg|max:1024',
        ], [
            'company_logo.image' => 'Company logo must be an image.',
            'company_logo.mimes' => 'Company logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'company_logo.max' => 'Company logo may not be greater than 2MB.',
            'company_favicon.image' => 'Company favicon must be an image.',
            'company_favicon.mimes' => 'Company favicon must be a file of type: jpeg, png, jpg, gif, ico, svg.',
            'company_favicon.max' => 'Company favicon may not be greater than 1MB.',
        ]);

        DB::transaction(function () use ($request) {
            $company = CompanySetting::first();
            $data = [];

            // Handle file uploads
            if ($request->hasFile('company_logo')) {
                $data['company_logo'] = $request->file('company_logo')->store('company', 'public');
            }

            if ($request->hasFile('company_favicon')) {
                $data['company_favicon'] = $request->file('company_favicon')->store('company', 'public');
            }

            $company->update($data);
        });

        return redirect()->route('setup.admin');
    }

    public function showAdminSetup()
    {
        // Check if steps 1 & 2 are completed
        $company = CompanySetting::first();
        if (!$company || !$company->company_name) {
            return redirect()->route('setup.company');
        }

        // Check if branch exists
        if (!Branch::where('is_main_branch', true)->exists()) {
            return redirect()->route('setup.branch');
        }

        return view('setup.wizard.step3', compact('company'));
    }

    public function storeAdminSetup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255|unique:users|alpha_dash|min:3',
            'password' => ['required', 'confirmed'],
            'phone' => 'nullable|string|regex:/^[\+]?[0-9\(\)\-\s]+$/',
        ], [
            'name.required' => 'Full name is required.',
            'name.regex' => 'Name should only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
            'username.min' => 'Username must be at least 3 characters.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        DB::transaction(function () use ($request) {
            // Get the main branch
            $branch = Branch::where('is_main_branch', true)->first();

            // Create super admin user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'base_password' => base64_encode($request->password),
                'phone' => $request->phone,
                'branch_id' => $branch ? $branch->id : null,
                'is_active' => true,
            ]);

            // Create roles and permissions if they don't exist
            $this->createRolesAndPermissions();

            // Assign super admin role
            $user->assignRole('Super Admin');

            // Mark setup as completed
            CompanySetting::first()->update(['is_setup_completed' => true]);
        });

        return redirect()->route('team.dashboard')->with('success', 'Setup completed successfully!');
    }

    public function storeBranchSetup(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:10|unique:branches,branch_code',
            'branch_address' => 'required|string',
            'branch_country_id' => 'required|exists:countries,id',
            'branch_state_id' => 'required|exists:states,id',
            'branch_city_id' => 'required|exists:cities,id',
            'branch_phone' => 'nullable|string|regex:/^[\+]?[0-9\(\)\-\s]+$/',
            'branch_email' => 'nullable|email',
        ], [
            'branch_name.required' => 'Branch name is required.',
            'branch_code.required' => 'Branch code is required.',
            'branch_code.unique' => 'This branch code is already taken.',
            'branch_address.required' => 'Branch address is required.',
            'branch_country_id.required' => 'Country is required.',
            'branch_country_id.exists' => 'Selected country is invalid.',
            'branch_state_id.required' => 'State is required.',
            'branch_state_id.exists' => 'Selected state is invalid.',
            'branch_city_id.required' => 'City is required.',
            'branch_city_id.exists' => 'Selected city is invalid.',
            'branch_phone.regex' => 'Please enter a valid phone number.',
            'branch_email.email' => 'Please enter a valid email address.',
        ]);

        DB::transaction(function () use ($request) {
            // Get location names from IDs
            $country = Country::find($request->branch_country_id);
            $state = State::find($request->branch_state_id);
            $city = City::find($request->branch_city_id);

            // Create main branch
            $branch = Branch::create([
                'branch_code' => strtoupper($request->branch_code),
                'branch_name' => $request->branch_name,
                'address' => $request->branch_address,
                'city_id' => $request->branch_city_id,
                'state_id' => $request->branch_state_id,
                'country_id' => $request->branch_country_id,
                'phone' => $request->branch_phone,
                'email' => $request->branch_email,
                'is_main_branch' => true,
                'is_active' => true,
            ]);
        });

        return redirect()->route('setup.admin');
    }

    private function createRolesAndPermissions()
    {
        // Create permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage company settings',
            'manage branches',
            'view dashboard',
            'manage students',
            'manage partners',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);

        // Assign all permissions to Super Admin
        $superAdmin->syncPermissions(Permission::all());

        // Assign default admin role if it doesn't exist
        $admin->syncPermissions(['view dashboard', 'manage students', 'manage partners']);
    }

    /**
     * Get states by country ID for AJAX calls
     */
    public function getStatesByCountry($countryId)
    {
        $states = State::where('country_id', $countryId)
                      ->orderBy('name')
                      ->get(['id', 'name']);
        
        return response()->json($states);
    }

    /**
     * Get cities by state ID for AJAX calls
     */
    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
                     ->orderBy('name')
                     ->get(['id', 'name']);
        
        return response()->json($cities);
    }
}
