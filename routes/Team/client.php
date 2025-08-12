<?php

use App\Http\Controllers\Team\Document\DocumentChecklistController;
use App\Http\Controllers\Team\Lead\ClientProfileController;
use App\Http\Controllers\Team\Lead\LeadController;
use Illuminate\Support\Facades\Route;


Route::resource('client', ClientProfileController::class)
      ->only(['show'])
      ->names('client');

Route::get('/coaching/{client}', [ClientProfileController::class, 'getCoaching'])->name('get.coaching');
Route::get('/registrations/{client}', [ClientProfileController::class, 'getRegistrations'])->name('get.registrations');
Route::delete('/registrations/{id}', [ClientProfileController::class, 'RegistrationsDestroy'])->name('registrations.destroy');
Route::delete('/invoice/{id}', [ClientProfileController::class, 'InvoiceDestroy'])->name('invoice.Destroy');
// Document tabs
Route::get('/document/{id}', [DocumentChecklistController::class, 'DocumentCheckList'])->name('document');
Route::get('/document/{id}/create', [DocumentChecklistController::class, 'DocumentCheckListCreate'])->name('document.create');
Route::put('/document/{id}/store', [DocumentChecklistController::class, 'DocumentCheckListStore'])->name('document-checklist.store');
Route::post('document/upload/{clientId}/store', [DocumentChecklistController::class, 'documentUploadeStore'])
    ->name('document-uploaded.store');

    Route::post('document/status/{clientId}/update', [DocumentChecklistController::class, 'documentStatusUpdate'])
    ->name('document-status.update');



Route::post('/upload-avatar', [ClientProfileController::class, 'uploadAvatar'])->name('avatar.upload');
Route::post('/remove-avatar', [ClientProfileController::class, 'removeAvatar'])->name('avatar.remove');

Route::post('/education-details/{client}', [ClientProfileController::class, 'EducationDetailsProfile'])->name('education.details.profile');
Route::post('/english-proficiency-tests-details/{client}', [ClientProfileController::class, 'EnglishProficiencyTestsProfile'])->name('english.proficiency.tests.profile');
Route::post('/employment-details/{client}', [ClientProfileController::class, 'EmploymentDetailsProfile'])->name('employment.details.profile');
Route::post('/passport-details/{client}', [ClientProfileController::class, 'PassportProfile'])->name('passport.details.profile');
Route::post('/rejection-details/{client}', [ClientProfileController::class, 'RejectionProfile'])->name('rejection.details.profile');
Route::post('/relative-details/{client}', [ClientProfileController::class, 'RelativrProfile'])->name('relative.details.profile');
Route::post('/visited-details/{client}', [ClientProfileController::class, 'VisitedCountryDetails'])->name('visited.details.profile');
Route::post('/demo-details/{client}', [ClientProfileController::class, 'DemoDetails'])->name('demo.details.profile');
Route::post('/register-details/{client}', [ClientProfileController::class, 'RegisterDetails'])->name('register.details.profile');
Route::post('/invoice-details/{client}', [ClientProfileController::class, 'InvoiceDetails'])->name('invoice.details.profile');


Route::middleware(['permission:lead:show'])->group(function () {
    Route::resource('lead', LeadController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('lead');
});

Route::middleware(['permission:lead:export'])->group(function () {
    Route::post('leads/export', [LeadController::class, 'exportLeads'])->name('leads.export');

});


Route::post('lead/assign-owner', [LeadController::class, 'assignOwner'])->name('lead.assign.owner');
Route::post('lead/tag', [LeadController::class, 'updateTag'])->name('lead.ajax.update.tag');
Route::get('lead/get-tags', [LeadController::class, 'getTags'])->name('lead.ajax.get.tags');

      // Team Dashboard Routes
Route::get('lead/new-lead/{client}', [LeadController::class, 'NewLead'])->name('client.new.lead');
Route::post('lead/new-lead/store/{client}', [LeadController::class, 'NewLeadStore'])->name('client.new.lead.store');
