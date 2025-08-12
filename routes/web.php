<?php

use App\Http\Controllers\Team\FcmTokenController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

// Root redirect based on company setup status
Route::get('/', function () {
    if (!\App\Models\CompanySetting::isSetupCompleted()) {
        return redirect()->route('setup.company');
    }
    return redirect()->route('team.dashboard');
})->name('home');

// Setup Routes (accessible without company setup)
Route::prefix('setup')->name('setup.')->group(function () {
    // Step 1: Company Information + Branding
    Route::get('company', [SetupController::class, 'showCompanySetup'])->name('company');
    Route::post('company', [SetupController::class, 'storeCompanySetup'])->name('company.store');

    // Step 2: Branch Setup
    Route::get('branch', [SetupController::class, 'showBranchSetup'])->name('branch');
    Route::post('branch', [SetupController::class, 'storeBranchSetup'])->name('branch.store');

    // Step 3: Admin User Setup (Final Step)
    Route::get('admin', [SetupController::class, 'showAdminSetup'])->name('admin');
    Route::post('admin', [SetupController::class, 'storeAdminSetup'])->name('admin.store');

    // AJAX routes for location dependencies
    Route::get('states/{country}', [SetupController::class, 'getStatesByCountry'])->name('states');
    Route::get('cities/{state}', [SetupController::class, 'getCitiesByState'])->name('cities');
});


Route::post('/save-fcm-token', [FcmTokenController::class, 'store']);
Route::post('/remove-fcm-token',  [FcmTokenController::class, 'destroy']);


// Admin Routes
Route::prefix('team')->name('team.')->middleware('company.setup')->group(function () {
    // Admin Authentication (Public Routes)
    require __DIR__.'/Team/auth.php';

    // Protected Admin Routes
    Route::middleware(['auth:web'])->group(function () {
        // Dashboard Module
        require __DIR__.'/Team/dashboard.php';

        // Follow Up Module
        require __DIR__.'/Team/follow-up.php';

        // Invoice Module
        require __DIR__.'/Team/invoice.php';

        // Coaching Module
        require __DIR__.'/Team/coaching.php';

        // Application Module
        require __DIR__.'/Team/application.php';

        // Demo Module
        require __DIR__.'/Team/demo.php';

        // System Settings Module
        require __DIR__.'/Team/systemSettings.php';

        //Todo Module
        require __DIR__.'/Team/todo.php';

        //Task Module
        require __DIR__.'/Team/task.php';

        //Client Profile
        require __DIR__.'/Team/client.php';

        // Team Profile
        require __DIR__.'/Team/profile.php';

        //Automation Module
        require __DIR__.'/Team/automation.php';
    });
    require __DIR__.'/Team/ajax.php';
});

// Facebook Integration Routes
require __DIR__.'/facebook_web.php';

// Client Routes
Route::prefix('client')->name('client.')->middleware('company.setup')->group(function () {
    require __DIR__.'/Client/guest.php';
});

// Partner Routes
Route::prefix('partner')->name('partner.')->middleware('company.setup')->group(function () {

});
