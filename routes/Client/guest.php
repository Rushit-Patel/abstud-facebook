<?php
use App\Http\Controllers\Client\GuestLeadController;
use Illuminate\Support\Facades\Route;


Route::prefix('guest')->name('guest.')->group(function () {

    Route::get('session-branch/{branchId}', [GuestLeadController::class,'sessionBranch'])->name('session-branch');
    Route::get('no-session-branch', [GuestLeadController::class,'noSessionBranch'])->name('no-session-branch');

    Route::middleware(['branch.session'])->group(function () {
        Route::get('welcome', [GuestLeadController::class,'welcome'])->name('welcome');
        Route::post('welcome', [GuestLeadController::class,'checkMobile'])->name('welcome.post');
        Route::get('otp/{mobile}', [GuestLeadController::class,'otp'])->name('otp');
        Route::post('otp-verify', [GuestLeadController::class,'otpVerify'])->name('otp.verify');
        Route::get('personal-info/{mobileNo}', [GuestLeadController::class,'personalInfo'])->name('personal-info');
        Route::post('personal-info-store', [GuestLeadController::class,'personalInfoStore'])->name('personal.info.store');
        Route::get('service/{client}', [GuestLeadController::class,'service'])->name('service');
        Route::post('service-store', [GuestLeadController::class,'serviceStore'])->name('service.store');
        Route::get('academic-info/{client}/{service}', [GuestLeadController::class,'academicInfo'])->name('academic-info');
        Route::post('academic-info-store/{client}/{service}', [GuestLeadController::class,'academicInfoStore'])->name('academic-info.store');
        Route::get('immigration-history/{clientLead}', [GuestLeadController::class,'immigrationHistory'])->name('immigration-history');
        Route::post('immigration-history-store/{clientLead}', [GuestLeadController::class,'immigrationHistoryStore'])->name('immigration-history.store');
        Route::post('visit-history-store/{clientLead}', [GuestLeadController::class,'visitHistoryStore'])->name('visit-history.store');
        Route::get('thankyou/{clientLead}', [GuestLeadController::class,'thankyou'])->name('thankyou');
    });

});
