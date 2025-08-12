<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\ForeignCountry;
use App\Models\ForeignState;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ForeignStateDataTable;

class ForeignStateController extends Controller
{
    /**
     * Display a listing of ForeignState
     */
    public function index(ForeignStateDataTable $ForeignStateDataTable)
    {
        return $ForeignStateDataTable->render('team.settings.foreign-state.index');
    }

    /**
     * Show the form for creating a new ForeignState
     */
    public function create()
    {
        $foreign_countries = ForeignCountry::active()->orderBy('name')->get();
        return view('team.settings.foreign-state.create', compact('foreign_countries'));
    }

    /**
     * Store a newly created ForeignState
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'country_id' => 'required|exists:foreign_countries,id',
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'country_id.required' => 'ForeignCountry selection is required.',
                'country_id.exists' => 'Selected foreign country does not exist.',
                'name.required' => 'Foreign State name is required.',
                'name.unique' => 'This Foreign State already exists.',
            ]);

            // Check for duplicate state name in the same foreign country
            $existingState = ForeignState::where('country_id', $validated['country_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This Foreign State already exists in the selected Foreign Country.');
            }

            // Set default values
            $validated['country_id'] = $request->country_id;
            $validated['status'] = $request->has('status');

            ForeignState::create($validated);

            return redirect()->route('team.settings.foreign-state.index')
                ->with('success', "Foreign State '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Foreign State: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ForeignState
     */
    public function show(ForeignState $ForeignState)
    {
        $ForeignState->load(['getCountry']);
        return view('team.settings.foreign-state.show', compact('ForeignState'));
    }

    /**
     * Show the form for editing the specified ForeignState
     */
    public function edit(ForeignState $ForeignState)
    {
        $foreign_countries = ForeignCountry::active()->orderBy('name')->get();
        return view('team.settings.foreign-state.edit', compact('ForeignState', 'foreign_countries'));
    }

    /**
     * Update the specified ForeignState
     */
    public function update(Request $request, ForeignState $ForeignState)
    {
        try {
            $validated = $request->validate([
                'country_id' => 'required|exists:foreign_countries,id',
                'name' => 'required|string|max:255|unique:foreign_states,name,' . $ForeignState->id,
                'status' => 'boolean',
            ], [
                'country_id.required' => 'Foreign Country selection is required.',
                'country_id.exists' => 'Selected foreign country does not exist.',
                'name.required' => 'Foreign State name is required.',
                'name.unique' => 'This Foreign State already exists.',
            ]);

            // Check for duplicate state name in the same foreign country
            $existingState = ForeignState::where('country_id', $validated['country_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This Foreign State already exists in the selected Foreign Country.');
            }

            // Set default values
            $validated['country_id'] = $request->country_id;
            $validated['status'] = $request->has('status');

            $ForeignState->update($validated);

            return redirect()->route('team.settings.foreign-state.index')
                ->with('success', "foreign-state '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating foreign-state: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ForeignState
     */
    public function destroy(ForeignState $ForeignState)
    {
        try {
            $name = $ForeignState->name;
            $ForeignState->delete();

            return redirect()->route('team.settings.foreign-state.index')
                ->with('success', "State '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Foreign State: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle ForeignState status
     */
    public function toggleStatus(ForeignState $ForeignState)
    {
        try {
            $ForeignState->update(['status' => !$ForeignState->status]);

            $status = $ForeignState->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Foreign State '{$ForeignState->name}' has been {$status} successfully.",
                'new_status' => $ForeignState->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Foreign State status: ' . $e->getMessage()
            ], 500);
        }
    }
}
