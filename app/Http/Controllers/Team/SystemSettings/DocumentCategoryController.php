<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\DocumentCategoryDataTable;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of DocumentCategory
     */
    public function index(DocumentCategoryDataTable $DocumentCategoryDataTable)
    {
        return $DocumentCategoryDataTable->render('team.settings.document-category.index');
    }

    /**
     * Show the form for creating a new DocumentCategory
     */
    public function create()
    {
        return view('team.settings.document-category.create');
    }

    /**
     * Store a newly created DocumentCategory
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'document-category name is required.',
                'name.unique' => 'This document-category already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            DocumentCategory::create($validated);

            return redirect()->route('team.settings.document-category.index')
                ->with('success', "document-category '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating document-category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified DocumentCategory
     */
    public function show(DocumentCategory $documentCategory)
    {
        return view('team.settings.document-category.show', compact('documentCategory'));
    }

    /**
     * Show the form for editing the specified DocumentCategory
     */
    public function edit(DocumentCategory $documentCategory)
    {
        return view('team.settings.document-category.edit', compact('documentCategory'));
    }

    /**
     * Update the specified DocumentCategory
     */
    public function update(Request $request, DocumentCategory $documentCategory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:document_categories,name,' . $documentCategory->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'document-category name is required.',
                'name.unique' => 'This document-category already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $documentCategory->update($validated);

            return redirect()->route('team.settings.document-category.index')
                ->with('success', "document-category '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating document-category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified DocumentCategory
     */
    public function destroy(DocumentCategory $documentCategory)
    {
        try {
            $name = $documentCategory->name;
            $documentCategory->delete();

            return redirect()->route('team.settings.document-category.index')
                ->with('success', "document-category '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document-category: ' . $e->getMessage()
            ], 500);
        }
    }
}
