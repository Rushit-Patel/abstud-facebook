<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\LeadType;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\LeadTypeDataTable;

class LeadTypesController extends Controller
{
    /**
     * Display a listing of lead types
     */
    public function index(LeadTypeDataTable $leadTypeDataTable)
    {
        return $leadTypeDataTable->render('team.settings.lead-types.index');
    }

    /**
     * Show the form for creating a new lead type
     */
    public function create()
    {
        return view('team.settings.lead-types.create');
    }

    /**
     * Store a newly created lead type
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:lead_types,name',
                'status' => 'boolean',
            ], [
                'name.required' => 'Lead type name is required.',
                'name.unique' => 'This lead type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            LeadType::create($validated);

            return redirect()->route('team.settings.lead-types.index')
                ->with('success', "Lead type '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating lead type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lead type
     */
    public function show(LeadType $leadType)
    {
        return view('team.settings.lead-types.show', compact('leadType'));
    }

    /**
     * Show the form for editing the specified lead type
     */
    public function edit(LeadType $leadType)
    {
        return view('team.settings.lead-types.edit', compact('leadType'));
    }

    /**
     * Update the specified lead type
     */
    public function update(Request $request, LeadType $leadType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:lead_types,name,' . $leadType->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Lead type name is required.',
                'name.unique' => 'This lead type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $leadType->update($validated);

            return redirect()->route('team.settings.lead-types.index')
                ->with('success', "Lead type '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating lead type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lead type
     */
    public function destroy(LeadType $leadType)
    {
        try {
            $name = $leadType->name;
            $leadType->delete();

            return redirect()->route('team.settings.lead-types.index')
                ->with('success', "Lead type '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lead type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle lead type status
     */
    public function toggleStatus(LeadType $leadType)
    {
        try {
            $leadType->update(['status' => !$leadType->status]);

            $status = $leadType->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Lead type '{$leadType->name}' has been {$status} successfully.",
                'new_status' => $leadType->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lead type status: ' . $e->getMessage()
            ], 500);
        }
    }
}
