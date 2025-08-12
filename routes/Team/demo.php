<?php
use App\Http\Controllers\Team\Demo\DemoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:demo:show'])->group(function () {
    Route::resource('demo', DemoController::class)
        ->only(['edit', 'update', 'destroy'])
        ->names('demo');

    Route::get('demo/pending', [DemoController::class, 'pending'])
        ->name('demo.pending');
    Route::get('demo/attended', [DemoController::class, 'attended'])
        ->name('demo.attended');
    Route::get('demo/cancelled', [DemoController::class, 'cancelled'])
        ->name('demo.cancelled');
});
Route::middleware(['permission:demo:export'])->group(function () {
    Route::post('demo/export', [DemoController::class, 'exportDemo'])->name('demo.export');
});
