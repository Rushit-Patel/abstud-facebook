<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\MaritalStatus;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\MaritalStatusDataTable;

class MaritalStatusController extends Controller
{
    /**
     * Display a listing of MaritalStatus
     */
    public function index(MaritalStatusDataTable $MaritalStatusDataTable)
    {
        return $MaritalStatusDataTable->render('team.settings.marital-status.index');
    }

    /**
     * Show the form for creating a new MaritalStatus
     */
    public function create()
    {
        return view('team.settings.marital-status.create');
    }

    /**
     * Store a newly created MaritalStatus
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Marital Status name is required.',
                'name.unique' => 'This Marital Status already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            MaritalStatus::create($validated);

            return redirect()->route('team.settings.marital-status.index')
                ->with('success', "Marital Status '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Marital Status: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified MaritalStatus
     */
    public function show(MaritalStatus $maritalStatus)
    {
        return view('team.settings.marital-status.show', compact('maritalStatus'));
    }

    /**
     * Show the form for editing the specified MaritalStatus
     */
    public function edit(MaritalStatus $maritalStatus)
    {
        return view('team.settings.marital-status.edit', compact('maritalStatus'));
    }

    /**
     * Update the specified MaritalStatus
     */
    public function update(Request $request, MaritalStatus $maritalStatus)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:marital_statuses,name,' . $maritalStatus->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Marital Status name is required.',
                'name.unique' => 'This Purpose already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $maritalStatus->update($validated);

            return redirect()->route('team.settings.marital-status.index')
                ->with('success', "marital status '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating marital-status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified MaritalStatus
     */
    public function destroy(MaritalStatus $maritalStatus)
    {
        try {
            $name = $maritalStatus->name;
            $maritalStatus->delete();

            return redirect()->route('team.settings.marital-status.index')
                ->with('success', "Marital Status '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Marital Status: ' . $e->getMessage()
            ], 500);
        }
    }


}
