<?php

use App\Http\Controllers\Partner\DashboardController;
use App\Http\Controllers\Partner\ProfileController;
use App\Http\Controllers\Partner\StudentController;
use App\Http\Controllers\Partner\ProgramController;
use App\Http\Controllers\Partner\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Partner Routes (Partner Guard)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:partner', 'guard:partner'])->group(function () {
    
    // Dashboard for all partners
    Route::get('/partner/dashboard', [DashboardController::class, 'index'])
        ->middleware('guard.role:partner,Corporate Partner')
        ->name('partner.dashboard');

    // Profile and company management
    Route::middleware('guard.permission:partner,view_profile')->group(function () {
        Route::get('/partner/profile', [ProfileController::class, 'show'])
            ->name('partner.profile.show');
        Route::get('/partner/profile/edit', [ProfileController::class, 'edit'])
            ->middleware('guard.permission:partner,edit_profile')
            ->name('partner.profile.edit');
        Route::put('/partner/profile', [ProfileController::class, 'update'])
            ->middleware('guard.permission:partner,edit_profile')
            ->name('partner.profile.update');
        Route::put('/partner/company', [ProfileController::class, 'updateCompany'])
            ->middleware('guard.permission:partner,manage_company_info')
            ->name('partner.company.update');
    });

    // Student management and nominations
    Route::middleware('guard.permission:partner,view_partner_students')->group(function () {
        Route::get('/partner/students', [StudentController::class, 'index'])
            ->name('partner.students.index');
        Route::post('/partner/students/{student}/nominate', [StudentController::class, 'nominate'])
            ->middleware('guard.permission:partner,nominate_students')
            ->name('partner.students.nominate');
        Route::get('/partner/students/{student}/progress', [StudentController::class, 'progress'])
            ->middleware('guard.permission:partner,track_student_progress')
            ->name('partner.students.progress');
    });

    // Program management (Educational Partners)
    Route::middleware('guard.role:partner,Educational Partner')->group(function () {
        Route::resource('/partner/programs', ProgramController::class, [
            'as' => 'partner'
        ]);
        Route::post('/partner/programs/{program}/activate', [ProgramController::class, 'activate'])
            ->name('partner.programs.activate');
    });

    // Job postings (Corporate and Recruitment Partners)
    Route::middleware('guard.permission:partner,create_job_postings')->group(function () {
        Route::resource('/partner/jobs', JobController::class, [
            'as' => 'partner'
        ]);
        Route::post('/partner/jobs/{job}/publish', [JobController::class, 'publish'])
            ->name('partner.jobs.publish');
    });

    // Recruitment Partner specific features
    Route::middleware('guard.role:partner,Recruitment Partner')->group(function () {
        Route::get('/partner/analytics', function () {
            return view('partner.analytics.index');
        })->middleware('guard.permission:partner,view_analytics')
          ->name('partner.analytics.index');

        Route::get('/partner/reports', function () {
            return view('partner.reports.index');
        })->middleware('guard.permission:partner,view_partner_reports')
          ->name('partner.reports.index');

        Route::post('/partner/reports/export', function () {
            return response()->json(['message' => 'Data exported']);
        })->middleware('guard.permission:partner,export_data')
          ->name('partner.reports.export');
    });

    // Communication
    Route::get('/partner/messages', function () {
        return view('partner.messages.index');
    })->middleware('guard.permission:partner,view_messages')
      ->name('partner.messages.index');

    Route::post('/partner/messages', function () {
        return response()->json(['message' => 'Message sent']);
    })->middleware('guard.permission:partner,send_messages')
      ->name('partner.messages.store');

    // Resources and documents
    Route::get('/partner/resources', function () {
        return view('partner.resources.index');
    })->middleware('guard.permission:partner,view_partner_resources')
      ->name('partner.resources.index');

    Route::post('/partner/resources/upload', function () {
        return response()->json(['message' => 'Resource uploaded']);
    })->middleware('guard.permission:partner,upload_resources')
      ->name('partner.resources.upload');

    // Support
    Route::get('/partner/support', function () {
        return view('partner.support.index');
    })->middleware('guard.permission:partner,view_support_tickets')
      ->name('partner.support.index');

    Route::post('/partner/support', function () {
        return response()->json(['message' => 'Support ticket created']);
    })->middleware('guard.permission:partner,submit_support_ticket')
      ->name('partner.support.store');
});
