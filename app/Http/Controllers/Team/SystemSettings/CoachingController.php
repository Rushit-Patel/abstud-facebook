<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Coaching;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CoachingDataTable;

class CoachingController extends Controller
{
    /**
     * Display a listing of Coaching
     */
    public function index(CoachingDataTable $CoachingDataTable)
    {
        return $CoachingDataTable->render('team.settings.coaching.index');
    }

    /**
     * Show the form for creating a new Coaching
     */
    public function create()
    {
        return view('team.settings.coaching.create');
    }

    /**
     * Store a newly created Coaching
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Coaching name is required.',
                'name.unique' => 'This Coaching already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['is_coaching'] = $request->has('is_coaching');
            $validated['priority'] = $request->priority;

            Coaching::create($validated);

            return redirect()->route('team.settings.coaching.index')
                ->with('success', "Coaching '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Coaching: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Coaching
     */
    public function show(Coaching $coaching)
    {
        return view('team.settings.coaching.show', compact('coaching'));
    }

    /**
     * Show the form for editing the specified Coaching
     */
    public function edit(Coaching $coaching)
    {
        return view('team.settings.coaching.edit', compact('coaching'));
    }

    /**
     * Update the specified Coaching
     */
    public function update(Request $request, Coaching $coaching)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:coachings,name,' . $coaching->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Coaching name is required.',
                'name.unique' => 'This Coaching already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['is_coaching'] = $request->has('is_coaching');
            $validated['priority'] = $request->priority;

            $coaching->update($validated);

            return redirect()->route('team.settings.coaching.index')
                ->with('success', "coaching '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating coaching: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Coaching
     */
    public function destroy(Coaching $coaching)
    {
        try {
            $name = $coaching->name;

            // Check if state has batches
            if ($coaching->batches()->count() > 0) {
                return back()->with('error', "Cannot delete '{$name}' as it has associated batches.");
            }

            $coaching->delete();

            return redirect()->route('team.settings.coaching.index')
                ->with('success', "Coaching '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Coaching: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Coaching status
     */
    public function toggleStatus(Coaching $coaching)
    {
        try {
            $coaching->update(['status' => !$coaching->status]);

            $status = $coaching->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Coaching '{$coaching->name}' has been {$status} successfully.",
                'new_status' => $coaching->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Coaching status: ' . $e->getMessage()
            ], 500);
        }
    }
}
