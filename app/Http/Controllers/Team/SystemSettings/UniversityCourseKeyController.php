<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\UniversityCourseKey;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\UniversityCourseKeyDataTable;

class UniversityCourseKeyController extends Controller
{
    /**
     * Display a listing of UniversityCourseKey
     */
    public function index(UniversityCourseKeyDataTable $UniversityCourseKeyDataTable)
    {
        return $UniversityCourseKeyDataTable->render('team.settings.university-course-key.index');
    }

    /**
     * Show the form for creating a new UniversityCourseKey
     */
    public function create()
    {
        return view('team.settings.university-course-key.create');
    }

    /**
     * Store a newly created UniversityCourseKey
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'title' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'University Course Key name is required.',
                'name.unique' => 'This University Course Key already exists.',
                'title.required' => 'University Course Key title is required.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['title'] = $request->title;
            $validated['priority'] = $request->priority;

            UniversityCourseKey::create($validated);

            return redirect()->route('team.settings.university-course-key.index')
                ->with('success', "University Course Key '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating University Course Key: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified UniversityCourseKey
     */
    public function show(UniversityCourseKey $UniversityCourseKey)
    {
        return view('team.settings.university-course-key.show', compact('UniversityCourseKey'));
    }

    /**
     * Show the form for editing the specified UniversityCourseKey
     */
    public function edit(UniversityCourseKey $UniversityCourseKey)
    {
        return view('team.settings.university-course-key.edit', compact('UniversityCourseKey'));
    }

    /**
     * Update the specified UniversityCourseKey
     */
    public function update(Request $request, UniversityCourseKey $UniversityCourseKey)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:foreign_countries,name,' . $UniversityCourseKey->id,
                'title' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'University Course Key name is required.',
                'name.unique' => 'This University Course Key already exists.',
                'title.required' => 'University Course Key title is required.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['title'] = $request->title;
            $validated['priority'] = $request->priority;

            $UniversityCourseKey->update($validated);

            return redirect()->route('team.settings.university-course-key.index')
                ->with('success', "university-course-key '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating university-course-key: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified UniversityCourseKey
     */
    public function destroy(UniversityCourseKey $UniversityCourseKey)
    {
        try {
            $name = $UniversityCourseKey->name;
            $UniversityCourseKey->delete();

            return redirect()->route('team.settings.university-course-key.index')
                ->with('success', "University Course Key '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting University Course Key: ' . $e->getMessage()
            ], 500);
        }
    }
}
