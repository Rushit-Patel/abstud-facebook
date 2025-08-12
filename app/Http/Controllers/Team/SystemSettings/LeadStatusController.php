<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\LeadStatusDataTable;

class LeadStatusController extends Controller
{
    /**
     * Display a listing of LeadStatus
     */
    public function index(LeadStatusDataTable $LeadStatusDataTable)
    {
        return $LeadStatusDataTable->render('team.settings.lead-status.index');
    }

    /**
     * Show the form for creating a new LeadStatus
     */
    public function create()
    {
        return view('team.settings.lead-status.create');
    }

    /**
     * Store a newly created LeadStatus
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'LeadStatus name is required.',
                'name.unique' => 'This LeadStatus already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            LeadStatus::create($validated);

            return redirect()->route('team.settings.lead-status.index')
                ->with('success', "Lead Status '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Lead Status: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified LeadStatus
     */
    public function show(LeadStatus $LeadStatus)
    {
        return view('team.settings.lead-status.show', compact('LeadStatus'));
    }

    /**
     * Show the form for editing the specified LeadStatus
     */
    public function edit(LeadStatus $LeadStatus)
    {
        return view('team.settings.lead-status.edit', compact('LeadStatus'));
    }

    /**
     * Update the specified LeadStatus
     */
    public function update(Request $request, LeadStatus $LeadStatus)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:lead_statuses,name,' . $LeadStatus->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Lead Status name is required.',
                'name.unique' => 'This Lead Status already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $LeadStatus->update($validated);

            return redirect()->route('team.settings.lead-status.index')
                ->with('success', "lead-status '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating lead-status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified LeadStatus
     */
    public function destroy(LeadStatus $LeadStatus)
    {
        try {
            $name = $LeadStatus->name;
            $LeadStatus->delete();

            return redirect()->route('team.settings.lead-status.index')
                ->with('success', "Lead Status '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Lead Status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle LeadStatus status
     */
    public function toggleStatus(LeadStatus $LeadStatus)
    {
        try {
            $LeadStatus->update(['status' => !$LeadStatus->status]);

            $status = $LeadStatus->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Lead Status '{$LeadStatus->name}' has been {$status} successfully.",
                'new_status' => $LeadStatus->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Lead Status status: ' . $e->getMessage()
            ], 500);
        }
    }
}
