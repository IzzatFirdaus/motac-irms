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
| Last Updated: 2025-08-11 14:45:38 UTC by IzzatFirdaus
| Changes: Added missing admin controllers (Department, Position, User),
|          API routes, and Helpdesk controller routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
// --------------------------------------------------
// Controllers - Admin Controllers
// --------------------------------------------------
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\Admin\PositionController as AdminPositionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\HelpdeskApiController;
// --------------------------------------------------
// Controllers - API Controllers
// --------------------------------------------------
use App\Http\Controllers\ApprovalController;
// --------------------------------------------------
// Controllers - Helpdesk Controllers
// --------------------------------------------------
use App\Http\Controllers\DashboardController;
// --------------------------------------------------
// Controllers - Main Application Controllers
// --------------------------------------------------
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\Helpdesk\TicketController as HelpdeskTicketController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\MiscErrorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebhookController;
use App\Livewire\Charts\LoanSummaryChart;
// --------------------------------------------------
// Livewire Components - Core Components
// --------------------------------------------------
use App\Livewire\ContactUs as ContactUsLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;
use App\Livewire\Dashboard\ApproverDashboard as ApproverDashboardLW;
// --------------------------------------------------
// Livewire Components - Dashboards
// --------------------------------------------------
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\BpmDashboard as BpmDashboardLW;
use App\Livewire\Dashboard\ItAdminDashboard as ItAdminDashboardLW;
use App\Livewire\Dashboard\UserDashboard as UserDashboardLW;
use App\Livewire\EquipmentChecklist as EquipmentChecklistLW;
use App\Livewire\Helpdesk\Admin\TicketManagement as AdminTicketManagementLW;
// --------------------------------------------------
// Livewire Components - Charts
// --------------------------------------------------
use App\Livewire\Helpdesk\CreateTicketForm;
// --------------------------------------------------
// Livewire Components - Resource Management
// --------------------------------------------------
// My Applications and Loan Application Form
use App\Livewire\Helpdesk\MyTicketsIndex;
use App\Livewire\Helpdesk\TicketDetails;
// Approval System
use App\Livewire\HumanResource\Structure\Departments as HRDepartmentsLW;
use App\Livewire\HumanResource\Structure\EmployeeInfo as HREmployeeInfoLW;
// Admin - Equipment Management
use App\Livewire\HumanResource\Structure\Positions as HRPositionsLW;
use App\Livewire\LoanRequestForm as LoanRequestFormLW;
// Admin - BPM Operations
use App\Livewire\Misc\ComingSoon as ComingSoonLW;
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans as AdminIssuedLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans as AdminOutstandingLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance as AdminProcessIssuanceLW;
// Admin - Grades Management
use App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn as AdminProcessReturnLW;
// Admin - Users Management
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentForm as AdminEquipmentFormLW;
// Admin - Reports
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentIndex as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Admin\Grades\GradeIndex as AdminGradeIndexLW;
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentInventoryReport as AdminEquipmentInventoryReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentReport as AdminEquipmentReportLW;
// Regular Reports (non-admin)
use App\Livewire\ResourceManagement\Admin\Reports\LoanApplicationsReport as AdminLoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport as AdminUserActivityReportLW;
use App\Livewire\ResourceManagement\Admin\Users\UserIndex as AdminUserIndexLW;
use App\Livewire\ResourceManagement\Approval\ApprovalDashboard as ApprovalDashboardLW;
// --------------------------------------------------
// Livewire Components - Settings
// --------------------------------------------------
// User Management
use App\Livewire\ResourceManagement\Approval\ApprovalHistory as ApprovalHistoryLW;
use App\Livewire\ResourceManagement\LoanApplication\LoanApplicationForm as LoanApplicationFormLW;
use App\Livewire\ResourceManagement\MyApplications\Loan\LoanApplicationsIndex as MyLoanApplicationsIndexLW;
use App\Livewire\ResourceManagement\Reports\EquipmentReport as EquipmentReportLW;
// Roles, Permissions, Departments, Positions
use App\Livewire\ResourceManagement\Reports\LoanApplicationsReport as LoanApplicationsReportLW;
use App\Livewire\ResourceManagement\Reports\ReportsIndex as ReportsIndexLW;
use App\Livewire\ResourceManagement\Reports\UserActivityReport as UserActivityReportLW;
// Note: Positions Livewire component exists in views but not in app/Livewire
// Uncomment when the Livewire class is created
// use App\Livewire\Settings\Positions\PositionsIndex as SettingsPositionsIndexLW;

// --------------------------------------------------
// Livewire Components - Shared Components
// --------------------------------------------------
use App\Livewire\Settings\Departments\DepartmentsIndex as SettingsDepartmentsIndexLW;
// --------------------------------------------------
// Livewire Components - Sections (Layout Components)
// --------------------------------------------------

// --------------------------------------------------
// Livewire Components - Helpdesk
// --------------------------------------------------
use App\Livewire\Settings\Permissions\PermissionsIndex as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Roles\RolesIndex as SettingsRolesIndexLW;
use App\Livewire\Settings\Users\UsersCreate as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\UsersEdit as SettingsUsersEditLW;
// --------------------------------------------------
// Livewire Components - Human Resource
// --------------------------------------------------
use App\Livewire\Settings\Users\UsersIndex as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\UsersShow as SettingsUsersShowLW;
use App\Livewire\Shared\Notifications\NotificationsList;
// --------------------------------------------------
// Livewire Components - Miscellaneous
// --------------------------------------------------
use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// Public Routes (accessible without authentication)
// --------------------------------------------------

// Home page route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Static policy and terms pages
Route::get('/privacy-policy', [LegalController::class, 'policy'])->name('policy');
Route::get('/terms-of-service', [LegalController::class, 'terms'])->name('terms');

// Contact Us page - using Livewire component directly
Route::get('/contact-us', ContactUsLW::class)->name('contact-us');

// Redundant non-Livewire Contact Us page (for testing/fallback/documentation purposes)
Route::view('/contact-us-blade', 'contact-us-blade')->name('contact-us.blade');

// --------------------------------------------------
// Language Switching Route (accessible to all users)
// --------------------------------------------------
Route::get('lang/{lang}', [LanguageController::class, 'swap'])
    ->where('lang', 'en|ms')
    ->name('language.swap');

// --------------------------------------------------
// === TEST ROUTE FOR TRANSLATION LOADING ===
// --------------------------------------------------
// This route is for debugging the translation system. It will attempt to load
// specific translation keys for the current locale and return them as JSON.
// Used to verify that the SuffixedFileLoader and SuffixedTranslator are working
// and that translation files are being found correctly.
Route::get('/test-lang', function () {
    return [
        // Checks if the file exists (for Malay locale)
        'loaded_file_ms' => file_exists(resource_path('lang/ms/app_ms.php')),
        // Checks if the file exists (for English locale)
        'loaded_file_en' => file_exists(resource_path('lang/en/app_en.php')),
        // Will output the current app locale (should be 'en' or 'ms')
        'current_locale' => app()->getLocale(),
        // Test translation keys for app, dashboard, common
        'system_name'     => __('app.system_name'),
        'motac_full_name' => __('app.motac_full_name'),
        'dashboard_apply' => __('dashboard.apply_ict_loan_title'),
        'common_login'    => __('common.login'),
    ];
});

// --------------------------------------------------
// API Routes (for external integrations and AJAX calls)
// --------------------------------------------------
Route::prefix('api')->name('api.')->group(function () {
    // Helpdesk API routes (for AJAX operations, real-time updates, etc.)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/helpdesk/tickets', [HelpdeskApiController::class, 'index'])->name('helpdesk.tickets.index');
        Route::post('/helpdesk/tickets', [HelpdeskApiController::class, 'store'])->name('helpdesk.tickets.store');
        Route::get('/helpdesk/tickets/{ticket}', [HelpdeskApiController::class, 'show'])->name('helpdesk.tickets.show');
        Route::put('/helpdesk/tickets/{ticket}', [HelpdeskApiController::class, 'update'])->name('helpdesk.tickets.update');
        Route::delete('/helpdesk/tickets/{ticket}', [HelpdeskApiController::class, 'destroy'])->name('helpdesk.tickets.destroy');
    });
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
    // Dashboard Routes (role-specific dashboards)
    // -------------------------
    Route::get('/dashboard', DashboardLW::class)->name('dashboard');

    // Admin Dashboard
    Route::get('/dashboard/admin', AdminDashboardLW::class)
        ->name('admin.dashboard')
        ->middleware(['role:Admin|IT Admin']);

    // Role-specific dashboards
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
    // Notification Management
    // -------------------------
    Route::get('/notifications', NotificationsList::class)->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    // -------------------------
    // User Profile Management
    // -------------------------
    // Points to Livewire UsersShow component to fix "View [profile.show] not found" error
    Route::get('/user/profile', SettingsUsersShowLW::class)->name('profile.show');

    // -------------------------
    // ICT Equipment Loan Application Routes
    // -------------------------
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        // Static routes first (to avoid conflicts with dynamic routes)
        Route::get('/create', LoanApplicationFormLW::class)->name('create');
        Route::get('/my-applications', MyLoanApplicationsIndexLW::class)->name('my-applications.index');

        // Transaction sub-routes (must come before {loanApplication} catch-all)
        Route::get('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'showReturnForm'])
            ->name('return')
            ->whereNumber('loanTransaction')
            ->middleware('can:processReturn,loanTransaction,loanTransaction.loanApplication');

        Route::post('/transactions/{loanTransaction}/return', [LoanTransactionController::class, 'storeReturn'])
            ->name('return.store')
            ->whereNumber('loanTransaction')
            ->middleware('can:processReturn,loanTransaction,loanTransaction.loanApplication');

        // Dynamic routes (constrained to numbers to avoid conflicts)
        Route::get('/{loanApplication}/edit', LoanApplicationFormLW::class)
            ->name('edit')
            ->whereNumber('loanApplication');

        Route::get('/{loanApplication}/print', [LoanApplicationController::class, 'printPdf'])
            ->name('print')
            ->whereNumber('loanApplication');

        // Equipment issuance routes (BPM staff only, using processIssuance policy)
        Route::get('/{loanApplication}/issue', [LoanTransactionController::class, 'showIssueForm'])
            ->name('issue')
            ->whereNumber('loanApplication')
            ->middleware('can:processIssuance,loanApplication');

        Route::post('/{loanApplication}/issue', [LoanTransactionController::class, 'storeIssue'])
            ->name('issue.store')
            ->whereNumber('loanApplication')
            ->middleware('can:processIssuance,loanApplication');

        // Show specific application (must be last among {loanApplication} routes)
        Route::get('/{loanApplication}', [LoanApplicationController::class, 'show'])
            ->name('show')
            ->whereNumber('loanApplication');
    });

    // -------------------------
    // Equipment Management (View Only for Regular Users)
    // -------------------------
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // -------------------------
    // Approval Workflow System
    // -------------------------
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', ApprovalDashboardLW::class)
            ->name('dashboard')
            ->middleware(['view_approval_tasks']);

        Route::get('/history', ApprovalHistoryLW::class)
            ->name('history')
            ->middleware(['view_approval_history']);
    });

    // -------------------------
    // Admin Resource Management (Restricted Access)
    // -------------------------
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin|IT Admin'])->group(function () {

        // Equipment management (full CRUD for admins via controllers)
        Route::resource('equipment', AdminEquipmentController::class)->except(['show']);

        // Equipment management (Livewire UI components)
        Route::get('equipment-items', AdminEquipmentIndexLW::class)->name('equipment-items.index');
        Route::get('equipment-form', AdminEquipmentFormLW::class)->name('equipment-items.form');

        // BPM Operations - Livewire components for loan management
        Route::get('equipment/issued-loans', AdminIssuedLoansLW::class)->name('equipment.issued-loans');
        Route::get('equipment/outstanding-loans', AdminOutstandingLoansLW::class)->name('equipment.outstanding-loans');
        Route::get('equipment/process-issuance', AdminProcessIssuanceLW::class)->name('equipment.process-issuance');
        Route::get('equipment/process-return', AdminProcessReturnLW::class)->name('equipment.process-return');

        // User management (both controller and Livewire)
        Route::resource('users', AdminUserController::class);
        Route::get('users-livewire', AdminUserIndexLW::class)->name('users-livewire.index');

        // Department management (controller resource)
        Route::resource('departments', AdminDepartmentController::class);

        // Position management (controller resource)
        Route::resource('positions', AdminPositionController::class);

        // Grades management (both controller and Livewire)
        Route::resource('grades', AdminGradeController::class)->parameters(['grades' => 'grade']);
        Route::get('grades-livewire', AdminGradeIndexLW::class)->name('grades-livewire.index');

        // Admin Reports (Livewire components)
        Route::get('reports/equipment-inventory', AdminEquipmentInventoryReportLW::class)->name('reports.equipment-inventory');
        Route::get('reports/equipment-report', AdminEquipmentReportLW::class)->name('reports.equipment-report');
        Route::get('reports/loan-applications', AdminLoanApplicationsReportLW::class)->name('reports.loan-applications');
        Route::get('reports/user-activity', AdminUserActivityReportLW::class)->name('reports.user-activity');
    });

    // -------------------------
    // Reports Module (Livewire components + legacy controller reports)
    // -------------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        // Livewire report components
        Route::get('/', ReportsIndexLW::class)->name('index');
        Route::get('/equipment-inventory', EquipmentReportLW::class)->name('equipment-inventory');
        Route::get('/loan-applications', LoanApplicationsReportLW::class)->name('loan-applications');
        Route::get('/user-activity', UserActivityReportLW::class)->name('user-activity');

        // Legacy/non-Livewire controller-based reports
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history');
        Route::get('/loan-status-summary', [ReportController::class, 'loanStatusSummary'])->name('loan-status-summary');
        Route::get('/utilization-report', [ReportController::class, 'utilizationReport'])->name('utilization-report');
        Route::get('/helpdesk-tickets', [ReportController::class, 'helpdeskTickets'])->name('helpdesk-tickets');
    });

    // -------------------------
    // Helpdesk Module (Support Ticket System)
    // -------------------------
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        // Admin/IT Admin/Helpdesk Agent routes (must come before catch-all routes)
        Route::middleware(['role:Admin|IT Admin|Helpdesk Agent'])->group(function () {
            Route::get('/admin/tickets', AdminTicketManagementLW::class)->name('admin.index');
            // Traditional controller routes for ticket management
            Route::resource('/admin/tickets-controller', HelpdeskTicketController::class)
                ->names([
                    'index'   => 'admin.tickets-controller.index',
                    'create'  => 'admin.tickets-controller.create',
                    'store'   => 'admin.tickets-controller.store',
                    'show'    => 'admin.tickets-controller.show',
                    'edit'    => 'admin.tickets-controller.edit',
                    'update'  => 'admin.tickets-controller.update',
                    'destroy' => 'admin.tickets-controller.destroy',
                ]);
        });

        // User routes - view own tickets
        Route::get('/', MyTicketsIndex::class)->name('index');
        Route::get('/create', CreateTicketForm::class)->name('create');

        // Ticket details (constrain to numbers to avoid conflicts with '/admin')
        Route::get('/{ticket}', TicketDetails::class)
            ->name('show')
            ->whereNumber('ticket');
    });

    // -------------------------
    // Human Resource Management (Optional - Alternative to Settings)
    // -------------------------
    Route::prefix('hr')->name('hr.')->middleware(['role:Admin|HR Admin'])->group(function () {
        Route::get('/departments', HRDepartmentsLW::class)->name('departments.index');
        Route::get('/positions', HRPositionsLW::class)->name('positions.index');
        Route::get('/employees', HREmployeeInfoLW::class)->name('employees.index');
    });

    // -------------------------
    // System Settings Panel (Admin Only Access)
    // -------------------------
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        // Default settings page
        Route::get('/', SettingsUsersIndexLW::class)->name('index');

        // User Management (Livewire Components)
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index');
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create');
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit');

        // Roles Management
        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index');

        // Permissions Management
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index');

        // Grades (controller-based resource for CRUD operations)
        Route::resource('grades', AdminGradeController::class)->parameters(['grades' => 'grade']);

        // Departments Management
        Route::get('/departments', SettingsDepartmentsIndexLW::class)->name('departments.index');

        // Positions Management (Livewire component not available, using controller)
        // Route::get('/positions', SettingsPositionsIndexLW::class)->name('positions.index');
        // Using controller until Livewire component is created
        Route::resource('positions', AdminPositionController::class);

        // Log Viewer (3rd party package)
        Route::get('/log-viewer/{view?}', [\Opcodes\LogViewer\Http\Controllers\IndexController::class, '__invoke'])
            ->where('view', '(?!api).*')
            ->name('log-viewer.index')
            ->middleware(['view_logs']);
    });

    // -------------------------
    // Charts and Analytics
    // -------------------------
    Route::get('/charts/loan-summary', LoanSummaryChart::class)->name('charts.loan-summary');

    // -------------------------
    // Miscellaneous Pages
    // -------------------------
    Route::get('/coming-soon', ComingSoonLW::class)->name('misc.coming-soon');
    Route::get('/equipment-checklist', EquipmentChecklistLW::class)->name('misc.equipment-checklist');
    Route::get('/loan-request-form', LoanRequestFormLW::class)->name('misc.loan-request-form');

    // -------------------------
    // API Webhook Routes (for external integrations)
    // -------------------------
    Route::prefix('webhooks')->name('webhooks.')->middleware(['validate.webhook.signature'])->group(function () {
        // Webhook endpoints for external system integrations
        Route::post('/equipment-update', [WebhookController::class, 'equipmentUpdate'])->name('equipment.update');
        Route::post('/loan-status-update', [WebhookController::class, 'loanStatusUpdate'])->name('loan.status.update');
        Route::post('/user-sync', [WebhookController::class, 'userSync'])->name('user.sync');
    });
});

// --------------------------------------------------
// ApprovalController and LoanTransactionController: Traditional (non-Livewire) routes
// --------------------------------------------------

// ApprovalController standard routes (for approval officers)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('approvals')
    ->name('approvals.')
    ->group(function () {
        // Static routes first
        Route::get('/tasks', [ApprovalController::class, 'index'])->name('tasks');

        // Dynamic routes (constrained to numbers)
        Route::get('/{approval}', [ApprovalController::class, 'show'])
            ->name('show')
            ->whereNumber('approval');

        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])
            ->name('decision')
            ->whereNumber('approval');
    });

// Traditional transaction listing and detail routes (LoanTransactionController)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('loan-transactions')
    ->name('loan-transactions.')
    ->group(function () {
        Route::get('/', [LoanTransactionController::class, 'index'])->name('index');

        Route::get('/{loanTransaction}', [LoanTransactionController::class, 'show'])
            ->name('show')
            ->whereNumber('loanTransaction');
    });

// --------------------------------------------------
// Dashboard Controller Routes (Traditional MVC, if still needed)
// --------------------------------------------------
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        // Alternative dashboard routes using controller (if Livewire components are not preferred)
        Route::get('/dashboard-controller', [DashboardController::class, 'index'])->name('dashboard.controller');
        Route::get('/dashboard-controller/admin', [DashboardController::class, 'admin'])
            ->name('dashboard.controller.admin')
            ->middleware(['role:Admin|IT Admin']);
    });

// --------------------------------------------------
// Fallback Route - Handles 404 errors for undefined routes
// --------------------------------------------------
// Returns a custom 404 error view if no other route matches.
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
| NOTE: The vertical menu is now standardized to use the Livewire component
| at resources/views/livewire/sections/menu/vertical-menu.blade.php.
|
| All layouts should include the menu as:
|     @livewire('sections.menu.vertical-menu')
| and remove any legacy includes such as:
|     @include('layouts.sections.menu.vertical-menu')
|     @include('partials.sidebar-partial')
|
| The menu is dynamically rendered based on config/menu.php and user role.
| No routes are required for sidebar/vertical menu rendering as it is
| handled by the Livewire component and the main layout.
|
| See documentation in the component and Blade files for details.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Route Debugging and Documentation
|--------------------------------------------------------------------------
| To view all registered routes, use: php artisan route:list
| To view routes for a specific name: php artisan route:list --name=admin
| To view routes for a specific method: php artisan route:list --method=GET
|
| Route naming conventions:
| - Public routes: simple names (home, contact-us, etc.)
| - Admin routes: admin.{resource}.{action}
| - API routes: api.{resource}.{action}
| - Settings routes: settings.{resource}.{action}
| - Livewire routes: Use class names directly
|--------------------------------------------------------------------------
*/
