<?php

// routes/web.php - Facebook Integration Routes

use App\Http\Controllers\Facebook\FacebookWebhookController;
use App\Http\Controllers\Facebook\FacebookIntegrationController;
use App\Http\Controllers\Facebook\FacebookSocialAuthController;
use Illuminate\Support\Facades\Route;

// Facebook OAuth Routes (protected - auth required)
Route::middleware(['auth', 'web'])->prefix('facebook')->name('facebook.auth.')->group(function () {
    Route::get('auth', [FacebookSocialAuthController::class, 'redirectToFacebook'])->name('redirect');
    Route::get('callback', [FacebookSocialAuthController::class, 'handleFacebookCallback'])->name('callback');
    Route::post('disconnect', [FacebookSocialAuthController::class, 'disconnect'])->name('disconnect');
    Route::post('refresh-token', [FacebookSocialAuthController::class, 'refreshToken'])->name('refresh-token');
});

// Facebook Webhook Routes (public - no auth required)
Route::prefix('facebook')->group(function () {
    // Webhook verification and processing
    Route::get('webhook', [FacebookWebhookController::class, 'verify'])->name('facebook.webhook.verify');
    Route::post('webhook', [FacebookWebhookController::class, 'handle'])->name('facebook.webhook.handle');
});

// Public Legal Pages (no auth required)
Route::prefix('facebook')->name('facebook.')->group(function () {
    Route::get('privacy-policy', [FacebookIntegrationController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::get('terms-of-service', [FacebookIntegrationController::class, 'termsOfService'])->name('terms-of-service');
});

// Protected Facebook Integration Web Routes
Route::middleware(['auth', 'web'])->prefix('team/facebook-integration')->name('facebook.')->group(function () {
    
    // Dashboard
    Route::get('/', [FacebookIntegrationController::class, 'dashboard'])->name('dashboard');
    Route::get('overview', [FacebookIntegrationController::class, 'overview'])->name('overview');
    
    // Business Account Management
    Route::get('business-account', [FacebookIntegrationController::class, 'businessAccount'])->name('business-account');
    Route::post('business-account/connect', [FacebookIntegrationController::class, 'connectAccount'])->name('business-account.connect');
    Route::post('business-account/disconnect', [FacebookIntegrationController::class, 'disconnectAccount'])->name('business-account.disconnect');
    Route::post('business-account/refresh-token', [FacebookIntegrationController::class, 'refreshToken'])->name('business-account.refresh-token');
    Route::post('business-account/sync-pages', [FacebookIntegrationController::class, 'syncPages'])->name('business-account.sync-pages');
    
    // Facebook Pages Management
    Route::get('pages', [FacebookIntegrationController::class, 'pages'])->name('pages');
    Route::post('pages/{page}/toggle', [FacebookIntegrationController::class, 'togglePage'])->name('pages.toggle');
    Route::post('pages/{page}/sync-forms', [FacebookIntegrationController::class, 'syncLeadForms'])->name('pages.sync-forms');
    Route::post('pages/{page}/refresh', [FacebookIntegrationController::class, 'refreshPageData'])->name('pages.refresh');
    Route::post('pages/{page}/subscribe', [FacebookIntegrationController::class, 'subscribePageToWebhook'])->name('pages.subscribe');
    Route::post('pages/{page}/unsubscribe', [FacebookIntegrationController::class, 'unsubscribePageFromWebhook'])->name('pages.unsubscribe');
    
    // Lead Forms Management
    Route::get('lead-forms', [FacebookIntegrationController::class, 'leadForms'])->name('lead-forms');
    Route::get('lead-forms/{leadForm}', [FacebookIntegrationController::class, 'showLeadForm'])->name('lead-forms.show');
    Route::post('lead-forms/{leadForm}/toggle', [FacebookIntegrationController::class, 'toggleLeadForm'])->name('lead-forms.toggle');
    
    // Parameter Mapping Configuration
    Route::get('lead-forms/{leadForm}/mappings', [FacebookIntegrationController::class, 'mappings'])->name('lead-forms.mappings');
    Route::post('lead-forms/{leadForm}/mappings', [FacebookIntegrationController::class, 'saveMappings'])->name('lead-forms.mappings.save');
    Route::delete('mappings/{mapping}', [FacebookIntegrationController::class, 'deleteMapping'])->name('mappings.delete');
    
    // Custom Field Mappings
    Route::get('lead-forms/{leadForm}/custom-mappings', [FacebookIntegrationController::class, 'customMappings'])->name('lead-forms.custom-mappings');
    Route::post('lead-forms/{leadForm}/custom-mappings', [FacebookIntegrationController::class, 'saveCustomMappings'])->name('lead-forms.custom-mappings.save');
    Route::delete('custom-mappings/{mapping}', [FacebookIntegrationController::class, 'deleteCustomMapping'])->name('custom-mappings.delete');
    
    // Lead Management
    Route::get('leads', [FacebookIntegrationController::class, 'leads'])->name('leads');
    Route::post('leads/sync', [FacebookIntegrationController::class, 'syncLeads'])->name('leads.sync');
    Route::get('leads/{lead}', [FacebookIntegrationController::class, 'showLead'])->name('leads.show');
    Route::post('leads/{lead}/retry', [FacebookIntegrationController::class, 'retryLead'])->name('leads.retry');
    Route::post('leads/{lead}/mark-processed', [FacebookIntegrationController::class, 'markProcessed'])->name('leads.mark-processed');
    
    // Lead Sources & Analytics
    Route::get('analytics', [FacebookIntegrationController::class, 'analytics'])->name('analytics');
    Route::get('lead-sources', [FacebookIntegrationController::class, 'leadSources'])->name('lead-sources');
    
    // Webhook Settings
    Route::get('webhook-settings', [FacebookIntegrationController::class, 'webhookSettings'])->name('webhook-settings');
    Route::post('webhook-settings', [FacebookIntegrationController::class, 'saveWebhookSettings'])->name('webhook-settings.save');
    Route::post('webhook-settings/test', [FacebookIntegrationController::class, 'testWebhook'])->name('webhook-settings.test');
    Route::post('webhook-settings/regenerate-token', [FacebookIntegrationController::class, 'regenerateWebhookToken'])->name('webhook-settings.regenerate-token');
    
    // General Settings
    Route::get('settings', [FacebookIntegrationController::class, 'settings'])->name('settings');
    Route::post('settings', [FacebookIntegrationController::class, 'saveSettings'])->name('settings.save');
    
    // System Variables Reference
    Route::get('system-variables', [FacebookIntegrationController::class, 'systemVariables'])->name('system-variables');
    
    // AJAX Routes for dynamic data
    Route::get('api/stats', [FacebookIntegrationController::class, 'getStats'])->name('api.stats');
    Route::get('api/recent-leads', [FacebookIntegrationController::class, 'getRecentLeads'])->name('api.recent-leads');
    Route::get('api/system-variables', [FacebookIntegrationController::class, 'getSystemVariables'])->name('api.system-variables');
    Route::post('api/test-connection', [FacebookIntegrationController::class, 'testConnection'])->name('api.test-connection');
});
