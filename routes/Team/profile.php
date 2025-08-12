<?php

use App\Http\Controllers\Team\DashboardController;
use App\Http\Controllers\Team\FollowUp\FollowUpController;
use App\Http\Controllers\Team\Lead\LeadController;
use App\Http\Controllers\Team\ProfileController;
use Illuminate\Support\Facades\Route;

// Profile Routes
Route::get('profile/', [ProfileController::class, 'show'])->name('profile');
Route::get('profile-edit/{user}', [ProfileController::class, 'ProfileEdit'])->name('profile.edit');
Route::put('profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
