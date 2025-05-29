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
use App\Livewire\ResourceManagement\Admin\Grades\Index as AdminGradesIndexLW; // System Design 9.2 [cite: 1]
use App\Livewire\ResourceManagement\Admin\Users\Index as AdminUsersIndexLW;   // System Design 9.3 [cite: 1]
// Settings Livewire Components
use App\Livewire\ResourceManagement\MyApplications\Email\Index as EmailApplicationsIndexLW;
use App\Livewire\ResourceManagement\MyApplications\Loan\Index as LoanApplicationsIndexLW;
use App\Livewire\Settings\CreateUser as CreateSettingsUserLW;
use App\Livewire\Settings\Departments\Index as AdminDepartmentsIndexLW;
use App\Livewire\Settings\EditUser as EditSettingsUserLW;
use App\Livewire\Settings\Permissions as SettingsPermissionsLW;
use App\Livewire\Settings\Positions\Index as AdminPositionsIndexLW;
use App\Livewire\Settings\Roles as SettingsRolesLW;
use App\Livewire\Settings\ShowUser as ShowSettingsUserLW;
use App\Livewire\Settings\Users as SettingsUsersLW;
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
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // Notifications - System Design 9.5 [cite: 1]
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Approval Dashboard and Actions - System Design 6.2, 9.4 [cite: 1]
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index')->middleware(['permission:view_any_approvals']);
        Route::get('/dashboard', ApprovalDashboard::class)->name('dashboard')
            ->middleware(['permission:view_approval_tasks']);
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show')->middleware('can:view,approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision')->middleware('can:process,approval');
    });

    // --- User's Own Applications (My Applications Section) ---
    // Renamed existing groups to reflect "my-applications" context for clarity
    // URL: /my-email-applications/*, Names: resource-management.my-applications.email-applications.* (adjusting to match error if this is the structure)
    // OR keeping it simple as: email-applications.* (current)
    // System Design 9.2 [cite: 1]
    Route::prefix('email-applications')->name('email-applications.')->group(function () {
        Route::get('/', EmailApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . EmailApplication::class);
        // The create route for "My Applications" section might point to the same global form
        // Route::get('/create', EmailApplicationFormLW::class)->name('create')->middleware('can:create,' . EmailApplication::class); // This was email-applications.create
        Route::post('/', [EmailApplicationController::class, 'store'])->name('store')->middleware('can:create,' . EmailApplication::class);
        Route::get('/{email_application}', [EmailApplicationController::class, 'show'])->name('show')->middleware('can:view,email_application');
        Route::get('/{email_application}/edit', EmailApplicationFormLW::class)->name('edit')->middleware('can:update,email_application');
        Route::put('/{email_application}', [EmailApplicationController::class, 'update'])->name('update')->middleware('can:update,email_application');
        Route::delete('/{email_application}', [EmailApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,email_application');
        Route::post('/{email_application}/submit', [EmailApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,email_application');
    });

    // URL: /loan-applications/*, Names: loan-applications.*
    // System Design 9.3 [cite: 1]
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/', LoanApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . LoanApplication::class);
        // The create route for "My Applications" section might point to the same global form
        // Route::get('/create', LoanRequestFormLW::class)->name('create')->middleware('can:create,' . LoanApplication::class); // This was loan-applications.create
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store')->middleware('can:create,' . LoanApplication::class);
        Route::get('/{loan_application}', [LoanApplicationController::class, 'show'])->name('show')->middleware('can:view,loan_application');
        Route::get('/{loan_application}/edit', LoanRequestFormLW::class)->name('edit')->middleware('can:update,loan_application');
        Route::put('/{loan_application}', [LoanApplicationController::class, 'update'])->name('update')->middleware('can:update,loan_application');
        Route::delete('/{loan_application}', [LoanApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,loan_application');
        Route::post('/{loan_application}/submit', [LoanApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,loan_application');
    });


    // --- Resource Management (Contains Admin, BPM, and general application forms) ---
    Route::prefix('resource-management')->name('resource-management.')->group(function () {

        // General Application Forms (Create new applications)
        Route::prefix('application-forms')->name('application-forms.')->group(function () {
            // ** NEWLY ADDED ROUTE TO FIX THE ERROR **
            Route::get('/email/create', EmailApplicationFormLW::class)
                 ->name('email.create') // This makes the full name: resource-management.application-forms.email.create
                 ->middleware('can:create,' . EmailApplication::class);

            // You would add a similar one for loan applications if its create button uses a similar new name
            Route::get('/loan/create', LoanRequestFormLW::class)
                 ->name('loan.create') // Full name: resource-management.application-forms.loan.create
                 ->middleware('can:create,' . LoanApplication::class);
        });

        // Admin & BPM specific tasks
        Route::middleware(['role:Admin|BPM Staff|IT Admin'])->group(function () {
            Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPM Staff'])->group(function () {
                Route::get('/outstanding-loans', OutstandingLoans::class)->name('outstanding-loans');
                Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');
                Route::get('/loan-transactions/issue/{loanApplication}/form', fn (LoanApplication $loanApplication) => view('resource-management.admin.bpm.issue-page', compact('loanApplication')))->name('loan-transactions.issue.form')->middleware('can:processIssuance,loanApplication');
                Route::get('/loan-transactions/return/{loanTransaction}/form', fn (LoanTransaction $loanTransaction) => view('resource-management.admin.bpm.return-page', compact('loanTransaction')))->name('loan-transactions.return.form')->middleware('can:processReturn,loanTransaction.loanApplication');
                Route::get('/loan-transactions/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('loan-transactions.show')->middleware('can:view,loanTransaction');
            });

            Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPM Staff'])->group(function () {
                Route::get('/', AdminEquipmentIndexLW::class)->name('index')->middleware('can:viewAny,'.Equipment::class);
                Route::get('/create', fn () => view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => null]))->name('create')->middleware('can:create,'.Equipment::class);
                Route::get('/{equipment}/edit', fn (Equipment $equipment) => view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => $equipment->id]))->name('edit')->middleware('can:update,equipment');
            });

            Route::prefix('email-applications-admin')->name('email-applications-admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
                Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index')->middleware('can:viewAny,'.EmailApplication::class);
                Route::get('/{email_application}', [EmailAccountController::class, 'showForAdmin'])->name('show')->middleware('can:view,email_application');
                Route::post('/{email_application}/process', [EmailAccountController::class, 'processApplication'])->name('process')->middleware('can:processByIT,email_application');
            });

            Route::prefix('users-admin')->name('users-admin.')->middleware(['role:Admin'])->group(function () {
                Route::get('/', AdminUsersIndexLW::class)->name('index')->middleware('can:viewAny,'.User::class);
            });
        });
    });

    // Settings Section (Admin only) - System Design 9.1, 9.7 [cite: 1]
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        Route::get('/users', SettingsUsersLW::class)->name('users.index')->middleware('can:viewAny,'.User::class);
        Route::get('/users/create', CreateSettingsUserLW::class)->name('users.create')->middleware('can:create,'.User::class);
        Route::get('/users/{user}', ShowSettingsUserLW::class)->name('users.show')->middleware('can:view,user');
        Route::get('/users/{user}/edit', EditSettingsUserLW::class)->name('users.edit')->middleware('can:update,user');

        Route::get('/roles', SettingsRolesLW::class)->name('roles.index')->middleware('permission:manage_roles');
        Route::get('/permissions', SettingsPermissionsLW::class)->name('permissions.index')->middleware('permission:manage_permissions');

        Route::get('/grades', AdminGradesIndexLW::class)->name('grades.index')->middleware('can:viewAny,'.Grade::class);
        // Example: Route::get('/departments', AdminDepartmentsIndexLW::class)->name('departments.index')->middleware('can:viewAny,'.Department::class);
        // Example: Route::get('/positions', AdminPositionsIndexLW::class)->name('positions.index')->middleware('can:viewAny,'.Position::class);
    });

    // Reports Section - System Design 9.5 [cite: 1]
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPM Staff'])->group(function () {
        Route::get('/equipment', fn () => view('reports.equipment_report_page'))->name('equipment')->middleware('permission:view_equipment_reports');
        Route::get('/loan-applications', fn () => view('reports.loan_applications_report_page'))->name('loan-applications')->middleware('permission:view_loan_reports');
        Route::get('/user-activity', fn () => view('reports.user_activity_report_page'))->name('user-activity')->middleware('permission:view_user_activity_reports');
        Route::get('/email-accounts', fn () => view('reports.email_accounts_report_page'))->name('email-accounts')->middleware('permission:view_email_reports');
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    abort(404, __('Laman Tidak Ditemui. Sila semak URL atau kembali ke papan pemuka.'));
});
