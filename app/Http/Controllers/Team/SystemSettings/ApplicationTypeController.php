<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\ApplicationType;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ApplicationTypeDataTable;

class ApplicationTypeController extends Controller
{
    /**
     * Display a listing of ApplicationType
     */
    public function index(ApplicationTypeDataTable $ApplicationTypeDataTable)
    {
        return $ApplicationTypeDataTable->render('team.settings.application-type.index');
    }

    /**
     * Show the form for creating a new ApplicationType
     */
    public function create()
    {
        return view('team.settings.application-type.create');
    }

    /**
     * Store a newly created ApplicationType
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'application-type name is required.',
                'name.unique' => 'This application-type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            ApplicationType::create($validated);

            return redirect()->route('team.settings.application-type.index')
                ->with('success', "application-type '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating application-type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ApplicationType
     */
    public function show(ApplicationType $applicationType)
    {
        return view('team.settings.application-type.show', compact('applicationType'));
    }

    /**
     * Show the form for editing the specified ApplicationType
     */
    public function edit(ApplicationType $applicationType)
    {
        return view('team.settings.application-type.edit', compact('applicationType'));
    }

    /**
     * Update the specified ApplicationType
     */
    public function update(Request $request, ApplicationType $applicationType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:application_types,name,' . $applicationType->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'application-type name is required.',
                'name.unique' => 'This application-type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $applicationType->update($validated);

            return redirect()->route('team.settings.application-type.index')
                ->with('success', "application-type '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating application-type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ApplicationType
     */
    public function destroy(ApplicationType $applicationType)
    {
        try {
            $name = $applicationType->name;
            $applicationType->delete();

            return redirect()->route('team.settings.application-type.index')
                ->with('success', "application-type '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting application-type: ' . $e->getMessage()
            ], 500);
        }
    }
}
