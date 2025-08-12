<?php

namespace App\Http\Controllers\Team\Lead;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ClientCoachingDemoDetails;
use App\Models\ClientDetails;
use App\Models\ClientEmploymentDetails;
use App\Models\ClientEnglishProficiencyTest;
use App\Models\ClientEnglishProficiencyTestScore;
use App\Models\ClientInvoice;
use App\Models\ClientLead;
use App\Models\ClientLeadRegistration;
use App\Models\ClientPassportDetails;
use App\Models\ClientPreviousRejection;
use App\Models\ClientRelativeForeignCountry;
use App\Models\ClientVisitedCountry;
use App\Models\EducationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;
use Carbon\Carbon;

class ClientProfileController extends Controller
{
    public function show($id)
    {
        $client = ClientDetails::with([
            'leads.assignedOwner',
            'leads.getPurpose',
            'leads.getForeignCountry',
            'leads.getBranch',
            'leads.getCoaching',
            'leads.getFollowUps.createdByUser',
            'leads.getFollowUps.updatedByUser',
            'leads.examData.exam_dataScore',
            'educationDetails.getEducationLevel',
            'educationDetails.getEducationStream',
            'educationDetails.getEducationBoard',
            'passportDetails',
            'getClientRelativeDetails',
            'visaRejectionDetails',
            'anyVisitedDetails',
            'employmentDetails',
            'getSource'
        ])->findOrFail($id);

        return view('team.client.show', [
            'client' => $client
        ]);
    }

    public function getCoaching($id)
    {
        $client = ClientDetails::with([
            'leads.assignedOwner',
            'leads.getPurpose',
            'leads.getForeignCountry',
            'leads.getBranch',
            'leads.getCoaching',
        ])->findOrFail($id);

        return view('team.client.coaching', [
            'client' => $client
        ]);
    }

    public function getRegistrations($id)
    {
        $client = ClientDetails::with([
            'leads.assignedOwner',
            'leads.getPurpose',
            'leads.getForeignCountry',
            'leads.getBranch',
            'leads.getCoaching',
        ])->findOrFail($id);

        return view('team.client.registration', [
            'client' => $client
        ]);
    }

    public function RegistrationsDestroy($id)
    {
        try {
            $clientLead = ClientLeadRegistration::findOrFail($id);
            $clientName = $clientLead->clientLeadDetails->first_name . ' ' . $clientLead->clientLeadDetails->last_name;
            $clientLead->delete();

            return redirect()->route('team.get.registrations',$clientLead->clientLeadDetails->id)
                ->with('success', "Client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client: ' . $e->getMessage());
        }
    }

    public function InvoiceDestroy($id)
    {
        try {
            $clientLead = ClientInvoice::findOrFail($id);
            $clientName = $clientLead->clientLeadDetails->first_name . ' ' . $clientLead->clientLeadDetails->last_name;
            $clientLead->delete();

            return redirect()->route('team.get.registrations',$clientLead->clientLeadDetails->id)
                ->with('success', "Client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client: ' . $e->getMessage());
        }
    }


    public function uploadAvatar(Request $request)
    {
        $client = ClientDetails::findOrFail($request->client_id);

        // Check and delete old profile image if exists
        if ($client->client_profile_photo && Storage::disk('public')->exists($client->client_profile_photo)) {
            Storage::disk('public')->delete($client->client_profile_photo);
        }

        // Prepare folder path
        $folderName = 'ClientProfile/' . Str::slug($client->first_name . '_' . $client->last_name . '_' . $client->id);

        // Store new file
        $file = $request->file('profile_image');
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($folderName, $fileName, 'public');

        // Save new file path to database
        $client->client_profile_photo = $filePath;
        $client->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile image uploaded successfully.',
            'file_path' => $filePath,
            'avatar_url' => Storage::url($filePath),
        ]);
    }

    public function removeAvatar()
    {
        $client = auth()->user(); // or find($id)
        if ($client->avatar) {
            Storage::disk('public')->delete(str_replace('storage/', '', $client->avatar));
            $client->avatar = null;
            $client->save();
        }

        return response()->json([
            'html' => view('partials.client_avatar', compact('client'))->render()
        ]);
    }

    public function EducationDetailsProfile(Request $request ,$client){

        $clientDetails = ClientDetails::find($client);
         $existingEducationIds = $clientDetails->educationDetails()->pluck('id')->toArray();
            $newEducationIds = collect($request->education)->pluck('id')->filter()->map(fn($id) => (int)$id)->toArray();

            // Delete entries not in the request
            $toDelete = array_diff($existingEducationIds, $newEducationIds);
            EducationDetails::where('client_id',$clientDetails->id)->whereIn('id', $toDelete)->delete();

            // Loop through request data
            foreach ($request->education as $edu) {
                if (!empty($edu['id'])) {
                    // Update existing
                    $education = EducationDetails::find($edu['id']);
                    if ($education) {
                        $education->update([
                            'education_level' => $edu['education_level'],
                            'education_board' => $edu['education_board'] ?? null,
                            'language' => $edu['language'],
                            'education_stream' => $edu['education_stream'] ?? null,
                            'passing_year' => $edu['passing_year'],
                            'result' => $edu['result'],
                            'no_of_backlog' => $edu['no_of_backlog'],
                            'institute' => $edu['institute'],
                        ]);
                    }
                } else {
                    // Create new
                    $clientDetails->educationDetails()->create([
                        'education_level' => $edu['education_level'],
                        'education_board' => $edu['education_board'] ?? null,
                        'language' => $edu['language'],
                        'education_stream' => $edu['education_stream'] ?? null,
                        'passing_year' => $edu['passing_year'],
                        'result' => $edu['result'],
                        'no_of_backlog' => $edu['no_of_backlog'],
                        'institute' => $edu['institute'],
                    ]);
                }
            }
        return redirect()->route('team.client.show', $client);
    }

    public function EnglishProficiencyTestsProfile(Request $request ,$client){

        $clientDetails = ClientDetails::find($client);
        $clientLead = $clientDetails?->leadLastest;

        if ($request->has('exam_data')) {
            // Remove duplicates and keep only numeric exam IDs
            $currentExamIds = array_unique(array_filter($request->exam_data, 'is_numeric'));

            // 1. Get existing exams for this client lead
            $existingTests = ClientEnglishProficiencyTest::where('client_id', $clientDetails->id)
                ->where('client_lead_id', $clientLead->id)
                ->get();

            $existingExamIds = $existingTests->pluck('exam_id')->toArray();

            // 2. Delete removed exams and their scores
            $toDelete = array_diff($existingExamIds, $currentExamIds);

            foreach ($toDelete as $examId) {
                $test = $existingTests->where('exam_id', $examId)->first();
                if ($test) {
                    ClientEnglishProficiencyTestScore::where('client_test_id', $test->id)->delete();
                    $test->delete();
                }
            }

            // 3. Create or update selected exams only if they are truly selected
            foreach ($currentExamIds as $examId) {
                // Extra safety: ensure it's in the submitted exam_data
                if (!in_array($examId, $request->exam_data)) {
                    continue;
                }

                $clientTest = ClientEnglishProficiencyTest::updateOrCreate(
                    [
                        'client_id' => $clientDetails->id,
                        'exam_id' => $examId,
                    ],[
                        'client_lead_id' => $clientLead->id,
                    ]
                );
                ;
                if ($clientTest && isset($request->exam_date[$examId])) {
                    $rawDate = $request->exam_date[$examId];
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
                    $clientTest->update([
                        'exam_date' => $formattedDate,
                    ]);
                }

                // 4. Handle test scores only if present
                if (!empty($request->exam_score[$examId])) {
                    $moduleScores = $request->exam_score[$examId];
                    $scoreIds = $moduleScores['id'] ?? [];

                    foreach ($moduleScores as $moduleId => $scoreData) {
                        if ($moduleId === 'id') continue;

                        $scoreValue = $scoreData['score'] ?? null;
                        $scoreId = $scoreIds[$moduleId] ?? null;

                        if ($scoreValue !== null) {
                            // Create or update score
                            ClientEnglishProficiencyTestScore::updateOrCreate(
                                ['id' => $scoreId],
                                [
                                    'client_test_id' => $clientTest->id,
                                    'exam_modual_id' => $moduleId,
                                    'score' => $scoreValue,
                                ]
                            );
                        } else {
                            // Delete score if no value
                            if ($scoreId) {
                                ClientEnglishProficiencyTestScore::where('id', $scoreId)->delete();
                            } else {
                                ClientEnglishProficiencyTest::where('id', $clientTest->id)
                                    ->delete();
                                ClientEnglishProficiencyTestScore::where('client_test_id', $clientTest->id)
                                    ->where('exam_modual_id', $moduleId)
                                    ->delete();
                            }
                        }
                    }
                }
            }
        } else {
            // No exams selected — delete all existing tests and scores
            $allTests = ClientEnglishProficiencyTest::where('client_id', $clientDetails->id)
                ->where('client_lead_id', $clientLead->id)
                ->get();

            foreach ($allTests as $test) {
                ClientEnglishProficiencyTestScore::where('client_test_id', $test->id)->delete();
                $test->delete();
            }
        }
        return redirect()->route('team.client.show', $client);
    }

    public function EmploymentDetailsProfile(Request $request ,$client){

        if ($request->has('is_employment') && $request->filled('employment')) {
            $existingIds = ClientEmploymentDetails::where('client_id', $client)->pluck('id')->toArray();
            $incomingIds = [];

            foreach ($request->employment as $employment) {
                // Track ID if it exists in the form
                $empId = $employment['id'] ?? null;

                // Format dates
                $startDate = $this->formatDate($employment['start_date'] ?? null);
                $endDate = isset($employment['is_working'][0]) && $employment['is_working'][0] == 1
                            ? null
                            : $this->formatDate($employment['end_date'] ?? null);

                $noOfYear = isset($employment['is_working'][0]) && $employment['is_working'][0] == 1
                            ? null
                            : ($employment['no_of_year'] ?? null);

                $isWorking = $employment['is_working'][0] ?? 0;

                if ($empId && in_array($empId, $existingIds)) {
                    // ✅ Update
                    ClientEmploymentDetails::where('id', $empId)->update([
                        'company_name'  => $employment['company_name'] ?? null,
                        'designation'   => $employment['designation'] ?? null,
                        'start_date'    => $startDate,
                        'end_date'      => $endDate,
                        'no_of_year'    => $noOfYear,
                        'is_working'    => $isWorking,
                    ]);

                    $incomingIds[] = $empId;
                } else {
                    // ✅ Create
                    $new = ClientEmploymentDetails::create([
                        'client_id'     => $client,
                        'company_name'  => $employment['company_name'] ?? null,
                        'designation'   => $employment['designation'] ?? null,
                        'start_date'    => $startDate,
                        'end_date'      => $endDate,
                        'no_of_year'    => $noOfYear,
                        'is_working'    => $isWorking,
                    ]);

                    $incomingIds[] = $new->id;
                }
            }

            // ✅ Delete records not in incoming data
            $toDelete = array_diff($existingIds, $incomingIds);

            if (!empty($toDelete)) {
                ClientEmploymentDetails::whereIn('id', $toDelete)->delete();
            }
        }else{
            if (ClientEmploymentDetails::where('client_id', $client)->exists()) {
                // ✅ Delete only if records exist
                ClientEmploymentDetails::where('client_id', $client)->delete();
            }
        }
        return redirect()->route('team.client.show', $client);
    }


    public function PassportProfile(Request $request, $client){
        $clientDetails = ClientDetails::find($client);
        if(isset($request->passport) && $request->passport != null){
                $existingPassport = ClientPassportDetails::find($request->passport_id);
                // Initialize null

                if(isset($request->passport) && $request->passport != null){
                    $storedFilePath = null;
                    if (
                        $request->hasFile('passport_copy') &&
                        $request->file('passport_copy')->isValid()
                    ) {
                        $clientName = Str::slug($clientDetails->first_name . ' ' . $clientDetails->last_name);
                        $clientId = $clientDetails->id;

                        $folderPath = "passport_copies/{$clientName}_{$clientId}";

                        $file = $request->file('passport_copy');
                        $fileName = time() . '_' . $file->getClientOriginalName();

                        // Delete old file if exists
                        if ($existingPassport && $existingPassport->passport_copy) {
                            Storage::disk('public')->delete($existingPassport->passport_copy);
                        }

                        // Store new file
                        $storedFilePath = $file->storeAs($folderPath, $fileName, 'public');
                    }
                    // Handle date conversion
                    $rawDatepas = $request->passport_expiry_date;
                    $formattedDatePass = null;

                    if (!empty($rawDatepas)) {
                        try {
                            $formattedDatePass = Carbon::createFromFormat('d/m/Y', $rawDatepas)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $formattedDatePass = null;
                        }
                    }

                    // Build data array (only non-empty fields)
                    $passportData = array_filter([
                        'passport_number'        => $request->passport_number,
                        'passport_expiry_date'   => $formattedDatePass,
                    ] + ($storedFilePath ? ['passport_copy' => $storedFilePath] : []), function ($value) {
                        return !is_null($value) && $value !== '';
                    });

                    // Only store if any passport data is available
                    if (!empty($passportData)) {
                        $passportData['client_id'] = $clientDetails->id;

                        ClientPassportDetails::updateOrCreate(
                            ['client_id' => $clientDetails->id],
                            $passportData
                        );
                    }

                }else{
                    $existingPassport->delete();
                }
            }else{
                if(isset($request->passport_id) && $request->passport_id != null){
                    ClientPassportDetails::destroy($request->passport_id);
                }
            }
        return redirect()->route('team.client.show', $client);
    }

    public function RejectionProfile(Request $request, $client){
        $clientDetails = ClientDetails::find($client);

        if ($request->has('is_visa_rejection') && $request->is_visa_rejection == 1) {
            if ($request->has('visa_rejection')) {
                $existingIds = []; // to track IDs from the request
                $requestRejections = $request->visa_rejection;

                foreach ($requestRejections as $rejection) {
                    if (!empty($rejection['id'])) {
                        // Existing record – update
                        $existingIds[] = $rejection['id'];
                        $existingRecord = ClientPreviousRejection::where('id', $rejection['id'])
                                            ->where('client_id', $clientDetails->id)
                                            ->first();

                        if ($existingRecord) {
                            $existingRecord->update([
                                'rejection_country'     => $rejection['rejection_country'],
                                'rejection_month_year'  => $rejection['rejection_month_year'],
                                'rejection_visa_type'   => $rejection['rejection_visa_type'],
                            ]);
                        }
                    } else {
                        // New record – create
                        $rejectionData = ClientPreviousRejection::create([
                            'client_id'             => $clientDetails->id,
                            'rejection_country'     => $rejection['rejection_country'],
                            'rejection_month_year'  => $rejection['rejection_month_year'],
                            'rejection_visa_type'   => $rejection['rejection_visa_type'],
                        ]);
                        $existingIds[] = $rejectionData['id'];
                    }
                }

                // Delete records not in the current request
                ClientPreviousRejection::where('client_id', $clientDetails->id)
                    ->whereNotIn('id', $existingIds)
                    ->delete();
            }
        }else{
            if ($request->has('visa_rejection')) {
                $idsToDelete = [];

                foreach ($request->visa_rejection as $rejection) {
                    if (!empty($rejection['id'])) {
                        $exists = ClientPreviousRejection::where('id', $rejection['id'])
                            ->where('client_id', $clientDetails->id)
                            ->exists();

                        if ($exists) {
                            $idsToDelete[] = $rejection['id'];
                        }
                    }
                }

                if (!empty($idsToDelete)) {
                    ClientPreviousRejection::where('client_id', $clientDetails->id)
                        ->whereIn('id', $idsToDelete)
                        ->delete();
                }
            }
        }
        return redirect()->route('team.client.show', $client);
    }
    public function RelativrProfile(Request $request, $client){
        $clientDetails = ClientDetails::find($client);

        if (isset($request->is_relative) && $request->is_relative == 1) {
            // Update if client_relation_id is provided, else create new
            ClientRelativeForeignCountry::updateOrCreate(
                ['id' => $request->client_relation_id], // find by ID for update
                [
                    'client_id'             => $clientDetails->id,
                    'relative_relationship' => $request->relative_relationship,
                    'relative_country'      => $request->relative_country,
                    'visa_type'             => $request->visa_type,
                ]
            );
        } else {
            // Delete if exists and id is provided
            if (!empty($request->client_relation_id)) {
                ClientRelativeForeignCountry::where('id', $request->client_relation_id)->delete();
            }
        }
        return redirect()->route('team.client.show', $client);
    }

    public function VisitedCountryDetails(Request $request, $client){
        $clientDetails = ClientDetails::find($client);

        if ($request->has('is_visited') && $request->is_visited == 1) {
            $visitedVisas = $request->input('visited_visa', []);

            // 1. Get all existing record IDs for this client from DB
            $existingIds = ClientVisitedCountry::where('client_id', $clientDetails->id)->pluck('id')->toArray();

            // 2. Prepare list of request IDs for comparison
            $requestIds = collect($visitedVisas)->pluck('id')->filter()->map(fn($id) => (int)$id)->toArray();

            // 3. DELETE: Find IDs in DB but not in request
            $idsToDelete = array_diff($existingIds, $requestIds);
            ClientVisitedCountry::whereIn('id', $idsToDelete)->delete();

            // 4. LOOP over request for Update or Create
            foreach ($visitedVisas as $visa) {
                $data = [
                    'client_id' => $clientDetails->id,
                    'visited_country' => $visa['visited_country'],
                    'visited_visa_type' => $visa['visited_visa_type'],
                    'start_date' => Carbon::createFromFormat('d/m/Y', $visa['start_date'])->format('Y-m-d'),
                    'end_date' => Carbon::createFromFormat('d/m/Y', $visa['end_date'])->format('Y-m-d'),
                ];

                if (!empty($visa['id']) && in_array((int)$visa['id'], $existingIds)) {
                    // Update
                    ClientVisitedCountry::where('id', $visa['id'])->update($data);
                } else {
                    // Create
                    ClientVisitedCountry::create($data);
                }
            }
        }else{
            if ($request->has('visited_visa')) {
                $idsToDeleteVisited = [];

                foreach ($request->visited_visa as $visited) {
                    if (!empty($visited['id'])) {
                        $exists = ClientVisitedCountry::where('id', $visited['id'])
                            ->where('client_id', $clientDetails->id)
                            ->exists();

                        if ($exists) {
                            $idsToDeleteVisited[] = $visited['id'];
                        }
                    }
                }

                if (!empty($idsToDeleteVisited)) {
                    ClientVisitedCountry::where('client_id', $clientDetails->id)
                        ->whereIn('id', $idsToDeleteVisited)
                        ->delete();
                }
            }
        }
        return redirect()->route('team.client.show', $client);
    }

    public function DemoDetails(Request $request, $client){

        $data = [
            'client_id'        => $client,
            'client_lead_id'   => $request->input('client_lead_id'),
            'coaching_id'      => $request->input('coaching'),
            'batch_id'         => $request->input('batch'),
            'demo_date'        => Helpers::parseToYmd($request->input('demo_date')),
            'assign_owner'     => $request->input('assign_owner'),
            'status'           => $request->input('status') ?? 0,
        ];

        if ($request->filled('demo_id')) {
            ClientCoachingDemoDetails::find($request->demo_id)->update($data);
        } else {
            $data['added_by'] = auth()->user()->id;
            ClientCoachingDemoDetails::create($data);
        }


        return redirect()->route('team.client.show', $client);

    }

    public function RegisterDetails(Request $request, $client){
        $data = [
            'client_id'        => $client,
            'client_lead_id'   => $request->input('client_lead_id'),
            'reg_date'        => Helpers::parseToYmd($request->input('reg_date')),
            'reg_owner'     => auth()->user()->id,
            'assign_owner'     => $request->input('assign_owner'),
            'purpose'     => $request->input('purpose'),
            'country'     => $request->input('country'),
            'coaching'     => $request->input('coaching'),
        ];

        if ($request->filled('client_reg_id')) {
            ClientLeadRegistration::find($request->client_reg_id)->update($data);
        } else {
            $data['added_by'] = auth()->user()->id;
            ClientLeadRegistration::create($data);
        }

        $clientLead = ClientLead::find($request->input('client_lead_id'));
        $clientLead->status = '3';
        $clientLead->sub_status = $request->input('sub_status');
        $clientLead->save();

        return redirect()->route('team.client.show', $client);

    }

    public function InvoiceDetails(Request $request, $client){
        $data = [
            'client_id'        => $client,
            'client_lead_id'   => $request->input('client_lead_id'),
            'invoice_date'        => Helpers::parseToYmd($request->input('invoice_date')),
            'service_id'     => $request->input('service_id'),
            'total_amount'     => $request->input('total_amount'),
            'discount'     => $request->input('discount') ?? '0',
            'payable_amount'     => $request->input('payable_amount'),
            'billing_company_id'     => $request->input('billing_company_id'),
        ];

        if ($request->filled('client_invoice_id')) {
            ClientInvoice::find($request->client_invoice_id)->update($data);
        } else {
            $data['added_by'] = auth()->user()->id;
            ClientInvoice::create($data);
        }
        return redirect()->route('team.client.show', $client);
    }
    private function formatDate($date)
    {
        if (!$date) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
