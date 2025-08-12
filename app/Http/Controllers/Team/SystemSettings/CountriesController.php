<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CountryDataTable;

class CountriesController extends Controller
{
    /**
     * Display a listing of countries
     */
    public function index(CountryDataTable $CountryDataTable)
    {
        // $countries = Country::orderBy('name')->paginate(20);
        // return view('team.settings.countries.index', compact('countries'));
        return $CountryDataTable->render('team.settings.countries.index');
    }

    /**
     * Show the form for creating a new country
     */
    public function create()
    {
        return view('team.settings.countries.create');
    }

    /**
     * Store a newly created country
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:countries,name',
                'phone_code' => 'nullable|string|max:10',
                'currency' => 'nullable|string|max:3',
                'currency_symbol' => 'nullable|string|max:10',
                'timezones' => 'nullable|array',
                'icon' => 'nullable|string|max:255',
                'is_active' => 'boolean',
            ], [
                'name.required' => 'Country name is required.',
                'name.unique' => 'This country already exists.',
                'currency.max' => 'Currency code should be 3 characters (ISO 4217).',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['timezones'] = $validated['timezones'] ?? [];

            Country::create($validated);

            return redirect()->route('team.settings.countries.index')
                ->with('success', "Country '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating country: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified country
     */
    public function show(Country $country)
    {
        $country->load('states');
        return view('team.settings.countries.show', compact('country'));
    }

    /**
     * Show the form for editing the specified country
     */
    public function edit(Country $country)
    {
        return view('team.settings.countries.edit', compact('country'));
    }

    /**
     * Update the specified country
     */
    public function update(Request $request, Country $country)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
                'phone_code' => 'nullable|string|max:10',
                'currency' => 'nullable|string|max:3',
                'currency_symbol' => 'nullable|string|max:10',
                'timezones' => 'nullable|array',
                'icon' => 'nullable|string|max:255',
                'is_active' => 'boolean',
            ], [
                'name.required' => 'Country name is required.',
                'name.unique' => 'This country already exists.',
                'currency.max' => 'Currency code should be 3 characters (ISO 4217).',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['timezones'] = $validated['timezones'] ?? [];

            $country->update($validated);

            return redirect()->route('team.settings.countries.index')
                ->with('success', "Country '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating country: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified country
     */
    public function destroy(Country $country)
    {
        try {
            $countryName = $country->name;
            
            // Check if country has states
            if ($country->states()->count() > 0) {
                return back()->with('error', "Cannot delete '{$countryName}' as it has associated states.");
            }

            $country->delete();

            return redirect()->route('team.settings.countries.index')
                ->with('success', "Country '{$countryName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting country: ' . $e->getMessage());
        }
    }

    /**
     * Toggle country status
     */
    public function toggleStatus(Country $country)
    {
        try {
            $country->update(['is_active' => !$country->is_active]);
            
            $status = $country->is_active ? 'activated' : 'deactivated';
            
            return back()->with('success', "Country '{$country->name}' has been {$status} successfully.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating country status: ' . $e->getMessage());
        }
    }
}
