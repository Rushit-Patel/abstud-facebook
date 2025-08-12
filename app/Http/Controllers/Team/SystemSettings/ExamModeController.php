<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\EnglishProficiencyTest;
use App\Models\EnglishProficiencyTestModual;
use App\Models\ExamMode;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ExamModeDataTable;

class ExamModeController extends Controller
{
    /**
     * Display a listing of ExamMode
     */
    public function index(ExamModeDataTable $ExamModeDataTable)
    {
        return $ExamModeDataTable->render('team.settings.exam-mode.index');
    }

    /**
     * Show the form for creating a new ExamMode
     */
    public function create()
    {
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        return view('team.settings.exam-mode.create',compact('englishProficiencyTest'));
    }

    /**
     * Store a newly created ExamMode
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'exam-mode name is required.',
                'name.unique' => 'This exam-mode already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['english_proficiency_test_id'] = $request->english_proficiency_test_id;

            ExamMode::create($validated);

            return redirect()->route('team.settings.exam-mode.index')
                ->with('success', "exam-mode '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating exam-mode: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ExamMode
     */
    public function show(ExamMode $examMode)
    {
        return view('team.settings.exam-mode.show', compact('examMode'));
    }

    /**
     * Show the form for editing the specified ExamMode
     */
    public function edit(ExamMode $examMode)
    {
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        return view('team.settings.exam-mode.edit', compact('examMode','englishProficiencyTest'));
    }

    /**
     * Update the specified ExamMode
     */
    public function update(Request $request, ExamMode $examMode)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:exam_modes,name,' . $examMode->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'exam-mode name is required.',
                'name.unique' => 'This exam-mode already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['english_proficiency_test_id'] = $request->english_proficiency_test_id;

            $examMode->update($validated);

            return redirect()->route('team.settings.exam-mode.index')
                ->with('success', "exam-mode '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating exam-mode: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ExamMode
     */
    public function destroy(ExamMode $examMode)
    {
        try {
            $name = $examMode->name;
            $examMode->delete();

            return redirect()->route('team.settings.exam-mode.index')
                ->with('success', "exam-mode '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting exam-mode: ' . $e->getMessage()
            ], 500);
        }
    }
}
