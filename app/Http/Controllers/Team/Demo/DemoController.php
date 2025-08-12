<?php

namespace App\Http\Controllers\Team\Demo;

use App\DataTables\Team\Demo\AttendedDemoDataTable;
use App\DataTables\Team\Demo\CancelledDemoDataTable;
use App\DataTables\Team\Demo\PendingDemoDataTable;
use App\DataTables\Team\FollowUp\CompleteFollowUpDataTable;
use App\DataTables\Team\FollowUp\UpcomingFollowUpDataTable;
use App\Exports\DemoDataExport;
use App\Exports\FollowUpDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ClientCoachingDemoDetails;
use App\Models\ClientDetails;
use App\Models\ClientLead;
use App\Models\Coaching;
use App\Models\LeadFollowUp;
use App\Models\User;
use App\Repositories\Team\DemoRepository;
use App\Repositories\Team\FollowRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DemoController extends Controller
{

    public function pending(PendingDemoDataTable $PendingDemoDataTable)
    {
        $DemoName = "Pending";
        return $PendingDemoDataTable->render('team.demo.index',compact('DemoName'));
    }

    public function attended(AttendedDemoDataTable $AttendedDemoDataTable)
    {
        $DemoName = "Attended";
        return $AttendedDemoDataTable->render('team.demo.index',compact('DemoName'));
    }
    public function cancelled(CancelledDemoDataTable $CancelledDemoDataTable)
    {
        $DemoName = "Cancelled";
        return $CancelledDemoDataTable->render('team.demo.index',compact('DemoName'));
    }

    public function edit($id)
    {
        $demoData = ClientCoachingDemoDetails::findOrFail($id);
        $clientData = ClientDetails::find($demoData->client_id);
        $assignOwner = User::where('branch_id', $clientData->branch)
            ->active()
            ->permission('demo:create')
            ->get();
        $coaching = Coaching::active()->get();

         return view('team.demo.edit', compact('demoData','coaching','assignOwner'));
    }

    public function update(Request $request, $id)
    {
        try {

            $demo = ClientCoachingDemoDetails::find($id);
            $demo->update([
                'client_lead_id' => $request->client_lead_id,
                'coaching_id'    => $request->coaching,
                'batch_id'       => $request->batch,
                'demo_date'      => Helpers::parseToYmd($request->demo_date),
                'assign_owner'   => $request->assign_owner,
                'status'         => $request->status,
            ]);

            return redirect()->route('team.client.show', $demo->client_id)
                ->with('success', "Client Demo updated successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error updating Client Demo: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $clientLeadDemo = ClientCoachingDemoDetails::findOrFail($id);
            $clientName = $clientLeadDemo->clientLeadDetails->first_name . ' ' . $clientLeadDemo->clientLeadDetails->last_name;
            $demo = $clientLeadDemo?->getDemoCoaching?->name .'-'.$clientLeadDemo?->getDemoBatch?->name;
            $clientLeadDemo->delete();

            return redirect()->route('team.demo.pending')
                ->with('success', "The demo '{$demo}' for client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client: ' . $e->getMessage());
        }
    }

    public function exportDemo(Request $request,DemoRepository $demoRepository){

        $export = new DemoDataExport($demoRepository,$request->all());
        return Excel::download($export, 'demo.xlsx');

    }
}
