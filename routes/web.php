<?php
// routes/web.php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| This file defines all web routes for the MOTAC Integrated Resource Management System.
| Includes controller and Livewire routes for both public and authenticated users.
|
| IMPORTANT: This application uses a custom SuffixedTranslator that automatically
| loads language files with locale suffixes (e.g., forms_en.php, forms_ms.php)
| based on the current application locale.
|--------------------------------------------------------------------------
*/

// --------------------------------------------------
// Route Imports
// --------------------------------------------------

// Controllers
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebhookController;

// Livewire Components
use App\Livewire\ContactUs as ContactUsLW;
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Approval\Dashboard as ApprovalDashboardLW;
use App\Livewire\ResourceManagement\LoanApplication\ApplicationForm as LoanApplicationFormLW;
use App\Livewire\ResourceManagement\MyApplications\Loan\Index as MyLoanApplicationsIndexLW;
use App\Livewire\Settings\Departments\Index as SettingsDepartmentsIndexLW;
use App\Livewire\Settings\Permissions\Index as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Positions\Index as SettingsPositionsIndexLW;
use App\Livewire\Settings\Roles\Index as SettingsRolesIndexLW;
use App\Livewire\Settings\Users\Create as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\Edit as SettingsUsersEditLW;
use App\Livewire\Settings\Users\Index as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\Show as SettingsUsersShowLW;

// Helpdesk Livewire Components
use App\Livewire\Shared\Notifications\NotificationsList;
use App\Livewire\Helpdesk\CreateTicketForm;
use App\Livewire\Helpdesk\MyTicketsIndex;
use App\Livewire\Helpdesk\TicketDetails;
use App\Livewire\Helpdesk\Admin\TicketManagement as AdminTicketManagementLW;

use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// Public Routes (accessible without authentication)
// --------------------------------------------------

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Static policy and terms pages
Route::view('/privacy-policy', 'policy')->name('policy');
Route::view('/terms-of-service', 'terms')->name('terms');

// Contact Us page via Livewire
Route::get('/contact-us', ContactUsLW::class)->name('contact-us');

// --------------------------------------------------
// Language Switching Route (accessible to all users)
// --------------------------------------------------
// This route allows users to switch between 'en' and 'ms' locales.
// The LanguageController handles session and user preference updates.
Route::get('lang/{lang}', [LanguageController::class, 'swap'])
    ->where('lang', 'en|ms')
    ->name('language.swap');

// --------------------------------------------------
// Authenticated Routes (protected by Jetstream/Sanctum/verified middleware)
// --------------------------------------------------
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Main user dashboard
    Route::get('/dashboard', DashboardLW::class)->name('dashboard');

    // Admin dashboard (restricted to Admin and IT Admin roles)
    Route::get('/dashboard/admin', AdminDashboardLW::class)
        ->name('admin.dashboard')
        ->middleware(['role:Admin|IT Admin']);

    // Notifications
    Route::get('/notifications', NotificationsList::class)->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // User Profile (Jetstream)
    Route::get('/user/profile', function () {
        return view('profile.show');
    })->name('profile.show');

    // ICT Equipment Loan Application Routes
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/create', LoanApplicationFormLW::class)->name('create');
        Route::get('/my-applications', MyLoanApplicationsIndexLW::class)->name('my-applications.index');
        Route::get('/{loanApplication}/issue', [LoanTransactionController::class, 'createIssue'])
            ->name('issue')
            ->middleware('can:issue,loanApplication');
        Route::post('/{loanApplication}/issue', [LoanTransactionController::class, 'storeIssue'])
            ->name('issue.store')
            ->middleware('can:issue,loanApplication');
        Route::get('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'createReturn'])
            ->name('return')
            ->middleware('can:return,loanTransaction');
        Route::post('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'storeReturn'])
            ->name('return.store')
            ->middleware('can:return,loanTransaction');
    });

    // Equipment management (view only for authenticated users)
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // Approval workflow (dashboard and history)
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', ApprovalDashboardLW::class)
            ->name('dashboard')
            ->middleware(['view_approval_tasks']);
        Route::get('/history', [ApprovalController::class, 'history'])
            ->name('history')
            ->middleware(['view_approval_history']);
    });

    // Admin Resource Management
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
        Route::resource('equipment', AdminEquipmentController::class)->except(['show']);
        Route::get('equipment/issued-loans', IssuedLoans::class)->name('equipment.issued-loans');
        Route::get('equipment-items', AdminEquipmentIndexLW::class)->name('equipment-items.index');
    });

    // Reports Module
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/equipment-inventory', [ReportController::class, 'equipmentInventory'])->name('equipment-inventory');
        Route::get('/loan-applications', [ReportController::class, 'loanApplications'])->name('loan-applications');
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history');
        Route::get('/loan-status-summary', [ReportController::class, 'loanStatusSummary'])->name('loan-status-summary');
        Route::get('/utilization-report', [ReportController::class, 'utilizationReport'])->name('utilization-report');
        Route::get('/user-activity-log', [ReportController::class, 'userActivityLog'])->name('user-activity-log');
    });

    // Helpdesk Module
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        Route::get('/', MyTicketsIndex::class)->name('index');
        Route::get('/create', CreateTicketForm::class)->name('create');
        Route::get('/{ticket}', TicketDetails::class)->name('show');
        Route::middleware(['role:Admin|IT Admin|Helpdesk Agent'])->group(function () {
            Route::get('/admin/tickets', AdminTicketManagementLW::class)->name('admin.index');
            // Additional admin-specific helpdesk routes as needed
        });
    });

    // Admin - Settings Panel (Accessible only by Admin role)
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        Route::get('/', SettingsUsersIndexLW::class)->name('index');
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index');
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create');
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit');
        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index');
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index');
        Route::resource('grades', AdminGradeController::class)->parameters(['grades' => 'grade']);
        Route::get('/departments', SettingsDepartmentsIndexLW::class)->name('departments.index');
        Route::get('/positions', SettingsPositionsIndexLW::class)->name('positions.index');
        Route::get('/log-viewer/{view?}', [\Opcodes\LogViewer\Http\Controllers\IndexController::class, '__invoke'])
            ->where('view', '(?!api).*')
            ->name('log-viewer.index')
            ->middleware(['view_logs']);
    });
});

// --------------------------------------------------
// Fallback Route - Handles 404 errors for undefined routes
// --------------------------------------------------
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
