<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\ForeignCountry;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ForeignCountryDataTable;

class ForeignCountryController extends Controller
{
    /**
     * Display a listing of ForeignCountry
     */
    public function index(ForeignCountryDataTable $ForeignCountryDataTable)
    {
        return $ForeignCountryDataTable->render('team.settings.foreign-country.index');
    }

    /**
     * Show the form for creating a new ForeignCountry
     */
    public function create()
    {
        return view('team.settings.foreign-country.create');
    }

    /**
     * Store a newly created ForeignCountry
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Foreign Country name is required.',
                'name.unique' => 'This Foreign Country already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['priority'] = $request->priority;

            ForeignCountry::create($validated);

            return redirect()->route('team.settings.foreign-country.index')
                ->with('success', "Foreign Country '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Foreign Country: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ForeignCountry
     */
    public function show(ForeignCountry $ForeignCountry)
    {
        return view('team.settings.foreign-country.show', compact('ForeignCountry'));
    }

    /**
     * Show the form for editing the specified ForeignCountry
     */
    public function edit(ForeignCountry $ForeignCountry)
    {
        return view('team.settings.foreign-country.edit', compact('ForeignCountry'));
    }

    /**
     * Update the specified ForeignCountry
     */
    public function update(Request $request, ForeignCountry $ForeignCountry)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:foreign_countries,name,' . $ForeignCountry->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Foreign Country name is required.',
                'name.unique' => 'This Foreign Country already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['priority'] = $request->priority;

            $ForeignCountry->update($validated);

            return redirect()->route('team.settings.foreign-country.index')
                ->with('success', "foreign-country '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating foreign-country: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ForeignCountry
     */
    public function destroy(ForeignCountry $ForeignCountry)
    {
        try {
            $name = $ForeignCountry->name;
            $ForeignCountry->delete();

            return redirect()->route('team.settings.foreign-country.index')
                ->with('success', "Country '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Foreign Country: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle ForeignCountry status
     */
    public function toggleStatus(ForeignCountry $ForeignCountry)
    {
        try {
            $ForeignCountry->update(['status' => !$ForeignCountry->status]);

            $status = $ForeignCountry->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Foreign Country '{$ForeignCountry->name}' has been {$status} successfully.",
                'new_status' => $ForeignCountry->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Foreign Country status: ' . $e->getMessage()
            ], 500);
        }
    }
}
