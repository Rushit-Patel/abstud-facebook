<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\VisitorApplicant;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\VisitorApplicantDataTable;

class VisitorApplicantController extends Controller
{
    /**
     * Display a listing of VisitorApplicant
     */
    public function index(VisitorApplicantDataTable $VisitorApplicantDataTable)
    {
        return $VisitorApplicantDataTable->render('team.settings.visitor-applicant.index');
    }

    /**
     * Show the form for creating a new VisitorApplicant
     */
    public function create()
    {
        return view('team.settings.visitor-applicant.create');
    }

    /**
     * Store a newly created VisitorApplicant
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'no_of_applicant' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Visitor Applicant name is required.',
                'no_of_applicant.required' => 'No of Applicant name is required.',
                'name.unique' => 'This Visitor Applicant already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            VisitorApplicant::create($validated);

            return redirect()->route('team.settings.visitor-applicant.index')
                ->with('success', "Visitor Applicant '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Visitor Applicant: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified VisitorApplicant
     */
    public function show(VisitorApplicant $visitorApplicant)
    {
        return view('team.settings.visitor-applicant.show', compact('visitorApplicant'));
    }

    /**
     * Show the form for editing the specified VisitorApplicant
     */
    public function edit(VisitorApplicant $visitorApplicant)
    {
        return view('team.settings.visitor-applicant.edit', compact('visitorApplicant'));
    }

    /**
     * Update the specified VisitorApplicant
     */
    public function update(Request $request, VisitorApplicant $visitorApplicant)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:visitor_applicants,name,' . $visitorApplicant->id,
                'no_of_applicant' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Visitor Applicant name is required.',
                'no_of_applicant.required' => 'No of Applicant name is required.',
                'name.unique' => 'This Visitor Applicant already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $visitorApplicant->update($validated);

            return redirect()->route('team.settings.visitor-applicant.index')
                ->with('success', "visitor applicants '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating visitor applicants: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified VisitorApplicant
     */
    public function destroy(VisitorApplicant $visitorApplicant)
    {
        try {
            $name = $visitorApplicant->name;
            $visitorApplicant->delete();

            return redirect()->route('team.settings.visitor-applicant.index')
                ->with('success', "Visitor Applicant '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Visitor Applicant: ' . $e->getMessage()
            ], 500);
        }
    }

}
