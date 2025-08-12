<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\SourceDataTable;

class SourceController extends Controller
{
    /**
     * Display a listing of Source
     */
    public function index(SourceDataTable $SourceDataTable)
    {
        return $SourceDataTable->render('team.settings.source.index');
    }

    /**
     * Show the form for creating a new Source
     */
    public function create()
    {
        return view('team.settings.source.create');
    }

    /**
     * Store a newly created Source
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Source name is required.',
                'name.unique' => 'This Source already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            Source::create($validated);

            return redirect()->route('team.settings.source.index')
                ->with('success', "Source '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Source: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Source
     */
    public function show(Source $source)
    {
        return view('team.settings.source.show', compact('source'));
    }

    /**
     * Show the form for editing the specified Source
     */
    public function edit(Source $source)
    {
        return view('team.settings.source.edit', compact('source'));
    }

    /**
     * Update the specified Source
     */
    public function update(Request $request, Source $source)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:sources,name,' . $source->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Source name is required.',
                'name.unique' => 'This Purpose already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $source->update($validated);

            return redirect()->route('team.settings.source.index')
                ->with('success', "source '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating source: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Source
     */
    public function destroy(Source $Source)
    {
        try {
            $name = $Source->name;
            $Source->delete();

            return redirect()->route('team.settings.source.index')
                ->with('success', "Source '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Source: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Source status
     */
    public function toggleStatus(Source $Source)
    {
        try {
            $Source->update(['status' => !$Source->status]);

            $status = $Source->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Source '{$Source->name}' has been {$status} successfully.",
                'new_status' => $Source->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Source status: ' . $e->getMessage()
            ], 500);
        }
    }
}
