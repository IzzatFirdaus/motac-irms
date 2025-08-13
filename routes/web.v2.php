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
|
| Last Updated: 2025-08-11 15:05:52 UTC by IzzatFirdaus
| Changes: Streamlined routes to prioritize Livewire components and remove duplicates
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// Controllers - Core Application Controllers
// --------------------------------------------------
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\MiscErrorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EquipmentController;

// --------------------------------------------------
// Livewire Components - Core Components
// --------------------------------------------------
use App\Livewire\ContactUs as ContactUsLW;
use App\Livewire\EquipmentChecklist as EquipmentChecklistLW;
use App\Livewire\LoanRequestForm as LoanRequestFormLW;

// --------------------------------------------------
// Livewire Components - Dashboards
// --------------------------------------------------
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;
use App\Livewire\Dashboard\ApproverDashboard as ApproverDashboardLW;
use App\Livewire\Dashboard\BpmDashboard as BpmDashboardLW;
use App\Livewire\Dashboard\ItAdminDashboard as ItAdminDashboardLW;
use App\Livewire\Dashboard\UserDashboard as UserDashboardLW;

// --------------------------------------------------
// Livewire Components - Charts
// --------------------------------------------------
use App\Livewire\Charts\LoanSummaryChart;

// --------------------------------------------------
// Livewire Components - Resource Management
// --------------------------------------------------
// My Applications and Loan Application Form
use App\Livewire\ResourceManagement\MyApplications\Loan\LoanApplicationsIndex as MyLoanApplicationsIndexLW;
use App\Livewire\ResourceManagement\LoanApplication\LoanApplicationForm as LoanApplicationFormLW;

// Approval System
use App\Livewire\ResourceManagement\Approval\ApprovalDashboard as ApprovalDashboardLW;
use App\Livewire\ResourceManagement\Approval\ApprovalHistory as ApprovalHistoryLW;

// Admin - Equipment Management
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentForm as AdminEquipmentFormLW;
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentIndex as AdminEquipmentIndexLW;

// Admin - BPM Operations
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans as AdminIssuedLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans as AdminOutstandingLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance as AdminProcessIssuanceLW;
use App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn as AdminProcessReturnLW;

// Admin - Grades Management
use App\Livewire\ResourceManagement\Admin\Grades\GradeIndex as AdminGradeIndexLW;

// Admin - Users Management
use App\Livewire\ResourceManagement\Admin\Users\UserIndex as AdminUserIndexLW;

// Admin - Reports
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentInventoryReport as AdminEquipmentInventoryReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentReport as AdminEquipmentReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\LoanApplicationsReport as AdminLoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport as AdminUserActivityReportLW;

// Regular Reports (non-admin)
use App\Livewire\ResourceManagement\Reports\EquipmentReport as EquipmentReportLW;
use App\Livewire\ResourceManagement\Reports\LoanApplicationsReport as LoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Reports\ReportsIndex as ReportsIndexLW;
use App\Livewire\ResourceManagement\Reports\UserActivityReport as UserActivityReportLW;

// --------------------------------------------------
// Livewire Components - Settings
// --------------------------------------------------
// User Management
use App\Livewire\Settings\Users\UsersCreate as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\UsersEdit as SettingsUsersEditLW;
use App\Livewire\Settings\Users\UsersIndex as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\UsersShow as SettingsUsersShowLW;

// Roles, Permissions, Departments, Positions
use App\Livewire\Settings\Departments\DepartmentsIndex as SettingsDepartmentsIndexLW;
use App\Livewire\Settings\Permissions\PermissionsIndex as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Roles\RolesIndex as SettingsRolesIndexLW;
// Uncomment when the PositionsIndex Livewire class is created
// use App\Livewire\Settings\Positions\PositionsIndex as SettingsPositionsIndexLW;

// --------------------------------------------------
// Livewire Components - Shared Components
// --------------------------------------------------
use App\Livewire\Shared\Notifications\NotificationsList;

// --------------------------------------------------
// Livewire Components - Helpdesk
// --------------------------------------------------
use App\Livewire\Helpdesk\CreateTicketForm;
use App\Livewire\Helpdesk\MyTicketsIndex;
use App\Livewire\Helpdesk\TicketDetails;
use App\Livewire\Helpdesk\Admin\TicketManagement as AdminTicketManagementLW;

// --------------------------------------------------
// Livewire Components - Human Resource
// --------------------------------------------------
use App\Livewire\HumanResource\Structure\Departments as HRDepartmentsLW;
use App\Livewire\HumanResource\Structure\EmployeeInfo as HREmployeeInfoLW;
use App\Livewire\HumanResource\Structure\Positions as HRPositionsLW;

// --------------------------------------------------
// Livewire Components - Miscellaneous
// --------------------------------------------------
use App\Livewire\Misc\ComingSoon as ComingSoonLW;

// --------------------------------------------------
// Public Routes (accessible without authentication)
// --------------------------------------------------

// Home page route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Static policy and terms pages (direct Blade views, no controller needed)
Route::view('/privacy-policy', 'policy')->name('policy');
Route::view('/terms-of-service', 'terms')->name('terms');

// Contact Us page - using Livewire component directly
Route::get('/contact-us', ContactUsLW::class)->name('contact-us');

// --------------------------------------------------
// Language Switching Route (accessible to all users)
// --------------------------------------------------
Route::get('lang/{lang}', [LanguageController::class, 'swap'])
    ->where('lang', 'en|ms')
    ->name('language.swap');

// --------------------------------------------------
// === TEST ROUTE FOR TRANSLATION LOADING ===
// --------------------------------------------------
// Debugging route for translation system testing
Route::get('/test-lang', function () {
    return [
        'loaded_file_ms' => file_exists(resource_path('lang/ms/app_ms.php')),
        'loaded_file_en' => file_exists(resource_path('lang/en/app_en.php')),
        'current_locale' => app()->getLocale(),
        'system_name' => __('app.system_name'),
        'motac_full_name' => __('app.motac_full_name'),
        'dashboard_apply' => __('dashboard.apply_ict_loan_title'),
        'common_login' => __('common.login'),
    ];
});

// --------------------------------------------------
// Authenticated Routes (protected by Jetstream/Sanctum/verified middleware)
// --------------------------------------------------
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // -------------------------
    // Dashboard Routes (Livewire)
    // -------------------------
    Route::get('/dashboard', DashboardLW::class)->name('dashboard');

    Route::get('/dashboard/admin', AdminDashboardLW::class)
        ->name('admin.dashboard')
        ->middleware(['role:Admin|IT Admin']);

    Route::get('/dashboard/approver', ApproverDashboardLW::class)
        ->name('dashboard.approver')
        ->middleware(['role:Approver|Admin|IT Admin']);

    Route::get('/dashboard/bpm', BpmDashboardLW::class)
        ->name('dashboard.bpm')
        ->middleware(['role:BPM|Admin|IT Admin']);

    Route::get('/dashboard/itadmin', ItAdminDashboardLW::class)
        ->name('dashboard.itadmin')
        ->middleware(['role:IT Admin|Admin']);

    Route::get('/dashboard/user', UserDashboardLW::class)
        ->name('dashboard.user')
        ->middleware(['role:User|Admin|IT Admin']);

    // -------------------------
    // Notification Management (Livewire + controller for AJAX)
    // -------------------------
    Route::get('/notifications', NotificationsList::class)->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    // -------------------------
    // User Profile Management (Livewire)
    // -------------------------
    Route::get('/user/profile', SettingsUsersShowLW::class)->name('profile.show');

    // -------------------------
    // ICT Equipment Loan Application Routes (mixed Livewire/controller)
    // -------------------------
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        // Livewire routes for UI
        Route::get('/create', LoanApplicationFormLW::class)->name('create');
        Route::get('/my-applications', MyLoanApplicationsIndexLW::class)->name('my-applications.index');
        Route::get('/{loanApplication}/edit', LoanApplicationFormLW::class)
            ->name('edit')
            ->whereNumber('loanApplication');

        // Controller routes for core functions and processing
        Route::get('/{loanApplication}', [LoanApplicationController::class, 'show'])
            ->name('show')
            ->whereNumber('loanApplication');

        Route::get('/{loanApplication}/print', [LoanApplicationController::class, 'printPdf'])
            ->name('print')
            ->whereNumber('loanApplication');

        // Transaction/processing routes
        Route::get('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'showReturnForm'])
            ->name('return')
            ->whereNumber('loanTransaction')
            ->middleware('can:processReturn,loanTransaction,loanTransaction.loanApplication');

        Route::post('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'storeReturn'])
            ->name('return.store')
            ->whereNumber('loanTransaction')
            ->middleware('can:processReturn,loanTransaction,loanTransaction.loanApplication');

        Route::get('/{loanApplication}/issue', [LoanTransactionController::class, 'showIssueForm'])
            ->name('issue')
            ->whereNumber('loanApplication')
            ->middleware('can:processIssuance,loanApplication');

        Route::post('/{loanApplication}/issue', [LoanTransactionController::class, 'storeIssue'])
            ->name('issue.store')
            ->whereNumber('loanApplication')
            ->middleware('can:processIssuance,loanApplication');
    });

    // -------------------------
    // Equipment Management (View Only for Regular Users)
    // -------------------------
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // -------------------------
    // Approval Workflow System (Livewire)
    // -------------------------
    Route::prefix('approvals')->name('approvals.')->group(function () {
        // Livewire dashboard and history views
        Route::get('/', ApprovalDashboardLW::class)
            ->name('dashboard')
            ->middleware(['view_approval_tasks']);

        Route::get('/history', ApprovalHistoryLW::class)
            ->name('history')
            ->middleware(['view_approval_history']);

        // Controller routes for AJAX and form submissions
        Route::get('/tasks', [ApprovalController::class, 'index'])->name('tasks');
        Route::get('/{approval}', [ApprovalController::class, 'show'])
            ->name('show')
            ->whereNumber('approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])
            ->name('decision')
            ->whereNumber('approval');
    });

    // -------------------------
    // Admin Resource Management (Livewire-centric)
    // -------------------------
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
        // Equipment - Livewire UI
        Route::get('equipment-items', AdminEquipmentIndexLW::class)->name('equipment-items.index');
        Route::get('equipment-form', AdminEquipmentFormLW::class)->name('equipment-items.form');

        // BPM Operations - Livewire UI
        Route::get('equipment/issued-loans', AdminIssuedLoansLW::class)->name('equipment.issued-loans');
        Route::get('equipment/outstanding-loans', AdminOutstandingLoansLW::class)->name('equipment.outstanding-loans');
        Route::get('equipment/process-issuance', AdminProcessIssuanceLW::class)->name('equipment.process-issuance');
        Route::get('equipment/process-return', AdminProcessReturnLW::class)->name('equipment.process-return');

        // Users - Livewire UI
        Route::get('users', AdminUserIndexLW::class)->name('users.index');

        // Grades - Livewire UI
        Route::get('grades', AdminGradeIndexLW::class)->name('grades.index');

        // Admin Reports - Livewire UI
        Route::get('reports/equipment-inventory', AdminEquipmentInventoryReportLW::class)->name('reports.equipment-inventory');
        Route::get('reports/equipment-report', AdminEquipmentReportLW::class)->name('reports.equipment-report');
        Route::get('reports/loan-applications', AdminLoanApplicationsReportLW::class)->name('reports.loan-applications');
        Route::get('reports/user-activity', AdminUserActivityReportLW::class)->name('reports.user-activity');
    });

    // -------------------------
    // Reports Module (Livewire + controller for legacy reports)
    // -------------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        // Livewire report UI components
        Route::get('/', ReportsIndexLW::class)->name('index');
        Route::get('/equipment-inventory', EquipmentReportLW::class)->name('equipment-inventory');
        Route::get('/loan-applications', LoanApplicationsReportLW::class)->name('loan-applications');
        Route::get('/user-activity', UserActivityReportLW::class)->name('user-activity');

        // Controller-based reports (legacy/specialized formats)
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history');
        Route::get('/loan-status-summary', [ReportController::class, 'loanStatusSummary'])->name('loan-status-summary');
        Route::get('/utilization-report', [ReportController::class, 'utilizationReport'])->name('utilization-report');
        Route::get('/helpdesk-tickets', [ReportController::class, 'helpdeskTickets'])->name('helpdesk-tickets');
    });

    // -------------------------
    // Helpdesk Module (Livewire)
    // -------------------------
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        // Admin routes (must come before the catch-all route)
        Route::middleware(['role:Admin|IT Admin|Helpdesk Agent'])->group(function () {
            Route::get('/admin/tickets', AdminTicketManagementLW::class)->name('admin.index');
        });

        // User-facing helpdesk routes
        Route::get('/', MyTicketsIndex::class)->name('index');
        Route::get('/create', CreateTicketForm::class)->name('create');
        Route::get('/{ticket}', TicketDetails::class)
            ->name('show')
            ->whereNumber('ticket');
    });

    // -------------------------
    // Human Resource Management (Optional Livewire module)
    // -------------------------
    Route::prefix('hr')->name('hr.')->middleware(['role:Admin|HR Admin'])->group(function () {
        Route::get('/departments', HRDepartmentsLW::class)->name('departments.index');
        Route::get('/positions', HRPositionsLW::class)->name('positions.index');
        Route::get('/employees', HREmployeeInfoLW::class)->name('employees.index');
    });

    // -------------------------
    // System Settings Panel (Livewire)
    // -------------------------
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        // Default settings index
        Route::get('/', SettingsUsersIndexLW::class)->name('index');

        // User Management
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index');
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create');
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit');

        // Roles, Permissions, Departments
        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index');
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index');
        Route::get('/departments', SettingsDepartmentsIndexLW::class)->name('departments.index');

        // Positions - Using Controller since Livewire component is not yet available
        // Route::get('/positions', SettingsPositionsIndexLW::class)->name('positions.index');

        // Log Viewer (3rd party package)
        Route::get('/log-viewer/{view?}', [\Opcodes\LogViewer\Http\Controllers\IndexController::class, '__invoke'])
            ->where('view', '(?!api).*')
            ->name('log-viewer.index')
            ->middleware(['view_logs']);
    });

    // -------------------------
    // Transaction Management (controller for operations)
    // -------------------------
    Route::prefix('loan-transactions')->name('loan-transactions.')->group(function () {
        Route::get('/', [LoanTransactionController::class, 'index'])->name('index');
        Route::get('/{loanTransaction}', [LoanTransactionController::class, 'show'])
            ->name('show')
            ->whereNumber('loanTransaction');
    });

    // -------------------------
    // Charts and Analytics (Livewire)
    // -------------------------
    Route::get('/charts/loan-summary', LoanSummaryChart::class)->name('charts.loan-summary');

    // -------------------------
    // Miscellaneous Pages (Livewire)
    // -------------------------
    Route::get('/coming-soon', ComingSoonLW::class)->name('misc.coming-soon');
    Route::get('/equipment-checklist', EquipmentChecklistLW::class)->name('misc.equipment-checklist');
    Route::get('/loan-request-form', LoanRequestFormLW::class)->name('misc.loan-request-form');
});

// --------------------------------------------------
// Fallback Route - Handles 404 errors for undefined routes
// --------------------------------------------------
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// Custom error page route for explicit error handling
Route::get('/error/{statusCode}', [MiscErrorController::class, 'show'])
    ->whereNumber('statusCode')
    ->name('misc.error');

/*
|--------------------------------------------------------------------------
| Vertical Menu Component Usage (Livewire)
|--------------------------------------------------------------------------
| NOTE: The vertical menu is rendered via the Livewire component at
| resources/views/livewire/sections/menu/vertical-menu.blade.php.
|
| It's included in layouts using: @livewire('sections.menu.vertical-menu')
|
| The menu is dynamically rendered based on config/menu.php and user role.
|--------------------------------------------------------------------------
*/
