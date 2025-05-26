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
Route::get('/contact-us', ContactUs::class)->name('contact-us'); // Assuming ContactUs Livewire component exists
Route::webhooks('/deploy', 'deploy'); // For CI/CD GitHub webhook, ensure a controller or action handles 'deploy'

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::redirect('/', '/dashboard', 301); // Redirect root to dashboard after login
    Route::get('/dashboard', Dashboard::class)->name('dashboard'); // Main user dashboard

    // MOTAC: ICT Equipment - General user view and Admin CRUD if not fully Livewire.
    // Using resourceful controller for equipment.
    Route::resource('equipment', EquipmentController::class); // [cite: 1]

    // MOTAC: Approval Workflow
    Route::get('/approval-dashboard', ApprovalDashboard::class)->name('approval-dashboard'); // Livewire component for approvers [cite: 1]
    Route::prefix('approvals')->name('approvals.')->group(function (): void { // [cite: 1]
        Route::get('/', [ApprovalController::class, 'index'])->name('index'); // List pending approvals for the user [cite: 1]
        // Route::get('/history', [ApprovalController::class, 'showHistory'])->name('history'); // Consider if needed
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show'); // Show specific approval details [cite: 1]
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision'); // Record approval decision [cite: 1]
    });

    // MOTAC: Core Resource Management Modules (Email/ID Apps & ICT Loan Apps)
    Route::prefix('resource-management')->name('resource-management.')->group(function (): void { // [cite: 1]
        // Application Forms (Livewire components)
        Route::prefix('application-forms')->name('application-forms.')->group(function (): void { // [cite: 1]
            Route::get('/email/create', EmailApplicationForm::class)->name('email.create'); // [cite: 1]
            Route::get('/loan/create', LoanRequestForm::class)->name('loan.create'); // [cite: 1]
        });

        // User's own applications
        Route::prefix('my-applications')->name('my-applications.')->group(function (): void { // [cite: 1]
            Route::get('/email', EmailApplicationsIndexLW::class)->name('email.index'); // Livewire list for user's email apps [cite: 1]
            Route::get('/loan', LoanApplicationsIndexLW::class)->name('loan.index');   // Livewire list for user's loan apps [cite: 1]

            // CRUD for user's own Email Applications (excluding index, handled by Livewire)
            Route::resource('email-applications', EmailApplicationController::class)->except(['index', 'create']); // `create` is EmailApplicationForm
            Route::post('/email-applications/{emailApplication}/submit', [EmailApplicationController::class, 'submitApplication'])->name('email-applications.submit'); // Custom action to submit a drafted application [cite: 1]

            // CRUD for user's own Loan Applications (excluding index, handled by Livewire)
            Route::resource('loan-applications', LoanApplicationController::class)->except(['index', 'create']); // `create` is LoanRequestForm
             // Add submit route for loan applications if needed, similar to email apps
        });

        // Admin sections for Resource Management (Protected by roles)
        Route::prefix('admin')->name('admin.')->middleware(['role:Admin|BPMStaff|IT Admin'])->group(function (): void { // Ensure roles are defined in Spatie [cite: 1]
            Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // [cite: 1]
                Route::get('/outstanding-loans', OutstandingLoans::class)->name('outstanding-loans'); // Livewire for BPM [cite: 1]
                Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');             // Livewire for BPM [cite: 1]
            });
            Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // [cite: 1]
                Route::get('/', AdminEquipmentIndexLW::class)->name('index'); // Livewire for admin equipment list [cite: 1]
                // Standard CRUD for equipment managed by Admin/BPM staff can use EquipmentController
                // Or be handled within the Livewire component if it manages create/edit/delete.
                // Example: Route::get('/create', [EquipmentController::class, 'create'])->name('create');
                // Route::post('/', [EquipmentController::class, 'store'])->name('store');
                // Route::get('/{equipment}/edit', [EquipmentController::class, 'edit'])->name('edit');
                // Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
                // Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('loan-transactions')->name('loan-transactions.')->middleware(['role:Admin|BPMStaff'])->group(function (): void { // [cite: 1]
                Route::get('/issue/{loanApplication}/form', [LoanTransactionController::class, 'showIssueForm'])->name('issue.form'); // Show form to issue equipment [cite: 1]
                Route::post('/issue/{loanApplication}', [LoanTransactionController::class, 'issueEquipment'])->name('issue'); // Process equipment issuance [cite: 1]
                Route::get('/issued-loans-list', [LoanTransactionController::class, 'listIssuedLoans'])->name('issued-loans-list'); // List of issued loans [cite: 1]
                Route::get('/return/{loanTransaction}/form', [LoanTransactionController::class, 'showReturnForm'])->name('return.form'); // Show form to return equipment [cite: 1]
                Route::post('/return/{loanTransaction}', [LoanTransactionController::class, 'processReturn'])->name('return'); // Process equipment return [cite: 1]
                Route::get('/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('show'); // Show details of a specific transaction [cite: 1]
            });

            // Email Account Processing by IT Admin
            Route::prefix('email-accounts')->name('email-accounts.')->middleware(['role:Admin|IT Admin'])->group(function() {
                 Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('admin.index'); // Admin view of all email applications
                 Route::post('/{emailApplication}/process', [EmailAccountController::class, 'processApplication'])
                     ->name('process')
                     ->middleware('can:process,emailApplication'); // Policy check [cite: 1]
            });
        });
    });

    // MOTAC: System Settings (Admin only)
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function (): void { // Ensure 'Admin' role [cite: 1]
        Route::get('/users', SettingsUsersLW::class)->name('users.index'); // Livewire for user list [cite: 1]
        Route::get('/users/create', CreateSettingsUserLW::class)->name('users.create'); // Livewire for creating user [cite: 1]
        Route::get('/users/{user}', ShowSettingsUserLW::class)->name('users.show');       // Livewire for showing user [cite: 1]
        Route::get('/users/{user}/edit', EditSettingsUserLW::class)->name('users.edit');   // Livewire for editing user [cite: 1]
        Route::get('/roles', SettingsRolesLW::class)->name('roles.index'); // Livewire for roles [cite: 1]
        Route::get('/permissions', SettingsPermissionsLW::class)->name('permissions.index'); // Livewire for permissions [cite: 1]
        Route::get('/grades', AdminGradesIndexLW::class)->name('grades.index'); // Livewire for MOTAC grades [cite: 1]
        // Add routes for Department and Position management if needed, e.g.,
        // Route::get('/departments', AdminDepartmentsIndexLW::class)->name('departments.index');
        // Route::get('/positions', AdminPositionsIndexLW::class)->name('positions.index');
    });

    // MOTAC: Reports (Protected by role, e.g., Admin or specific reporting role)
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin'])->group(function (): void { // Ensure 'Admin' role [cite: 1]
        Route::get('/equipment-status', [ReportController::class, 'equipmentStatusReport'])->name('equipment-status'); // Report on ICT Equipment [cite: 1]
        Route::get('/loan-application-summary', [ReportController::class, 'loanApplicationSummaryReport'])->name('loan-application-summary'); // [cite: 1]
        Route::get('/loan-history', [ReportController::class, 'loanHistoryReport'])->name('loan-history'); // [cite: 1]
        Route::get('/user-activity-log', [ReportController::class, 'userActivityLogReport'])->name('user-activity-log'); // [cite: 1]
        Route::get('/email-application-status', [ReportController::class, 'emailApplicationStatusReport'])->name('email-application-status');
    });

    // MOTAC: User Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index'); // [cite: 1]
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

});

// Fallback route (optional, good for handling 404s gracefully)
// Route::fallback(function() {
//     return response()->view('errors.404', [], 404);
// });
