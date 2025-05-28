<?php

declare(strict_types=1);

// General Controllers for MOTAC System & Livewire Components
// System Design Reference: 3.1 (Key active PHP controllers, Livewire Components) [cite: 1]
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EmailAccountController;       // Admin side for email apps
use App\Http\Controllers\EmailApplicationController;  // User side for email apps
use App\Http\Controllers\EquipmentController;         // Public viewing of equipment
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LoanApplicationController;    // User side for loan apps
use App\Http\Controllers\LoanTransactionController;   // Admin side for loan transactions
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WebhookController;
// Misc controllers for error pages etc.
// use App\Http\Controllers\MiscErrorController;

// Livewire Components
use App\Livewire\ApprovalDashboard;
use App\Livewire\ContactUs; // Public page
use App\Livewire\Dashboard;         // System Design 9.6 [cite: 1]
// Corrected namespaces for Livewire form components
use App\Livewire\EmailApplicationForm as EmailApplicationFormLW;         // System Design 6.3, 9.2 [cite: 1]
use App\Livewire\LoanRequestForm as LoanRequestFormLW;                   // System Design 6.3, 9.3 [cite: 1]
// Admin & Settings Livewire Components (from System Design 9.1, 9.3, 9.7) [cite: 1]
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
// Email/Loan Application Index Livewire Components
use App\Livewire\ResourceManagement\MyApplications\Email\Index as EmailApplicationsIndexLW; // System Design 9.2 [cite: 1]
use App\Livewire\ResourceManagement\MyApplications\Loan\Index as LoanApplicationsIndexLW;   // System Design 9.3 [cite: 1]
// Settings Livewire Components
use App\Livewire\Settings\CreateUser as CreateSettingsUserLW;
use App\Livewire\Settings\Departments\Index as AdminDepartmentsIndexLW; // Assuming you might add this for settings/admin
use App\Livewire\Settings\EditUser as EditSettingsUserLW;
use App\Livewire\Settings\Permissions as SettingsPermissionsLW;
use App\Livewire\Settings\Positions\Index as AdminPositionsIndexLW;   // Assuming you might add this for settings/admin
use App\Livewire\Settings\Roles as SettingsRolesLW;
use App\Livewire\Settings\ShowUser as ShowSettingsUserLW;
use App\Livewire\Settings\Users as SettingsUsersLW;
use App\Livewire\ResourceManagement\Admin\Grades\Index as AdminGradesIndexLW; // For settings/admin
use App\Livewire\ResourceManagement\Admin\Users\Index as AdminUsersIndexLW;   // For settings/admin


// Models for Route Model Binding
use App\Models\Approval;
use App\Models\Department;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Language switcher route - System Design 3.1, 3.3 [cite: 1]
Route::get('lang/{locale}', LanguageController::class)->name('language.swap');
Route::get('/contact-us', ContactUs::class)->name('contact-us'); // Public contact page

// Webhook for CI/CD deployment - System Design 3.2, 8.3 [cite: 1]
Route::post('/webhooks/deploy', [WebhookController::class, 'handleDeploy'])
    ->name('webhooks.deploy')
    ->middleware('validate.webhook.signature');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    // 'session.locale', // Assuming LocaleMiddleware is in the global stack or a group
])->group(function () {
    Route::redirect('/', '/dashboard', 301); // Default redirect to dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard'); // System Design 9.6 [cite: 1]

    // Public viewing of equipment - System Design 9.3 [cite: 1]
    // (Kept outside 'resource-management' prefix for simpler URL like /equipment)
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // Notifications - System Design 9.5 [cite: 1]
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Approval Dashboard and Actions - System Design 6.2, 9.4 [cite: 1]
    // URL: /approvals/*, Names: approvals.*
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index')->middleware(['permission:view_any_approvals']); // System Design 9.4 [cite: 1]
        Route::get('/dashboard', ApprovalDashboard::class)->name('dashboard') // Changed from /approval-dashboard to /approvals/dashboard
            ->middleware(['permission:view_approval_tasks']); // System Design 6.2, 7.1 [cite: 1]
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show')->middleware('can:view,approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision')->middleware('can:process,approval');
    });

    // --- User's Own Applications (Email & Loan) ---
    // URL: /email-applications/*, Names: email-applications.*
    // System Design 9.2 [cite: 1]
    Route::prefix('email-applications')->name('email-applications.')->group(function () {
        Route::get('/', EmailApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . EmailApplication::class); // User's list of email apps
        Route::get('/create', EmailApplicationFormLW::class)->name('create')->middleware('can:create,' . EmailApplication::class); // Create form
        Route::post('/', [EmailApplicationController::class, 'store'])->name('store')->middleware('can:create,' . EmailApplication::class); // Store new
        Route::get('/{email_application}', [EmailApplicationController::class, 'show'])->name('show')->middleware('can:view,email_application'); // View details
        Route::get('/{email_application}/edit', EmailApplicationFormLW::class)->name('edit')->middleware('can:update,email_application'); // Edit Form (using Livewire)
        Route::put('/{email_application}', [EmailApplicationController::class, 'update'])->name('update')->middleware('can:update,email_application'); // Update existing
        Route::delete('/{email_application}', [EmailApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,email_application'); // Delete draft
        Route::post('/{email_application}/submit', [EmailApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,email_application'); // Submit application
    });

    // URL: /loan-applications/*, Names: loan-applications.*
    // System Design 9.3 [cite: 1]
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/', LoanApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . LoanApplication::class); // User's list of loan apps
        Route::get('/create', LoanRequestFormLW::class)->name('create')->middleware('can:create,' . LoanApplication::class); // Create form
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store')->middleware('can:create,' . LoanApplication::class); // Store new
        Route::get('/{loan_application}', [LoanApplicationController::class, 'show'])->name('show')->middleware('can:view,loan_application'); // View details
        Route::get('/{loan_application}/edit', LoanRequestFormLW::class)->name('edit')->middleware('can:update,loan_application'); // Edit form (using Livewire)
        Route::put('/{loan_application}', [LoanApplicationController::class, 'update'])->name('update')->middleware('can:update,loan_application'); // Update existing
        Route::delete('/{loan_application}', [LoanApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,loan_application'); // Delete draft
        Route::post('/{loan_application}/submit', [LoanApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,loan_application'); // Submit application
    });


    // --- Resource Management (Admin, BPM specific tasks) ---
    // URL: /resource-management/*, Names: resource-management.*
    Route::prefix('resource-management')->name('resource-management.')->middleware(['role:Admin|BPM Staff|IT Admin'])->group(function () {
        // BPM Specific Routes (Loan Management) - System Design 6.2, 9.3 [cite: 1]
        Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPM Staff'])->group(function () {
            Route::get('/outstanding-loans', OutstandingLoans::class)->name('outstanding-loans');
            Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');
            Route::get('/loan-transactions/issue/{loanApplication}/form', fn (LoanApplication $loanApplication) => view('resource-management.admin.bpm.issue-page', compact('loanApplication')))->name('loan-transactions.issue.form')->middleware('can:processIssuance,loanApplication');
            Route::get('/loan-transactions/return/{loanTransaction}/form', fn (LoanTransaction $loanTransaction) => view('resource-management.admin.bpm.return-page', compact('loanTransaction')))->name('loan-transactions.return.form')->middleware('can:processReturn,loanTransaction.loanApplication');
            Route::get('/loan-transactions/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('loan-transactions.show')->middleware('can:view,loanTransaction'); // System Design 9.3 [cite: 1]
        });

        // Equipment Admin (CRUD for equipment inventory) - System Design 9.3 [cite: 1]
        Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPM Staff'])->group(function () {
            Route::get('/', AdminEquipmentIndexLW::class)->name('index')->middleware('can:viewAny,'.Equipment::class);
            Route::get('/create', fn () => view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => null]))->name('create')->middleware('can:create,'.Equipment::class);
            Route::get('/{equipment}/edit', fn (Equipment $equipment) => view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => $equipment->id]))->name('edit')->middleware('can:update,equipment');
        });

        // Email Applications Admin (Processing by IT Admin) - System Design 9.2 [cite: 1]
        Route::prefix('email-applications-admin')->name('email-applications-admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
            Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index')->middleware('can:viewAny,'.EmailApplication::class);
            Route::get('/{email_application}', [EmailAccountController::class, 'showForAdmin'])->name('show')->middleware('can:view,email_application');
            Route::post('/{email_application}/process', [EmailAccountController::class, 'processApplication'])->name('process')->middleware('can:processByIT,email_application');
        });

        // Users Admin (Listing by Admin, if different from Settings) - System Design 9.1 [cite: 1]
        // This might be redundant if Settings/Users covers it, or it's a specific view for resource mgmt context.
        Route::prefix('users-admin')->name('users-admin.')->middleware(['role:Admin'])->group(function () {
            Route::get('/', AdminUsersIndexLW::class)->name('index')->middleware('can:viewAny,'.User::class);
        });
    });

    // Settings Section (Admin only) - System Design 9.1, 9.7 [cite: 1]
    // URL: /settings/*, Names: settings.*
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        // User CRUD for settings - System Design 9.1 [cite: 1]
        Route::get('/users', SettingsUsersLW::class)->name('users.index')->middleware('can:viewAny,'.User::class);
        Route::get('/users/create', CreateSettingsUserLW::class)->name('users.create')->middleware('can:create,'.User::class);
        Route::get('/users/{user}', ShowSettingsUserLW::class)->name('users.show')->middleware('can:view,user');
        Route::get('/users/{user}/edit', EditSettingsUserLW::class)->name('users.edit')->middleware('can:update,user');

        // Roles & Permissions Management - System Design 9.1 [cite: 1]
        Route::get('/roles', SettingsRolesLW::class)->name('roles.index')->middleware('permission:manage_roles');
        Route::get('/permissions', SettingsPermissionsLW::class)->name('permissions.index')->middleware('permission:manage_permissions');

        // Grades, Departments, Positions Management - System Design 9.1 [cite: 1]
        Route::get('/grades', AdminGradesIndexLW::class)->name('grades.index')->middleware('can:viewAny,'.Grade::class);
        // You may add Department & Position admin routes here if they are managed under settings
        // Example: Route::get('/departments', AdminDepartmentsIndexLW::class)->name('departments.index')->middleware('can:viewAny,'.Department::class);
        // Example: Route::get('/positions', AdminPositionsIndexLW::class)->name('positions.index')->middleware('can:viewAny,'.Position::class);
    });

    // Reports Section - System Design 9.5 [cite: 1]
    // URL: /reports/*, Names: reports.*
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPM Staff'])->group(function () {
        Route::get('/equipment', fn () => view('reports.equipment_report_page'))->name('equipment')->middleware('permission:view_equipment_reports');
        Route::get('/loan-applications', fn () => view('reports.loan_applications_report_page'))->name('loan-applications')->middleware('permission:view_loan_reports');
        Route::get('/user-activity', fn () => view('reports.user_activity_report_page'))->name('user-activity')->middleware('permission:view_user_activity_reports');
        Route::get('/email-accounts', fn () => view('reports.email_accounts_report_page'))->name('email-accounts')->middleware('permission:view_email_reports');
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    // Ensure the message is translatable as per design principles
    abort(404, __('Laman Tidak Ditemui. Sila semak URL atau kembali ke papan pemuka.'));
});
