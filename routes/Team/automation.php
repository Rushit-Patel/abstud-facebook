<?php

use App\Http\Controllers\Team\Automation\AutomationController;
use App\Http\Controllers\Team\Automation\EmailAutomationController;
use App\Http\Controllers\Team\Automation\EmailCampaignController;
use App\Http\Controllers\Team\Automation\EmailAnalyticsController;
use App\Http\Controllers\Team\Automation\WhatsappAutomationController;
use App\Http\Controllers\Team\Automation\WhatsappCampaignController;
use Illuminate\Support\Facades\Route;



Route::prefix('automation')->name('automation.')->group(function () {

// Main Automation Dashboard
Route::get('/', [AutomationController::class, 'index'])->name('index');

// Email Automation Routes
Route::prefix('email')->name('email.')->group(function () {
    Route::get('/', [EmailAutomationController::class, 'index'])->name('index');
    
    // Email Campaigns
    Route::resource('campaigns', EmailCampaignController::class);
    Route::post('campaigns/{campaign}/toggle', [EmailCampaignController::class, 'toggle'])->name('campaigns.toggle');
    Route::post('campaigns/{campaign}/test', [EmailCampaignController::class, 'test'])->name('campaigns.test');
    
    // Email Logs
    Route::get('logs', [EmailAutomationController::class, 'logs'])->name('logs');
    Route::post('logs/{log}/retry', [EmailAutomationController::class, 'retryEmail'])->name('logs.retry');
});

// Analytics
Route::get('analytics', [EmailAnalyticsController::class, 'index'])->name('analytics');
Route::get('analytics/campaigns/{campaign}', [EmailAnalyticsController::class, 'campaignDetails'])->name('analytics.campaign');

// WhatsApp Automation Routes
Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('/', [WhatsappAutomationController::class, 'index'])->name('index');
    Route::post('send-message', [WhatsappAutomationController::class, 'sendMessage'])->name('send-message');
    Route::post('send-template', [WhatsappAutomationController::class, 'sendTemplate'])->name('send-template');
    Route::get('logs', [WhatsappAutomationController::class, 'logs'])->name('logs');
    Route::post('logs/{message}/retry', [WhatsappAutomationController::class, 'retryMessage'])->name('logs.retry');
    Route::get('templates/{templateName}/variables', [WhatsappAutomationController::class, 'getTemplateVariables'])->name('template-variables');
    
    // WhatsApp Campaigns
    Route::resource('campaigns', WhatsappCampaignController::class);
    Route::post('campaigns/{campaign}/execute', [WhatsappCampaignController::class, 'execute'])->name('campaigns.execute');
    Route::post('campaigns/{campaign}/toggle', [WhatsappCampaignController::class, 'toggle'])->name('campaigns.toggle');
    Route::post('campaigns/{campaign}/duplicate', [WhatsappCampaignController::class, 'duplicate'])->name('campaigns.duplicate');
    Route::post('campaigns/{campaign}/send-test', [WhatsappCampaignController::class, 'sendTest'])->name('campaigns.send-test');
    Route::post('campaigns/preview', [WhatsappCampaignController::class, 'preview'])->name('campaigns.preview');
});
});