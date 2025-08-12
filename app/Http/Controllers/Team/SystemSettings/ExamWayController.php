<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\ExamWay;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ExamWayDataTable;

class ExamWayController extends Controller
{
    /**
     * Display a listing of ExamWay
     */
    public function index(ExamWayDataTable $ExamWayDataTable)
    {
        return $ExamWayDataTable->render('team.settings.exam-way.index');
    }

    /**
     * Show the form for creating a new ExamWay
     */
    public function create()
    {
        return view('team.settings.exam-way.create');
    }

    /**
     * Store a newly created ExamWay
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'exam-way name is required.',
                'name.unique' => 'This exam-way already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            ExamWay::create($validated);

            return redirect()->route('team.settings.exam-way.index')
                ->with('success', "exam-way '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating exam-way: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ExamWay
     */
    public function show(ExamWay $examWay)
    {
        return view('team.settings.exam-way.show', compact('examWay'));
    }

    /**
     * Show the form for editing the specified ExamWay
     */
    public function edit(ExamWay $examWay)
    {
        return view('team.settings.exam-way.edit', compact('examWay'));
    }

    /**
     * Update the specified ExamWay
     */
    public function update(Request $request, ExamWay $examWay)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:exam_ways,name,' . $examWay->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'exam-way name is required.',
                'name.unique' => 'This exam-way already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $examWay->update($validated);

            return redirect()->route('team.settings.exam-way.index')
                ->with('success', "exam-way '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating exam-way: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ExamWay
     */
    public function destroy(ExamWay $examWay)
    {
        try {
            $name = $examWay->name;
            $examWay->delete();

            return redirect()->route('team.settings.exam-way.index')
                ->with('success', "exam-way '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting exam-way: ' . $e->getMessage()
            ], 500);
        }
    }

}
