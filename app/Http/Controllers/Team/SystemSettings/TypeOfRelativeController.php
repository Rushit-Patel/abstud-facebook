<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\TypeOfRelative;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\TypeOfRelativeDataTable;

class TypeOfRelativeController extends Controller
{
    /**
     * Display a listing of TypeOfRelative
     */
    public function index(TypeOfRelativeDataTable $TypeOfRelativeDataTable)
    {
        return $TypeOfRelativeDataTable->render('team.settings.type-of-relative.index');
    }

    /**
     * Show the form for creating a new TypeOfRelative
     */
    public function create()
    {
        return view('team.settings.type-of-relative.create');
    }

    /**
     * Store a newly created TypeOfRelative
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Type Of Relative name is required.',
                'name.unique' => 'This Type Of Relative already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            TypeOfRelative::create($validated);

            return redirect()->route('team.settings.type-of-relative.index')
                ->with('success', "Type Of Relative '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Type Of Relative: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified TypeOfRelative
     */
    public function show(TypeOfRelative $typeOfRelative)
    {
        return view('team.settings.type-of-relative.show', compact('typeOfRelative'));
    }

    /**
     * Show the form for editing the specified TypeOfRelative
     */
    public function edit(TypeOfRelative $typeOfRelative)
    {
        return view('team.settings.type-of-relative.edit', compact('typeOfRelative'));
    }

    /**
     * Update the specified TypeOfRelative
     */
    public function update(Request $request, TypeOfRelative $typeOfRelative)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:type_of_relatives,name,' . $typeOfRelative->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Type Of Relative name is required.',
                'name.unique' => 'This Type Of Relative already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $typeOfRelative->update($validated);

            return redirect()->route('team.settings.type-of-relative.index')
                ->with('success', "type-of-relative '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating type-of-relative: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified TypeOfRelative
     */
    public function destroy(TypeOfRelative $typeOfRelative)
    {
        try {
            $name = $typeOfRelative->name;
            $typeOfRelative->delete();

            return redirect()->route('team.settings.type-of-relative.index')
                ->with('success', "Type Of Relative '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Type Of Relative: ' . $e->getMessage()
            ], 500);
        }
    }
}
