<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coaching;
use App\Models\CoachingMaterial;
use App\Models\CoachingMaterialStock;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\CoachingMaterialDataTable;

class CoachingMaterialController extends Controller
{
    /**
     * Display a listing of CoachingMaterial
     */
    public function index(CoachingMaterialDataTable $CoachingMaterialDataTable)
    {
        return $CoachingMaterialDataTable->render('team.settings.coaching-material.index');
    }

    /**
     * Show the form for creating a new CoachingMaterial
     */
    public function create()
    {
        $coaching = Coaching::active()->get();
        return view('team.settings.coaching-material.create',compact('coaching'));
    }

    /**
     * Store a newly created CoachingMaterial
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'boolean',
            ], [
                'name.required' => 'coaching-material name is required.',
                'name.unique' => 'This coaching-material already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['coaching'] = $request->coaching;

            CoachingMaterial::create($validated);

            return redirect()->route('team.settings.coaching-material.index')
                ->with('success', "coaching-material '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating coaching-material: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified CoachingMaterial
     */
    public function show(CoachingMaterial $coachingMaterial)
    {
        return view('team.settings.coaching-material.show', compact('coachingMaterial'));
    }

    /**
     * Show the form for editing the specified CoachingMaterial
     */
    public function edit(CoachingMaterial $coachingMaterial)
    {
        $coaching = Coaching::active()->get();
        return view('team.settings.coaching-material.edit', compact('coachingMaterial','coaching'));
    }

    /**
     * Update the specified CoachingMaterial
     */
    public function update(Request $request, CoachingMaterial $coachingMaterial)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:coaching_materials,name,' . $coachingMaterial->id,
                'status' => 'boolean',
            ], [
                'name.required' => 'coaching-material name is required.',
                'name.unique' => 'This coaching-material already exists.',
            ]);

            // Set default values
            $validated['status'] = $request->has('status');
            $validated['coaching'] = $request->coaching;

            $coachingMaterial->update($validated);

            return redirect()->route('team.settings.coaching-material.index')
                ->with('success', "coaching-material '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating coaching-material: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified CoachingMaterial
     */
    public function destroy(CoachingMaterial $coachingMaterial)
    {
        try {
            $name = $coachingMaterial->name;
            $coachingMaterial->delete();

            return redirect()->route('team.settings.coaching-material.index')
                ->with('success', "coaching-material '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting coaching-material: ' . $e->getMessage()
            ], 500);
        }
    }

    public function CoachingMaterialStock($id ,Request $request){

        $branch = Branch::active()->get();
        return view('team.settings.coaching-material.stock.create',compact('branch','id'));
    }

    public function CoachingMaterialStore($id, Request $request)
    {
        $data = [
            'branch_id' => $request->branch,
            'stock_date' => Helpers::parseToYmd($request->stock_date),
            'coaching_material_id' => $id,
            'stock' => $request->stock,
            'remarks' => $request->remarks,
            'added_by' => auth()->user()->id
        ];

        CoachingMaterialStock::create($data);

        return redirect()->route('team.settings.coaching-material.index')
            ->with('success', "Coaching-material stock '{$request->stock}' has been updated successfully.");
    }

    public function CoachingMaterialStockList($id){

        $getStock = CoachingMaterialStock::where('coaching_material_id',$id)->get();
        return view('team.settings.coaching-material.stock.list',compact('getStock','id'));
    }

    public function CoachingMaterialStockEdit($material,$id){

        $editStock = CoachingMaterialStock::find($id);

        $branch = Branch::active()->get();
        return view('team.settings.coaching-material.stock.edit',compact('branch','material','editStock',));
    }

    public function CoachingMaterialStockUpdate($material,$id,Request $request){

        $data = [
            'branch_id' => $request->branch,
            'stock_date' => Helpers::parseToYmd($request->stock_date),
            'coaching_material_id' => $material,
            'stock' => $request->stock,
            'remarks' => $request->remarks
        ];

        CoachingMaterialStock::find($id)->update($data);

         return redirect()->route('team.settings.coaching-material.index')
            ->with('success', "Coaching-material stock '{$request->stock}' has been updated successfully.");
    }

    public function CoachingMaterialStockDestroy($id,Request $request){
       $materialStock =  CoachingMaterialStock::find($id);

        try {
            $materialStock->delete();

            return redirect()->route('team.settings.coaching-material.index')
                ->with('success', "coaching-material Stock'{$materialStock->stock}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting coaching-material: ' . $e->getMessage()
            ], 500);
        }
    }

}
