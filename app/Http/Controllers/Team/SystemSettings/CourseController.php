<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CourseDataTable;

class CourseController extends Controller
{
    /**
     * Display a listing of Course
     */
    public function index(CourseDataTable $CourseDataTable)
    {
        return $CourseDataTable->render('team.settings.course.index');
    }

    /**
     * Show the form for creating a new Course
     */
    public function create()
    {
        return view('team.settings.course.create');
    }

    /**
     * Store a newly created Course
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Course name is required.',
                'name.unique' => 'This Course already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            Course::create($validated);

            return redirect()->route('team.settings.course.index')
                ->with('success', "Course '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Course
     */
    public function show(Course $course)
    {
        return view('team.settings.course.show', compact('course'));
    }

    /**
     * Show the form for editing the specified Course
     */
    public function edit(Course $course)
    {
        return view('team.settings.course.edit', compact('course'));
    }

    /**
     * Update the specified Course
     */
    public function update(Request $request, Course $course)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:courses,name,' . $course->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Course name is required.',
                'name.unique' => 'This Course already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $course->update($validated);

            return redirect()->route('team.settings.course.index')
                ->with('success', "course '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating course: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Course
     */
    public function destroy(Course $course)
    {
        try {
            $name = $course->name;
            $course->delete();

            return redirect()->route('team.settings.course.index')
                ->with('success', "Course '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Course: ' . $e->getMessage()
            ], 500);
        }
    }
}
