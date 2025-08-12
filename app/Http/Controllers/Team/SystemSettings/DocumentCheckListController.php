<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Coaching;
use App\Models\DocumentCategory;
use App\Models\DocumentCheckList;
use App\Models\ForeignCountry;
use App\Models\Purpose;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\DocumentCheckListDataTable;

class DocumentCheckListController extends Controller
{
    /**
     * Display a listing of DocumentCheckList
     */
    public function index(DocumentCheckListDataTable $DocumentCheckListDataTable)
    {
        return $DocumentCheckListDataTable->render('team.settings.document-check-list.index');
    }

    /**
     * Show the form for creating a new DocumentCheckList
     */
    public function create()
    {
        $documentCategory = DocumentCategory::active()->get();
        $country = ['all' => 'All'] + ForeignCountry::active()
                    ->pluck('name', 'id')
                    ->toArray();

        $coaching = ['all' => 'All'] + Coaching::active()
                        ->pluck('name', 'id')
                        ->toArray();
        $purpose = Purpose::active()->get();
        return view('team.settings.document-check-list.create',compact('documentCategory','country','coaching','purpose'));
    }

    /**
     * Store a newly created DocumentCheckList
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'document-check-list name is required.',
                'name.unique' => 'This document-check-list already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['category_id'] = $request->category_id;
            $validated['type'] = $request->type;
            $validated['tags'] = $request->tags;

            $validated['applicable_for'] = implode(',', $request->applicable_for);
            $validated['country'] = implode(',', $request->country);
            $validated['coaching'] = implode(',', $request->coaching);

            DocumentCheckList::create($validated);

            return redirect()->route('team.settings.document-check-list.index')
                ->with('success', "document-check-list '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating document-check-list: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified DocumentCheckList
     */
    public function show(DocumentCheckList $documentCheckList)
    {
        return view('team.settings.document-check-list.show', compact('documentCheckList'));
    }

    /**
     * Show the form for editing the specified DocumentCheckList
     */
    public function edit(DocumentCheckList $documentCheckList)
    {
        $documentCategory = DocumentCategory::active()->get();
        $country = ['all' => 'All'] + ForeignCountry::active()
                    ->pluck('name', 'id')
                    ->toArray();

        $coaching = ['all' => 'All'] + Coaching::active()
                        ->pluck('name', 'id')
                        ->toArray();
        $purpose = Purpose::active()->get();
        return view('team.settings.document-check-list.edit', compact('documentCheckList','documentCategory','country','coaching','purpose'));
    }

    /**
     * Update the specified DocumentCheckList
     */
    public function update(Request $request, DocumentCheckList $documentCheckList)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:document_check_lists,name,' . $documentCheckList->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'document-check-list name is required.',
                'name.unique' => 'This document-check-list already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['category_id'] = $request->category_id;
            $validated['type'] = $request->type;
            $validated['tags'] = $request->tags;

            $validated['applicable_for'] = implode(',', $request->applicable_for);
            $validated['country'] = implode(',', $request->country);
            $validated['coaching'] = implode(',', $request->coaching);

            $documentCheckList->update($validated);

            return redirect()->route('team.settings.document-check-list.index')
                ->with('success', "document-check-list '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating document-check-list: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified DocumentCheckList
     */
    public function destroy(DocumentCheckList $documentCheckList)
    {
        try {
            $name = $documentCheckList->name;
            $documentCheckList->delete();

            return redirect()->route('team.settings.document-check-list.index')
                ->with('success', "document-check-list '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document-check-list: ' . $e->getMessage()
            ], 500);
        }
    }
}
