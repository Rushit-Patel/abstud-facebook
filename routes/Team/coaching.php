<?php
use App\Http\Controllers\Team\Coaching\AttendanceController;
use App\Http\Controllers\Team\Coaching\CoachingController;
use App\Http\Controllers\Team\Coaching\ExamBookingController;
use App\Http\Controllers\Team\Coaching\MockTestController;
use App\Http\Controllers\Team\Coaching\CoachingMaterialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:coaching:show'])->group(function () {

    // Coaching Create Time
    Route::resource('coaching', CoachingController::class)
        ->only([ 'edit', 'update', 'destroy'])
        ->names('coaching');

        // Coaching Edit Time
        Route::get('coachings/{id}/edit', [CoachingController::class, 'editCoaching'])->name('coachings.Edit');
        Route::put('coachings/{id}', [CoachingController::class, 'updateCoaching'])->name('coachings.Update');
        Route::delete('coachings/{id}', [CoachingController::class, 'destroyCoaching'])->name('coachings.destroy');

    Route::get('coaching/pending', [CoachingController::class, 'CoachingPending'])
        ->name('coaching.pending');
    Route::get('coaching/running', [CoachingController::class, 'CoachingRunning'])
        ->name('coaching.running');
    Route::get('coaching/drop', [CoachingController::class, 'CoachingDrop'])
        ->name('coaching.drop');
    Route::get('coaching/completed', [CoachingController::class, 'CoachingCompleted'])
        ->name('coaching.completed');


    // Exam Booking Create
    Route::resource('exam-booking', ExamBookingController::class)
        ->only([ 'index','edit', 'update', 'destroy'])
        ->names('exam-booking');

    // Exam Booking Edit Time
    Route::get('booking-exam/{id}/edit', [ExamBookingController::class, 'editExamBooking'])->name('exam-booking.Edit');
    Route::put('booking-exam/{id}', [ExamBookingController::class, 'updateExamBooking'])->name('exam-booking.Update');
    Route::delete('booking-exam/{id}', [ExamBookingController::class, 'destroyExamBooking'])->name('exam-booking.Destroy');

        // Attendence Create
    Route::resource('attendance', AttendanceController::class)
        ->only([ 'index','create', 'store','edit', 'update', 'destroy'])
        ->names('attendance');

    // Mock Test- Create
    Route::resource('mock-test', MockTestController::class)
        ->only([ 'index','create', 'store','edit', 'update', 'destroy'])
        ->names('mock-test');

    Route::get('mock-test-client/{id}/show', [MockTestController::class, 'ClientGetShow'])->name('mock-test-client.show');
    Route::get('mock-test-client/{id}/show/{client}', [MockTestController::class, 'ClientGetShowResult'])->name('mock-test-client.show.result');
    Route::put('mock-test-client/{id}/store/{client}/result', [MockTestController::class, 'ClientGetStoreResult'])->name('mock-test-store.result');

    // Material Modual
    Route::resource('coaching-material', CoachingMaterialController::class)
        ->only(['index'])
        ->names('coaching-material');

});

Route::middleware(['permission:coaching:export'])->group(function () {
    Route::post('coaching/export', [CoachingController::class, 'exportCoaching'])->name('coaching.export');
    Route::post('attendance/export', [AttendanceController::class, 'exportAttendance'])->name('attendance.export');
    Route::post('exam-booking/export', [ExamBookingController::class, 'exportExamBooking'])->name('exam-booking.export');
});
