<?php

declare(strict_types=1);

// General Controllers for MOTAC System
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailApplicationController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController; // Keep for show method if still used
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebhookController;

// Livewire Components for MOTAC System
use App\Livewire\ApprovalDashboard;
use App\Livewire\ContactUs;
use App\Livewire\Dashboard;
use App\Livewire\EmailApplicationForm;
use App\Livewire\LoanRequestForm;
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans;
// Livewire components for new forms
use App\Livewire\ResourceManagement\Admin\Equipment\EquipmentForm; // For create/edit equipment
use App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance;    // For issuing loans
use App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn;     // For returning loans

use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Admin\Grades\Index as AdminGradesIndexLW;
use App\Livewire\ResourceManagement\Admin\Users\Index as AdminUsersIndexLW;

// Report Livewire Components
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
use App\Models\Equipment; // For route model binding
use App\Models\LoanApplication; // For route model binding
use App\Models\LoanTransaction; // For route model binding

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Language switcher route
Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('language.swap');

// Publicly accessible routes
Route::get('/contact-us', ContactUs::class)->name('contact-us');
Route::post('/webhooks/deploy', [WebhookController::class, 'handleDeploy'])->name('webhooks.deploy');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::redirect('/', '/dashboard', 301);
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // MOTAC: ICT Equipment - General user view (index and show only).
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

    // MOTAC: Approval Workflow
    Route::get('/approval-dashboard', ApprovalDashboard::class)->name('approval-dashboard');
    Route::prefix('approvals')->name('approvals.')->group(function (): void {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision');
    });

    // MOTAC: Core Resource Management Modules
    Route::prefix('resource-management')->name('resource-management.')->group(function (): void {
        // Application Forms (Livewire components)
        Route::prefix('application-forms')->name('application-forms.')->group(function (): void {
            Route::get('/email/create', EmailApplicationForm::class)->name('email.create');
            Route::get('/loan/create', LoanRequestForm::class)->name('loan.create'); // This is the ICT Loan Application Form
            // If you have an edit route for LoanRequestForm
            Route::get('/loan/{loan_application}/edit', LoanRequestForm::class)->name('loan.edit');
        });

        // User's own applications
        Route::prefix('my-applications')->name('my-applications.')->group(function (): void {
            Route::get('/email', EmailApplicationsIndexLW::class)->name('email.index');
            Route::get('/loan', LoanApplicationsIndexLW::class)->name('loan.index');

            // CRUD for user's own Email Applications
            Route::get('/email-applications/{email_application}', [EmailApplicationController::class, 'show'])->name('email-applications.show');
            Route::resource('email-applications', EmailApplicationController::class)->except(['index', 'create', 'show']);
            Route::post('/email-applications/{emailApplication}/submit', [EmailApplicationController::class, 'submitApplication'])->name('email-applications.submit');

            // CRUD for user's own Loan Applications
            Route::get('/loan-applications/{loan_application}', [LoanApplicationController::class, 'show'])->name('loan-applications.show');
            Route::resource('loan-applications', LoanApplicationController::class)->except(['index', 'create', 'show']);
            Route::post('/loan-applications/{loanApplication}/submit', [LoanApplicationController::class, 'submitApplication'])->name('loan-applications.submit');
        });

        // Admin sections for Resource Management
        Route::prefix('admin')->name('admin.')->middleware(['role:Admin|BPMStaff|IT Admin'])->group(function (): void {
            Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPMStaff'])->group(function (): void {
                Route::get('/outstanding-loans', OutstandingLoans::class)->name('outstanding-loans');
                Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');
            });

            // Admin Equipment Management (using Livewire Form)
            Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPMStaff'])->group(function (): void {
                Route::get('/', AdminEquipmentIndexLW::class)->name('index');
                // Route to display the Livewire form for creating equipment
                Route::get('/create', function () {
                    return view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => null]);
                })->name('create');
                // Route to display the Livewire form for editing equipment
                Route::get('/{equipment}/edit', function (Equipment $equipment) {
                    return view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => $equipment->id]);
                })->name('edit');
                // Note: The old EquipmentController store/update for admin are replaced by the Livewire component's saveEquipment method.
                // EquipmentController@show for admin might use the same public 'equipment.show' or a dedicated admin view if needed.
            });


            Route::prefix('loan-transactions')->name('loan-transactions.')->middleware(['role:Admin|BPMStaff'])->group(function (): void {
                // Updated route to show Livewire Issuance Form
                Route::get('/issue/{loanApplicationId}/form', function ($loanApplicationId) { // Changed param to loanApplicationId
                    return view('resource-management.admin.bpm.issue-page', ['loanApplicationId' => $loanApplicationId]);
                })->name('issue.form');
                // OLD: Route::get('/issue/{loanApplication}/form', [LoanTransactionController::class, 'showIssueForm'])->name('issue.form');

                // The POST route for issuing is handled by ProcessIssuance Livewire component
                // OLD: Route::post('/issue/{loanApplication}', [LoanTransactionController::class, 'issueEquipment'])->name('issue'); //

                // Updated route to show Livewire Return Form
                // Expects LoanTransaction ID based on link from issued-loans.blade.php
                Route::get('/return/{loanTransaction}/form', function (LoanTransaction $loanTransaction) {
                     // Pass the LoanTransaction ID to the view/Livewire component
                    return view('resource-management.admin.bpm.return-page', ['loanTransactionId' => $loanTransaction->id]);
                })->name('return.form');
                // OLD: Route::get('/return/{loanTransaction}/form', [LoanTransactionController::class, 'showReturnForm'])->name('return.form');

                // The POST route for returning is handled by ProcessReturn Livewire component
                // OLD: Route::post('/return/{loanTransaction}', [LoanTransactionController::class, 'processReturn'])->name('return'); //

                Route::get('/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('show'); // // Keep for viewing transaction details
            });

            // Email Account Processing by IT Admin
            Route::prefix('email-applications')->name('email-applications.')->middleware(['role:Admin|IT Admin'])->group(function() {
                 Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index');
                 Route::get('/{emailApplication}', [EmailAccountController::class, 'showForAdmin'])->name('show');
                 Route::post('/{emailApplication}/process', [EmailAccountController::class, 'processApplication'])
                     ->name('process')
                     ->middleware('can:process,emailApplication');
            });

            Route::prefix('users')->name('users.')->middleware(['role:Admin'])->group(function() {
                Route::get('/', AdminUsersIndexLW::class)->name('index');
            });
        });
    });

    // MOTAC: System Settings (Admin only)
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function (): void {
        Route::get('/users', SettingsUsersLW::class)->name('users.index');
        Route::get('/users/create', CreateSettingsUserLW::class)->name('users.create');
        Route::get('/users/{user}', ShowSettingsUserLW::class)->name('users.show');
        Route::get('/users/{user}/edit', EditSettingsUserLW::class)->name('users.edit');
        Route::get('/roles', SettingsRolesLW::class)->name('roles.index');
        Route::get('/permissions', SettingsPermissionsLW::class)->name('permissions.index');
        Route::get('/grades', AdminGradesIndexLW::class)->name('grades.index');
    });

    // MOTAC: Reports
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPMStaff'])->group(function (): void {
        Route::get('/equipment', fn() => view('reports.equipment_report_page'))->name('equipment');
        Route::get('/loan-applications', fn() => view('reports.loan_applications_report_page'))->name('loan-applications');
        Route::get('/user-activity', fn() => view('reports.user_activity_report_page'))->name('user-activity');
        Route::get('/email-accounts', fn() => view('reports.email_accounts_report_page'))->name('email-accounts');
    });

    // MOTAC: User Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

Route::fallback(function() {
    abort(404, 'Halaman Tidak Ditemui.');
});
