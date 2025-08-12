<?php

namespace App\Http\Controllers\Team\Application;

use App\DataTables\Team\Application\ApplicationDataTable;
use App\DataTables\Team\Coaching\CompletedCoachingDataTable;
use App\DataTables\Team\Coaching\DropCoachingDataTable;
use App\DataTables\Team\Coaching\PendingCoachingDataTable;
use App\DataTables\Team\Coaching\RunningCoachingDataTable;
use App\Exports\CoachingDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ClientCoaching;
use App\Models\ClientCoachingMaterial;
use App\Models\ClientLeadRegistration;
use App\Models\Coaching;
use App\Models\CoachingLength;
use App\Models\CoachingMaterial;
use App\Models\User;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\RegistrationRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ApplicationController extends Controller
{
    public function ApplicationPending(ApplicationDataTable $ApplicationDataTable)
    {
        $application = "Pending";
        return $ApplicationDataTable->render('team.application.index',compact('application'));
    }

    public function CoachingRunning(RunningCoachingDataTable $RunningCoachingDataTable)
    {
        $coaching_type = "Running";
        return $RunningCoachingDataTable->render('team.coaching.index',compact('coaching_type'));
    }

    // Application Create
    public function edit($id)
    {
        return view('team.application.create', compact('registerData',));
    }
    // Invoice Create
    public function update(Request $request, $id)
    {
        $registerData = ClientLeadRegistration::findOrFail($id);
        try {
            $data = [
                'client_id' => $registerData->clientLead->client_id,
                'client_lead_id' => $registerData->clientLead->id,
                'client_lead_reg_id' => $id,
                'branch_id' => $registerData->clientLead->branch,
                'coaching_id'     => $request->input('coaching_id'),
                'batch_id'     => $request->input('batch_id'),
                'joining_date'     => Helpers::parseToYmd($request->input('joining_date')),
                'faculty'     => $request->input('faculty'),
                'coaching_length'     => $request->input('coaching_length'),
            ];

                $data['added_by'] = auth()->user()->id;
                $clientCoaching = ClientCoaching::create($data);

                if (request()->has('is_material') && request()->filled('materials')) {

                    foreach ($request->materials as $materialId) {
                        ClientCoachingMaterial::create([
                            'client_coaching_id' => $clientCoaching->id,
                            'material_id' => $materialId,
                            'is_provided' => $request->is_material,
                            'added_by' => auth()->user()->id
                        ]);
                    }

                }


            return redirect()->route('team.coaching.running')
                ->with('success', "Client Coaching Transfer successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error Create Client Coaching Transfer: ' . $e->getMessage());
        }
    }

    public function editCoaching($id)
    {
        $coachingData = ClientCoaching::findOrFail($id);
        $coaching = Coaching::active()->get();
        $coachingLength = CoachingLength::pluck('name', 'name')->toArray();
        $faculty = User::where('branch_id',$coachingData->branch_id)->active()->get();

        return view('team.coaching.edit', compact('coachingData','coachingLength','coaching','faculty'));
    }
    // Invoice Create
    public function updateCoaching(Request $request, $id)
    {
        $coachingData = ClientCoaching::findOrFail($id);
        try {
            $data = [
                'client_id' => $coachingData->client_id,
                'client_lead_id' => $coachingData->client_lead_id,
                'client_lead_reg_id' => $coachingData->client_lead_reg_id,
                'branch_id' => $coachingData->branch_id,
                'coaching_id'     => $request->input('coaching_id'),
                'batch_id'     => $request->input('batch_id'),
                'joining_date'     => Helpers::parseToYmd($request->input('joining_date')),
                'faculty'     => $request->input('faculty'),
                'coaching_length'     => $request->input('coaching_length'),
                'is_complete_coaching'     => $request->input('is_complete_coaching') ?? 0,
                'is_drop_coaching'     => $request->input('is_drop_coaching') ?? 0,
            ];

            $coachingData->update($data);


            if (request()->has('is_material') && request()->filled('materials')) {
                $materialIds = $request->materials;
                $clientCoachingId = $coachingData->id;

                // Step 1: Get existing material_ids from DB
                $existingMaterials = ClientCoachingMaterial::where('client_coaching_id', $clientCoachingId)
                    ->pluck('material_id')
                    ->toArray();

                foreach ($materialIds as $materialId) {
                    // Try to find existing record
                    $material = ClientCoachingMaterial::where('client_coaching_id', $clientCoachingId)
                        ->where('material_id', $materialId)
                        ->first();

                    if ($material) {
                        // If exists, only update is_provided
                        $material->update([
                            'is_provided' => $request->is_material,
                            // 'added_by' is not updated here
                        ]);
                    } else {
                        // If not exists, create new with added_by
                        ClientCoachingMaterial::create([
                            'client_coaching_id' => $clientCoachingId,
                            'material_id' => $materialId,
                            'is_provided' => $request->is_material,
                            'added_by' => auth()->user()->id
                        ]);
                    }
                }

                // Step 3: Delete materials not present in incoming array
                $toDelete = array_diff($existingMaterials, $materialIds);

                if (!empty($toDelete)) {
                    ClientCoachingMaterial::where('client_coaching_id', $clientCoachingId)
                        ->whereIn('material_id', $toDelete)
                        ->delete();
                }
            }

            return redirect()->route('team.coaching.running')
                ->with('success', "Client Coaching Update successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error Create Client Coaching Update: ' . $e->getMessage());
        }
    }

    public function destroyCoaching($id){
        try {
            $clientLead = ClientCoaching::findOrFail($id);
            $clientName = $clientLead->clientLeadDetails->first_name . ' ' . $clientLead->clientLeadDetails->last_name;
            $clientLead->delete();

            return redirect()->route('team.coaching.pending')
                ->with('success', "Coaching for client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client: ' . $e->getMessage());
        }
    }

    /**
     * Get all follow-ups for a specific lead
     */

    public function exportCoaching(Request $request,CoachingRepository $CoachingRepository,RegistrationRepository $registrationRepository){

        $export = new CoachingDataExport($CoachingRepository,$registrationRepository,$request->all());
        return Excel::download($export, 'coaching.xlsx');

    }
}
