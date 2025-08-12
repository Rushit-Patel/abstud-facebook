<?php

namespace App\Http\Controllers\Team\Lead;

use App\DataTables\Team\Lead\LeadDataTable;
use App\Exports\LeadDataExport;
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
use App\Notifications\AssignLeadNotification;
use App\Notifications\NewLeadNotification;
use App\Repositories\Team\LeadRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LeadWelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function index(LeadDataTable $LeadDataTable)
    {
        $userCounselors = User::where('branch_id', auth()->user()->branch_id)->active()->get();

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
            $validated['branch'] = $request->branch;
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
            $PersonalDetails->generateClientCode();

            $rawDateLead = $request->client_date;
            $formattedDateLead = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDateLead)->format('Y-m-d');


            $otherDetails = [
                'client_id' => $PersonalDetails->id,
                'client_date' => $formattedDateLead,
                'lead_type' => $request->lead_type,
                'purpose' => $request->purpose,
                'country' => $request->country ?? null,
                'second_country' => $request->second_country ? implode(',', $request->second_country) : null,
                'coaching' => $request->coaching ?? null,
                'branch' => $request->branch,
                'assign_owner' => $request->assign_owner,
                'added_by' => auth()->user()->id,
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

            (new NewLeadNotification($clientLead))->send();
          
            return redirect()->route('team.lead.index')
                ->with('success', "Client Details '{$validated['first_name']}' has been created successfully and welcome email has been sent.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Client Details: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $clientLead = ClientLead::find($id);
        $countries = Country::active()->get();
        $sources = Source::active()->get();
        $leadTypes = LeadType::active()->get();
        $purposes = Purpose::active()->get();
        $country = ForeignCountry::active()->get();
        $coaching = Coaching::active()->get();
        $branch = Branch::active()->get();
        $assign_owner = User::where('branch_id', $clientLead->branch)->get();
        $lead_status = LeadStatus::active()->get();
        $lead_sub_status = LeadSubStatus::active()->get();
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
                    'second_country' => $request->second_country ? implode(',', $request->second_country) : null,
                    'branch' => $request->branch,
                    'assign_owner' => $request->assign_owner,
                    'tag' => $request->tags,
                    'status' => $request->lead_status,
                    'sub_status' => $request->lead_sub_status,
                    'remark' => $request->remark,
                    'genral_remark' => $request->genral_remark
                ]);
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
                'second_country' => $request->second_country ? implode(',', $request->second_country) : null,
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

            (new AssignLeadNotification($clientLead))->send();

            return response()->json(['success' => 'Owner has been assigned successfully.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error assigning owner: ' . $e->getMessage()], 500);
        }
    }

    public function updateTag(Request $request){

        try {
            $leadId = $request->input('lead_id');
            $tags = $request->input('tags');

            $clientLead = ClientLead::findOrFail($leadId);
            $clientLead->tag = $tags;
            $clientLead->save();

            return response()->json(['success' => 'tag has been change successfully.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error tag change: ' . $e->getMessage()], 500);
        }
    }

    public function exportLeads(Request $request,LeadRepository $leadRepository){

        $export = new LeadDataExport($leadRepository,$request->all());
        return Excel::download($export, 'leads.xlsx');

    }
}
