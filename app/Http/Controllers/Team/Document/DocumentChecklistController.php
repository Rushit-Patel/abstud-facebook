<?php

namespace App\Http\Controllers\Team\Document;

use App\Exports\DemoDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ClientCoachingDemoDetails;
use App\Models\ClientDetails;
use App\Models\ClientDocumentCheckList;
use App\Models\ClientDocumentUpload;
use App\Models\Coaching;
use App\Models\DocumentCheckList;
use App\Models\User;
use App\Repositories\Team\DemoRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


class DocumentChecklistController extends Controller
{
    public function DocumentCheckList($id){

        $client = ClientDetails::with([
            'leads.assignedOwner',
            'leads.getPurpose',
            'leads.getForeignCountry',
            'leads.getBranch',
            'leads.getCoaching',
        ])->findOrFail($id);

        return view('team.client.document-checklist.index', [
            'client' => $client
        ]);

    }

    public function DocumentCheckListCreate($id){
        $documentlist = DocumentCheckList::active()->get();
        $client = ClientDetails::find($id);
        $existingDocs = $client->documentChecklists->keyBy('document_check_list_id');

        return view('team.client.document-checklist.create', compact('documentlist','client','existingDocs'));
    }

    public function DocumentCheckListStore(Request $request, $id){

        $inputDocs = collect($request->studentDocument);

        // DB me jo docs already hai
        $existingDocs = ClientDocumentCheckList::where('client_id', $id)
            ->get()
            ->keyBy('document_check_list_id');

        // ---- 1. CREATE or UPDATE ----
        foreach ($inputDocs as $doc) {
            $docId = $doc['document_check_list_id'];

            if ($existingDocs->has($docId)) {
                // UPDATE case
                $existingDocs[$docId]->update([
                    'document_type' => $doc['document_type'],
                    'notes'         => $doc['notes'],
                ]);
            } else {
                // CREATE case
                ClientDocumentCheckList::create([
                    'client_id'              => $id,
                    'client_lead_id'         => $request->client_lead_id,
                    'document_check_list_id' => $docId,
                    'document_type'          => $doc['document_type'],
                    'status'                 => 'request',
                    'notes'                  => $doc['notes'],
                    'added_by'               => auth()->user()->id,
                ]);
            }
        }

        // ---- 2. DELETE ----
        $inputDocIds = $inputDocs->pluck('document_check_list_id')->toArray();
        $dbDocIds = $existingDocs->keys()->toArray();

        $toDelete = array_diff($dbDocIds, $inputDocIds);

        if (!empty($toDelete)) {
            ClientDocumentCheckList::where('client_id', $id)
                ->whereIn('document_check_list_id', $toDelete)
                ->delete();
        }

        return redirect()->route('team.document', $id)
                ->with('success', "Client Document Check list selected successfully.");

    }


    public function documentUploadeStore(Request $request, $clientId)
    {
        $request->validate([
            'client_document_check_list_id' => 'required|integer',
            'status' => 'required|string|in:request,uploaded,re-uploaded',
            'master_check_list_id' => 'required'
        ]);

        $client = ClientDetails::findOrFail($clientId);
        $documentChecklist_Name = DocumentCheckList::findOrFail($request->master_check_list_id);

        $clientName = str_replace(' ', '_', $client->first_name . '_' . $client->last_name);
        $folderPath = "client_document/{$clientName}_{$client->id}";
        $status = 'uploaded';

        $files = collect($request->file('file'))->flatten(1)->filter();

    if ($files->isNotEmpty()) {
        foreach ($files as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = "{$folderPath}/{$fileName}";

            $file->storeAs($folderPath, $fileName, 'public');

            ClientDocumentUpload::create([
                'client_document_check_list_id' => $request->client_document_check_list_id,
                'document_name' => $documentChecklist_Name->name,
                'document_path' => $filePath,
                'status' => $status,
            ]);
        }

        ClientDocumentChecklist::findOrFail($request->client_document_check_list_id)
            ->update(['status' => $status]);

        return response()->json(['success' => true, 'message' => 'Documents uploaded successfully!']);
    }

        return response()->json(['success' => false, 'message' => 'No files uploaded.'], 400);
    }

    public function documentStatusUpdate($clientId, Request $request){

        if ($request->has('studentDocument')) {
            foreach ($request->studentDocument as $docId => $data) {
                if (isset($data['status'])) {
                    ClientDocumentCheckList::where('client_id', $clientId)
                        ->where('id', $docId)
                        ->update(['status' => $data['status']]);
                }
            }
        }

        return redirect()->route('team.document', $clientId)
                ->with('success', "Client Document Check list Status Change successfully.");

    }
}
