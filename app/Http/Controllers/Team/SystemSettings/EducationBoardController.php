<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\EducationBoard;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\EducationBoardDataTable;

class EducationBoardController extends Controller
{
    /**
     * Display a listing of EducationBoard
     */
    public function index(EducationBoardDataTable $EducationBoardDataTable)
    {
        return $EducationBoardDataTable->render('team.settings.education-board.index');
    }

    /**
     * Show the form for creating a new EducationBoard
     */
    public function create()
    {
        return view('team.settings.education-board.create');
    }

    /**
     * Store a newly created EducationBoard
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Education Board name is required.',
                'name.unique' => 'This Education Board already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            EducationBoard::create($validated);

            return redirect()->route('team.settings.education-board.index')
                ->with('success', "Education Board '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Education Board: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified EducationBoard
     */
    public function show(EducationBoard $educationBoard)
    {
        return view('team.settings.education-board.show', compact('educationBoard'));
    }

    /**
     * Show the form for editing the specified EducationBoard
     */
    public function edit(EducationBoard $educationBoard)
    {
        return view('team.settings.education-board.edit', compact('educationBoard'));
    }

    /**
     * Update the specified EducationBoard
     */
    public function update(Request $request, EducationBoard $educationBoard)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:education_boards,name,' . $educationBoard->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Education Board name is required.',
                'name.unique' => 'This Purpose already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $educationBoard->update($validated);

            return redirect()->route('team.settings.education-board.index')
                ->with('success', "education-board '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating education-board: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified educationBoard
     */
    public function destroy(EducationBoard $educationBoard)
    {
        try {
            $name = $educationBoard->name;
            $educationBoard->delete();

            return redirect()->route('team.settings.education-board.index')
                ->with('success', "Education Board '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Education Board: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle EducationBoard status
     */
    public function toggleStatus(EducationBoard $educationBoard)
    {
        try {
            $educationBoard->update(['status' => !$educationBoard->status]);

            $status = $educationBoard->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Education Board '{$educationBoard->name}' has been {$status} successfully.",
                'new_status' => $educationBoard->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Education Board status: ' . $e->getMessage()
            ], 500);
        }
    }
}
