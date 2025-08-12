<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseType;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CourseTypeDataTable;

class CourseTypeController extends Controller
{
    /**
     * Display a listing of CourseType
     */
    public function index(CourseTypeDataTable $CourseTypeDataTable)
    {
        return $CourseTypeDataTable->render('team.settings.course-type.index');
    }

    /**
     * Show the form for creating a new CourseType
     */
    public function create()
    {
        $course = Course::active()->get();
        return view('team.settings.course-type.create',compact('course'));
    }

    /**
     * Store a newly created CourseType
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'course-type name is required.',
                'name.unique' => 'This course-type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['course_id'] = $request->course_id;

            CourseType::create($validated);

            return redirect()->route('team.settings.course-type.index')
                ->with('success', "course-type '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating course-type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified CourseType
     */
    public function show(CourseType $courseType)
    {
        return view('team.settings.course-type.show', compact('courseType'));
    }

    /**
     * Show the form for editing the specified CourseType
     */
    public function edit(CourseType $courseType)
    {
        $course = Course::active()->get();
        return view('team.settings.course-type.edit', compact('courseType','course'));
    }

    /**
     * Update the specified CourseType
     */
    public function update(Request $request, CourseType $courseType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'boolean',
            ], [
                'name.required' => 'course-type name is required.',
                'name.unique' => 'This course-type already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['course_id'] = $request->course_id;

            $courseType->update($validated);

            return redirect()->route('team.settings.course-type.index')
                ->with('success', "course-type '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating course-type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified CourseType
     */
    public function destroy(CourseType $courseType)
    {
        try {
            $name = $courseType->name;
            $courseType->delete();

            return redirect()->route('team.settings.course-type.index')
                ->with('success', "course-type '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting course-type: ' . $e->getMessage()
            ], 500);
        }
    }

}
