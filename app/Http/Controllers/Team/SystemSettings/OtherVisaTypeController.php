<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\OtherVisaType;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\OtherVisaTypeDataTable;

class OtherVisaTypeController extends Controller
{
    /**
     * Display a listing of OtherVisaType
     */
    public function index(OtherVisaTypeDataTable $OtherVisaTypeDataTable)
    {
        return $OtherVisaTypeDataTable->render('team.settings.other-visa-type.index');
    }

    /**
     * Show the form for creating a new OtherVisaType
     */
    public function create()
    {
        return view('team.settings.other-visa-type.create');
    }

    /**
     * Store a newly created OtherVisaType
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Visa Type name is required.',
                'name.unique' => 'This Visa Type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            OtherVisaType::create($validated);

            return redirect()->route('team.settings.other-visa-type.index')
                ->with('success', "Visa Type '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Visa Type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified OtherVisaType
     */
    public function show(OtherVisaType $otherVisaType)
    {
        return view('team.settings.other-visa-type.show', compact('otherVisaType'));
    }

    /**
     * Show the form for editing the specified OtherVisaType
     */
    public function edit(OtherVisaType $otherVisaType)
    {
        return view('team.settings.other-visa-type.edit', compact('otherVisaType'));
    }

    /**
     * Update the specified OtherVisaType
     */
    public function update(Request $request, OtherVisaType $otherVisaType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:other_visa_types,name,' . $otherVisaType->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Visa Type name is required.',
                'name.unique' => 'This Visa Type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $otherVisaType->update($validated);

            return redirect()->route('team.settings.other-visa-type.index')
                ->with('success', "Visa Type '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating Visa Type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified OtherVisaType
     */
    public function destroy(OtherVisaType $otherVisaType)
    {
        try {
            $name = $otherVisaType->name;
            $otherVisaType->delete();

            return redirect()->route('team.settings.other-visa-type.index')
                ->with('success', "Visa Type '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Visa Type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle OtherVisaType status
     */
    public function toggleStatus(OtherVisaType $otherVisaType)
    {
        try {
            $otherVisaType->update(['status' => !$otherVisaType->status]);

            $status = $otherVisaType->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Visa Type '{$otherVisaType->name}' has been {$status} successfully.",
                'new_status' => $otherVisaType->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Visa Type status: ' . $e->getMessage()
            ], 500);
        }
    }
}
