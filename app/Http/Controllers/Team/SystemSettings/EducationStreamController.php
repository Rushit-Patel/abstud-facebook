<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use App\Models\EducationStream;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\EducationStreamDataTable;

class EducationStreamController extends Controller
{
    /**
     * Display a listing of EducationStream
     */
    public function index(EducationStreamDataTable $EducationStreamDataTable)
    {
        return $EducationStreamDataTable->render('team.settings.education-stream.index');
    }

    /**
     * Show the form for creating a new EducationStream
     */
    public function create()
    {
        $educationLevels = EducationLevel::active()->orderBy('name')->get();
        return view('team.settings.education-stream.create',compact('educationLevels'));
    }

    /**
     * Store a newly created EducationStream
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
                'education_level_id' => 'required|array|min:1',
                'education_level_id.*' => 'exists:education_levels,id',
            ], [
                'name.required' => 'Education Stream name is required.',
                'name.unique' => 'This Education Stream already exists.',
                'education_level_id.required' => 'At least one education level must be selected.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['education_level_id'] = implode(',', $request->education_level_id);

            EducationStream::create($validated);

            return redirect()->route('team.settings.education-stream.index')
                ->with('success', "Education Stream '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Education Stream: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified EducationStream
     */
    public function show(EducationStream $educationStream)
    {
        return view('team.settings.education-stream.show', compact('educationStream'));
    }

    /**
     * Show the form for editing the specified EducationStream
     */
    public function edit(EducationStream $educationStream)
    {
        $educationLevels = EducationLevel::active()->orderBy('name')->get();
        $selectedEducationLevels = !empty($educationStream->education_level_id)
            ? explode(',', $educationStream->education_level_id)
            : [];

        return view('team.settings.education-stream.edit', compact('educationStream', 'educationLevels', 'selectedEducationLevels'));
    }

    /**
     * Update the specified EducationStream
     */
    public function update(Request $request, EducationStream $educationStream)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:education_streams,name,' . $educationStream->id,
                'status' => 'boolean',
                'education_level_id' => 'required|array|min:1',
                'education_level_id.*' => 'exists:education_levels,id',
            ], [
                'name.required' => 'Education Stream name is required.',
                'name.unique' => 'This Purpose already exists.',
                'education_level_id.required' => 'At least one education level must be selected.',
            ]);
            // Set default values
            $validated['status'] = $request->has('status');
            $validated['education_level_id'] = collect($request->education_level_id)->implode(',');

            $educationStream->update($validated);

            return redirect()->route('team.settings.education-stream.index')
                ->with('success', "education-stream '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating education-stream: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified educationStream
     */
    public function destroy(EducationStream $educationStream)
    {
        try {
            $name = $educationStream->name;
            $educationStream->delete();

            return redirect()->route('team.settings.education-stream.index')
                ->with('success', "Education Stream '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Education Stream: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle EducationStream status
     */
    public function toggleStatus(EducationStream $educationStream)
    {
        try {
            $educationStream->update(['status' => !$educationStream->status]);

            $status = $educationStream->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Education Stream '{$educationStream->name}' has been {$status} successfully.",
                'new_status' => $educationStream->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Education Stream status: ' . $e->getMessage()
            ], 500);
        }
    }
}
