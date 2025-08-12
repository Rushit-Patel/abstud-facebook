<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Associate;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\AssociateDataTable;

class AssociateController extends Controller
{
    /**
     * Display a listing of Associate
     */
    public function index(AssociateDataTable $AssociateDataTable)
    {
        return $AssociateDataTable->render('team.settings.associate.index');
    }

    /**
     * Show the form for creating a new Associate
     */
    public function create()
    {
        return view('team.settings.associate.create');
    }

    /**
     * Store a newly created Associate
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Associate name is required.',
                'name.unique' => 'This Associate already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            Associate::create($validated);

            return redirect()->route('team.settings.associate.index')
                ->with('success', "Associate '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Associate: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Associate
     */
    public function show(Associate $associate)
    {
        return view('team.settings.associate.show', compact('associate'));
    }

    /**
     * Show the form for editing the specified Associate
     */
    public function edit(Associate $associate)
    {
        return view('team.settings.associate.edit', compact('associate'));
    }

    /**
     * Update the specified Associate
     */
    public function update(Request $request, Associate $associate)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:associates,name,' . $associate->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Associate name is required.',
                'name.unique' => 'This Purpose already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $associate->update($validated);

            return redirect()->route('team.settings.associate.index')
                ->with('success', "Associate '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating Associate: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Associate
     */
    public function destroy(Associate $associate)
    {
        try {
            $name = $associate->name;
            $associate->delete();

            return redirect()->route('team.settings.associate.index')
                ->with('success', "Associate '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Associate: ' . $e->getMessage()
            ], 500);
        }
    }
}
