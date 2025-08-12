<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Purpose;
use App\Models\Service;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\ServiceDataTable;

class ServiceController extends Controller
{
    /**
     * Display a listing of Service
     */
    public function index(ServiceDataTable $ServiceDataTable)
    {
        return $ServiceDataTable->render('team.settings.service.index');
    }

    /**
     * Show the form for creating a new Service
     */
    public function create()
    {
        $purpose = Purpose::active()->get();
        return view('team.settings.service.create',compact('purpose'));
    }

    /**
     * Store a newly created Service
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'Service name is required.',
                'name.unique' => 'This Service already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['amount'] = $request->amount;
            $validated['purpose'] = implode(',', $request->purpose);

            Service::create($validated);

            return redirect()->route('team.settings.service.index')
                ->with('success', "Service '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Service: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Service
     */
    public function show(Service $service)
    {
        return view('team.settings.service.show', compact('service'));
    }

    /**
     * Show the form for editing the specified Service
     */
    public function edit(Service $service)
    {
        $purpose = Purpose::active()->get();
        return view('team.settings.service.edit', compact('service','purpose'));
    }

    /**
     * Update the specified Service
     */
    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:services,name,' . $service->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'Service name is required.',
                'name.unique' => 'This Service already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['amount'] = $request->amount;
            $validated['purpose'] = implode(',', $request->purpose);

            $service->update($validated);

            return redirect()->route('team.settings.service.index')
                ->with('success', "service '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating service: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Service
     */
    public function destroy(Service $service)
    {
        try {
            $name = $service->name;
            $service->delete();

            return redirect()->route('team.settings.service.index')
                ->with('success', "Service '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Service: ' . $e->getMessage()
            ], 500);
        }
    }
}
