<?php

use App\Http\Controllers\Team\ProfileController;
use App\Http\Controllers\Team\SystemSettings\{
    EmailTemplateController,
    EnglishProficiencyTestController,
    SystemSettingsController,
    CompanySettingsController,
    BranchesController,
    UsersController,
    RolesController,
    PermissionsController,
    CountriesController,
    StatesController,
    CitiesController,
    LeadTypesController,
    PurposeController,
    SourceController,
    LeadStatusController,
    LeadSubStatusController,
    CoachingController,
    BatchController,
    ForeignCountryController,
    EducationLevelController,
    EducationStreamController,
    EducationBoardController,
    TagsController,
    OtherVisaTypeController,
    MaritalStatusController,
    PurposeVisitController,
    VisitorApplicantController,
    TypeOfRelativeController,
    NotificationTypeController,
    NotificationManagementController,
    NotificationConfigController,
    WhatsappController,
    CoachingLengthController,
    ExamCenterController,
    DocumentCategoryController,
    BillingCompanyController,
    DocumentCheckListController,
    ServiceController,
    PaymentModeController,
    TaskCategoryController,
    TaskPriorityController,
    TaskStatusController,
    ExamWayController,
    ExamModeController,
    CoachingMaterialController,
    ForeignStateController,
    ForeignCityController,
    CourseController,
    CourseTypeController,
    CampusController,
    AssociateController,
    IntakeController,
    UniversityController,
    UniversityCourseKeyController,
    UniversityCourseController,
    ApplicationTypeController
};

use Illuminate\Support\Facades\Route;

// Settings Routes
Route::get('settings', [ProfileController::class, 'settings'])->name('settings');

// System Settings Routes - Master Administration Panel
Route::prefix('settings')->name('settings.')->group(function () {
    // Main settings dashboard
    Route::get('/', [SystemSettingsController::class, 'index'])->name('index');

    // Company Settings
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [CompanySettingsController::class, 'index'])->name('index');
        Route::get('edit', [CompanySettingsController::class, 'edit'])->name('edit');
        Route::put('update', [CompanySettingsController::class, 'update'])->name('update');
        Route::post('logo/upload', [CompanySettingsController::class, 'uploadLogo'])->name('logo.upload');
        Route::delete('logo/remove', [CompanySettingsController::class, 'removeLogo'])->name('logo.remove');
        Route::post('favicon/upload', [CompanySettingsController::class, 'uploadFavicon'])->name('favicon.upload');
        Route::delete('favicon/remove', [CompanySettingsController::class, 'removeFavicon'])->name('favicon.remove');
        Route::post('test-smtp', [CompanySettingsController::class, 'testSmtp'])->name('test-smtp');
        Route::get('preview-smtp-email', [CompanySettingsController::class, 'previewSmtpTestEmail'])->name('preview-smtp-email');
        Route::get('delivery-troubleshooting', [CompanySettingsController::class, 'getDeliveryTroubleshooting'])->name('delivery-troubleshooting');
        Route::get('smtp-status', [CompanySettingsController::class, 'getSmtpStatus'])->name('smtp-status');
        Route::get('email-logs', [CompanySettingsController::class, 'checkEmailLogs'])->name('email-logs');

        Route::post('whatsapp-update', [CompanySettingsController::class, 'whatsappUpdate'])->name('whatsapp-update');

        // AJAX routes for location dependencies
        Route::get('states/{country}', [CompanySettingsController::class, 'getStatesByCountry'])->name('states');
        Route::get('cities/{state}', [CompanySettingsController::class, 'getCitiesByState'])->name('cities');
    });

    // Branch Management
    Route::resource('branches', BranchesController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('branches');

    // Role Management
    Route::resource('roles', RolesController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('roles');

    // Permission Management
    Route::resource('permissions', PermissionsController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('permissions');

    // User Management
    Route::resource('users', UsersController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('users');
    Route::patch('users/{user}/toggle-status', [UsersController::class, 'toggleStatus'])
        ->name('users.toggle-status');
    Route::get('users/role-permissions', [UsersController::class, 'getRolePermissions'])
        ->name('users.role-permissions');

    // Country Management
    Route::resource('countries', CountriesController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('countries');
    Route::patch('countries/{country}/toggle-status', [CountriesController::class, 'toggleStatus'])
        ->name('countries.toggle-status');

    // State Management
    Route::resource('states', StatesController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('states');
    Route::patch('states/{state}/toggle-status', [StatesController::class, 'toggleStatus'])
        ->name('states.toggle-status');

    // City Management
    Route::resource('cities', CitiesController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('cities');
    Route::patch('cities/{city}/toggle-status', [CitiesController::class, 'toggleStatus'])
        ->name('cities.toggle-status');

    // Lead Type Management
    Route::resource('lead-types', LeadTypesController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('lead-types');
    Route::patch('lead-types/{leadType}/toggle-status', [LeadTypesController::class, 'toggleStatus'])
        ->name('lead-types.toggle-status');

    // Purpose Management
    Route::resource('purpose', PurposeController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('purpose');

    // Source Management
    Route::resource('source', SourceController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('source');

    // Lead Status Management
    Route::resource('lead-status', LeadStatusController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('lead-status');

    // Lead Sub Status Management
    Route::resource('lead-sub-status', LeadSubStatusController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('lead-sub-status');

    // Coaching Management
    Route::resource('coaching', CoachingController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('coaching');

    // Batch Management
    Route::resource('batch', BatchController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('batch');

    // Country Management
    Route::resource('foreign-country', ForeignCountryController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('foreign-country');

    // State Management
    Route::resource('foreign-state', ForeignStateController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('foreign-state');

    // City Management
    Route::resource('foreign-city', ForeignCityController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('foreign-city');

    // Email Templates Management
    Route::resource('email-templates', EmailTemplateController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('email-templates');

    Route::get('email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])
        ->name('email-templates.preview');

    Route::post('email-templates/{emailTemplate}/test', [EmailTemplateController::class, 'test'])
        ->name('email-templates.test');

    // Additional email template routes
    Route::post('email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])
        ->name('email-templates.duplicate');
    Route::patch('email-templates/{emailTemplate}/toggle-status', [EmailTemplateController::class, 'toggleStatus'])
        ->name('email-templates.toggle-status');

    //English Proficiency Test
    Route::resource('english-proficiency-test', EnglishProficiencyTestController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('english-proficiency-test');

    //Education Level
    Route::resource('education-level', EducationLevelController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('education-level');

    //Education Level
    Route::resource('education-stream', EducationStreamController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('education-stream');

    //Education Board
    Route::resource('education-board', EducationBoardController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('education-board');

    // Lead Tags
    Route::resource('tags', TagsController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('tags');

    // Other Visa Type
    Route::resource('other-visa-type', OtherVisaTypeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('other-visa-type');

    // Marital Status
    Route::resource('marital-status', MaritalStatusController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('marital-status');

    // Purpose of Visit
    Route::resource('purpose-visit', PurposeVisitController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('purpose-visit');

    // Visitor Applicant
    Route::resource('visitor-applicant', VisitorApplicantController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('visitor-applicant');

    // Type of Relative
    Route::resource('type-of-relative', TypeOfRelativeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('type-of-relative');

    // Notification Type Management
    Route::resource('notification-type', NotificationTypeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('notification-type');
    Route::patch('notification-type/{notificationType}/toggle-status', [NotificationTypeController::class, 'toggleStatus'])
        ->name('notification-type.toggle-status');

    // Notification Configuration Management
    Route::resource('notification-config', NotificationConfigController::class)
        ->only(['index', 'show', 'edit', 'update'])
        ->names('notification-config');
    Route::patch('notification-config/{notificationConfig}/toggle-channel', [NotificationConfigController::class, 'toggleChannel'])
        ->name('notification-config.toggle-channel');
    Route::post('notification-config/bulk-update', [NotificationConfigController::class, 'bulkUpdate'])
        ->name('notification-config.bulk-update');

    // WhatsApp Templates Management
    Route::prefix('whatsapp-templates')->name('whatsapp-templates.')->group(function () {
        Route::get('/', [WhatsappController::class, 'index'])->name('index');
        Route::get('/view/{templateName}', [WhatsappController::class, 'view'])->name('view');
        Route::get('/all-templates', [WhatsappController::class, 'getAllTemplates'])->name('all-templates');
        Route::get('/provider/{providerId}/templates', [WhatsappController::class, 'getProviderTemplates'])->name('provider-templates');
        Route::get('/provider/{providerId}/user-messages', [WhatsappController::class, 'getUserMessages'])->name('user-messages');

        // Variable Mappings
        Route::post('/variable-mappings/{templateName}', [WhatsappController::class, 'saveVariableMappings'])->name('save-variable-mappings');
        Route::get('/variable-mappings/{templateName}', [WhatsappController::class, 'getVariableMappings'])->name('get-variable-mappings');
    });

    // coaching-length Management
    Route::resource('coaching-length', CoachingLengthController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('coaching-length');

    // Exam Center Management
    Route::resource('exam-center', ExamCenterController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('exam-center');

        // document-category Management
    Route::resource('document-category', DocumentCategoryController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('document-category');

    // Billing Company Management
    Route::resource('billing-company', BillingCompanyController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('billing-company');

    // Document Check list Management
    Route::resource('document-check-list', DocumentCheckListController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('document-check-list');

    // Service Management
    Route::resource('service', ServiceController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('service');

    // Payment Mode Management
    Route::resource('payment-mode', PaymentModeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('payment-mode');

    // Task Category Management
    Route::resource('task-category', TaskCategoryController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('task-category');

    // Task Category Management
    Route::resource('task-priority', TaskPriorityController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('task-priority');

    // Task Status Management
    Route::resource('task-status', TaskStatusController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('task-status');

    // Exam Way Management
    Route::resource('exam-way', ExamWayController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('exam-way');

    // Exam Mode Management
    Route::resource('exam-mode', ExamModeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('exam-mode');

    // Exam Mode Management
    Route::resource('coaching-material', CoachingMaterialController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('coaching-material');

    Route::get('/coaching-material/stock/{material}', [CoachingMaterialController::class, 'CoachingMaterialStock'])->name('coaching-material.stock');
    Route::put('/coaching-material/stock/{material}/store', [CoachingMaterialController::class, 'CoachingMaterialStore'])->name('coaching-material.stock.store');
    Route::get('/coaching-material/stock/{material}/list', [CoachingMaterialController::class, 'CoachingMaterialStockList'])->name('coaching-material.stock.list');
    Route::get('/coaching-material/stock/{material}/edit/{id}', [CoachingMaterialController::class, 'CoachingMaterialStockEdit'])->name('coaching-material.stock.edit');
    Route::put('/coaching-material/stock/{material}/update/{id}', [CoachingMaterialController::class, 'CoachingMaterialStockUpdate'])->name('coaching-material.stock.update');
    Route::delete('/coaching-material/stock/destroy/{id}', [CoachingMaterialController::class, 'CoachingMaterialStockDestroy'])->name('coaching-material.stock.destroy');

    // course Management
    Route::resource('course', CourseController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('course');

    // course Management
    Route::resource('course-type', CourseTypeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('course-type');

    // course Management
    Route::resource('campus', CampusController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('campus');

    //Associate  Management
    Route::resource('associate', AssociateController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('associate');

    //intake  Management
    Route::resource('intake', IntakeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('intake');

    // University Management
    Route::resource('university', UniversityController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('university');

    // University Course Management
    Route::resource('university-course', UniversityCourseController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('university-course');

    // University Course Key Management
    Route::resource('university-course-key', UniversityCourseKeyController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('university-course-key');

    // Application - Type Management
    Route::resource('application-type', ApplicationTypeController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->names('application-type');
});
