<?php

namespace App\Http\Controllers\Team\FollowUp;

use App\DataTables\Team\FollowUp\CompleteFollowUpDataTable;
use App\DataTables\Team\FollowUp\UpcomingFollowUpDataTable;
use App\DataTables\Team\Lead\LeadDataTable;
use App\DataTables\Team\FollowUp\PendingFollowUpDataTable;
use App\Exports\FollowUpDataExport;
use App\Http\Controllers\Controller;
use App\Models\ClientLead;
use App\Models\LeadFollowUp;
use App\Repositories\Team\FollowRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FollowUpController extends Controller
{
    public function index(LeadDataTable $LeadDataTable)
    {
        return $LeadDataTable->render('team.lead.index');
    }

    public function pending(PendingFollowUpDataTable $PendingFollowUpDataTable)
    {
        $followUpName = "Pending";
        return $PendingFollowUpDataTable->render('team.follow-up.index',compact('followUpName'));
    }

    public function upcoming(UpcomingFollowUpDataTable $UpcomingFollowUpDataTable)
    {
        $followUpName = "Upcoming";
        return $UpcomingFollowUpDataTable->render('team.follow-up.index',compact('followUpName'));
    }
    public function complete(CompleteFollowUpDataTable $CompleteFollowUpDataTable)
    {
        $followUpName = "Completed";
        return $CompleteFollowUpDataTable->render('team.follow-up.index',compact('followUpName'));
    }

    /**
     * Show the form for creating a new Source
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created Source
     */
    public function store(Request $request)
    {
        $client_lead_id = $request->query('client_lead_id');
        try {
            $validated = $request->validate([
                'followup_date' => 'required',
                'remarks' => 'required',
            ], [
                'followup_date.required' => 'follow-up date is required.',
                'remarks.unique' => 'follow-up remarks is required.'
            ]);
            $validated['followup_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->followup_date)->format('Y-m-d');
            $validated['client_lead_id'] = $client_lead_id;
            $validated['status'] = '0';
            $validated['created_by'] = auth()->user()->id;
            $PersonalDetails = LeadFollowUp::create($validated);

            return redirect()->route('team.lead.index')
                ->with('success', "Client Follow-up '{$validated['remarks']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Client Follow-up: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $followUp = LeadFollowUp::findOrFail($id);

         return response()->json([
            'client_lead_id' => $followUp->client_lead_id,
            'remarks' => $followUp->remarks,
            'followup_date' => $followUp->followup_date,
            'status' => $followUp->status,
            'communication' => $followUp->communication,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {

            if ($request->has('check') && $request->check == '1') {
                $followUp = LeadFollowUp::findOrFail($request->follow_id);
                $followUp->status = '1';
                $followUp->communication = $request->communication;
                $followUp->updated_by = auth()->user()->id;
                $followUp->save();

                LeadFollowUp::create([
                    'followup_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->next_follow_up_date)->format('Y-m-d'),
                    'remarks' => $request->next_remarks,
                    'created_by' => auth()->user()->id,
                    'status' => '0',
                    'client_lead_id' => $request->client_lead_id,
                ]);
            } else {
                $followUp = LeadFollowUp::findOrFail($request->follow_id);
                $followUp->status = '1';
                $followUp->communication = $request->communication;
                $followUp->updated_by = auth()->user()->id;
                $followUp->save();
            }

            return redirect()->route('team.lead.index')
                ->with('success', "Client Follow-up updated successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error updating Client Follow-up: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $clientLead = ClientLead::findOrFail($id);
            $clientName = $clientLead->client->first_name . ' ' . $clientLead->client->last_name;
            $clientLead->delete();

            return redirect()->route('team.lead.index')
                ->with('success', "Client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client: ' . $e->getMessage());
        }
    }

    /**
     * Get all follow-ups for a specific lead
     */
    public function getAllFollowUps($leadId)
    {
        try {
            $clientLead = ClientLead::with(['client', 'getBranch'])->findOrFail($leadId);
            $followUps = LeadFollowUp::with(['createdByUser', 'updatedByUser'])
                ->where('client_lead_id', $leadId)
                ->orderBy('created_at', 'desc') // Changed to created_at for timeline order
                ->get();

            // Add additional attributes for the timeline component
            $followUps->each(function ($followUp) {
                $followUp->created_by_name = $followUp->createdByUser?->name ?? 'Unknown';
                $followUp->updated_by_name = $followUp->updatedByUser?->name ?? null;
            });

            $client = [
                'name' => $clientLead->client->first_name . ' ' . $clientLead->client->last_name,
                'mobile' => $clientLead->client->country_code . $clientLead->client->mobile_no,
                'email' => $clientLead->client->email_id,
                'branch' => $clientLead->getBranch->branch_name,
            ];

            // Render the timeline component
            $timelineHtml = view('components.team.lead.followup-timeline', compact('followUps', 'client'))->render();

            return response()->json([
                'success' => true,
                'client' => $client,
                'followUps' => $followUps,
                'timelineHtml' => $timelineHtml
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching follow-ups: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportFollow(Request $request,FollowRepository $leadRepository){

        $export = new FollowUpDataExport($leadRepository,$request->all());
        return Excel::download($export, 'follow-up.xlsx');

    }
}
