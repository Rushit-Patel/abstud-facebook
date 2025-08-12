<?php

namespace App\Http\Controllers\Team\Lead;

use App\DataTables\Team\Lead\LeadDataTable;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Models\ClientEmploymentDetails;
use App\Models\ClientEnglishProficiencyTest;
use App\Models\ClientEnglishProficiencyTestScore;
use App\Models\ClientDetails;
use App\Models\ClientLead;
use App\Models\ClientPassportDetails;
use App\Models\ClientPreviousRejection;
use App\Models\ClientRelativeForeignCountry;
use App\Models\ClientVisitedCountry;
use App\Models\Coaching;
use App\Models\Country;
use App\Models\EducationBoard;
use App\Models\EducationDetails;
use App\Models\EducationLevel;
use App\Models\EducationStream;
use App\Models\EnglishProficiencyTest;
use App\Models\ForeignCountry;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use App\Models\LeadTag;
use App\Models\LeadType;
use App\Models\MaritalStatus;
use App\Models\OtherVisaType;
use App\Models\Purpose;
use App\Models\Source;
use App\Models\State;
use App\Models\TypeOfRelative;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LeadWelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LeadControllerold extends Controller
{
    public function index(LeadDataTable $LeadDataTable)
    {
        $userCounselors = User::active()->get();

        return $LeadDataTable->render('team.lead.index',[
            'userCounselors' => $userCounselors
        ]);
    }

    /**
     * Show the form for creating a new Source
     */
    public function create()
    {
        $countries = Country::active()->get();
        $sources = Source::active()->get();
        $leadTypes = LeadType::active()->get();
        $purposes = Purpose::active()->get();
        $country = ForeignCountry::active()->get();
        $coaching = Coaching::active()->get();
        $branch = Branch::active()->orderBy('branch_name')->get();
        $assign_owner = User::active()->get();
        $lead_status = LeadStatus::active()->get();
        $lead_sub_status = LeadSubStatus::active()->get();
        $educationLevel = EducationLevel::active()->get();
        $educationBoard = EducationBoard::active()->get();
        $educationStream = EducationStream::active()->get();
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        $maritalStatus = MaritalStatus::active()->get();
        $tags = LeadTag::active()->pluck('name')->toArray();
        $typeOfRelation = TypeOfRelative::active()->get();
        $otherVisaType = OtherVisaType::active()->get();

        return view('team.lead.create' , compact('countries','sources','leadTypes','purposes','country','coaching','branch','assign_owner','lead_status','lead_sub_status','educationLevel','educationBoard','educationStream','englishProficiencyTest','maritalStatus','tags','typeOfRelation','otherVisaType'));
    }

    /**
     * Store a newly created Source
     */
    public function store(Request $request)
    {
        // try {
            $validated = $request->validate([
                'first_name' => 'required',
                'middle_name' => 'nullable',
                'last_name' => 'required',
                'mobile_no' => 'required',
                'email_id' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'whatsapp_no' => 'required',
                'source' => 'required',
                'lead_type' => 'required',
            ], [
                'mobile_no.required' => 'mobile no is required.',
                'mobile_no.unique' => 'This mobile no already exists.',
                'first_name.required' => 'first name is required.',
                'last_name.required' => 'last name is required.',
                'email_id.required' => 'email is required.',
                'country_id.required' => 'country is required.',
                'state_id.required' => 'state is required.',
                'city_id.required' => 'city is required.',
                'whatsapp_no.required' => 'whatsapp is required.',
                'source.required' => 'source is required.',
                'lead_type.required' => 'lead-type is required.',
            ]);

            $validated['country'] = $request->country_id;
            $validated['state'] = $request->state_id;
            $validated['city'] = $request->city_id;
            $validated['address'] = $request->address;
            $validated['gender'] = $request->gender;
            $validated['maratial_status'] = $request->maratial_status;
            if(isset($request->date_of_birth)){
                $rawDateDateOfBirth = $request->date_of_birth;
                $formattedDateOfBirth = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDateDateOfBirth)->format('Y-m-d');
            }else{
                $formattedDateOfBirth = null;
            }
            $validated['date_of_birth'] = $formattedDateOfBirth;
            $validated['country_code'] = $request->mobile_no_country_code ?? null;
            $validated['whatsapp_country_code'] = $request->whatsapp_no_country_code ?? null;
            $PersonalDetails = ClientDetails::create($validated);

            $rawDateLead = $request->client_date;
            $formattedDateLead = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDateLead)->format('Y-m-d');


            $otherDetails = [
                'client_id' => $PersonalDetails->id,
                'client_date' => $formattedDateLead,
                'lead_type' => $request->lead_type,
                'purpose' => $request->purpose,
                'country' => $request->country ?? null,
                'coaching' => $request->coaching ?? null,
                'branch' => $request->branch,
                'assign_owner' => $request->assign_owner,
                'tag' => $request->tags,
                'status' => $request->lead_status,
                'sub_status' => $request->lead_sub_status,
                'remark' => $request->remark,
                'genral_remark' => $request->genral_remark,
            ];
           $clientLead = ClientLead::create($otherDetails);


            if ($request->has('education')) {
                foreach ($request->education as $edu) {
                    EducationDetails::create([
                        'client_id' => $PersonalDetails->id,
                        'education_level' => $edu['education_level'] ?? null,
                        'education_board' => $edu['education_board'] ?? null,
                        'language' => $edu['language'] ?? null,
                        'education_stream' => $edu['education_stream'] ?? null,
                        'passing_year' => $edu['passing_year'] ?? null,
                        'result' => $edu['result'] ?? null,
                        'no_of_backlog' => $edu['no_of_backlog'] ?? null,
                        'institute' => $edu['institute'] ?? null,
                    ]);
                }
            }

            if ($request->has('exam_data')) {

                foreach ($request->exam_data as $examId) {

                    $rawDate = $request->exam_date[$examId];
                    $formattedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');

                    $clientTest = ClientEnglishProficiencyTest::create([
                        'client_id' => $PersonalDetails->id,
                        'client_lead_id' => $clientLead->id,
                        'exam_id' => $examId,
                        'exam_date' => $formattedDate,
                    ]);

                    // Check and loop through scores
                    if($request->has('exam_score')){
                        if (isset($request->exam_score[$examId])) {
                            foreach ($request->exam_score[$examId] as $moduleId => $scoreData) {
                                if (!empty($scoreData['score'])) {
                                    ClientEnglishProficiencyTestScore::create([
                                        'client_test_id' => $clientTest->id,
                                        'exam_modual_id' => $moduleId,
                                        'score' => $scoreData['score'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }



            if ( $request->hasFile('passport_copy') && $request->file('passport_copy')->isValid()) {
                $clientName = Str::slug($PersonalDetails->first_name . ' ' . $PersonalDetails->last_name); // Slug for safe folder name
                $clientId = $PersonalDetails->id;

                $folderPath = "passport_copies/{$clientName}_{$clientId}";

                $file = $request->file('passport_copy');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Store file in public disk
                $storedFilePath = $file->storeAs($folderPath, $fileName, 'public');

                $passportCopyPath = $storedFilePath; // This will be stored in DB
            } else {
                $passportCopyPath = null;
            }

            $formattedDatePass = null;
            $rawDatepas = $request->passport_expiry_date;

            if (!empty($rawDatepas)) {
                try {
                    $formattedDatePass = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDatepas)->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::error('Invalid passport expiry date format: ' . $rawDatepas, ['error' => $e->getMessage()]);
                }
            }


            // Filter only non-empty values
            $passportData = array_filter([
                'passport_number'        => $request->passport_number,
                'passport_expiry_date'   => $formattedDatePass,
                'passport_copy'          => $passportCopyPath,
            ], function ($value) {
                return !is_null($value) && $value !== '';
            });

            // Only store if something is present
            if (!empty($passportData)) {
                $passportData['client_id'] = $PersonalDetails->id;

                ClientPassportDetails::updateOrCreate(
                    ['client_id' => $PersonalDetails->id],
                    $passportData
                );
            }


            if(isset($request->is_relative) && $request->is_relative == 1){
                ClientRelativeForeignCountry::create([
                    'client_id'             => $PersonalDetails->id,
                    'relative_relationship'=> $request->relative_relationship,
                    'relative_country'     => $request->relative_country,
                    'visa_type'            => $request->visa_type,
                ]);
            }

            if ($request->has('is_visa_rejection') && $request->is_visa_rejection == 1) {
                if ($request->has('visa_rejection')) {
                    foreach ($request->visa_rejection as $rejection) {
                        ClientPreviousRejection::create([
                            'client_id'             => $PersonalDetails->id,
                            'rejection_country'     => $rejection['rejection_country'],
                            'rejection_month_year'  => $rejection['rejection_month_year'],
                            'rejection_visa_type'   => $rejection['rejection_visa_type'],
                        ]);
                    }
                }
            }

            if ($request->has('is_visited') && $request->is_visited == 1) {
                $visitedVisas = $request->input('visited_visa', []);

                foreach ($visitedVisas as $visa) {
                    ClientVisitedCountry::create([
                        'client_id' => $PersonalDetails->id, // Replace this with actual client ID
                        'visited_country' => $visa['visited_country'],
                        'visited_visa_type' => $visa['visited_visa_type'],
                        'start_date' => Carbon::createFromFormat('d/m/Y', $visa['start_date'])->format('Y-m-d'),
                        'end_date' => Carbon::createFromFormat('d/m/Y', $visa['end_date'])->format('Y-m-d'),
                    ]);
                }
            }


            if ($request->has('is_employment') && $request->filled('employment')) {
                foreach ($request->employment as $employment) {
                    ClientEmploymentDetails::create([
                        'client_id'     => $PersonalDetails->id,
                        'company_name'  => $employment['company_name'] ?? null,
                        'designation'   => $employment['designation'] ?? null,
                        'start_date'    => $this->formatDate($employment['start_date'] ?? null),
                        'end_date'      => isset($employment['is_working'][0]) && $employment['is_working'][0] == 1
                                            ? null
                                            : $this->formatDate($employment['end_date'] ?? null),
                        'no_of_year'    => isset($employment['is_working'][0]) && $employment['is_working'][0] == 1
                                            ? null
                                            : ($employment['no_of_year'] ?? null),
                        'is_working'    => $employment['is_working'][0] ?? 0,
                    ]);
                }
            }

            // Send welcome email to the lead
            try {
                $subject = 'Welcome to ' . config('app.name') . ' - Thank You for Your Interest';

                $content = [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email_id'],
                    'phone' => $validated['mobile_no'],
                    'service' => $request->purpose ? Purpose::find($request->purpose)?->name : null,
                    'website_url' => config('app.url'),
                ];

                Mail::to($validated['email_id'])->send(
                    new LeadWelcomeMail($subject, $content, null)
                );

                Log::info('Lead welcome email sent successfully to: ' . $validated['email_id']);

            } catch (\Exception $mailException) {
                // Log the error but don't fail the lead creation
                Log::error('Failed to send welcome email to lead: ' . $validated['email_id'], [
                    'error' => $mailException->getMessage(),
                    'lead_id' => $PersonalDetails->id
                ]);
            }

            return redirect()->route('team.lead.index')
                ->with('success', "Client Details '{$validated['first_name']}' has been created successfully and welcome email has been sent.");

        // } catch (\Exception $e) {
        //     return back()->withInput()
        //         ->with('error', 'Error creating Client Details: ' . $e->getMessage());
        // }
    }

    public function edit($id)
    {
        $clientLead = ClientLead::find($id);
        $countries = Country::orderBy('name')->get();
        $sources = Source::orderBy('name')->get();
        $leadTypes = LeadType::orderBy('name')->get();
        $purposes = Purpose::orderBy('name')->get();
        $country = ForeignCountry::orderBy('name')->get();
        $coaching = Coaching::orderBy('name')->get();
        $branch = Branch::orderBy('branch_name')->get();
        $assign_owner = User::orderBy('name')->get();
        $lead_status = LeadStatus::orderBy('name')->get();
        $lead_sub_status = LeadSubStatus::orderBy('name')->get();
        $states = $clientLead && $clientLead?->client?->country ? State::where('country_id', $clientLead?->client?->country)->get(): collect();
        $cities = $clientLead && $clientLead?->client?->state ? City::where('state_id', $clientLead?->client?->state)->get() : collect();
        $educationLevel = EducationLevel::active()->get();
        $educationBoard = EducationBoard::active()->get();
        $educationStream = EducationStream::active()->get();
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        $maritalStatus = MaritalStatus::active()->get();
        $tags = LeadTag::active()->pluck('name')->toArray();
        $typeOfRelation = TypeOfRelative::active()->get();
        $otherVisaType = OtherVisaType::active()->get();

        return view('team.lead.edit', compact('clientLead','countries','sources','leadTypes','purposes','country','coaching','branch','assign_owner','lead_status','lead_sub_status','states','cities','educationLevel','educationBoard','educationStream','englishProficiencyTest','maritalStatus','tags','typeOfRelation','otherVisaType'));
    }

    public function update(Request $request, $id)
    {
        $clientLead = ClientLead::findOrFail($id);
        try {
            $validated = $request->validate([
                'first_name' => 'required',
                'middle_name' => 'nullable',
                'last_name' => 'required',
                'mobile_no' => 'required',
                'email_id' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'whatsapp_no' => 'required',
                'source' => 'required',
                'lead_type' => 'required',
            ], [
                'mobile_no.required' => 'mobile no is required.',
                'mobile_no.unique' => 'This mobile no already exists.',
                'first_name.required' => 'first name is required.',
                'last_name.required' => 'last name is required.',
                'email_id.required' => 'email is required.',
                'country_id.required' => 'country is required.',
                'state_id.required' => 'state is required.',
                'city_id.required' => 'city is required.',
                'whatsapp_no.required' => 'whatsapp is required.',
                'source.required' => 'source is required.',
                'lead_type.required' => 'lead-type is required.',
            ]);

            $validated['country'] = $request->country_id;
            $validated['state'] = $request->state_id;
            $validated['city'] = $request->city_id;
            $validated['address'] = $request->address;
            $validated['gender'] = $request->gender;
            $validated['maratial_status'] = $request->maratial_status;
            if(isset($request->date_of_birth)){
                $rawDateDateOfBirth = $request->date_of_birth;
                $formattedDateOfBirth = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDateDateOfBirth)->format('Y-m-d');
            }else{
                $formattedDateOfBirth = null;
            }
            $validated['date_of_birth'] = $formattedDateOfBirth;
            $validated['country_code'] = $request->mobile_no_country_code ?? null;
            $validated['whatsapp_country_code'] = $request->whatsapp_no_country_code ?? null;
            // Fetch and update ClientDetails
            $clientDetails = ClientDetails::findOrFail($clientLead->client_id);
            $clientDetails->update($validated);

            // Fetch and update related ClientLead

            if ($clientLead) {
                $clientLead->update([
                    'lead_type' => $request->lead_type,
                    'purpose' => $request->purpose,
                    'country' => $request->country,
                    'coaching' => $request->coaching,
                    'branch' => $request->branch,
                    'assign_owner' => $request->assign_owner,
                    'tag' => $request->tags,
                    'status' => $request->lead_status,
                    'sub_status' => $request->lead_sub_status,
                    'remark' => $request->remark,
                    'genral_remark' => $request->genral_remark
                ]);
            }

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


            if ($request->has('is_employment') && $request->filled('employment')) {
                $existingIds = ClientEmploymentDetails::where('client_id', $clientDetails->id)->pluck('id')->toArray();
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
                            'client_id'     => $clientDetails->id,
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
                if (ClientEmploymentDetails::where('client_id', $clientDetails->id)->exists()) {
                    // ✅ Delete only if records exist
                    ClientEmploymentDetails::where('client_id', $clientDetails->id)->delete();
                }
            }


            return redirect()->route('team.lead.index')
                ->with('success', "Client Details '{$validated['first_name']}' has been updated successfully.");

        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error updating Client Details: ' . $e->getMessage());
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

    public function NewLead($client){
        $sources = Source::active()->get();
        $leadTypes = LeadType::active()->get();
        $purposes = Purpose::active()->get();
        $country = ForeignCountry::active()->get();
        $coaching = Coaching::active()->get();
        $branch = Branch::active()->orderBy('branch_name')->get();
        $assign_owner = User::active()->get();
        $lead_status = LeadStatus::active()->get();
        $lead_sub_status = LeadSubStatus::active()->get();
        $tags = LeadTag::active()->pluck('name')->toArray();
        return view('team.lead.new-lead' , compact('client','sources','leadTypes','purposes','country','coaching','branch','assign_owner','lead_status','lead_sub_status','tags'));
    }

    public function NewLeadStore(Request $request ,$client){

        $clientData = ClientDetails::find($client);
        $rawDateLead = $request->client_date;
        $formattedDateLead = Carbon::createFromFormat('d/m/Y', $rawDateLead)->format('Y-m-d');

        $otherDetails = [
                'client_id' => $client,
                'client_date' => $formattedDateLead,
                'lead_type' => $request->lead_type,
                'purpose' => $request->purpose,
                'country' => $request->country ?? null,
                'coaching' => $request->coaching ?? null,
                'branch' => $request->branch,
                'assign_owner' => $request->assign_owner,
                'added_by' => auth()->user()->id,
                'source' => $request->source,
                'tag' => $request->tags,
                'status' => $request->lead_status,
                'sub_status' => $request->lead_sub_status,
                'remark' => $request->remark,
            ];
           $clientLead = ClientLead::create($otherDetails);

           $purposeName = $clientLead?->getPurpose?->name;

        return redirect()->route('team.client.show', $client)
            ->with('success', "Client '{$clientData['first_name']}' has successfully availed a new service: '{$purposeName}'. Welcome email has been sent.");

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

    public function assignOwner(Request $request)
    {
        try {
            $leadId = $request->input('lead_id');
            $counselorId = $request->input('counselor_id');

            $clientLead = ClientLead::findOrFail($leadId);
            $clientLead->assign_owner = $counselorId;
            $clientLead->save();

            return response()->json(['success' => 'Owner has been assigned successfully.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error assigning owner: ' . $e->getMessage()], 500);
        }
    }
}
