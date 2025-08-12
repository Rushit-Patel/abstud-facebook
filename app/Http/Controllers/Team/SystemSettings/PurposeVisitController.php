<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\PurposeVisit;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\PurposeVisitDataTable;

class PurposeVisitController extends Controller
{
    /**
     * Display a listing of PurposeVisit
     */
    public function index(PurposeVisitDataTable $PurposeVisitDataTable)
    {
        return $PurposeVisitDataTable->render('team.settings.purpose-visit.index');
    }

    /**
     * Show the form for creating a new PurposeVisit
     */
    public function create()
    {
        return view('team.settings.purpose-visit.create');
    }

    /**
     * Store a newly created PurposeVisit
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Purpose Visit name is required.',
                'name.unique' => 'This Purpose Visit already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            PurposeVisit::create($validated);

            return redirect()->route('team.settings.purpose-visit.index')
                ->with('success', "Purpose Visit '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Purpose Visit: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified PurposeVisit
     */
    public function show(PurposeVisit $purposeVisit)
    {
        return view('team.settings.purpose-visit.show', compact('purposeVisit'));
    }

    /**
     * Show the form for editing the specified PurposeVisit
     */
    public function edit(PurposeVisit $purposeVisit)
    {
        return view('team.settings.purpose-visit.edit', compact('purposeVisit'));
    }

    /**
     * Update the specified PurposeVisit
     */
    public function update(Request $request, PurposeVisit $purposeVisit)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:purpose_visits,name,' . $purposeVisit->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Purpose Visit name is required.',
                'name.unique' => 'This Purpose Visit already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $purposeVisit->update($validated);

            return redirect()->route('team.settings.purpose-visit.index')
                ->with('success', "purpose visit '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating purpose visit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified PurposeVisit
     */
    public function destroy(PurposeVisit $purposeVisit)
    {
        try {
            $name = $purposeVisit->name;
            $purposeVisit->delete();

            return redirect()->route('team.settings.purpose-visit.index')
                ->with('success', "Purpose Visit '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Purpose Visit: ' . $e->getMessage()
            ], 500);
        }
    }
}
