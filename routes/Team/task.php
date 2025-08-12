<?php

use App\Http\Controllers\Team\Task\TaskController;
use Illuminate\Support\Facades\Route;

// Task Management Routes
Route::prefix('task')->name('task.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::get('/create', [TaskController::class, 'create'])->name('create');
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::get('/{task}', [TaskController::class, 'show'])->name('show');
    Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    
    // Task Status Routes
    Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
    Route::patch('/{task}/progress', [TaskController::class, 'updateProgress'])->name('update-progress');
    Route::patch('/{task}/toggle-archive', [TaskController::class, 'toggleArchive'])->name('toggle-archive');
    
    // Task Assignment Routes
    Route::post('/{task}/assign', [TaskController::class, 'assignUsers'])->name('assign');
    Route::delete('/{task}/assignment/{assignment}', [TaskController::class, 'removeAssignment'])->name('remove-assignment');
    
    // Task Comments Routes
    Route::post('/{task}/comments', [TaskController::class, 'storeComment'])->name('store-comment');
    Route::delete('/comments/{comment}', [TaskController::class, 'destroyComment'])->name('destroy-comment');
    
    // Task Attachments Routes
    Route::post('/{task}/attachments', [TaskController::class, 'storeAttachment'])->name('store-attachment');
    Route::delete('/attachments/{attachment}', [TaskController::class, 'destroyAttachment'])->name('destroy-attachment');
    
    // Task Time Logging Routes
    Route::post('/{task}/time-logs', [TaskController::class, 'storeTimeLog'])->name('store-time-log');
    Route::delete('/time-logs/{timeLog}', [TaskController::class, 'destroyTimeLog'])->name('destroy-time-log');
    
    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/categories', [TaskController::class, 'getCategories'])->name('categories');
        Route::get('/priorities', [TaskController::class, 'getPriorities'])->name('priorities');
        Route::get('/statuses', [TaskController::class, 'getStatuses'])->name('statuses');
        Route::get('/users', [TaskController::class, 'getUsers'])->name('users');
        Route::get('/{task}/activity', [TaskController::class, 'getActivity'])->name('activity');
        // Temporary file upload for Dropzone
        Route::post('/temp/store-file', [TaskController::class, 'storeTempFile'])->name('temp.store-file');
        Route::delete('/temp/delete-file', [TaskController::class, 'deleteTempFile'])->name('temp.delete-file');
    });
});