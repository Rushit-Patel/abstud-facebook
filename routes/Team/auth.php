<?php

use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Admin Authentication Routes
Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AdminAuthController::class, 'login'])->name('login.store');
Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

// Debug route to test authentication
Route::get('debug-auth', function() {
    return response()->json([
        'authenticated' => Auth::guard('web')->check(),
        'user' => Auth::guard('web')->user(),
        'session_id' => session()->getId(),
        'guard' => config('auth.defaults.guard'),
        'provider' => config('auth.guards.web.provider'),
    ]);
})->name('debug.auth');

// Password Reset Routes
Route::get('forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');
