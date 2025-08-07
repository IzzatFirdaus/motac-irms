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

// Livewire Components (renamed for clarity and consistency)
use App\Livewire\ContactUs as ContactUsLW;
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;

// Resource Management - My Applications (renamed from Index to LoanApplicationsIndex)
use App\Livewire\ResourceManagement\MyApplications\Loan\LoanApplicationsIndex as MyLoanApplicationsIndexLW;

// Resource Management - Loan Application Form (renamed from ApplicationForm)
use App\Livewire\ResourceManagement\LoanApplication\LoanApplicationForm as LoanApplicationFormLW;

// Resource Management - Approval System (renamed from Dashboard and History)
use App\Livewire\ResourceManagement\Approval\ApprovalDashboard as ApprovalDashboardLW;
use App\Livewire\ResourceManagement\Approval\ApprovalHistory as ApprovalHistoryLW;

// Resource Management - Admin Equipment
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentIndex as AdminEquipmentIndexLW;

// Resource Management - Admin BPM Operations
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;

// Resource Management - Admin Users (renamed from Index to UserIndex)
use App\Livewire\ResourceManagement\Admin\Users\UserIndex as AdminUserIndexLW;

// Reports (renamed to follow convention)
use App\Livewire\ResourceManagement\Reports\ReportsIndex as ReportsIndexLW;
use App\Livewire\ResourceManagement\Reports\EquipmentReport as EquipmentReportLW;
use App\Livewire\ResourceManagement\Reports\LoanApplicationsReport as LoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Reports\UserActivityReport as UserActivityReportLW;

// Settings - User Management (correct class names for Livewire components)
use App\Livewire\Settings\Users\UsersCreate as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\UsersEdit as SettingsUsersEditLW;
use App\Livewire\Settings\Users\UsersIndex as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\UsersShow as SettingsUsersShowLW;

// Settings - Roles, Permissions, Departments, Positions (correct class names)
use App\Livewire\Settings\Roles\RolesIndex as SettingsRolesIndexLW;
use App\Livewire\Settings\Permissions\PermissionsIndex as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Departments\DepartmentsIndex as SettingsDepartmentsIndexLW;
use App\Livewire\Settings\Positions\PositionsIndex as SettingsPositionsIndexLW;

// Shared Components
use App\Livewire\Shared\Notifications\NotificationsList;

// Helpdesk Livewire Components
use App\Livewire\Helpdesk\CreateTicketForm;
use App\Livewire\Helpdesk\MyTicketsIndex;
use App\Livewire\Helpdesk\TicketDetails;
use App\Livewire\Helpdesk\Admin\TicketManagement as AdminTicketManagementLW;

use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// Public Routes (accessible without authentication)
// --------------------------------------------------

// Home page route
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

    // --------------------------------------------------
    // Dashboard Routes
    // --------------------------------------------------
    Route::get('/dashboard', DashboardLW::class)->name('dashboard');
    Route::get('/dashboard/admin', AdminDashboardLW::class)
        ->name('admin.dashboard')
        ->middleware(['role:Admin|IT Admin']);

    // --------------------------------------------------
    // Notification Management
    // --------------------------------------------------
    Route::get('/notifications', NotificationsList::class)->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    // --------------------------------------------------
    // User Profile Management (Jetstream)
    // --------------------------------------------------
    Route::get('/user/profile', function () {
        return view('profile.show');
    })->name('profile.show');

    // --------------------------------------------------
    // ICT Equipment Loan Application Routes
    // --------------------------------------------------
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        // Create new loan application (Livewire form)
        Route::get('/create', LoanApplicationFormLW::class)->name('create');
        // Edit an existing loan application (Livewire form)
        Route::get('/{loanApplication}/edit', LoanApplicationFormLW::class)->name('edit');
        // View my applications (renamed to LoanApplicationsIndex)
        Route::get('/my-applications', MyLoanApplicationsIndexLW::class)->name('my-applications.index');
        // View a specific application (Controller)
        Route::get('/{loanApplication}', [LoanApplicationController::class, 'show'])->name('show');
        // Print application (Controller)
        Route::get('/{loanApplication}/print', [LoanApplicationController::class, 'print'])->name('print');
        // Equipment issuance routes (BPM staff only)
        Route::get('/{loanApplication}/issue', [LoanTransactionController::class, 'createIssue'])
            ->name('issue')
            ->middleware('can:issue,loanApplication');
        Route::post('/{loanApplication}/issue', [LoanTransactionController::class, 'storeIssue'])
            ->name('issue.store')
            ->middleware('can:issue,loanApplication');
        // Equipment return routes
        Route::get('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'createReturn'])
            ->name('return')
            ->middleware('can:return,loanTransaction');
        Route::post('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'storeReturn'])
            ->name('return.store')
            ->middleware('can:return,loanTransaction');
    });

    // --------------------------------------------------
    // Equipment Management (View Only for Regular Users)
    // --------------------------------------------------
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // --------------------------------------------------
    // Approval Workflow System (renamed components)
    // --------------------------------------------------
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', ApprovalDashboardLW::class)
            ->name('dashboard')
            ->middleware(['view_approval_tasks']);
        Route::get('/history', ApprovalHistoryLW::class)
            ->name('history')
            ->middleware(['view_approval_history']);
    });

    // --------------------------------------------------
    // Admin Resource Management (Restricted Access)
    // --------------------------------------------------
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
        // Equipment management (full CRUD for admins)
        Route::resource('equipment', AdminEquipmentController::class)->except(['show']);
        // BPM Operations - Issued loans management
        Route::get('equipment/issued-loans', IssuedLoans::class)->name('equipment.issued-loans');
        // Equipment inventory management (renamed EquipmentIndex)
        Route::get('equipment-items', AdminEquipmentIndexLW::class)->name('equipment-items.index');
        // User management
        Route::get('users', AdminUserIndexLW::class)->name('users.index');
    });

    // --------------------------------------------------
    // Reports Module (now uses Livewire index)
    // --------------------------------------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        // Main reports index page (Livewire)
        Route::get('/', ReportsIndexLW::class)->name('index');
        // Equipment inventory report
        Route::get('/equipment-inventory', EquipmentReportLW::class)->name('equipment-inventory');
        // Loan applications report
        Route::get('/loan-applications', LoanApplicationsReportLW::class)->name('loan-applications');
        // User activity report
        Route::get('/user-activity', UserActivityReportLW::class)->name('user-activity');
        // (Legacy/non-Livewire controller-based reports)
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history');
        Route::get('/loan-status-summary', [ReportController::class, 'loanStatusSummary'])->name('loan-status-summary');
        Route::get('/utilization-report', [ReportController::class, 'utilizationReport'])->name('utilization-report');
        // Example: Helpdesk tickets report (add corresponding Livewire/Controller if needed)
        Route::get('/helpdesk-tickets', [ReportController::class, 'helpdeskTickets'])->name('helpdesk-tickets');
    });

    // --------------------------------------------------
    // Helpdesk Module (Support Ticket System)
    // --------------------------------------------------
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        // User routes - view own tickets
        Route::get('/', MyTicketsIndex::class)->name('index');
        Route::get('/create', CreateTicketForm::class)->name('create');
        Route::get('/{ticket}', TicketDetails::class)->name('show');
        // Admin/IT Admin/Helpdesk Agent routes
        Route::middleware(['role:Admin|IT Admin|Helpdesk Agent'])->group(function () {
            Route::get('/admin/tickets', AdminTicketManagementLW::class)->name('admin.index');
            // Additional admin-specific helpdesk routes here if needed
        });
    });

    // --------------------------------------------------
    // System Settings Panel (Admin Only Access)
    // --------------------------------------------------
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        // User Management (Livewire Components)
        Route::get('/', SettingsUsersIndexLW::class)->name('index');
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index');
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create');
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit');
        // Roles Management
        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index');
        // Permissions Management
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index');
        // Grades (Controller-based resource)
        Route::resource('grades', AdminGradeController::class)->parameters(['grades' => 'grade']);
        // Departments Management
        Route::get('/departments', SettingsDepartmentsIndexLW::class)->name('departments.index');
        // Positions Management
        Route::get('/positions', SettingsPositionsIndexLW::class)->name('positions.index');
        // Log Viewer (3rd party)
        Route::get('/log-viewer/{view?}', [\Opcodes\LogViewer\Http\Controllers\IndexController::class, '__invoke'])
            ->where('view', '(?!api).*')
            ->name('log-viewer.index')
            ->middleware(['view_logs']);
    });

    // --------------------------------------------------
    // API Webhook Routes (for external integrations)
    // --------------------------------------------------
    Route::prefix('webhooks')->name('webhooks.')->middleware(['validate.webhook.signature'])->group(function () {
        // Add webhook routes here as needed for external system integrations
        // Example: Route::post('/equipment-update', [WebhookController::class, 'equipmentUpdate'])->name('equipment.update');
    });
});

// --------------------------------------------------
// Fallback Route - Handles 404 errors for undefined routes
// --------------------------------------------------
// This will return a custom 404 error view if no other route matches.
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
