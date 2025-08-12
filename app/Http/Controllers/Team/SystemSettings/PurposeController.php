<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Purpose;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\PurposeDataTable;
use Illuminate\Support\Facades\Storage;

class  PurposeController extends Controller
{
    /**
     * Display a listing of Purpose
     */
    public function index(PurposeDataTable $PurposeDataTable)
    {
        return $PurposeDataTable->render('team.settings.purpose.index');
    }

    /**
     * Show the form for creating a new purpose
     */
    public function create()
    {
        return view('team.settings.purpose.create');
    }

    /**
     * Store a newly created purpose
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,svg',
                'status' => 'boolean',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('purposes', 'public');
            }
            $validated['priority'] = $request->priority;
            Purpose::create($validated);

            return redirect()->route('team.settings.purpose.index')
                ->with('success', "Purpose '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating purpose: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified purpose
     */
    public function edit(Purpose $purpose)
    {
        return view('team.settings.purpose.edit', compact('purpose'));
    }

    /**
     * Update the specified purpose
     */
    public function update(Request $request, Purpose $purpose)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:purposes,name,' . $purpose->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'status' => 'boolean',
            ], [
                'name.required' => 'Purpose name is required.',
                'name.unique' => 'This Purpose already exists.',
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg.',
                'image.max' => 'Image may not be greater than 2MB.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($purpose->image) {
                    Storage::disk('public')->delete($purpose->image);
                }
                $validated['image'] = $request->file('image')->store('purposes', 'public');
            }
            $validated['priority'] = $request->priority;
            $purpose->update($validated);

            return redirect()->route('team.settings.purpose.index')
                ->with('success', "Purpose '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating Purpose: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified status
     */
    public function destroy(Purpose $purpose)
    {
        try {
            $purposeName = $purpose->name;

            // Delete associated image file
            if ($purpose->image) {
                Storage::disk('public')->delete($purpose->image);
            }

            $purpose->delete();

            return redirect()->route('team.settings.purpose.index')
                ->with('success', "Purpose '{$purposeName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting purpose: ' . $e->getMessage());
        }
    }

    /**
     * Toggle purpose status
     */
    public function toggleStatus(Purpose $purpose)
    {
        try {
            $purpose->update(['status' => !$purpose->status]);

            $status = $purpose->status ? 'activated' : 'deactivated';

            return back()->with('success', "purpose '{$purpose->name}' has been {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating purpose status: ' . $e->getMessage());
        }
    }
}
