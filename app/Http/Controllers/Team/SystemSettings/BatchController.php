<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Coaching;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\BatchDataTable;

class BatchController extends Controller
{
    /**
     * Display a listing of Batch
     */
    public function index(BatchDataTable $BatchDataTable)
    {
        return $BatchDataTable->render('team.settings.batch.index');
    }

    /**
     * Show the form for creating a new Batch
     */
    public function create()
    {
        $coachings = Coaching::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('branch_name')->get();
        return view('team.settings.batch.create', compact('coachings', 'branches'));
    }

    /**
     * Store a newly created Batch
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'coaching_id' => 'required|exists:coachings,id',
                'branch_id' => 'required|exists:branches,id',
                'time' => 'required',
                'capacity' => 'required|integer',
                'is_demo' => 'boolean',
                'status' => 'boolean',
            ], [
                'name.required' => 'Batch name is required.',
                'name.unique' => 'This Batch already exists.',
                'coaching_id.required' => 'Coaching selection is required.',
                'coaching_id.exists' => 'Selected coaching does not exist.',
                'branch_id.required' => 'Branch selection is required.',
                'branch_id.exists' => 'Selected branch does not exist.',
                'time.required' => 'Time is required.',
                'capacity.required' => 'Capacity is required.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            Batch::create($validated);

            return redirect()->route('team.settings.batch.index')
                ->with('success', "Batch '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Batch: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Batch
     */
    public function show(Batch $batch)
    {
        return view('team.settings.batch.show', compact('batch'));
    }

    /**
     * Show the form for editing the specified Batch
     */
    public function edit(Batch $batch)
    {
        $coachings = Coaching::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('branch_name')->get();
        return view('team.settings.batch.edit', compact('batch', 'coachings', 'branches'));
    }

    /**
     * Update the specified Batch
     */
    public function update(Request $request, Batch $batch)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:batches,name,' . $batch->id,
                'coaching_id' => 'required|exists:coachings,id',
                'branch_id' => 'required|exists:branches,id',
                'time' => 'required',
                'capacity' => 'required|integer',
                'is_demo' => 'boolean',
                'status' => 'boolean',
            ], [
                'name.required' => 'Batch name is required.',
                'name.unique' => 'This Batch already exists.',
                'coaching_id.required' => 'Coaching selection is required.',
                'coaching_id.exists' => 'Selected coaching does not exist.',
                'branch_id.required' => 'Branch selection is required.',
                'branch_id.exists' => 'Selected branch does not exist.',
                'time.required' => 'Time is required.',
                'capacity.required' => 'Capacity is required.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $batch->update($validated);

            return redirect()->route('team.settings.batch.index')
                ->with('success', "batch '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating batch: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Batch
     */
    public function destroy(Batch $batch)
    {
        try {
            $name = $batch->name;
            $batch->delete();

            return redirect()->route('team.settings.batch.index')
                ->with('success', "Batch '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Batch status
     */
    public function toggleStatus(Batch $batch)
    {
        try {
            $batch->update(['status' => !$batch->status]);

            $status = $batch->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Batch '{$batch->name}' has been {$status} successfully.",
                'new_status' => $batch->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Batch status: ' . $e->getMessage()
            ], 500);
        }
    }
}
