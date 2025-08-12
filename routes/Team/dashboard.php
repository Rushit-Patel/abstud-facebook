<?php

use App\Http\Controllers\Team\DashboardController;
use App\Http\Controllers\Team\FollowUp\FollowUpController;
use App\Http\Controllers\Team\Lead\LeadController;
use App\Http\Controllers\Team\ProfileController;
use Illuminate\Support\Facades\Route;

// Team Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('dashboard1', [DashboardController::class, 'index1'])->name('dashboard.index1');
Route::post('dashboard/filter-leads', [DashboardController::class, 'filterLeads'])->name('dashboard.filter-leads');
Route::get('dashboard/filter-leads-analysis', [DashboardController::class, 'filterLeadsAnalysis'])->name('dashboard.filter-leads-analysis');
Route::get('dashboard/filter-performance-matrix', [DashboardController::class, 'filterLeadsPerformanceMatrix'])->name('dashboard.performance-matrix');
