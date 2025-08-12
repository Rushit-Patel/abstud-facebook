<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\ForeignCity;
use App\Models\ForeignCountry;
use App\Models\ForeignState;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ForeignCityDataTable;

class ForeignCityController extends Controller
{
    /**
     * Display a listing of ForeignCity
     */
    public function index(ForeignCityDataTable $ForeignCityDataTable)
    {
        return $ForeignCityDataTable->render('team.settings.foreign-city.index');
    }

    /**
     * Show the form for creating a new ForeignCity
     */
    public function create()
    {
        $foreign_countries = ForeignCountry::active()->orderBy('name')->get();

        $foreign_states = ForeignState::active()->orderBy('name')->get();

        return view('team.settings.foreign-city.create', compact('foreign_countries', 'foreign_states'));
    }

    /**
     * Store a newly created ForeignCity
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'country_id' => 'required|exists:foreign_countries,id',
                'state_id' => 'required|exists:foreign_states,id',
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'country_id.required' => 'ForeignCountry selection is required.',
                'country_id.exists' => 'Selected foreign country does not exist.',
                'state_id.required' => 'ForeignState selection is required.',
                'state_id.exists' => 'Selected foreign state does not exist.',
                'name.required' => 'Foreign City name is required.',
                'name.unique' => 'This Foreign City already exists.',
            ]);

            // Check for duplicate city name in the same foreign country
            $existingCity = ForeignCity::where('country_id', $validated['country_id'])
                ->where('state_id', $validated['state_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingCity) {
                return back()->withInput()
                    ->with('error', 'This Foreign City already exists in the selected Foreign Country and State.');
            }

            // Set default values
            $validated['country_id'] = $request->country_id;
            $validated['state_id'] = $request->state_id;
            $validated['status'] = $request->has('status');

            ForeignCity::create($validated);

            return redirect()->route('team.settings.foreign-city.index')
                ->with('success', "Foreign City '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Foreign City: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ForeignCity
     */
    public function show(ForeignCity $ForeignCity)
    {
        $ForeignCity->load(['getCountry', 'getState']);
        return view('team.settings.foreign-city.show', compact('ForeignCity'));
    }

    /**
     * Show the form for editing the specified ForeignCity
     */
    public function edit(ForeignCity $ForeignCity)
    {
        $foreign_countries = ForeignCountry::active()->orderBy('name')->get();

        $foreign_states = ForeignState::active()->orderBy('name')->get();

        return view('team.settings.foreign-city.edit', compact('ForeignCity', 'foreign_countries', 'foreign_states'));
    }

    /**
     * Update the specified ForeignCity
     */
    public function update(Request $request, ForeignCity $ForeignCity)
    {
        try {
            $validated = $request->validate([
                'country_id' => 'required|exists:foreign_countries,id',
                'state_id' => 'required|exists:foreign_states,id',
                'name' => 'required|string|max:255|unique:foreign_cities,name,' . $ForeignCity->id,
                'status' => 'boolean',
            ], [
                'country_id.required' => 'ForeignCountry selection is required.',
                'country_id.exists' => 'Selected foreign country does not exist.',
                'state_id.required' => 'ForeignState selection is required.',
                'state_id.exists' => 'Selected foreign state does not exist.',
                'name.required' => 'Foreign City name is required.',
                'name.unique' => 'This Foreign City already exists.',
            ]);

            // Check for duplicate state name in the same foreign country
            $existingState = ForeignState::where('country_id', $validated['country_id'])
            ->where('id', $validated['state_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This Foreign State already exists in the selected Foreign Country.');
            }

            // Set default values
            $validated['country_id'] = $request->country_id;
            $validated['state_id'] = $request->state_id;
            $validated['status'] = $request->has('status');

            $ForeignCity->update($validated);

            return redirect()->route('team.settings.foreign-city.index')
                ->with('success', "foreign-city '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating foreign-city: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ForeignCity
     */
    public function destroy(ForeignCity $ForeignCity)
    {
        try {
            $name = $ForeignCity->name;
            $ForeignCity->delete();

            return redirect()->route('team.settings.foreign-city.index')
                ->with('success', "City '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Foreign City: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle ForeignCity status
     */
    public function toggleStatus(ForeignCity $ForeignCity)
    {
        try {
            $ForeignCity->update(['status' => !$ForeignCity->status]);

            $status = $ForeignCity->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Foreign City '{$ForeignCity->name}' has been {$status} successfully.",
                'new_status' => $ForeignCity->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Foreign City status: ' . $e->getMessage()
            ], 500);
        }
    }
}
