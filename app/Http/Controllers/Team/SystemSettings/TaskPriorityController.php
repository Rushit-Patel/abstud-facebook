<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\TaskPriority;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\TaskPriorityDataTable;

class TaskPriorityController extends Controller
{
    /**
     * Display a listing of TaskPriority
     */
    public function index(TaskPriorityDataTable $TaskPriorityDataTable)
    {
        return $TaskPriorityDataTable->render('team.settings.task-priority.index');
    }

    /**
     * Show the form for creating a new TaskPriority
     */
    public function create()
    {
        return view('team.settings.task-priority.create');
    }

    /**
     * Store a newly created TaskPriority
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'is_active' => 'boolean',
            ], [
                'name.required' => 'task-priority name is required.',
                'name.unique' => 'This task-priority already exists.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['slug'] = $request->slug;
            $validated['color'] = $request->color;
            $validated['level'] = $request->level;

            TaskPriority::create($validated);

            return redirect()->route('team.settings.task-priority.index')
                ->with('success', "task-priority '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating task-priority: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified TaskPriority
     */
    public function show(TaskPriority $taskPriority)
    {
        return view('team.settings.task-priority.show', compact('taskPriority'));
    }

    /**
     * Show the form for editing the specified TaskPriority
     */
    public function edit(TaskPriority $taskPriority)
    {
        return view('team.settings.task-priority.edit', compact('taskPriority'));
    }

    /**
     * Update the specified TaskPriority
     */
    public function update(Request $request, TaskPriority $taskPriority)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:task_priorities,name,' . $taskPriority->id,
                'is_active' => 'boolean',
            ], [
                'name.required' => 'task-priority name is required.',
                'name.unique' => 'This task-priority already exists.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['slug'] = $request->slug;
            $validated['color'] = $request->color;
            $validated['level'] = $request->level;

            $taskPriority->update($validated);

            return redirect()->route('team.settings.task-priority.index')
                ->with('success', "task-priority '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating task-priority: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified TaskPriority
     */
    public function destroy(TaskPriority $taskPriority)
    {
        try {
            $name = $taskPriority->name;
            $taskPriority->delete();

            return redirect()->route('team.settings.task-priority.index')
                ->with('success', "task-priority '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting task-priority: ' . $e->getMessage()
            ], 500);
        }
    }
}
