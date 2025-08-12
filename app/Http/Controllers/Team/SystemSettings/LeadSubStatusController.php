<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\LeadSubStatusDataTable;

class LeadSubStatusController extends Controller
{
    /**
     * Display a listing of LeadStatus
     */
    public function index(LeadSubStatusDataTable $LeadSubStatusDataTable)
    {
          return $LeadSubStatusDataTable->render('team.settings.lead-sub-status.index');
    }

    /**
     * Show the form for creating a new LeadStatus
     */
    public function create()
    {
        $leadStatues = LeadStatus::active()->orderBy('name')->get();
        return view('team.settings.lead-sub-status.create', compact('leadStatues'));
    }

    /**
     * Store a newly created LeadStatus
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'name' => 'required|string|max:255',
                'status' => 'boolean',
            ], [
                'lead_status_id.required' => 'LeadStatus selection is required.',
                'lead_status_id.exists' => 'Selected lead status does not exist.',
                'name.required' => 'LeadSubStatus name is required.',
            ]);

            // Check for duplicate state name in the same lead status
            $existingState = LeadSubStatus::where('lead_status_id', $validated['lead_status_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This Lead Status already exists in the selected lead status.');
            }

            // Set default values
            $validated['status'] = $request->has('status');

            LeadSubStatus::create($validated);

            return redirect()->route('team.settings.lead-sub-status.index')
                ->with('success', "LeadSubStatus '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating lead-sub-status: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified state
     */
    public function show(LeadSubStatus $leadSubStatus)
    {
        $leadSubStatus->load(['leadStatus']);
        return view('team.settings.lead-sub-status.show', compact('leadSubStatus'));
    }

    /**
     * Show the form for editing the specified state
     */
    public function edit(LeadSubStatus $leadSubStatus)
    {
        $leadStatues = LeadStatus::active()->orderBy('name')->get();
        return view('team.settings.lead-sub-status.edit', compact('leadSubStatus', 'leadStatues'));
    }

    /**
     * Update the specified state
     */
    public function update(Request $request, LeadSubStatus $leadSubStatus)
    {
        try {
            $validated = $request->validate([
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'name' => 'required|string|max:255',
                'status' => 'boolean',
            ], [
                'lead_status_id.required' => 'LeadStatus selection is required.',
                'lead_status_id.exists' => 'Selected status does not exist.',
                'name.required' => 'LeadSubStatus name is required.',
            ]);

            // Check for duplicate lead_status_id name in the same lead status (excluding current state)
            $existingState = LeadSubStatus::where('lead_status_id', $validated['lead_status_id'])
                ->where('name', $validated['name'])
                ->where('id', '!=', $leadSubStatus->id)
                ->first();

            if ($existingState) {
                return back()->withInput()
                    ->with('error', 'This state already exists in the selected lead status.');
            }

            // Set default values
            $validated['status'] = $request->has('status');

            $leadSubStatus->update($validated);

            return redirect()->route('team.settings.lead-sub-status.index')
                ->with('success', "LeadSubStatus '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating state: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified state
     */
    public function destroy(LeadSubStatus $leadSubStatus)
    {
        try {
            $leadSubStatusName = $leadSubStatus->name;

            $leadSubStatus->delete();

            return redirect()->route('team.settings.lead-sub-status.index')
                ->with('success', "LeadSubStatus '{$leadSubStatusName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting state: ' . $e->getMessage());
        }
    }

    /**
     * Toggle state status
     */
    public function toggleStatus(LeadSubStatus $leadSubStatus)
    {
        try {
            $leadSubStatus->update(['status' => !$leadSubStatus->status]);

            $status = $leadSubStatus->status ? 'activated' : 'deactivated';

            return back()->with('success', "LeadSubStatus '{$leadSubStatus->name}' has been {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating state status: ' . $e->getMessage());
        }
    }
}
