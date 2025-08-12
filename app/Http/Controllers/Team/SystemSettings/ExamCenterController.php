<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\ExamCenter;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ExamCenterDataTable;

class ExamCenterController extends Controller
{
    /**
     * Display a listing of ExamCenter
     */
    public function index(ExamCenterDataTable $ExamCenterDataTable)
    {
        return $ExamCenterDataTable->render('team.settings.exam-center.index');
    }

    /**
     * Show the form for creating a new ExamCenter
     */
    public function create()
    {
        return view('team.settings.exam-center.create');
    }

    /**
     * Store a newly created ExamCenter
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Exam Center name is required.',
                'name.unique' => 'This exam-center already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            ExamCenter::create($validated);

            return redirect()->route('team.settings.exam-center.index')
                ->with('success', "exam-center '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating exam-center: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ExamCenter
     */
    public function show(ExamCenter $examCenter)
    {
        return view('team.settings.exam-center.show', compact('examCenter'));
    }

    /**
     * Show the form for editing the specified ExamCenter
     */
    public function edit(ExamCenter $examCenter)
    {
        return view('team.settings.exam-center.edit', compact('examCenter'));
    }

    /**
     * Update the specified ExamCenter
     */
    public function update(Request $request, ExamCenter $examCenter)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:exam_centers,name,' . $examCenter->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'exam-center name is required.',
                'name.unique' => 'This exam-center already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $examCenter->update($validated);

            return redirect()->route('team.settings.exam-center.index')
                ->with('success', "exam-center '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating exam-center: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ExamCenter
     */
    public function destroy(ExamCenter $examCenter)
    {
        try {
            $name = $examCenter->name;
            $examCenter->delete();

            return redirect()->route('team.settings.exam-center.index')
                ->with('success', "exam-center '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting exam-center: ' . $e->getMessage()
            ], 500);
        }
    }
}
