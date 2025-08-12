<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\EducationLevelDataTable;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of EducationLevel
     */
    public function index(EducationLevelDataTable $EducationLevelDataTable)
    {
        return $EducationLevelDataTable->render('team.settings.education-level.index');
    }

    /**
     * Show the form for creating a new EducationLevel
     */
    public function create()
    {
        return view('team.settings.education-level.create');
    }

    /**
     * Store a newly created EducationLevel
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
                'education_levels' => 'nullable|array',
            ], [
                'name.required' => 'EducationLevel name is required.',
                'name.unique' => 'This EducationLevel already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['required_details'] = json_encode($request->input('education_levels', []));
            unset($validated['education_levels']);

            $validated['priority'] = $request->priority;
            EducationLevel::create($validated);

            return redirect()->route('team.settings.education-level.index')
                ->with('success', "Education Level '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Education Level: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified EducationLevel
     */
    public function show(EducationLevel $educationLevel)
    {
        return view('team.settings.education-level.show', compact('educationLevel'));
    }

    /**
     * Show the form for editing the specified EducationLevel
     */
    public function edit(EducationLevel $educationLevel)
    {
        $selectedRequiredFields = json_decode($educationLevel->required_details, true) ?? [];
        return view('team.settings.education-level.edit', compact('educationLevel','selectedRequiredFields'));
    }

    /**
     * Update the specified EducationLevel
     */
    public function update(Request $request, EducationLevel $educationLevel)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:education_levels,name,' . $educationLevel->id,
                'status' => 'boolean',
                'education_levels' => 'nullable|array',
            ], [
                'name.required' => 'Education Level name is required.',
                'name.unique' => 'This Purpose already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['required_details'] = json_encode($request->input('education_levels', []));
            unset($validated['education_levels']);

            $validated['priority'] = $request->priority;
            $educationLevel->update($validated);

            return redirect()->route('team.settings.education-level.index')
                ->with('success', "education-level '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating education-level: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified EducationLevel
     */
    public function destroy(EducationLevel $educationLevel)
    {
        try {
            $name = $educationLevel->name;
            $educationLevel->delete();

            return redirect()->route('team.settings.education-level.index')
                ->with('success', "Education Level '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Education Level: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle EducationLevel status
     */
    public function toggleStatus(EducationLevel $educationLevel)
    {
        try {
            $educationLevel->update(['status' => !$educationLevel->status]);

            $status = $educationLevel->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Education Level '{$educationLevel->name}' has been {$status} successfully.",
                'new_status' => $educationLevel->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Education Level status: ' . $e->getMessage()
            ], 500);
        }
    }
}
