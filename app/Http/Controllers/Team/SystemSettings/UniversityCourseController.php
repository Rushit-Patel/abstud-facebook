<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Course;
use App\Models\ForeignCountry;
use App\Models\University;
use App\Models\UniversityCourse;
use App\Models\UniversityCourseKey;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\UniversityCourseDataTable;

class UniversityCourseController extends Controller
{
    /**
     * Display a listing of UniversityCourse
     */
    public function index(UniversityCourseDataTable $UniversityCourseDataTable)
    {
        return $UniversityCourseDataTable->render('team.settings.university-course.index');
    }

    /**
     * Show the form for creating a new UniversityCourse
     */
    public function create()
    {
        $universities = University::active()->orderBy('name')->get();
        $campuses = Campus::active()->orderBy('name')->get();
        $courses = Course::active()->orderBy('name')->get();
        $university_course_keys = UniversityCourseKey::active()->orderBy('name')->get();

        return view('team.settings.university-course.create', compact('universities', 'campuses', 'courses', 'university_course_keys'));
    }

    /**
     * Store a newly created UniversityCourse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'university_id' => 'required|exists:foreign_countries,id',
                'campus_id' => 'required|exists:foreign_countries,id',
                'course_id' => 'required|exists:foreign_countries,id',
                'status' => 'boolean',
            ], [
                'university_id.required' => 'University selection is required.',
                'university_id.exists' => 'Selected foreign country does not exist.',
                'campus_id.required' => 'Campus selection is required.',
                'campus_id.exists' => 'Selected Campus does not exist.',
                'course_id.required' => 'Course selection is required.',
                'course_id.exists' => 'Selected Course does not exist.',
            ]);

            // Set default values
            $validated['university_id'] = $request->university_id;
            $validated['campus_id'] = $request->campus_id;
            $validated['course_id'] = $request->course_id;
            $validated['status'] = $request->has('status');

            UniversityCourse::create($validated);

            return redirect()->route('team.settings.university-course.index')
                ->with('success', "University Course has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating University Course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified UniversityCourse
     */
    public function show(UniversityCourse $UniversityCourse)
    {
        $UniversityCourse->load(['getUniversity', 'getCampus', 'getCourse']);
        return view('team.settings.university-course.show', compact('UniversityCourse'));
    }

    /**
     * Show the form for editing the specified UniversityCourse
     */
    public function edit(UniversityCourse $UniversityCourse)
    {
        $universities = University::active()->orderBy('name')->get();
        $campuses = Campus::active()->orderBy('name')->get();
        $courses = Course::active()->orderBy('name')->get();
        $university_course_keys = UniversityCourseKey::active()->orderBy('name')->get();
        return view('team.settings.university-course.edit', compact('UniversityCourse', 'universities', 'campuses', 'courses', 'university_course_keys'));
    }

    /**
     * Update the specified UniversityCourse
     */
    public function update(Request $request, UniversityCourse $UniversityCourse)
    {
        try {
            $validated = $request->validate([
                'university_id' => 'required|exists:universities,id',
                'campus_id' => 'required|exists:campuses,id',
                'course_id' => 'required|exists:courses,id',
                'status' => 'boolean',
            ], [
                'university_id.required' => 'University selection is required.',
                'university_id.exists' => 'Selected university does not exist.',
                'campus_id.required' => 'Campus selection is required.',
                'campus_id.exists' => 'Selected campus does not exist.',
                'course_id.required' => 'Course selection is required.',
                'course_id.exists' => 'Selected course does not exist.',
            ]);

            // Set default values
            $validated['university_id'] = $request->university_id;
            $validated['course_id'] = $request->course_id;
            $validated['campus_id'] = $request->campus_id;
            $validated['status'] = $request->has('status');

            $UniversityCourse->update($validated);

            return redirect()->route('team.settings.university-course.index')
                ->with('success', "university-course has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating university-course: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified UniversityCourse
     */
    public function destroy(UniversityCourse $UniversityCourse)
    {
        try {
            $name = $UniversityCourse->name;
            $UniversityCourse->delete();

            return redirect()->route('team.settings.university-course.index')
                ->with('success', "University Course '{$name}' has been deleted successfully.");


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting University Course: ' . $e->getMessage()
            ], 500);
        }
    }
}
