<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ClientDetails;
use App\Models\ClientEnglishProficiencyTest;
use App\Models\ClientEnglishProficiencyTestScore;
use App\Models\ClientLead;
use App\Models\ClientMobileVerify;
use App\Models\ClientPreviousRejection;
use App\Models\ClientVisitHistory;
use App\Models\Coaching;
use App\Models\CompanySetting;
use App\Models\Country;
use App\Models\EducationBoard;
use App\Models\EducationDetails;
use App\Models\EducationLevel;
use App\Models\EducationStream;
use App\Models\EnglishProficiencyTest;
use App\Models\ForeignCountry;
use App\Models\LeadType;
use App\Models\OtherVisaType;
use App\Models\Purpose;
use App\Models\Source;
use App\Notifications\NewLeadNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;
use Mail;

class GuestLeadController extends Controller
{
    public function sessionBranch($branchId)
    {
        $branchId = base64_decode($branchId);
        $branch = Branch::findOrFail($branchId);

        session(['branch' => $branch]);

        return redirect()->route('client.guest.welcome');
    }

    public function noSessionBranch()
    {
        return view('client.guest.no-session-branch');
    }

    public function welcome()
    {
        return view('client.guest.welcome');
    }

    public function checkMobile(Request $request)
    {
        $request->validate([
            // 'mobile_no' => 'required|regex:/^[0-9]{10}$/',
            'mobile_no' => 'required',
        ]);

        $mobile_no = $request->mobile_no;

        $clientCheck = ClientDetails::where('mobile_no', $mobile_no)->first();

        if ($clientCheck) {

            $getClientLeads = $clientCheck->leads;

            return view('client.guest.lead-history', compact('mobile_no', 'getClientLeads', 'clientCheck'));
        } else {
            $otp = rand(1000, 9999);
            ClientMobileVerify::updateOrCreate(
                ['mobile_no' => $mobile_no],
                ['otp' => $otp, 'is_verify' => '0']
            );

            $mobile_no = base64_encode($mobile_no);

            return redirect()->route('client.guest.otp', ['mobile' => $mobile_no]);
        }
    }

    public function otp($mobile)
    {
        $mobile_no = base64_decode($mobile);
        $otpRecord = ClientMobileVerify::where('mobile_no', $mobile_no)
            ->latest()
            ->first();

        return view('client.guest.otp', compact('mobile_no', 'otpRecord'));
    }



    public function otpVerify(Request $request)
    {
        $request->validate([
            'code_0' => 'required|numeric|digits:1',
            'code_1' => 'required|numeric|digits:1',
            'code_2' => 'required|numeric|digits:1',
            'code_3' => 'required|numeric|digits:1',
        ]);

        $otp = $request->input('code_0') .
            $request->input('code_1') .
            $request->input('code_2') .
            $request->input('code_3');

        $mobile_no = $request->input('mobile_no');

        $otpRecord = ClientMobileVerify::where('mobile_no', $mobile_no)
            ->where('otp', $otp)
            ->where('is_verify', 0)
            ->latest()
            ->first();

        if ($otpRecord) {
            $otpRecord->update(['is_verify' => 1]);
            $mobile_no = base64_encode($mobile_no);

            return redirect()->route('client.guest.personal-info', ['mobileNo' => $mobile_no])->with('success', 'OTP verified successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }



    public function personalInfo($mobileNo)
    {
        $mobile_no = base64_decode($mobileNo);
        $countries = Country::active()->orderBy('name')->get();
        $branch = Session::get('branch');
        $sources = Source::active()->orderBy('name')->get();
        return view('client.guest.personal-info', compact('mobile_no', 'countries', 'branch', 'sources'));
    }

    public function personalInfoStore(Request $request)
    {
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
        ]);

        $validated['country'] = $request->country_id;
        $branch = Session::get('branch');
        $validated['branch'] = $branch->id;
        $validated['state'] = $request->state_id;
        $validated['city'] = $request->city_id;
        $validated['address'] = $request->address;
        $validated['gender'] = $request->gender;
        if (isset($request->date_of_birth)) {
            $rawDateDateOfBirth = $request->date_of_birth;
            $formattedDateOfBirth = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDateDateOfBirth)->format('Y-m-d');
        } else {
            $formattedDateOfBirth = null;
        }
        $validated['date_of_birth'] = $formattedDateOfBirth;
        $validated['country_code'] = $request->mobile_no_country_code ?? null;
        $validated['whatsapp_country_code'] = $request->whatsapp_no_country_code ?? null;
        $validated['lead_type'] = '1';

        $PersonalDetails = ClientDetails::create($validated);
        $PersonalDetails->generateClientCode();


        $clientId = base64_encode($PersonalDetails->id);

        return redirect()->route('client.guest.service', ['client' => $clientId]);
    }

    public function service($client_id)
    {
        $branch = Session::get('branch');
        $purposes = Purpose::active()->orderBy('name')->get();
        $country = ForeignCountry::active()->orderBy('name')->get();
        $coaching = Coaching::active()->orderBy('name')->get();
        $leadTypes = LeadType::active()->orderBy('name')->get();

        return view('client.guest.service', compact('client_id', 'branch', 'purposes', 'country', 'coaching', 'leadTypes'));
    }

    public function serviceStore(Request $request)
    {
        $otherDetails = [
            'client_id' => $request->client_id,
            'client_date' => date('Y-m-d'),
            'lead_type' => '1',
            'purpose' => $request->purpose,
            'country' => $request->country ?? null,
            'second_country' => $request->second_country ? implode(',', $request->second_country) : null,
            'coaching' => $request->coaching ?? null,
            'branch' => $request->branch,
            // 'assign_owner' => $request->assign_owner,
            'status' => '1',
            'sub_status' => '1',
        ];
        $clientLead = ClientLead::where('client_id', $request->client_id)
            ->where('purpose', $request->purpose)
            ->where('client_date', date('Y-m-d'))
            ->first();
        if ($clientLead) {
            $clientLead = ClientLead::find($clientLead->id);
        } else {
            $clientLead = ClientLead::create($otherDetails);

            (new NewLeadNotification($clientLead))->send();
        }

        $clientLeadId = base64_encode($clientLead->id);
        return redirect()->route('client.guest.academic-info', [
            'client' => $clientLeadId
        ]);
    }

    public function academicInfo($clientId, $serviceId)
    {
        $getDetails = ClientDetails::find(base64_decode($clientId));
        $educationLevel = EducationLevel::active()->get();
        $educationBoard = EducationBoard::active()->get();
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        $purpose = Purpose::find($serviceId);
        $coachings = Coaching::active()->get();
        $foreignCountries = ForeignCountry::active()->get();

        return view('client.guest.academic-info', compact(
            'clientId',
            'purpose',
            'educationLevel',
            'educationBoard',
            'englishProficiencyTest',
            'coachings',
            'foreignCountries',
            'getDetails'
        ));
    }

    public function academicInfoStore(Request $request, $clientId, $serviceId)
    {
        $clientId = base64_decode($clientId);

        $otherDetails = [
            'client_id' => $clientId,
            'client_date' => date('Y-m-d'),
            'lead_type' => '1',
            'purpose' => $serviceId,
            'country' => $request->country ?? null,
            'second_country' => $request->second_country ? implode(',', $request->second_country) : null,
            'coaching' => $request->coaching ?? null,
            'branch' => session('branch')->id,
            'status' => '1',
            'sub_status' => '1',
        ];
        $clientLead = ClientLead::create($otherDetails);
        $clientLeadId = $clientLead->id;

        $educationDetails = [
            'education_board' => $request->education_board,
            'language' => $request->language,
            'education_stream' => $request->education_stream,
            'passing_year' => $request->passing_year,
            'result' => $request->result,
            'no_of_backlog' => $request->no_of_backlog,
            'institute' => $request->institute,
        ];

        $educationDetail = EducationDetails::updateOrCreate([
            'client_id' => $clientId,
            'education_level' => $request->education_level
        ], $educationDetails);

        $clientVisitHistory = [
            'client_id' => $clientId,
            'client_lead_id' => $clientLeadId,
            'branch_id' => session('branch')->id,
            'token_no' => ClientVisitHistory::generateTokenNo(),
            'date' => date('Y-m-d')
        ];

        $clientVisitHistoryDetail = ClientVisitHistory::create($clientVisitHistory);

        if ($request->has('exam_data')) {
            foreach ($request->exam_data as $examId) {
                $rawDate = $request->exam_date[$examId];
                $formattedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
                $clientTest = ClientEnglishProficiencyTest::updateOrCreate(
                    [
                        'client_id' => $clientId,
                        'exam_id' => $examId
                    ],
                    [
                        'exam_date' => $formattedDate,
                        'client_lead_id' => $clientLeadId,
                    ]
                );

                // Check and loop through scores
                if ($request->has('exam_score')) {
                    if (isset($request->exam_score[$examId])) {
                        foreach ($request->exam_score[$examId] as $moduleId => $scoreData) {
                            if (!empty($scoreData['score'])) {
                                ClientEnglishProficiencyTestScore::updateOrCreate(
                                    [
                                        'client_test_id' => $clientTest->id,
                                        'exam_modual_id' => $moduleId,
                                    ],
                                    [
                                        'score' => $scoreData['score'],
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }

        try {
            $client = ClientDetails::findOrFail($clientId);
            (new NewLeadNotification($clientLead))->send();

            Log::info('Lead welcome email sent successfully to: ' . $client->email_id);

        } catch (\Exception $mailException) {
            // Log the error but don't fail the lead creation
            Log::error('Failed to send welcome email to lead: ' . $client->email_id, [
                'error' => $mailException->getMessage(),
                'lead_id' => $clientLeadId
            ]);
        }

        return redirect()->route('client.guest.immigration-history', base64_encode($clientLeadId));
    }

    public function immigrationHistory($clientLeadId)
    {
        $getLeadDetails = ClientLead::find(base64_decode($clientLeadId));
        $getDetails = ClientDetails::find($getLeadDetails->client_id);
        $otherVisaType = OtherVisaType::active()->orderBy('name')->get();
        $country = ForeignCountry::active()->orderBy('name')->get();

        return view('client.guest.immigration-history', compact('getDetails', 'clientLeadId', 'otherVisaType', 'country'));
    }

    public function immigrationHistoryStore(Request $request, $encodedClientLeadId)
    {
        $clientLeadId = base64_decode($encodedClientLeadId);
        $getLeadDetails = ClientLead::find($clientLeadId);

        if ($request->has('is_visa_rejection') && $request->is_visa_rejection) {
            ClientPreviousRejection::updateOrCreate(
                ['client_id' => $getLeadDetails->client_id],
                [
                    'rejection_country' => $request->input('visa_rejection.rejection_country'),
                    'rejection_month_year' => $request->input('visa_rejection.rejection_month_year'),
                    'rejection_visa_type' => $request->input('visa_rejection.rejection_visa_type'),
                ]
            );
        }
        // Send Mail

        return redirect()->route('client.guest.thankyou', $encodedClientLeadId);
    }

    public function visitHistoryStore(Request $request, $encodedClientLeadId)
    {
        $clientLeadId = base64_decode($encodedClientLeadId);
        $getLeadDetails = ClientLead::findOrFail($clientLeadId);

        $visitHistoryData = [
            'client_id' => $getLeadDetails->client_id,
            'client_lead_id' => $clientLeadId,
            'branch_id' => session('branch')->id,
            'assign_to' => $getLeadDetails->assign_owner ?? null,
            'token_no' => ClientVisitHistory::generateTokenNo(),
            'date' => date('Y-m-d'),
            'status' => '1',
        ];

        ClientVisitHistory::create($visitHistoryData);

        return redirect()->route('client.guest.thankyou', base64_encode($clientLeadId));
    }

    public function thankyou($clientLeadId)
    {
        $clientLeadId = base64_decode($clientLeadId);
        $clientLead = ClientLead::with('client')->findOrFail($clientLeadId);
        return view('client.guest.thankyou', compact('clientLead'));
    }
}
