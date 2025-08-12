<?php
use App\Http\Controllers\Team\FollowUp\FollowUpController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:follow-up:show'])->group(function () {
    Route::resource('lead-follow-up', FollowUpController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('lead-follow-up');
    Route::get('lead-follow-up/pending', [FollowUpController::class, 'pending'])
        ->name('lead-follow-up.pending');
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
