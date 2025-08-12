<?php

namespace App\Http\Controllers\Team\Coaching;


use App\DataTables\Team\Coaching\AttendanceDataTable;
use App\Exports\AttendanceDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ClientCoaching;
use App\Models\ClientCoachingAttendance;
use App\Models\ClientEnglishProficiencyTestBooking;
use App\Models\Coaching;
use App\Repositories\Team\CoachingRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function index(AttendanceDataTable $AttendanceDataTable){

         return $AttendanceDataTable->render('team.attendance.index');
    }

    public function create(){

        $coaching = Coaching::active()->get();
        return view('team.attendance.create',compact('coaching'));
    }

    public function store(Request $request){

        if (!empty($request->attendance)) {
            foreach ($request->attendance as $att) {

                if (!empty($att['status'])) {
                    ClientCoachingAttendance::create([
                        'client_coaching_id' => $att['client_coaching_id'],
                        'batch_id'           => isset($request->batch_id) ? implode(',', $request->batch_id) : null,
                        'attendance_date'    => Helpers::parseToYmd($request->joining_date),
                        'added_by'           => auth()->user()->id,
                        'status'             => $att['status'],
                    ]);
                }
            }
        }

         return redirect()->route('team.coaching.running')
                ->with('success', "Client Attendance Submit successfully.");
    }
    // Exam Date Booking Create Code
    public function edit($id)
    {
        $clientCoaching  = ClientCoaching::find($id);

        return view('team.attendance.attendance-list' , compact('clientCoaching'));
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
    /**
     * Get all follow-ups for a specific lead
     */

    public function exportAttendance(Request $request,CoachingRepository $CoachingRepository){

        $export = new AttendanceDataExport($CoachingRepository,$request->all());
        return Excel::download($export, 'attendance.xlsx');

    }
}
