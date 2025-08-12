<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\UniversityDataTable;

class UniversityController extends Controller
{
    /**
     * Display a listing of University
     */
    public function index(UniversityDataTable $UniversityDataTable)
    {
        return $UniversityDataTable->render('team.settings.university.index');
    }

    /**
     * Show the form for creating a new University
     */
    public function create()
    {
        return view('team.settings.university.create');
    }

    /**
     * Store a newly created University
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'University name is required.',
                'name.unique' => 'This University already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            University::create($validated);

            return redirect()->route('team.settings.university.index')
                ->with('success', "University '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating University: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified University
     */
    public function show(University $University)
    {
        return view('team.settings.university.show', compact('University'));
    }

    /**
     * Show the form for editing the specified University
     */
    public function edit(University $University)
    {
        return view('team.settings.university.edit', compact('University'));
    }

    /**
     * Update the specified University
     */
    public function update(Request $request, University $University)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:foreign_countries,name,' . $University->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'University name is required.',
                'name.unique' => 'This University already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $University->update($validated);

            return redirect()->route('team.settings.university.index')
                ->with('success', "university '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating university: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified University
     */
    public function destroy(University $University)
    {
        try {
            $name = $University->name;
            $University->delete();

            return redirect()->route('team.settings.university.index')
                ->with('success', "University '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting University: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle University status
     */
    public function toggleStatus(University $University)
    {
        try {
            $University->update(['status' => !$University->status]);

            $status = $University->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "University '{$University->name}' has been {$status} successfully.",
                'new_status' => $University->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating University status: ' . $e->getMessage()
            ], 500);
        }
    }
}
