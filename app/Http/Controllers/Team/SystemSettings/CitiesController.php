<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CitiesDataTable;

class CitiesController extends Controller
{
    /**
     * Display a listing of cities
     */
    public function index(CitiesDataTable $CitiesDataTable)
    {
        return $CitiesDataTable->render('team.settings.cities.index');
    }

    /**
     * Show the form for creating a new city
     */
    public function create()
    {
        $countries = Country::active()->orderBy('name')->get();
        $states = collect(); // Empty collection, will be populated via AJAX
        return view('team.settings.cities.create', compact('countries', 'states'));
    }

    /**
     * Store a newly created city
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'state_id' => 'required|exists:states,id',
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ], [
                'state_id.required' => 'State selection is required.',
                'state_id.exists' => 'Selected state does not exist.',
                'name.required' => 'City name is required.',
            ]);

            // Check for duplicate city name in the same state
            $existingCity = City::where('state_id', $validated['state_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingCity) {
                return back()->withInput()
                    ->with('error', 'This city already exists in the selected state.');
            }

            // Set default values
            $validated['is_active'] = $request->has('is_active');

            City::create($validated);

            return redirect()->route('team.settings.cities.index')
                ->with('success', "City '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating city: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified city
     */
    public function show(City $city)
    {
        $city->load(['state', 'state.country']);
        return view('team.settings.cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified city
     */
    public function edit(City $city)
    {
        $countries = Country::active()->orderBy('name')->get();
        $states = State::where('country_id', $city->state->country_id)->active()->orderBy('name')->get();
        return view('team.settings.cities.edit', compact('city', 'countries', 'states'));
    }

    /**
     * Update the specified city
     */
    public function update(Request $request, City $city)
    {
        try {
            $validated = $request->validate([
                'state_id' => 'required|exists:states,id',
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ], [
                'state_id.required' => 'State selection is required.',
                'state_id.exists' => 'Selected state does not exist.',
                'name.required' => 'City name is required.',
            ]);

            // Check for duplicate city name in the same state (excluding current city)
            $existingCity = City::where('state_id', $validated['state_id'])
                ->where('name', $validated['name'])
                ->where('id', '!=', $city->id)
                ->first();

            if ($existingCity) {
                return back()->withInput()
                    ->with('error', 'This city already exists in the selected state.');
            }

            // Set default values
            $validated['is_active'] = $request->has('is_active');

            $city->update($validated);

            return redirect()->route('team.settings.cities.index')
                ->with('success', "City '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating city: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified city
     */
    public function destroy(City $city)
    {
        try {
            $cityName = $city->name;
            $city->delete();

            return redirect()->route('team.settings.cities.index')
                ->with('success', "City '{$cityName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting city: ' . $e->getMessage());
        }
    }

    /**
     * Toggle city status
     */
    public function toggleStatus(City $city)
    {
        try {
            $city->update(['is_active' => !$city->is_active]);
            
            $status = $city->is_active ? 'activated' : 'deactivated';
            
            return back()->with('success', "City '{$city->name}' has been {$status} successfully.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating city status: ' . $e->getMessage());
        }
    }

    /**
     * Get states by country (AJAX endpoint)
     */
}
