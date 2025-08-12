<?php

namespace App\Http\Controllers\Team\Coaching;


use App\DataTables\Team\Coaching\AttendanceDataTable;
use App\DataTables\Team\Coaching\MockTestClientDataTable;
use App\DataTables\Team\Coaching\MockTestDataTable;
use App\Exports\CoachingDataExport;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client_E_P_T_Booking_Result;
use App\Models\ClientCoaching;
use App\Models\ClientCoachingAttendance;
use App\Models\ClientEnglishProficiencyTestBooking;
use App\Models\Coaching;
use App\Models\EnglishProficiencyTest;
use App\Models\ExamCenter;
use App\Models\ExamWay;
use App\Models\MockTest;
use App\Models\MockTestStudent;
use App\Models\MockTestStudentResult;
use App\Repositories\Team\CoachingRepository;
use App\Repositories\Team\RegistrationRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class MockTestController extends Controller
{

    public function index(MockTestDataTable $MockTestDataTable){

         return $MockTestDataTable->render('team.mock-test.index');
    }

    public function create(){

        $coaching = Coaching::active()->get();
        $branch = Branch::active()->get();
        return view('team.mock-test.create',compact('coaching','branch'));
    }

    public function store(Request $request){
        try {
            $data = [
                'name' => $request->name,
                'mock_test_date'     => Helpers::parseToYmd($request->input('mock_test_date')),
                'mock_test_time' => $request->time,
                'branch_id' => $request->branch,
                'coaching_id'     => $request->coaching_id,
                'batch_id'     => is_array($request->batch_id) ? implode(',', $request->batch_id) : $request->batch_id,
                'status'     => $request->status ?? 0,
                'remarks'     => $request->remarks,
                'added_by'     => auth()->user()->id,
            ];

            MockTest::create($data);

            return redirect()->route('team.mock-test.index')
                ->with('success', "Mock test Create successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error Create Mock test: ' . $e->getMessage());
        }
    }
    // Exam Date Booking Create Code
    public function edit($id)
    {
        $coaching = Coaching::active()->get();
        $branch = Branch::active()->get();
        $mockTest = MockTest::findOrFail($id);
        return view('team.mock-test.edit' , compact('mockTest','coaching','branch'));
    }
    // Exam Date Booking Create Code
    public function update(Request $request, $id)
    {
        $mockTest = MockTest::findOrFail($id);
        try {

            $data = [
                'name' => $request->name,
                'mock_test_date'     => Helpers::parseToYmd($request->input('mock_test_date')),
                'mock_test_time' => $request->time,
                'branch_id' => $request->branch,
                'coaching_id'     => $request->coaching_id,
                'batch_id'     => is_array($request->batch_id) ? implode(',', $request->batch_id) : $request->batch_id,
                'status'     => $request->status ?? 0,
                'remarks'     => $request->remarks,
            ];


            $mockTest->update($data);

            return redirect()->route('team.mock-test.index')
                ->with('success', "Mock test update successfully.");
        } catch (\Exception $e) {

            return back()->withInput()
                ->with('error', 'Error Update Mock test : ' . $e->getMessage());
        }
    }

    public function destroy($id){
         try {
            $mockTest = MockTest::findOrFail($id);
            $mockTetName = $mockTest->name;
            $mockTest->delete();

            return redirect()->route('team.mock-test.index')
                ->with('success', "Mock test '{$mockTetName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting Mock test: ' . $e->getMessage());
        }
    }

    public function ClientGetShow($id ,MockTestClientDataTable $MockTestClientDataTable){

        $mockTest = MockTest::findOrFail($id);

        return $MockTestClientDataTable->with([
            'coaching_id' => $mockTest->coaching_id,
            'branch_id'   => $mockTest->branch_id,
            'batch_id'   => explode(',', $mockTest->batch_id),
            'mocktest_id'   => $id,
        ])->render('team.mock-test.client-list', compact('mockTest'));

    }

    public function ClientGetShowResult($mockTest_id,$clientCoaching){

        $mockTest = MockTest::findOrFail($mockTest_id);
        $clientCoaching = ClientCoaching::findOrFail($clientCoaching);
        $englishProficiencyTest = EnglishProficiencyTest::where('coaching_id',$mockTest->coaching_id)->active()->get();

        return view('team.mock-test.client-result.create',compact('englishProficiencyTest','mockTest','clientCoaching'));
    }

    public function ClientGetStoreResult(Request $request ,$mockTest_id,$clientCoaching){

        $mockTestStudent = MockTestStudent::updateOrCreate(
                [
                    'mock_test_id' => $mockTest_id,
                    'client_coaching_student_id' => $clientCoaching
                ],
                [
                    'result_date' => Helpers::parseToYmd($request->result_date),
                ]
            );
            $mockTestStudentId = $mockTestStudent->id;

            foreach ($request->result_score as $clientCoachingStudentId => $modules) {
                foreach ($modules as $moduleId => $data) {
                MockTestStudentResult::updateOrCreate(
                    [
                        'mock_test_student_id' => $mockTestStudentId,
                        'modual_id' => $moduleId
                    ],
                    [
                        'score' => $data['score']
                    ]
                );
            }
        }

     return redirect()->route('team.mock-test.index')
                ->with('success', "Mock test Result Submit successfully.");
    }

    /**
     * Get all follow-ups for a specific lead
     */

    public function exportCoaching(Request $request,CoachingRepository $CoachingRepository,RegistrationRepository $registrationRepository){

        $export = new CoachingDataExport($CoachingRepository,$registrationRepository,$request->all());
        return Excel::download($export, 'coaching.xlsx');

    }
}
