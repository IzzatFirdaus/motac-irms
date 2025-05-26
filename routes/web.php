<?php

declare(strict_types=1);

// General Controllers for MOTAC System
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailApplicationController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebhookController; // Added for deploy webhook example

// Livewire Components for MOTAC System
use App\Livewire\ApprovalDashboard;
use App\Livewire\ContactUs;
use App\Livewire\Dashboard;
use App\Livewire\EmailApplicationForm;
use App\Livewire\LoanRequestForm;
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Admin\Grades\Index as AdminGradesIndexLW;
use App\Livewire\ResourceManagement\Admin\Users\Index as AdminUsersIndexLW; // Assuming this was intended for admin user listing
// Report Livewire Components (assuming they are embedded in views returned by ReportController)
use App\Livewire\ResourceManagement\Admin\Reports\EmailAccountsReport;
use App\Livewire\ResourceManagement\Admin\Reports\EquipmentReport;
use App\Livewire\ResourceManagement\Admin\Reports\LoanApplicationsReport;
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport;

use App\Livewire\ResourceManagement\MyApplications\Email\Index as EmailApplicationsIndexLW;
use App\Livewire\ResourceManagement\MyApplications\Loan\Index as LoanApplicationsIndexLW;
use App\Livewire\Settings\CreateUser as CreateSettingsUserLW;
use App\Livewire\Settings\EditUser as EditSettingsUserLW;
use App\Livewire\Settings\Permissions as SettingsPermissionsLW;
use App\Livewire\Settings\Roles as SettingsRolesLW;
use App\Livewire\Settings\ShowUser as ShowSettingsUserLW;
use App\Livewire\Settings\Users as SettingsUsersLW;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Language switcher route
Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('language.swap');

// Publicly accessible routes
Route::get('/contact-us', ContactUs::class)->name('contact-us');
// Corrected Webhook route to point to a controller action, assuming POST method
Route::post('/webhooks/deploy', [WebhookController::class, 'handleDeploy'])->name('webhooks.deploy'); // For CI/CD GitHub webhook

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::redirect('/', '/dashboard', 301);
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // MOTAC: ICT Equipment - General user view.
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']); // Users likely only view [cite: 1]

    // MOTAC: Approval Workflow
    Route::get('/approval-dashboard', ApprovalDashboard::class)->name('approval-dashboard'); // [cite: 1]
    Route::prefix('approvals')->name('approvals.')->group(function (): void { // [cite: 1]
        Route::get('/', [ApprovalController::class, 'index'])->name('index'); // [cite: 1]
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show'); // [cite: 1]
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision'); // [cite: 1]
    });

    // MOTAC: Core Resource Management Modules
    Route::prefix('resource-management')->name('resource-management.')->group(function (): void { // [cite: 1]
        // Application Forms (Livewire components)
        Route::prefix('application-forms')->name('application-forms.')->group(function (): void { // [cite: 1]
            Route::get('/email/create', EmailApplicationForm::class)->name('email.create'); // [cite: 1]
            Route::get('/loan/create', LoanRequestForm::class)->name('loan.create'); // [cite: 1]
        });

        // User's own applications
        Route::prefix('my-applications')->name('my-applications.')->group(function (): void { // [cite: 1]
            Route::get('/email', EmailApplicationsIndexLW::class)->name('email.index'); // [cite: 1]
            Route::get('/loan', LoanApplicationsIndexLW::class)->name('loan.index');   // [cite: 1]

            // CRUD for user's own Email Applications
            Route::get('/email-applications/{email_application}', [EmailApplicationController::class, 'show'])->name('email-applications.show'); // Specific show route for user
            Route::resource('email-applications', EmailApplicationController::class)->except(['index', 'create', 'show']); // `create` is EmailApplicationForm, `index` by LW
            Route::post('/email-applications/{emailApplication}/submit', [EmailApplicationController::class, 'submitApplication'])->name('email-applications.submit'); // [cite: 1]

            // CRUD for user's own Loan Applications
            Route::get('/loan-applications/{loan_application}', [LoanApplicationController::class, 'show'])->name('loan-applications.show'); // Specific show route for user
            Route::resource('loan-applications', LoanApplicationController::class)->except(['index', 'create', 'show']); // `create` is LoanRequestForm, `index` by LW
            Route::post('/loan-applications/{loanApplication}/submit', [LoanApplicationController::class, 'submitApplication'])->name('loan-applications.submit'); // Added submit for consistency
        });

        // Admin sections for Resource Management
        Route::prefix('admin')->name('admin.')->middleware(['role:Admin|BPMStaff|IT Admin'])->group(function (): void { // [cite: 1]
            Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // [cite: 1]
                Route::get('/outstanding-loans', OutstandingLoans::class)->name('outstanding-loans'); // [cite: 1]
                Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');             // [cite: 1]
            });

            Route::prefix('equipment')->name('equipment.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // Renamed for clarity [cite: 1]
                Route::get('/', AdminEquipmentIndexLW::class)->name('index'); // [cite: 1]
                // CRUD for equipment within Livewire component or specific controller if needed
            });

            Route::prefix('loan-transactions')->name('loan-transactions.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // [cite: 1]
                Route::get('/issue/{loanApplication}/form', [LoanTransactionController::class, 'showIssueForm'])->name('issue.form'); // [cite: 1]
                Route::post('/issue/{loanApplication}', [LoanTransactionController::class, 'issueEquipment'])->name('issue'); // [cite: 1]
                // Route::get('/issued-loans-list', [LoanTransactionController::class, 'listIssuedLoans'])->name('issued-loans-list'); // This seems redundant with bpm.issued-loans [cite: 1]
                Route::get('/return/{loanTransaction}/form', [LoanTransactionController::class, 'showReturnForm'])->name('return.form'); // [cite: 1]
                Route::post('/return/{loanTransaction}', [LoanTransactionController::class, 'processReturn'])->name('return'); // [cite: 1]
                Route::get('/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('show'); // [cite: 1]
            });

            // Email Account Processing by IT Admin
            Route::prefix('email-applications')->name('email-applications.')->middleware(['role:Admin|IT Admin'])->group(function() { // Changed group name for clarity
                 Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index'); // Admin view of all email applications
                 Route::get('/{emailApplication}', [EmailAccountController::class, 'showForAdmin'])->name('show'); // Admin view specific application
                 Route::post('/{emailApplication}/process', [EmailAccountController::class, 'processApplication'])
                     ->name('process')
                     ->middleware('can:process,emailApplication'); // [cite: 1]
            });

            // Admin view of Users (distinct from Settings Users management)
            Route::prefix('users')->name('users.')->middleware(['role:Admin'])->group(function() {
                Route::get('/', AdminUsersIndexLW::class)->name('index');
            });
        });
    });

    // MOTAC: System Settings (Admin only)
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function (): void { // [cite: 1]
        Route::get('/users', SettingsUsersLW::class)->name('users.index'); // [cite: 1]
        Route::get('/users/create', CreateSettingsUserLW::class)->name('users.create'); // [cite: 1]
        Route::get('/users/{user}', ShowSettingsUserLW::class)->name('users.show');       // [cite: 1]
        Route::get('/users/{user}/edit', EditSettingsUserLW::class)->name('users.edit');   // [cite: 1]
        Route::get('/roles', SettingsRolesLW::class)->name('roles.index'); // [cite: 1]
        Route::get('/permissions', SettingsPermissionsLW::class)->name('permissions.index'); // [cite: 1]
        Route::get('/grades', AdminGradesIndexLW::class)->name('grades.index'); // [cite: 1]
        // Potential routes for Departments and Positions:
        // Route::get('/departments', \App\Livewire\Settings\Departments::class)->name('departments.index');
        // Route::get('/positions', \App\Livewire\Settings\Positions::class)->name('positions.index');
    });

    // MOTAC: Reports (Protected by role)
    // These routes will render a Blade view which then embeds the respective Livewire report component.
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // Widened role access for reports if BPMStaff needs them too
        Route::get('/equipment', fn() => view('reports.equipment_report_page'))->name('equipment'); // View embeds EquipmentReport LW [cite: 1]
        Route::get('/loan-applications', fn() => view('reports.loan_applications_report_page'))->name('loan-applications'); // View embeds LoanApplicationsReport LW [cite: 1]
        Route::get('/user-activity', fn() => view('reports.user_activity_report_page'))->name('user-activity'); // View embeds UserActivityReport LW [cite: 1]
        Route::get('/email-accounts', fn() => view('reports.email_accounts_report_page'))->name('email-accounts'); // View embeds EmailAccountsReport LW
    });

    // MOTAC: User Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index'); // [cite: 1]
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

// Fallback route (optional)
Route::fallback(function() {
    // You can customize this, e.g., return a view('errors.404-custom')
    abort(404, 'Halaman Tidak Ditemui.');
});
