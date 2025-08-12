<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\ForeignCity;
use App\Models\ForeignCountry;
use App\Models\ForeignState;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CampusDataTable;

class CampusController extends Controller
{
    /**
     * Display a listing of Campus
     */
    public function index(CampusDataTable $CampusDataTable)
    {
        return $CampusDataTable->render('team.settings.campus.index');
    }

    /**
     * Show the form for creating a new Campus
     */
    public function create()
    {
        $countries = ForeignCountry::active()->get();
        return view('team.settings.campus.create', compact('countries'));
    }

    /**
     * Store a newly created Campus
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Campus name is required.',
                'name.unique' => 'This Campus already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['f_country_id'] = $request->country_id;
            $validated['f_state_id'] = $request->state_id;
            $validated['f_city_id'] = $request->city_id;

            Campus::create($validated);

            return redirect()->route('team.settings.campus.index')
                ->with('success', "Campus '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Campus: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Campus
     */
    public function show(Campus $campus)
    {
        return view('team.settings.campus.show', compact('campus'));
    }

    /**
     * Show the form for editing the specified Campus
     */
    public function edit(Campus $campus)
    {
        $countries = ForeignCountry::active()->get();
        return view('team.settings.campus.edit', compact('campus','countries'));
    }

    /**
     * Update the specified Campus
     */
    public function update(Request $request, Campus $campus)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:campuses,name,' . $campus->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Campus name is required.',
                'name.unique' => 'This Campus already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['country_id'] = $request->country_id;
            $validated['state_id'] = $request->state_id;
            $validated['city_id'] = $request->city_id;

            $campus->update($validated);

            return redirect()->route('team.settings.campus.index')
                ->with('success', "Campus '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating Campus: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Campus
     */
    public function destroy(Campus $campus)
    {
        try {
            $name = $campus->name;
            $campus->delete();

            return redirect()->route('team.settings.campus.index')
                ->with('success', "Campus '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Campus: ' . $e->getMessage()
            ], 500);
        }
    }
}
