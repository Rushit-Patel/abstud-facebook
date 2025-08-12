<?php

namespace App\Http\Controllers\Team\Coaching;


use App\DataTables\Team\Coaching\ExamBookingDataTable;
use App\Exports\CoachingDataExport;
use App\Exports\ExamBookingDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Client_E_P_T_Booking_Result;
use App\Models\ClientCoaching;
use App\Models\ClientDetails;
use App\Models\ClientEnglishProficiencyTestBooking;
use App\Models\Coaching;
use App\Models\EnglishProficiencyTest;
use App\Models\ExamCenter;
use App\Models\ExamWay;
use App\Models\User;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\ExamBookingRepository;
use App\Repositories\Team\RegistrationRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExamBookingController extends Controller
{

    public function index(ExamBookingDataTable $ExamBookingDataTable){

         return $ExamBookingDataTable->render('team.exam-booking.index');
    }
    // Exam Date Booking Create Code
    public function edit($id)
    {
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        $examCenter = ExamCenter::active()->get();
        $examWay = ExamWay::active()->get();
        $clientCoaching  = ClientCoaching::find($id);

        return view('team.exam-booking.create' , compact('englishProficiencyTest','examCenter','examWay','clientCoaching'));
    }
    // Exam Date Booking Create Code
    public function update(Request $request, $id)
    {
        $clientCoaching = ClientCoaching::findOrFail($id);

        $days = (int) $request->input('result_days', 0);
        $resultDate = Carbon::today()->addDays($days)->format('Y-m-d');
        try {
            $data = [
                'client_id' => $clientCoaching->client_id,
                'client_lead_id' => $clientCoaching->client_lead_id,
                'client_coaching_id' => $id,
                'english_proficiency_test_id' => $request->input('english_proficiency_test_id'),
                'exam_way'     => $request->input('exam_way_id'),
                'exam_mode_id'     => $request->input('exam_mode_id'),
                'exam_date'     => Helpers::parseToYmd($request->input('exam_date')),
                'exam_center'     => $request->input('exam_center_id'),
                'result_date'     => $resultDate,
            ];

            ClientEnglishProficiencyTestBooking::create($data);

            return redirect()->route('team.exam-booking.index')
                ->with('success', "Client Exam Booking successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error Create Client Exam Booking: ' . $e->getMessage());
        }
    }

    public function editExamBooking($id)
    {
        $BookingData = ClientEnglishProficiencyTestBooking::findOrFail($id);
        $englishProficiencyTest = EnglishProficiencyTest::active()->get();
        $examCenter = ExamCenter::active()->get();
        $examWay = ExamWay::active()->get();

        return view('team.exam-booking.edit', compact('BookingData','englishProficiencyTest','examCenter','examWay'));
    }
    // Invoice Create
    public function updateExamBooking(Request $request, $id)
    {
        $examBookingData = ClientEnglishProficiencyTestBooking::findOrFail($id);

        try {
            $data = [
                'client_id' => $examBookingData->client_id,
                'client_lead_id' => $examBookingData->client_lead_id,
                'client_coaching_id' => $examBookingData->client_coaching_id,
                'english_proficiency_test_id' => $request->input('english_proficiency_test_id'),
                'exam_way'     => $request->input('exam_way_id'),
                'exam_date'     => Helpers::parseToYmd($request->input('exam_date')),
                'result_date'     => Helpers::parseToYmd($request->input('result_date')),
                'exam_mode_id'     => $request->input('exam_mode_id'),
                'exam_center'     => $request->input('exam_center_id'),
            ];

            $examBookingData->update($data);
            if ($request->has('exam_score')) {
                foreach ($request->exam_score as $testId => $moduals) {
                    foreach ($moduals as $modualId => $data) {
                        Client_E_P_T_Booking_Result::updateOrCreate(
                            [
                                'client_e_p_t_booking_result_id' => $id,
                                'exam_modual_id' => $modualId
                            ],
                            [
                                'score' => $data['score'] ?? null
                            ]
                        );
                    }
                }
            }

            return redirect()->route('team.coaching.running')
                ->with('success', "Client Coaching Update successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error Create Client Coaching Update: ' . $e->getMessage());
        }
    }

    public function destroyExamBooking($id){
        try {
            $clientBookingResult = ClientEnglishProficiencyTestBooking::findOrFail($id);
            Client_E_P_T_Booking_Result::where('client_e_p_t_booking_result_id', $id)->delete();
            $clientName = $clientBookingResult->clientLeadDetails->first_name . ' ' . $clientBookingResult->clientLeadDetails->last_name;
            $clientBookingResult->delete();

            return redirect()->route('team.exam-booking.index')
                ->with('success', "Exam Date Booking for client '{$clientName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Client Exam Date Booking: ' . $e->getMessage());
        }
    }

    /**
     * Get all follow-ups for a specific lead
     */

    public function exportExamBooking(Request $request,ExamBookingRepository $examBookingRepository){

        $export = new ExamBookingDataExport($examBookingRepository,$request->all());
        return Excel::download($export, 'Exam-Booking-result.xlsx');

    }
}
