<?php
use App\Http\Controllers\Team\Application\ApplicationController;
use App\Http\Controllers\Team\FollowUp\FollowUpController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:application:show'])->group(function () {

    Route::resource('application', ApplicationController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('application');

    Route::get('application/pending', [ApplicationController::class, 'ApplicationPending'])
        ->name('application.pending');


    Route::get('lead-follow-up/upcoming', [FollowUpController::class, 'upcoming'])
        ->name('lead-follow-up.upcoming');
    Route::get('lead-follow-up/complete', [FollowUpController::class, 'complete'])
        ->name('lead-follow-up.complete');
});
Route::get('lead-follow-up/all/{leadId}', [FollowUpController::class, 'getAllFollowUps'])
    ->name('lead-follow-up.all');

Route::middleware(['permission:follow-up:export'])->group(function () {
    Route::post('follow-up/export', [FollowUpController::class, 'exportFollow'])->name('follow-up.export');
});
