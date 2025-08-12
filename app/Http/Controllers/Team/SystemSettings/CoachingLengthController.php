<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\CoachingLength;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CoachingLengthDataTable;

class CoachingLengthController extends Controller
{
    /**
     * Display a listing of CoachingLength
     */
    public function index(CoachingLengthDataTable $CoachingLengthDataTable)
    {
        return $CoachingLengthDataTable->render('team.settings.coaching-length.index');
    }

    /**
     * Show the form for creating a new CoachingLength
     */
    public function create()
    {
        return view('team.settings.coaching-length.create');
    }

    /**
     * Store a newly created CoachingLength
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Coaching Length name is required.',
                'name.unique' => 'This Coaching Length already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            CoachingLength::create($validated);

            return redirect()->route('team.settings.coaching-length.index')
                ->with('success', "Coaching Length '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Coaching Length: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Coaching Length
     */
    public function show(CoachingLength $coachingLength)
    {
        return view('team.settings.coaching-length.show', compact('coachingLength'));
    }

    /**
     * Show the form for editing the specified CoachingLength
     */
    public function edit(CoachingLength $coachingLength)
    {
        return view('team.settings.coaching-length.edit', compact('coachingLength'));
    }

    /**
     * Update the specified CoachingLength
     */
    public function update(Request $request, CoachingLength $coachingLength)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:coaching_lengths,name,' . $coachingLength->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Coaching Length name is required.',
                'name.unique' => 'This Coaching Length already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $coachingLength->update($validated);

            return redirect()->route('team.settings.coaching-length.index')
                ->with('success', "coaching length '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating coaching length: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified CoachingLength
     */
    public function destroy(CoachingLength $coachingLength)
    {
        try {
            $name = $coachingLength->name;
            $coachingLength->delete();

            return redirect()->route('team.settings.coaching-length.index')
                ->with('success', "coaching Length '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting coaching Length: ' . $e->getMessage()
            ], 500);
        }
    }
}
