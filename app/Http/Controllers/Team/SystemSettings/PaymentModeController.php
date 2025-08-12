<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\PaymentModeDataTable;

class PaymentModeController extends Controller
{
    /**
     * Display a listing of PaymentMode
     */
    public function index(PaymentModeDataTable $PaymentModeDataTable)
    {
        return $PaymentModeDataTable->render('team.settings.payment-mode.index');
    }

    /**
     * Show the form for creating a new PaymentMode
     */
    public function create()
    {
        return view('team.settings.payment-mode.create');
    }

    /**
     * Store a newly created PaymentMode
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'payment-mode name is required.',
                'name.unique' => 'This payment-mode already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            PaymentMode::create($validated);

            return redirect()->route('team.settings.payment-mode.index')
                ->with('success', "payment-mode '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating payment-mode: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified PaymentMode
     */
    public function show(PaymentMode $paymentMode)
    {
        return view('team.settings.payment-mode.show', compact('paymentMode'));
    }

    /**
     * Show the form for editing the specified PaymentMode
     */
    public function edit(PaymentMode $paymentMode)
    {
        return view('team.settings.payment-mode.edit', compact('paymentMode'));
    }

    /**
     * Update the specified PaymentMode
     */
    public function update(Request $request, PaymentMode $paymentMode)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:payment_modes,name,' . $paymentMode->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'payment-mode name is required.',
                'name.unique' => 'This payment-mode already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');

            $paymentMode->update($validated);

            return redirect()->route('team.settings.payment-mode.index')
                ->with('success', "payment-mode '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating payment-mode: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified PaymentMode
     */
    public function destroy(PaymentMode $paymentMode)
    {
        try {
            $name = $paymentMode->name;
            $paymentMode->delete();

            return redirect()->route('team.settings.payment-mode.index')
                ->with('success', "payment-mode '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment-mode: ' . $e->getMessage()
            ], 500);
        }
    }
}
