<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\BillingCompany;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\BillingCompanyDataTable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BillingCompanyController extends Controller
{
    /**
     * Display a listing of BillingCompany
     */
    public function index(BillingCompanyDataTable $BillingCompanyDataTable)
    {
        return $BillingCompanyDataTable->render('team.settings.billing-company.index');
    }

    /**
     * Show the form for creating a new BillingCompany
     */
    public function create()
    {
        $branches = Branch::active()->get();
        return view('team.settings.billing-company.create',compact('branches'));
    }

    /**
     * Store a newly created BillingCompany
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'billing-company name is required.',
                'name.unique' => 'This billing-company already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['mobile_no'] = $request->mobile_no;
            $validated['email_id'] = $request->email_id;
            $validated['address'] = $request->address;
            if ( $request->hasFile('comapny_logo') && $request->file('comapny_logo')->isValid()) {
                    $clientName = Str::slug($request->name); // Slug for safe folder name
                    $timestamp = time();
                    $folderPath = "billing-company/{$clientName}_{$timestamp}";
                    $file = $request->file('comapny_logo');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $storedFilePath = $file->storeAs($folderPath, $fileName, 'public');
                    $comapnyLogoCopyPath = $storedFilePath; // This will be stored in DB
                } else {
                    $comapnyLogoCopyPath = null;
                }
                $validated['company_logo'] = $comapnyLogoCopyPath;
                $validated['branch'] = implode(',', $request->branch);
            if(isset($request->gst) && $request->gst == '1'){
                $validated['is_gst'] = $request->gst;
                $validated['gst_form_name'] = $request->gst_form_name;
                $validated['gst_number'] = $request->gst_number;
            }

            BillingCompany::create($validated);

            return redirect()->route('team.settings.billing-company.index')
                ->with('success', "billing-company '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating billing-company: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified BillingCompany
     */
    public function show(BillingCompany $billingCompany)
    {
        return view('team.settings.billing-company.show', compact('billingCompany'));
    }

    /**
     * Show the form for editing the specified BillingCompany
     */
    public function edit(BillingCompany $billingCompany)
    {
        $branches = Branch::active()->get();
        return view('team.settings.billing-company.edit', compact('billingCompany','branches'));
    }

    /**
     * Update the specified BillingCompany
     */
    public function update(Request $request, BillingCompany $billingCompany)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:billing_companies,name,' . $billingCompany->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'billing-company name is required.',
                'name.unique' => 'This billing-company already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['mobile_no'] = $request->mobile_no;
            $validated['email_id'] = $request->email_id;
            $validated['address'] = $request->address;
            $validated['branch'] = implode(',', $request->branch);

            if ($request->hasFile('comapny_logo') && $request->file('comapny_logo')->isValid()) {
                // Delete old file if exists
                if ($billingCompany->company_logo && Storage::disk('public')->exists($billingCompany->company_logo)) {
                    Storage::disk('public')->delete($billingCompany->company_logo);
                }

                $clientName = Str::slug($request->name);
                $timestamp = time();
                $folderPath = "billing-company/{$clientName}_{$timestamp}";
                $file = $request->file('comapny_logo');
                $fileName = $timestamp . '_' . $file->getClientOriginalName();
                $storedFilePath = $file->storeAs($folderPath, $fileName, 'public');

                $validated['company_logo'] = $storedFilePath;
            }
             if ($request->has('gst') && $request->gst == '1') {
                $validated['is_gst'] = 1;
                $validated['gst_form_name'] = $request->gst_form_name;
                $validated['gst_number'] = $request->gst_number;
            } else {
                $validated['is_gst'] = 0;
                $validated['gst_form_name'] = null;
                $validated['gst_number'] = null;
            }


            $billingCompany->update($validated);

            return redirect()->route('team.settings.billing-company.index')
                ->with('success', "billing-company '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating billing-company: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified BillingCompany
     */
    public function destroy(BillingCompany $billingCompany)
    {
        try {
            $name = $billingCompany->name;
            $billingCompany->delete();

            return redirect()->route('team.settings.billing-company.index')
                ->with('success', "billing-company '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting billing-company: ' . $e->getMessage()
            ], 500);
        }
    }
}
