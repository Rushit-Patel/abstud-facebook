<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\Coaching;
use App\Models\EnglishProficiencyTest;
use Illuminate\Http\Request;
use App\DataTables\Team\Setting\EnglishProficiencyTestDataTable;

class EnglishProficiencyTestController extends Controller
{
    /**
     * Display a listing of EnglishProficiencyTest
     */
    public function index(EnglishProficiencyTestDataTable $EnglishProficiencyTestDataTable)
    {
        return $EnglishProficiencyTestDataTable->render('team.settings.english-proficiency-test.index');
    }

    /**
     * Show the form for creating a new EnglishProficiencyTest
     */
    public function create()
    {
        $coaching = Coaching::active()->get();
        return view('team.settings.english-proficiency-test.create', compact('coaching'));
    }

    /**
     * Store a newly created EnglishProficiencyTest
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:english_proficiency_tests,name',
                'status' => 'nullable|boolean',
                'result_days' => 'nullable',
                'modules' => 'required|array',
                'modules.*.name' => 'required|string',
                'modules.*.minimum_score' => 'required|numeric',
                'modules.*.maximum_score' => 'required|numeric',
                'modules.*.range_score' => 'required|numeric',
            ], [
                'name.required' => 'English Proficiency Test name is required.',
                'name.unique' => 'This English Proficiency Test already exists.',
            ]);

            // Create the main English Proficiency Test record
            $test = EnglishProficiencyTest::create([
                'name' => $validated['name'],
                'result_days' => $validated['result_days'],
                'priority' => $request->priority,
                'coaching_id' => $request->coaching_id,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            // Create each related module
            foreach ($validated['modules'] as $module) {
                $test->moduals()->create([
                    'name' => $module['name'],
                    'minimum_score' => $module['minimum_score'],
                    'maximum_score' => $module['maximum_score'],
                    'range_score' => $module['range_score'],
                ]);
            }

            return redirect()->route('team.settings.english-proficiency-test.index')
                ->with('success', "English Proficiency Test '{$validated['name']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating EnglishProficiencyTest: ' . $e->getMessage());
        }
    }



    /**
     * Display the specified EnglishProficiencyTest
     */
    public function show(EnglishProficiencyTest $englishProficiencyTest)
    {
        return view('team.settings.english-proficiency-test.show', compact('englishProficiencyTest'));
    }

    /**
     * Show the form for editing the specified EnglishProficiencyTest
     */
    public function edit(EnglishProficiencyTest $englishProficiencyTest)
    {
        $coaching = Coaching::active()->get();
        return view('team.settings.english-proficiency-test.edit', compact('englishProficiencyTest','coaching'));
    }

    /**
     * Update the specified EnglishProficiencyTest
     */
    public function update(Request $request, EnglishProficiencyTest $englishProficiencyTest)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:english_proficiency_tests,name,' . $englishProficiencyTest->id,
                'status' => 'nullable|boolean',
                'result_days' => 'nullable',
                'modules' => 'required|array',
                'modules.*.name' => 'required|string',
                'modules.*.minimum_score' => 'required|numeric',
                'modules.*.maximum_score' => 'required|numeric',
                'modules.*.range_score' => 'required|numeric',
                'modules.*.id' => 'numeric',
            ]);

            // 1. Update the test
            $englishProficiencyTest->update([
                'name' => $validated['name'],
                'result_days' => $validated['result_days'],
                'priority' => $request->priority,
                'coaching_id' => $request->coaching_id,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            // 2. Get existing IDs from DB
            $existingModules = $englishProficiencyTest->moduals()->get();
            $existingIds = $existingModules->pluck('id')->map(fn($id) => (int)$id)->toArray();

            // 3. Collect submitted IDs while updating/creating
            $submittedModules = collect($validated['modules']);
            $processedIds = []; // IDs that were updated or are still valid

            foreach ($submittedModules as $module) {
                $moduleId = isset($module['id']) ? (int)$module['id'] : null;
                if ($moduleId && in_array($moduleId, $existingIds)) {
                    // Update
                    $englishProficiencyTest->moduals()->where('id', $moduleId)->update([
                        'name' => $module['name'],
                        'minimum_score' => $module['minimum_score'],
                        'maximum_score' => $module['maximum_score'],
                        'range_score' => $module['range_score'],
                    ]);
                    $processedIds[] = $moduleId;
                } elseif (!$moduleId) {
                    // Create new
                    $new = $englishProficiencyTest->moduals()->create([
                        'name' => $module['name'],
                        'minimum_score' => $module['minimum_score'],
                        'maximum_score' => $module['maximum_score'],
                        'range_score' => $module['range_score'],
                    ]);
                    $processedIds[] = $new->id;
                }
            }

            // 4. Delete modules that were not updated or created (i.e., removed from form)
            $idsToDelete = array_diff($existingIds, $processedIds);
            if (!empty($idsToDelete)) {
                $englishProficiencyTest->moduals()->where('english_proficiency_tests_id',$englishProficiencyTest->id)->whereIn('id', $idsToDelete)->delete();
            }

            return redirect()->route('team.settings.english-proficiency-test.index')
                ->with('success', "English Proficiency Test '{$validated['name']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating English Proficiency Test: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified EnglishProficiencyTest
     */
    public function destroy(EnglishProficiencyTest $EnglishProficiencyTest)
    {
        try {
            $name = $EnglishProficiencyTest->name;
            $EnglishProficiencyTest->delete();

            return redirect()->route('team.settings.english-proficiency-test.index')
                ->with('success', "EnglishProficiencyTest '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting EnglishProficiencyTest: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle EnglishProficiencyTest status
     */
    public function toggleStatus(EnglishProficiencyTest $EnglishProficiencyTest)
    {
        try {
            $EnglishProficiencyTest->update(['status' => !$EnglishProficiencyTest->status]);

            $status = $EnglishProficiencyTest->status ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "English Proficiency Test '{$EnglishProficiencyTest->name}' has been {$status} successfully.",
                'new_status' => $EnglishProficiencyTest->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating EnglishProficiencyTest status: ' . $e->getMessage()
            ], 500);
        }
    }
}
