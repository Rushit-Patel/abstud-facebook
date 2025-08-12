<?php

namespace App\Http\Controllers\Team;

use App\DataTables\Team\Lead\LeadDataTable;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BillingCompany;
use App\Models\Branch;
use App\Models\City;
use App\Models\ClientCoachingDemoDetails;
use App\Models\ClientDetails;
use App\Models\ClientDocumentCheckList;
use App\Models\ClientInvoice;
use App\Models\ClientLead;
use App\Models\ClientLeadRegistration;
use App\Models\Coaching;
use App\Models\CoachingMaterial;
use App\Models\Country;
use App\Models\EducationBoard;
use App\Models\EducationLevel;
use App\Models\EducationStream;
use App\Models\EnglishProficiencyTest;
use App\Models\ExamMode;
use App\Models\ForeignCity;
use App\Models\ForeignCountry;
use App\Models\ForeignState;
use App\Models\LeadSubStatus;
use App\Models\OtherVisaType;
use App\Models\Purpose;
use App\Models\Service;
use App\Models\State;
use App\Models\TeamNotification;
use App\Models\TypeOfRelative;
use App\Models\User;
use App\Repositories\Team\CoachingRepository;
use Auth;
use Illuminate\Http\Request;
use App\Repositories\Team\LeadRepository;
use Spatie\Permission\Models\Permission;

class AjaxController extends Controller
{
    protected $repository;
    protected $coachingRepository;

    public function __construct(LeadRepository $repository,CoachingRepository $coachingRepository)
    {
        $this->repository = $repository;
        $this->coachingRepository = $coachingRepository;
    }

    public function getSubStatuses(Request $request)
    {
        $statusId = $request->status_id;
        // Assuming you have a relation or model to fetch sub-statuses
        $subStatuses = LeadSubStatus::where('lead_status_id', $statusId)->pluck('name', 'id');
        return response()->json($subStatuses);
    }

    public function getLocationByBranch(Request $request)
    {
        $branch = Branch::find($request->branch_id);

        if (!$branch) {
            return response()->json(['error' => 'Branch not found'], 404);
        }

        $countries = Country::pluck('name', 'id');
        $states = State::where('country_id', $branch->country_id)->pluck('name', 'id');
        $cities = City::where('state_id', $branch->state_id)->pluck('name', 'id');

        return response()->json([
            'country_id' => $branch->country_id,
            'state_id' => $branch->state_id,
            'city_id' => $branch->city_id,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
        ]);
    }

    public function getStreams($levelId)
    {
        $educationLevel = EducationLevel::find($levelId);
        $requiredDetails = json_decode($educationLevel->required_details, true);
        $streams = EducationStream::whereRaw("FIND_IN_SET(?, education_level_id)", [$levelId])->pluck('name', 'id');

        return response()->json([
            'streams' => $streams,
            'required_details' => $requiredDetails,
        ]);
    }

    public function getStatesByCountry($countryId)
    {
        $states = State::where('country_id', $countryId)
                      ->orderBy('name')
                      ->get(['id', 'name']);

        return response()->json($states);
    }

    /**
     * Get cities by state ID for AJAX calls
     */
    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
                     ->orderBy('name')
                     ->get(['id', 'name']);

        return response()->json($cities);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $getLead = $this->repository->getLead();

        $leads = $getLead->with('client')
            ->whereHas('client', function ($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                ->orWhere('middle_name', 'like', '%' . $query . '%')
                ->orWhere('last_name', 'like', '%' . $query . '%')
                ->orWhere('email_id', 'like', '%' . $query . '%')
                ->orWhere('mobile_no', 'like', '%' . $query . '%')
                ->orWhere('client_code', 'like', '%' . $query . '%');
            })
            ->get();

        return view('team.comman-search.search_results', compact('leads'));
    }

    public function getEducation(Request $request){
        $educationLevel = EducationLevel::active()->get();
        $educationBoard = EducationBoard::active()->get();
        $educationStream = EducationStream::active()->get();
        $educations = ClientDetails::find($request->client_id)->educationDetails;

        return view('components.team.lead.edit-education', compact('educations','educationLevel', 'educationBoard', 'educationStream'));
    }
    public function getEnglishProficiencyTest(Request $request){
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        $clientLead = ClientDetails::find($request->client_id)->leadLastest;

        return view('components.team.lead.edit-english-proficiency', compact('clientLead','englishProficiencyTest'));
    }

    public function getEmployment(Request $request){
        $employmentData = ClientDetails::find($request->client_id)->employmentDetails;

        return view('components.team.lead.employment-details', compact('employmentData'));
    }

    public function getPassport(Request $request){
        $passportData = ClientDetails::find($request->client_id)->passportDetails;

        return view('components.team.lead.passport-details', compact('passportData'));
    }

    public function getRejectionData(Request $request){
        $visaRejectionDatas = ClientDetails::find($request->client_id)->visaRejectionDetails;
        $visaRejectionVisaType = OtherVisaType::active()->get();
        $visaRejectionCountry = ForeignCountry::orderBy('name')->get();

        return view('components.team.lead.visa-rejection-details', compact('visaRejectionDatas','visaRejectionCountry','visaRejectionVisaType'));
    }
    public function getRelativeData(Request $request){
        $relativeData = ClientDetails::find($request->client_id)->getClientRelativeDetails;
        $otherVisaTypes = OtherVisaType::active()->get();
        $countrys = ForeignCountry::orderBy('name')->get();
        $typeOfRelations = TypeOfRelative::active()->get();

        return view('components.team.lead.relative-foreign-country', compact('relativeData','otherVisaTypes','countrys','typeOfRelations'));
    }
    public function getVisitedData(Request $request){
        $visitedDataDatas = ClientDetails::find($request->client_id)->anyVisitedDetails;
        $visitedVisaType = OtherVisaType::active()->get();
        $visitedCountry = ForeignCountry::orderBy('name')->get();

        return view('components.team.lead.visited-details', compact('visitedDataDatas','visitedVisaType','visitedCountry'));
    }

    public function getUsersByBranch(Request $request)
    {
        $branchIds = $request->input('branch_ids', []);

        $users = User::whereIn('branch_id', $branchIds)
                    ->select('id', 'name')
                    ->get();

        return response()->json($users);
    }

    public function getBranchUser(Request $request){

        $branchId = $request->branch_id;

        // Get counselors of the branch
        $counselors = User::where('branch_id', $branchId)
            ->pluck('name', 'id');

        return response()->json([
            'counselors' => $counselors,
        ]);
    }

    public function DemoDetails(Request $request){
        $client_id = $request->client_id;
        $clientData = ClientDetails::find($client_id);
        $client_lead_id = $request->client_lead_id;
        $coaching = Coaching::active()->get();
        $user = User::where('branch_id', $clientData->branch)
            ->active()
            ->permission('demo:create')
            ->get();
        $getDemoData = ClientCoachingDemoDetails::find($request->demo_id);
        return view('components.team.lead.demo-details' ,compact('client_id','client_lead_id','coaching','user','getDemoData'));
    }

    public function RegisterDetails(Request $request){
        $client_lead_id = $request->client_lead_id;
        $clientData = ClientLead::find($request->client_lead_id);
        $user = User::where('branch_id', $clientData->branch)->active()->get();
        $subStatus = LeadSubStatus::where('lead_status_id','3')->active()->get();
        $regDetails = ClientLeadRegistration::find($request->client_reg_id);

        $coaching = Coaching::active()->get();
        $country = ForeignCountry::active()->get();
        $purposes = Purpose::active()->get();

        return view('components.team.lead.register-details' ,compact('client_lead_id','user','regDetails','subStatus','coaching','country','purposes'));
    }

    public function InvoiceDetails(Request $request){

        $client_id = $request->client_id;
        $clientLeadData = ClientLead::with(['getPurpose', 'getForeignCountry', 'getCoaching'])
        ->where('client_id',$client_id)
        ->get()
        ->mapWithKeys(function ($lead) {
            $purpose = $lead->getPurpose->name ?? '';

            // Country agar null hai to Coaching ka naam lo
            if (!empty($lead->getForeignCountry)) {
                $location = $lead->getForeignCountry->name;
            } elseif (!empty($lead->getCoaching)) {
                $location = $lead->getCoaching->name;
            } else {
                $location = '';
            }

            $label = trim($purpose . ' - ' . $location, ' -');
            return [$lead->id => $label];
        })
        ->toArray();

        $billingCompany = BillingCompany::active()->get();
        $InvoiceDetails = ClientInvoice::find($request->client_invoice_id);
        return view('components.team.lead.invoice-details' ,compact('clientLeadData','InvoiceDetails','billingCompany'));
    }


    public function getService(Request $request){

        $getLeadData = ClientLead::find($request->client_lead_id);
        $purposeId = $getLeadData->purpose;

        // Service fetch karna using FIND_IN_SET
        $service = Service::active()
            ->whereRaw("FIND_IN_SET(?, purpose)", [$purposeId]) // assuming purpose_id is CSV in services table
            ->select('id', 'name', 'amount')
            ->get()
            ->mapWithKeys(function ($srv) {
                return [
                    $srv->id => [
                        'label' => $srv->name,
                        'amount' => $srv->amount
                    ]
                ];
            });
        return response()->json($service);
    }

    public function getExamModes($testId)
    {
        $getEnglishTest = EnglishProficiencyTest::find($testId);
        $modes = ExamMode::where('english_proficiency_test_id', $testId)
            ->pluck('name', 'id');

        return response()->json([
            'modes'        => $modes,
            'result_days'  => $getEnglishTest?->result_days,
        ]);
    }


    // Get Coaching Wise batch Single:
    public function getBatchesByCoaching(Request $request){
            $batches = Batch::where('coaching_id', $request->coaching_id)
                ->select('id', 'name','time')
                ->get();

            return response()->json($batches);

    }

    // Get Coaching Wise batch Multiple:
    public function getBatchesByCoachingMultiple(Request $request)
    {
        $coachingIds = (array) $request->coaching_id; // Ensure array

        $batches = Batch::whereIn('coaching_id', $coachingIds)
            ->select('id', 'name', 'time')
            ->get();

        return response()->json($batches);
    }

    public function searchAttendanceCoachingBatch(Request $request)
    {
        $coachingData = $this->coachingRepository->getCoaching();

        $records = $coachingData
            ->where('is_complete_coaching', '!=', '1')
            ->where('is_drop_coaching', '!=', '1')
            ->when($request->joining_date, function ($query) use ($request) {
                // Date ko MySQL format me convert karo
                $date = Helpers::parseToYmd($request->joining_date);
                $query->whereDate('joining_date', $date);
            })
            ->when($request->coaching_id, function ($query) use ($request) {
                $query->whereIn('coaching_id', $request->coaching_id);
            })
            ->when($request->batch_id, function ($query) use ($request) {
                $query->whereIn('batch_id', $request->batch_id);
            })
            ->orderBy('joining_date', 'desc')
            ->get();

        $html = view('team.attendance.datatables.search-attendance-list', compact('records'))->render();

        return response()->json([
            'status' => true,
            'html' => $html
        ]);
    }



    // Notification Management Methods
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        try {
            $notification = TeamNotification::where('id', $notificationId)
                ->where('user_id', Auth::user()->id)
                ->first();

            if (!$notification) {
                return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
            }

            $notification->update(['is_seen' => true, 'seen_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Notification marked as read']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error marking notification as read'], 500);
        }
    }

    public function markAllNotificationsAsRead(Request $request)
    {
        try {
            $type = $request->input('type', 'all');
            $query = TeamNotification::where('user_id', Auth::user()->id)
                ->where('is_seen', false);

            if ($type !== 'all') {
                $query->where('type', $type);
            }

            $updated = $query->update(['is_seen' => true, 'seen_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updated} notifications as read"
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error marking notifications as read'], 500);
        }
    }

    public function getNotificationCount(Request $request)
    {
        try {
            $count = TeamNotification::where('user_id', Auth::user()->id)
                ->where('is_seen', false)
                ->count();

            return response()->json(['success' => true, 'count' => $count]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'count' => 0], 500);
        }
    }

    public function getCoachingMaterial(Request $request)
    {
        $coachingId = $request->coaching_id;
        $materials = CoachingMaterial::where('coaching', $coachingId)->get();
        $selectedMaterials = $request->selected_materials ?? [];


        $html = view('team.coaching.material-details', compact('materials','selectedMaterials'))->render();

        return response()->json(['html' => $html]);
    }

    public function viewDocument(Request $request){
        $documentChecklist = ClientDocumentCheckList::find($request->client_check_list_id);

        return view('team.client.document-checklist.view-document', compact('documentChecklist'))->render();

    }

    // Ajax Call
    public function getForeignStates($country_id){
         $states = ForeignState::where('country_id', $country_id)
            ->pluck('name', 'id');

        return response()->json($states);
    }
    public function getForeignCities($state_id)
    {
        $cities = ForeignCity::where('state_id', $state_id)
            ->pluck('name', 'id');

        return response()->json($cities);
    }

}
