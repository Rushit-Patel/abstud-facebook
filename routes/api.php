<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Facebook\FacebookIntegrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Facebook Integration API Routes
Route::prefix('facebook-integration')->group(function () {
    // Public endpoint for console command usage
    Route::post('/leads/sync', [FacebookIntegrationController::class, 'apiSyncLeads'])
        ->name('api.facebook.leads.sync');
    
    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/leads/sync-authenticated', [FacebookIntegrationController::class, 'apiSyncLeads'])
            ->name('api.facebook.leads.sync.authenticated');
    });
});
