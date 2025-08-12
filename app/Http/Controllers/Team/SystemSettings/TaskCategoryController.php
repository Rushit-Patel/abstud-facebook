<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\TaskCategory;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\TaskCategoryDataTable;

class TaskCategoryController extends Controller
{
    /**
     * Display a listing of TaskCategory
     */
    public function index(TaskCategoryDataTable $TaskCategoryDataTable)
    {
        return $TaskCategoryDataTable->render('team.settings.task-category.index');
    }

    /**
     * Show the form for creating a new TaskCategory
     */
    public function create()
    {
        return view('team.settings.task-category.create');
    }

    /**
     * Store a newly created TaskCategory
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'is_active' => 'boolean',
            ], [
                'name.required' => 'Task Category name is required.',
                'name.unique' => 'This Task Category already exists.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['slug'] = $request->slug;
            $validated['color'] = $request->color;
            $validated['description'] = $request->description;

            TaskCategory::create($validated);

            return redirect()->route('team.settings.task-category.index')
                ->with('success', "Task Category '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Task Category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified TaskCategory
     */
    public function show(TaskCategory $taskCategory)
    {
        return view('team.settings.task-category.show', compact('taskCategory'));
    }

    /**
     * Show the form for editing the specified Task Category
     */
    public function edit(TaskCategory $taskCategory)
    {
        return view('team.settings.task-category.edit', compact('taskCategory'));
    }

    /**
     * Update the specified TaskCategory
     */
    public function update(Request $request, TaskCategory $taskCategory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:task_categories,name,' . $taskCategory->id,
                'is_active' => 'boolean',
            ], [
                'name.required' => 'Task Category name is required.',
                'name.unique' => 'This Task Category already exists.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['slug'] = $request->slug;
            $validated['color'] = $request->color;
            $validated['description'] = $request->description;

            $taskCategory->update($validated);

            return redirect()->route('team.settings.task-category.index')
                ->with('success', "Task Category '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating Task Category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified TaskCategory
     */
    public function destroy(TaskCategory $taskCategory)
    {
        try {
            $name = $taskCategory->name;
            $taskCategory->delete();

            return redirect()->route('team.settings.task-category.index')
                ->with('success', "Task Category '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Task Category: ' . $e->getMessage()
            ], 500);
        }
    }
}
