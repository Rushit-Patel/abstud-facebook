<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\TaskStatusDataTable;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of TaskStatus
     */
    public function index(TaskStatusDataTable $TaskStatusDataTable)
    {
        return $TaskStatusDataTable->render('team.settings.task-status.index');
    }

    /**
     * Show the form for creating a new TaskStatus
     */
    public function create()
    {
        return view('team.settings.task-status.create');
    }

    /**
     * Store a newly created TaskStatus
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'is_active' => 'boolean',
            ], [
                'name.required' => 'task-status name is required.',
                'name.unique' => 'This task-status already exists.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['is_completed'] = $request->has('is_completed');
            $validated['slug'] = $request->slug;
            $validated['color'] = $request->color;
            $validated['order'] = $request->order;

            TaskStatus::create($validated);

            return redirect()->route('team.settings.task-status.index')
                ->with('success', "task-status '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating task-status: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified TaskStatus
     */
    public function show(TaskStatus $taskStatus)
    {
        return view('team.settings.task-status.show', compact('taskStatus'));
    }

    /**
     * Show the form for editing the specified TaskStatus
     */
    public function edit(TaskStatus $taskStatus)
    {
        return view('team.settings.task-status.edit', compact('taskStatus'));
    }

    /**
     * Update the specified TaskStatus
     */
    public function update(Request $request, TaskStatus $taskStatus)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:task_statuses,name,' . $taskStatus->id,
                'is_active' => 'boolean',
            ], [
                'name.required' => 'task-status name is required.',
                'name.unique' => 'This task-status already exists.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['is_completed'] = $request->has('is_completed');
            $validated['slug'] = $request->slug;
            $validated['color'] = $request->color;
            $validated['order'] = $request->order;

            $taskStatus->update($validated);

            return redirect()->route('team.settings.task-status.index')
                ->with('success', "task-status '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating task-status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified TaskStatus
     */
    public function destroy(TaskStatus $taskStatus)
    {
        try {
            $name = $taskStatus->name;
            $taskStatus->delete();

            return redirect()->route('team.settings.task-status.index')
                ->with('success', "task-status '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting task-status: ' . $e->getMessage()
            ], 500);
        }
    }
}
