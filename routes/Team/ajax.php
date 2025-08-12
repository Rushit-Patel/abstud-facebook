<?php

use App\Http\Controllers\Team\AjaxController;
use Illuminate\Support\Facades\Route;

Route::get('/ajax/lead-sub-status', [AjaxController::class, 'getSubStatuses'])->name('ajax.lead.sub.status');
Route::get('/branch/country-state-city', [AjaxController::class, 'getLocationByBranch'])->name('ajax.branch.country.state.city');
Route::get('/branch/user', [AjaxController::class, 'getBranchUser'])->name('ajax.branch.user');
Route::get('/get-education-streams/{levelId}', [AjaxController::class, 'getStreams'])->name('education.get-streams');
Route::get('states/{country}', [AjaxController::class, 'getStatesByCountry'])->name('get.states');
Route::get('cities/{state}', [AjaxController::class, 'getCitiesByState'])->name('get.cities');
Route::get('leads/search', [AjaxController::class, 'search'])->name('leads.search');

// Profile Page Model:
Route::get('get-education-form', [AjaxController::class, 'getEducation'])->name('get.education.form');
Route::get('get-english-proficiency-test', [AjaxController::class, 'getEnglishProficiencyTest'])->name('get.english.proficiency.test');
Route::get('get-employment-details', [AjaxController::class, 'getEmployment'])->name('get.employment.details');
Route::get('get-passport-details', [AjaxController::class, 'getPassport'])->name('get.passport.details');
Route::get('get-rejection-details', [AjaxController::class, 'getRejectionData'])->name('get.rejection.details');
Route::get('get-relative-details', [AjaxController::class, 'getRelativeData'])->name('get.relative.details');
Route::get('get-visited-details', [AjaxController::class, 'getVisitedData'])->name('get.visited.details');
Route::get('get-demo-details', [AjaxController::class, 'DemoDetails'])->name('get.demo.details');
Route::get('get-register-details', [AjaxController::class, 'RegisterDetails'])->name('get.register.details');
// Coaching Wise Batch Get Single
Route::get('get-coaching-batch', [AjaxController::class, 'getBatchesByCoaching'])->name('get.coaching.batch');
// Coaching Wise Batch Get Multiple
Route::get('get-coaching-batch-multiple', [AjaxController::class, 'getBatchesByCoachingMultiple'])->name('get.coaching.batch-multiple');
Route::get('search-attendance-coaching-batch', [AjaxController::class, 'searchAttendanceCoachingBatch'])->name('search.attendance.coaching.batch');
Route::get('get-invoice-details', [AjaxController::class, 'InvoiceDetails'])->name('get.invoice.details');
Route::get('get-service-details', [AjaxController::class, 'getService'])->name('get.service.details');
Route::get('get-coaching-material', [AjaxController::class, 'getCoachingMaterial'])->name('get.coaching.material');

// Foreign Country Ajax Call
Route::get('/foreign-states/{country_id}', [AjaxController::class, 'getForeignStates'])->name('get.foreign.states');
Route::get('/foreign-cities/{state_id}', [AjaxController::class, 'getForeignCities'])->name('get.foreign.cities');

// Client Document

Route::get('/client-document', [AjaxController::class, 'viewDocument'])
     ->name('view-client-document');


// Exam Data get

// routes/web.php
Route::get('/exam-modes/{testId}', [AjaxController::class, 'getExamModes'])->name('exam.modes');


// Get Branch wise Users:
Route::get('get-users-by-branch', [AjaxController::class, 'getUsersByBranch'])->name('get.users.by.branch');

// Notification Management Routes:
Route::post('notifications/{notificationId}/mark-read', [AjaxController::class, 'markNotificationAsRead'])->name('notifications.mark.read');
Route::post('notifications/mark-all-read', [AjaxController::class, 'markAllNotificationsAsRead'])->name('notifications.mark.all.read');
Route::get('notifications/count', [AjaxController::class, 'getNotificationCount'])->name('notifications.count');


