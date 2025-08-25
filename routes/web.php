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
| This version is a merge and correction of previous route files, ensuring all expected
| named routes are defined, especially those referenced by views such as:
|   - admin.equipment.index   → /admin/equipment-items (Livewire)
|   - admin.equipment.issued-loans → /admin/equipment/issued-loans
|   - approvals.dashboard     → /approvals (Livewire)
| and any other admin/equipment/approvals routes required by the dashboard and sidebar.
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// Controller Imports (for public/static/some controller routes)
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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Helpdesk\TicketController as HelpdeskTicketController;

// --------------------------------------------------
// Livewire Component Imports (for all Livewire-based UI)
// --------------------------------------------------
use App\Livewire\ContactUs as ContactUsLW;
use App\Livewire\EquipmentChecklist as EquipmentChecklistLW;
use App\Livewire\LoanRequestForm as LoanRequestFormLW;
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;
use App\Livewire\Dashboard\ApproverDashboard as ApproverDashboardLW;
use App\Livewire\Dashboard\BpmDashboard as BpmDashboardLW;
use App\Livewire\Dashboard\ItAdminDashboard as ItAdminDashboardLW;
use App\Livewire\Dashboard\UserDashboard as UserDashboardLW;
use App\Livewire\Charts\LoanSummaryChart;
use App\Livewire\ResourceManagement\MyApplications\Loan\LoanApplicationsIndex as MyLoanApplicationsIndexLW;
use App\Livewire\ResourceManagement\LoanApplication\LoanApplicationForm as LoanApplicationFormLW;
use App\Livewire\ResourceManagement\Approval\ApprovalDashboard as ApprovalDashboardLW;
use App\Livewire\ResourceManagement\Approval\ApprovalHistory as ApprovalHistoryLW;
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentIndex as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentForm as AdminEquipmentFormLW;
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans as AdminIssuedLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans as AdminOutstandingLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance as AdminProcessIssuanceLW;
use App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn as AdminProcessReturnLW;
use App\Livewire\ResourceManagement\Admin\Grades\GradeIndex as AdminGradeIndexLW;
use App\Livewire\ResourceManagement\Admin\Users\UserIndex as AdminUserIndexLW;
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentInventoryReport as AdminEquipmentInventoryReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentReport as AdminEquipmentReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\LoanApplicationsReport as AdminLoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport as AdminUserActivityReportLW;
use App\Livewire\ResourceManagement\Reports\EquipmentReport as EquipmentReportLW;
use App\Livewire\ResourceManagement\Reports\LoanApplicationsReport as LoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Reports\ReportsIndex as ReportsIndexLW;
use App\Livewire\ResourceManagement\Reports\UserActivityReport as UserActivityReportLW;
use App\Livewire\Settings\Users\UsersCreate as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\UsersEdit as SettingsUsersEditLW;
use App\Livewire\Settings\Users\UsersIndex as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\UsersShow as SettingsUsersShowLW;
use App\Livewire\Settings\Roles\RolesIndex as SettingsRolesIndexLW;
use App\Livewire\Settings\Permissions\PermissionsIndex as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Departments\DepartmentsIndex as SettingsDepartmentsIndexLW;
use App\Livewire\Settings\Positions\PositionsIndex as SettingsPositionsIndexLW;
use App\Livewire\Shared\Notifications\NotificationsList;
use App\Livewire\Helpdesk\CreateTicketForm;
use App\Livewire\Helpdesk\MyTicketsIndex;
use App\Livewire\Helpdesk\TicketDetails;
use App\Livewire\Helpdesk\Admin\TicketManagement as AdminTicketManagementLW;
use App\Livewire\Misc\ComingSoon as ComingSoonLW;
use App\Livewire\HumanResource\Structure\Departments as HRDepartmentsLW;
use App\Livewire\HumanResource\Structure\EmployeeInfo as HREmployeeInfoLW;
use App\Livewire\HumanResource\Structure\Positions as HRPositionsLW;

// --------------------------------------------------
// PUBLIC ROUTES
// --------------------------------------------------

// Home page (welcome)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Static policy and terms pages (static blade)
Route::view('/privacy-policy', 'policy')->name('policy');
Route::view('/terms-of-service', 'terms')->name('terms');

// Contact Us (Livewire)
Route::get('/contact-us', ContactUsLW::class)->name('contact-us');

// Language switcher
Route::get('lang/{lang}', [LanguageController::class, 'swap'])
    ->where('lang', 'en|ms')
    ->name('language.swap');

// Test translation system (debug)
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
// AUTHENTICATED ROUTES (Livewire UI + controllers for process endpoints)
// --------------------------------------------------
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // -------------------------
    // Dashboard Routes (role-based)
    // -------------------------
    // Use controller-based dashboard to return a concrete Blade view for tests
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
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
    // Notifications (Livewire + mark-as-read)
    // -------------------------
    Route::get('/notifications', NotificationsList::class)->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    // -------------------------
    // User Profile (Livewire)
    // -------------------------
    Route::get('/user/profile', SettingsUsersShowLW::class)->name('profile.show');

    // -------------------------
    // Loan Applications (Livewire + controller for process/print)
    // -------------------------
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        // Store new application (traditional form flow)
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store');
        Route::get('/create', LoanApplicationFormLW::class)->name('create');
        Route::get('/my-applications', MyLoanApplicationsIndexLW::class)->name('my-applications.index');
        Route::get('/{loanApplication}/edit', LoanApplicationFormLW::class)
            ->name('edit')
            ->whereNumber('loanApplication');
        Route::get('/{loanApplication}/print', [LoanApplicationController::class, 'printPdf'])
            ->name('print')
            ->whereNumber('loanApplication');
        Route::get('/{loanApplication}', [LoanApplicationController::class, 'show'])
            ->name('show')
            ->whereNumber('loanApplication');
        // Equipment issuance/return (process endpoints)
        Route::get('/{loanApplication}/issue', [LoanTransactionController::class, 'showIssueForm'])
            ->name('issue')
            ->whereNumber('loanApplication')
            ->middleware('can:processIssuance,loanApplication');
        Route::post('/{loanApplication}/issue', [LoanTransactionController::class, 'storeIssue'])
            ->name('issue.store')
            ->whereNumber('loanApplication')
            ->middleware('can:processIssuance,loanApplication');
        Route::get('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'showReturnForm'])
            ->name('return')
            ->whereNumber('loanTransaction')
            ->middleware('can:processReturn,loanTransaction,loanTransaction.loanApplication');
        Route::post('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'storeReturn'])
            ->name('return.store')
            ->whereNumber('loanTransaction')
            ->middleware('can:processReturn,loanTransaction,loanTransaction.loanApplication');
    });

    // -------------------------
    // Equipment Management (User: index/show only)
    // -------------------------
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // -------------------------
    // Approval Workflows (Livewire + process endpoints)
    // -------------------------
    Route::prefix('approvals')->name('approvals.')->group(function () {
        // Use Spatie's built-in permission middleware to avoid alias resolution issues.
        // This ensures the route is accessible only to users with the 'view approval tasks' permission.
        Route::get('/', ApprovalDashboardLW::class)
            ->name('dashboard')
            ->middleware(['permission:view approval tasks']);

        // Approval history is protected by a separate permission for finer access control.
        Route::get('/history', ApprovalHistoryLW::class)
            ->name('history')
            ->middleware(['permission:view approval history']);

        // Officer tasks
        Route::get('/tasks', [ApprovalController::class, 'index'])->name('tasks');
        Route::get('/{approval}', [ApprovalController::class, 'show'])
            ->name('show')
            ->whereNumber('approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])
            ->name('decision')
            ->whereNumber('approval');

        // Alias for legacy/tests naming convention
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])
            ->name('recordDecision')
            ->whereNumber('approval');
    });

    // -------------------------
    // ADMIN RESOURCE MANAGEMENT (Livewire only)
    // -------------------------
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin|IT Admin|BPM Staff'])->group(function () {
        // Equipment management (Livewire)
        Route::get('equipment-items', AdminEquipmentIndexLW::class)->name('equipment.index');
        Route::get('equipment-form', AdminEquipmentFormLW::class)->name('equipment.form');
        // Issued/Outstanding/Process/Return (Livewire)
        Route::get('equipment/issued-loans', AdminIssuedLoansLW::class)->name('equipment.issued-loans');
        Route::get('equipment/outstanding-loans', AdminOutstandingLoansLW::class)->name('equipment.outstanding-loans');
        Route::get('equipment/process-issuance', AdminProcessIssuanceLW::class)->name('equipment.process-issuance');
        Route::get('equipment/process-return', AdminProcessReturnLW::class)->name('equipment.process-return');
        // Users (Livewire)
        Route::get('users', AdminUserIndexLW::class)->name('users.index');
        // Grades (Livewire)
        Route::get('grades', AdminGradeIndexLW::class)->name('grades.index');
        // Reports (Livewire)
        Route::get('reports/equipment-inventory', AdminEquipmentInventoryReportLW::class)->name('equipment.reports-inventory');
        Route::get('reports/equipment-report', AdminEquipmentReportLW::class)->name('equipment.reports');
        Route::get('reports/loan-applications', AdminLoanApplicationsReportLW::class)->name('loan-applications.reports');
        Route::get('reports/user-activity', AdminUserActivityReportLW::class)->name('user-activity.reports');
    });

    // -------------------------
    // Reports (Livewire + legacy controller)
    // -------------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', ReportsIndexLW::class)->name('index');
        Route::get('/equipment-inventory', EquipmentReportLW::class)->name('equipment-inventory');
        Route::get('/loan-applications', LoanApplicationsReportLW::class)->name('loan-applications');
        Route::get('/user-activity', UserActivityReportLW::class)->name('user-activity');
        // Controller-based
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history');
        Route::get('/loan-status-summary', [ReportController::class, 'loanStatusSummary'])->name('loan-status-summary');
        Route::get('/utilization-report', [ReportController::class, 'utilizationReport'])->name('utilization-report');
        Route::get('/helpdesk-tickets', [ReportController::class, 'helpdeskTickets'])->name('helpdesk-tickets');
    });

    // -------------------------
    // Helpdesk (Livewire)
    // -------------------------
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        // Admin/IT Admin/Helpdesk Agent routes
        Route::middleware(['role:Admin|IT Admin|Helpdesk Agent'])->group(function () {
            // Ensure route name matches tests: helpdesk.admin.index
            Route::get('/admin/tickets', AdminTicketManagementLW::class)->name('admin.index');
        });
        Route::get('/', MyTicketsIndex::class)->name('index');
        Route::get('/create', CreateTicketForm::class)->name('create');
        Route::get('/{ticket}', TicketDetails::class)
            ->name('show')
            ->whereNumber('ticket');
    });

    // Helpdesk TicketController Web Routes (for controller-based tests)
    Route::prefix('helpdesk')->name('helpdesk.tickets.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/tickets', [HelpdeskTicketController::class, 'index'])->name('index');
        Route::get('/tickets/create', [HelpdeskTicketController::class, 'create'])->name('create');
        Route::post('/tickets', [HelpdeskTicketController::class, 'store'])->name('store');
        Route::get('/tickets/{ticket}', [HelpdeskTicketController::class, 'show'])->name('show')->whereNumber('ticket');
        Route::put('/tickets/{ticket}', [HelpdeskTicketController::class, 'update'])->name('update')->whereNumber('ticket');
        Route::delete('/tickets/{ticket}', [HelpdeskTicketController::class, 'destroy'])->name('destroy')->whereNumber('ticket');
    });

    // Legacy alias used in tests to view a specific ticket
    Route::get('/helpdesk/view/{ticket}', [HelpdeskTicketController::class, 'show'])
        ->name('helpdesk.view')
        ->whereNumber('ticket')
        ->middleware(['auth', 'verified']);

    // -------------------------
    // Human Resource (Livewire, optional)
    // -------------------------
    Route::prefix('hr')->name('hr.')->middleware(['role:Admin|HR Admin'])->group(function () {
        Route::get('/departments', HRDepartmentsLW::class)->name('departments.index');
        Route::get('/positions', HRPositionsLW::class)->name('positions.index');
        Route::get('/employees', HREmployeeInfoLW::class)->name('employees.index');
    });

    // -------------------------
    // System Settings (Livewire)
    // -------------------------
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        Route::get('/', SettingsUsersIndexLW::class)->name('index');
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index');
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create');
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit');
        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index');
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index');
        Route::get('/departments', SettingsDepartmentsIndexLW::class)->name('departments.index');
        Route::get('/positions', SettingsPositionsIndexLW::class)->name('positions.index');
        Route::get('/log-viewer/{view?}', [\Opcodes\LogViewer\Http\Controllers\IndexController::class, '__invoke'])
            ->where('view', '(?!api).*')
            ->name('log-viewer.index')
            ->middleware(['view_logs']);
    });

    // -------------------------
    // Charts (Livewire)
    // -------------------------
    Route::get('/charts/loan-summary', LoanSummaryChart::class)->name('charts.loan-summary');

    // -------------------------
    // Miscellaneous (Livewire)
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
| Route Documentation
|--------------------------------------------------------------------------
| The Livewire route admin.equipment.index points to the EquipmentIndex component at:
|   /admin/equipment-items    (name: admin.equipment.index)
| If you update this route, update all references to route('admin.equipment.index').
|
| The route admin.equipment.issued-loans is defined at:
|   /admin/equipment/issued-loans (name: admin.equipment.issued-loans)
| Used for loaned equipment statistics and links.
|
| The route approvals.dashboard is defined at:
|   /approvals (name: approvals.dashboard)
| Used for pending approvals/statistics.
|
| Use php artisan route:list to confirm all route names and paths.
|--------------------------------------------------------------------------
*/
