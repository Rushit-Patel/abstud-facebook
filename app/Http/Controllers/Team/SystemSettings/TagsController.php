<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\LeadTag;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\TagsDataTable;

class TagsController extends Controller
{
    /**
     * Display a listing of LeadTag
     */
    public function index(TagsDataTable $TagsDataTable)
    {
        return $TagsDataTable->render('team.settings.tags.index');
    }

    /**
     * Show the form for creating a new LeadTag
     */
    public function create()
    {
        return view('team.settings.tags.create');
    }

    /**
     * Store a newly created LeadTag
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Lead Tag name is required.',
                'name.unique' => 'This Lead Tag already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            LeadTag::create($validated);

            return redirect()->route('team.settings.tags.index')
                ->with('success', "Lead Tag '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Lead Tag: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified LeadTag
     */
    public function show(LeadTag $tags)
    {
        return view('team.settings.tags.show', compact('tags'));
    }

    /**
     * Show the form for editing the specified LeadTag
     */
    public function edit($id)
    {
        $leadTag = LeadTag::withTrashed()->findOrFail($id);
        return view('team.settings.tags.edit', compact('leadTag'));
    }

    /**
     * Update the specified LeadTag
     */
    public function update(Request $request, $id)
    {
        $leadTag = LeadTag::withTrashed()->findOrFail($id);
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:lead_tags,name,' . $leadTag->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Lead Tag name is required.',
                'name.unique' => 'This Purpose already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $leadTag->update($validated);

            return redirect()->route('team.settings.tags.index')
                ->with('success', "tags '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating tags: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified LeadTag
     */
    public function destroy($id)
    {
        $LeadTag = LeadTag::withTrashed()->findOrFail($id);
        try {
            $name = $LeadTag->name;
            $LeadTag->delete();

            return redirect()->route('team.settings.tags.index')
                ->with('success', "Lead Tag '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Lead Tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Lead Tag status
     */
    public function toggleStatus(LeadTag $LeadTag)
    {
        try {
            $LeadTag->update(['status' => !$LeadTag->status]);

            $status = $LeadTag->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Lead Tag '{$LeadTag->name}' has been {$status} successfully.",
                'new_status' => $LeadTag->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Lead Tag status: ' . $e->getMessage()
            ], 500);
        }
    }
}
