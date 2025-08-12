<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\StateDataTable;

class StatesController extends Controller
{
    /**
     * Display a listing of states
     */
    public function index(StateDataTable $StateDataTable)
    {
          return $StateDataTable->render('team.settings.states.index');
    }

    /**
     * Show the form for creating a new state
     */
    public function create()
    {
        $countries = Country::active()->orderBy('name')->get();
        return view('team.settings.states.create', compact('countries'));
    }

    /**
     * Store a newly created state
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'country_id' => 'required|exists:countries,id',
                'name' => 'required|string|max:255',
                'state_code' => 'nullable|string|max:10',
                'is_active' => 'boolean',
            ], [
                'country_id.required' => 'Country selection is required.',
                'country_id.exists' => 'Selected country does not exist.',
                'name.required' => 'State name is required.',
            ]);

            // Check for duplicate state name in the same country
            $existingState = State::where('country_id', $validated['country_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This state already exists in the selected country.');
            }

            // Set default values
            $validated['is_active'] = $request->has('is_active');

            State::create($validated);

            return redirect()->route('team.settings.states.index')
                ->with('success', "State '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating state: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified state
     */
    public function show(State $state)
    {
        $state->load(['country', 'cities']);
        return view('team.settings.states.show', compact('state'));
    }

    /**
     * Show the form for editing the specified state
     */
    public function edit(State $state)
    {
        $countries = Country::active()->orderBy('name')->get();
        return view('team.settings.states.edit', compact('state', 'countries'));
    }

    /**
     * Update the specified state
     */
    public function update(Request $request, State $state)
    {
        try {
            $validated = $request->validate([
                'country_id' => 'required|exists:countries,id',
                'name' => 'required|string|max:255',
                'state_code' => 'nullable|string|max:10',
                'is_active' => 'boolean',
            ], [
                'country_id.required' => 'Country selection is required.',
                'country_id.exists' => 'Selected country does not exist.',
                'name.required' => 'State name is required.',
            ]);

            // Check for duplicate state name in the same country (excluding current state)
            $existingState = State::where('country_id', $validated['country_id'])
                ->where('name', $validated['name'])
                ->where('id', '!=', $state->id)
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This state already exists in the selected country.');
            }

            // Set default values
            $validated['is_active'] = $request->has('is_active');

            $state->update($validated);

            return redirect()->route('team.settings.states.index')
                ->with('success', "State '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating state: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified state
     */
    public function destroy(State $state)
    {
        try {
            $stateName = $state->name;
            
            // Check if state has cities
            if ($state->cities()->count() > 0) {
                return back()->with('error', "Cannot delete '{$stateName}' as it has associated cities.");
            }

            $state->delete();

            return redirect()->route('team.settings.states.index')
                ->with('success', "State '{$stateName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting state: ' . $e->getMessage());
        }
    }

    /**
     * Toggle state status
     */
    public function toggleStatus(State $state)
    {
        try {
            $state->update(['is_active' => !$state->is_active]);
            
            $status = $state->is_active ? 'activated' : 'deactivated';
            
            return back()->with('success', "State '{$state->name}' has been {$status} successfully.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating state status: ' . $e->getMessage());
        }
    }
}
