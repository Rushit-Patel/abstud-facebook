<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\GradeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Routes (Student Guard)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:student', 'guard:student'])->group(function () {
    
    // Dashboard for all students
    Route::get('/student/dashboard', [DashboardController::class, 'index'])
        ->middleware('guard.role:student,Active Student')
        ->name('student.dashboard');

    // Profile management (all active students)
    Route::middleware('guard.role:student,Active Student')->group(function () {
        Route::get('/student/profile', [ProfileController::class, 'show'])
            ->name('student.profile.show');
        Route::get('/student/profile/edit', [ProfileController::class, 'edit'])
            ->middleware('guard.permission:student,edit_profile')
            ->name('student.profile.edit');
        Route::put('/student/profile', [ProfileController::class, 'update'])
            ->middleware('guard.permission:student,edit_profile')
            ->name('student.profile.update');
        Route::post('/student/profile/photo', [ProfileController::class, 'uploadPhoto'])
            ->middleware('guard.permission:student,upload_profile_photo')
            ->name('student.profile.photo');
    });

    // Academic features
    Route::middleware('guard.permission:student,view_courses')->group(function () {
        Route::get('/student/courses', [CourseController::class, 'index'])
            ->name('student.courses.index');
        Route::post('/student/courses/{course}/enroll', [CourseController::class, 'enroll'])
            ->middleware('guard.permission:student,enroll_courses')
            ->name('student.courses.enroll');
    });

    // Grades and progress
    Route::get('/student/grades', [GradeController::class, 'index'])
        ->middleware('guard.permission:student,view_grades')
        ->name('student.grades.index');

    Route::get('/student/attendance', function () {
        return view('student.attendance.index');
    })->middleware('guard.permission:student,view_attendance')
      ->name('student.attendance.index');

    // Resources and downloads
    Route::get('/student/resources', function () {
        return view('student.resources.index');
    })->middleware('guard.permission:student,view_resources')
      ->name('student.resources.index');

    Route::get('/student/certificates', function () {
        return view('student.certificates.index');
    })->middleware('guard.permission:student,download_certificates')
      ->name('student.certificates.index');

    // Communication
    Route::get('/student/announcements', function () {
        return view('student.announcements.index');
    })->middleware('guard.permission:student,view_announcements')
      ->name('student.announcements.index');

    Route::get('/student/messages', function () {
        return view('student.messages.index');
    })->middleware('guard.permission:student,view_messages')
      ->name('student.messages.index');

    // Support
    Route::get('/student/support', function () {
        return view('student.support.index');
    })->middleware('guard.permission:student,view_support_tickets')
      ->name('student.support.index');

    Route::post('/student/support', function () {
        return response()->json(['message' => 'Support ticket created']);
    })->middleware('guard.permission:student,submit_support_ticket')
      ->name('student.support.store');

    // Restricted routes for graduated students
    Route::middleware('guard.role:student,Graduated Student')->group(function () {
        Route::get('/student/alumni', function () {
            return view('student.alumni.index');
        })->name('student.alumni.index');
    });
});
