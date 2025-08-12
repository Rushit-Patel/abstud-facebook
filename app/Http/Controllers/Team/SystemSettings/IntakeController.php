<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Intake;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\IntakeDataTable;

class IntakeController extends Controller
{
    /**
     * Display a listing of Intake
     */
    public function index(IntakeDataTable $IntakeDataTable)
    {
        return $IntakeDataTable->render('team.settings.intake.index');
    }

    /**
     * Show the form for creating a new Intake
     */
    public function create()
    {
        return view('team.settings.intake.create');
    }

    /**
     * Store a newly created Intake
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Intake name is required.',
                'name.unique' => 'This Intake already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['year'] = $request->year;
            $validated['month'] = implode(',', $request->month);

            Intake::create($validated);

            return redirect()->route('team.settings.intake.index')
                ->with('success', "Intake '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Intake: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Intake
     */
    public function show(Intake $intake)
    {
        return view('team.settings.intake.show', compact('intake'));
    }

    /**
     * Show the form for editing the specified Intake
     */
    public function edit(Intake $intake)
    {
        return view('team.settings.intake.edit', compact('intake'));
    }

    /**
     * Update the specified Intake
     */
    public function update(Request $request, Intake $intake)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:intakes,name,' . $intake->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Intake name is required.',
                'name.unique' => 'This intakes already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['year'] = $request->year;
            $validated['month'] = implode(',', $request->month);

            $intake->update($validated);

            return redirect()->route('team.settings.intake.index')
                ->with('success', "intake '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating intake: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Intake
     */
    public function destroy(Intake $intake)
    {
        try {
            $name = $intake->name;
            $intake->delete();

            return redirect()->route('team.settings.intake.index')
                ->with('success', "Intake '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Intake: ' . $e->getMessage()
            ], 500);
        }
    }
}
