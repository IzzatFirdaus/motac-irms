<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file defines all the web routes for the application. It is organized
| into public, authenticated, and administrative sections.
|
*/

// General Controllers
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailApplicationController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebhookController;
// Livewire Components
use App\Livewire\ContactUs as ContactUsLW;
<<<<<<< HEAD
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
// CORRECTED: Ensure UserActivityReport Livewire component is imported
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport;
use App\Livewire\ResourceManagement\Approval\Dashboard as ApprovalDashboardLW;
=======

// Resource Management - Application Forms (Using LW suffix for clarity)
>>>>>>> 94d7072 (EDIT 3/6/25 FILES)
use App\Livewire\ResourceManagement\EmailAccount\ApplicationForm as EmailApplicationFormLW;
use App\Livewire\ResourceManagement\LoanApplication\ApplicationForm as LoanApplicationFormLW;
use App\Livewire\ResourceManagement\MyApplications\Email\Index as MyEmailApplicationsIndexLW;
use App\Livewire\ResourceManagement\MyApplications\Loan\Index as MyLoanApplicationsIndexLW;
use App\Livewire\Settings\Departments\Index as SettingsDepartmentsIndexLW;
use App\Livewire\Settings\Permissions\Index as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Positions\Index as SettingsPositionsIndexLW;
use App\Livewire\Settings\Roles\Index as SettingsRolesIndexLW;
use App\Livewire\Settings\Users\Create as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\Edit as SettingsUsersEditLW;
use App\Livewire\Settings\Users\Index as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\Show as SettingsUsersShowLW;
// --- ISSUED LOANS VIEW (BPM Staff & Admin) ---
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;
// Facades
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('lang/{locale}', LanguageController::class)->name('language.swap');
Route::get('/contact-us', ContactUsLW::class)->name('contact-us');
Route::post('/webhooks/deploy', [WebhookController::class, 'handleDeploy'])
    ->name('webhooks.deploy')
    ->middleware('validate.webhook.signature');

Route::get('/terms-of-service', function () {
<<<<<<< HEAD
  $path = resource_path('markdown/terms.md');
  abort_if(! File::exists($path), 404);

  return view('terms', ['terms' => (new GithubFlavoredMarkdownConverter)->convert(File::get($path))->getContent()]);
})->name('terms.show');

Route::get('/privacy-policy', function () {
  $path = resource_path('markdown/policy.md');
  abort_if(! File::exists($path), 404);

  return view('policy', ['policy' => (new GithubFlavoredMarkdownConverter)->convert(File::get($path))->getContent()]);
})->name('policy.show');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function (): void {
  Route::redirect('/', '/dashboard', 301);

  // UPDATED: This route now points to the main Dashboard Livewire component, which handles role-based view logic.
  Route::get('/dashboard', DashboardLW::class)->name('dashboard');

  // --- LOAN APPLICATIONS ---
  Route::prefix('loan-applications')->name('loan-applications.')->group(function (): void {
    Route::get('/', MyLoanApplicationsIndexLW::class)->name('index');
    Route::get('/create', LoanApplicationFormLW::class)->name('create');
    Route::post('/', [LoanApplicationController::class, 'store'])->name('store');
    Route::get('/{loan_application}/edit', LoanApplicationFormLW::class)->name('edit');
    Route::get('/{loan_application}/print', [LoanApplicationController::class, 'printPdf'])->name('print');
    Route::get('/{loan_application}', [LoanApplicationController::class, 'show'])->name('show');
    Route::delete('/{loan_application}', [LoanApplicationController::class, 'destroy'])->name('destroy');
    Route::post('/{loan_application}/submit', [LoanApplicationController::class, 'submitApplication'])->name('submit');
  });

  // --- LOAN TRANSACTIONS (Issuance and Returns by BPM Staff) ---
  Route::get('/loan-transactions', [LoanTransactionController::class, 'index'])->name('loan-transactions.index');
  Route::get('/loan-transactions/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('loan-transactions.show');
  Route::get('/loan-applications/{loanApplication}/issue-form', [LoanTransactionController::class, 'showIssueForm'])->name('loan-applications.issue.form');
  Route::post('/loan-applications/{loanApplication}/issue', [LoanTransactionController::class, 'storeIssue'])->name('loan-applications.issue.store');
  Route::post('/loan-transactions/{loanTransaction}/return', [LoanTransactionController::class, 'storeReturn'])->name('loan-transactions.return.store');
  Route::get('/loan-transactions/{loanTransaction}/return-form', [LoanTransactionController::class, 'showReturnForm'])->name('loan-transactions.return.form');

  // --- EMAIL APPLICATIONS ---
  Route::prefix('email-applications')->name('email-applications.')->group(function (): void {
    Route::get('/', MyEmailApplicationsIndexLW::class)->name('index');
    Route::get('/create', EmailApplicationFormLW::class)->name('create');
    Route::post('/', [EmailApplicationController::class, 'store'])->name('store');
    Route::get('/{email_application}/edit', EmailApplicationFormLW::class)->name('edit');
    Route::get('/{email_application}', [EmailApplicationController::class, 'show'])->name('show');
    Route::delete('/{email_application}', [EmailApplicationController::class, 'destroy'])->name('destroy');
    Route::post('/{email_application}/submit', [EmailApplicationController::class, 'submitApplication'])->name('submit');
  });

  // ==============================================================================
  // --- APPROVALS (FIX APPLIED HERE) ---
  // ==============================================================================
  Route::prefix('approvals')->name('approvals.')->middleware('check.gradelevel:9')->group(function (): void {
    // ADDED: Redirect from the old base URL to the new, explicit dashboard URL.
    Route::redirect('/', '/approvals/dashboard', 301);

    // CHANGED: The dashboard is now explicitly at the /dashboard path. This fixes the 404 error.
    Route::get('/dashboard', ApprovalDashboardLW::class)->name('dashboard');

    // UNCHANGED: Specific history route.
    Route::get('/history', [ApprovalController::class, 'showHistory'])->name('history');

    // UNCHANGED: Wildcard route for specific approval records. Must come after specific routes.
    Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show');

    // UNCHANGED: Action route for a specific approval.
    Route::post('/{approval}/record-decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision');
  });

  // --- NOTIFICATIONS ---
  Route::prefix('notifications')->name('notifications.')->group(function (): void {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
  });

  // --- PUBLIC EQUIPMENT CATALOG ---
  Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);

  /*
|--------------------------------------------------------------------------
| Admin & Privileged User Routes
|--------------------------------------------------------------------------
*/

  // NEW: Explicit route for the enhanced admin dashboard.
  Route::get('/admin/dashboard', AdminDashboardLW::class)
    ->name('admin.dashboard')
    ->middleware(['role:Admin|IT Admin|BPM Staff']);

  Route::prefix('admin/bpm')->name('resource-management.bpm.')->middleware(['role:Admin|BPM Staff'])->group(function (): void {
    Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');
  });

  // --- EQUIPMENT MANAGEMENT (BPM Staff & Admin) ---
  Route::prefix('admin/equipment')->name('admin.equipment.')->middleware(['can:view-equipment-admin'])->group(function (): void {
    Route::get('/', AdminEquipmentIndexLW::class)->name('index');
    Route::get('/create', [AdminEquipmentController::class, 'create'])->name('create');
    Route::post('/', [AdminEquipmentController::class, 'store'])->name('store');
    Route::get('/{equipment}', [AdminEquipmentController::class, 'show'])->name('show');
    Route::get('/{equipment}/edit', [AdminEquipmentController::class, 'edit'])->name('edit');
    Route::put('/{equipment}', [AdminEquipmentController::class, 'update'])->name('update');
    Route::delete('/{equipment}', [AdminEquipmentController::class, 'destroy'])->name('destroy');
  });

  // --- EMAIL ACCOUNT PROCESSING (IT Admin & Admin) ---
  Route::prefix('admin/email-processing')->name('admin.email-processing.')->middleware(['role:Admin|IT Admin'])->group(function (): void {
    Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index');
    Route::get('/{email_application}', [EmailAccountController::class, 'showForAdmin'])->name('show');
    Route::post('/{email_application}/process', [EmailAccountController::class, 'processApplication'])->name('process');
  });

// --- REPORTS ---
Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPM Staff|IT Admin'])->group(function (): void {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/equipment-inventory', [ReportController::class, 'equipmentInventory'])->name('equipment-inventory');
    Route::get('/loan-applications', [ReportController::class, 'loanApplications'])->name('loan-applications');
    // CHANGED: Use the Livewire component directly for user activity log
    Route::get('/user-activity-log', UserActivityReport::class)->name('activity-log');
    Route::get('/email-accounts', [ReportController::class, 'emailAccounts'])->name('email-accounts');
    Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history');
    Route::get('/utilization', [ReportController::class, 'utilizationReport'])->name('utilization-report');
    Route::get('/loan-status-summary', [ReportController::class, 'loanStatusSummary'])->name('loan-status-summary');
});

  /*
    |--------------------------------------------------------------------------
    | System Settings (Admin Only)
    |--------------------------------------------------------------------------
    */
  Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function (): void {
    Route::get('/', SettingsUsersIndexLW::class)->name('index'); // Default settings page is users list
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
      ->middleware('permission:view_logs');
  });
=======
    $markdownPath = resource_path('markdown/terms.md');
    $termsHtml = '';
    if (File::exists($markdownPath)) {
        $markdownContent = File::get($markdownPath);
        $converter = new GithubFlavoredMarkdownConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);
        $termsHtml = $converter->convert($markdownContent)->getContent();
    } else {
        Log::warning('Terms of Service markdown file not found at: ' . $markdownPath);
        $termsHtml = '<p>' . __('The Terms of Service content is currently unavailable. Please try again later.') . '</p>';
    }
    return view('terms', ['terms' => $termsHtml]);
})->name('terms.show');

Route::get('/privacy-policy', function () {
    $markdownPath = resource_path('markdown/policy.md');
    $policyHtml = '';
    if (File::exists($markdownPath)) {
        $markdownContent = File::get($markdownPath);
        $converter = new GithubFlavoredMarkdownConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);
        $policyHtml = $converter->convert($markdownContent)->getContent();
    } else {
        Log::warning('Privacy Policy markdown file not found at: ' . $markdownPath);
        $policyHtml = '<p>' . __('The Privacy Policy content is currently unavailable. Please try again later.') . '</p>';
    }
    return view('policy', ['policy' => $policyHtml]);
})->name('policy.show');


// Authenticated User Routes (Require authentication, session, verification)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Root redirect
    Route::redirect('/', '/dashboard', 301);

    // Dashboard for general users
    Route::get('/dashboard', DashboardLW::class)->name('dashboard');

    // Admin Dashboard (if distinct from settings.users.index)
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin'])->group(function () {
        Route::get('/dashboard', SettingsUsersIndexLW::class)->name('dashboard');
    });

    // General Equipment Listing (for all authenticated users)
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show'])
        ->parameters(['equipment' => 'equipment']);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    });

    // Approvals Module
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index')->middleware(['permission:view_any_approvals']);
        Route::get('/dashboard', ApprovalDashboardLW::class)->name('dashboard')->middleware(['permission:view_approval_tasks']);
        Route::get('/history', [ApprovalController::class, 'showHistory'])->name('history')->middleware(['permission:view_approval_history']);
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show')->middleware('can:view,approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision')->middleware('can:update,approval');
    });

    // My Email Applications (User's own applications)
    Route::prefix('email-applications')->name('email-applications.')->group(function () {
        Route::get('/', MyEmailApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . EmailApplication::class);
        Route::get('/create', EmailApplicationFormLW::class)->name('create')->middleware('can:create,' . EmailApplication::class);
        Route::get('/{email_application}', [EmailApplicationController::class, 'show'])->name('show')->middleware('can:view,email_application');
        Route::get('/{email_application}/edit', EmailApplicationFormLW::class)->name('edit')->middleware('can:update,email_application');
        Route::delete('/{email_application}', [EmailApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,' . EmailApplication::class);
        Route::post('/{email_application}/submit', [EmailApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,' . EmailApplication::class);
    });

    // My Loan Applications (User's own applications)
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/', MyLoanApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . LoanApplication::class);
        Route::get('/create', LoanApplicationFormLW::class)->name('create')->middleware('can:create,' . LoanApplication::class);
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store')->middleware('can:create,' . LoanApplication::class);
        Route::get('/{loan_application}', [LoanApplicationController::class, 'show'])->name('show')->middleware('can:view,loan_application');
        Route::get('/{loan_application}/edit', LoanApplicationFormLW::class)->name('edit')->middleware('can:update,loan_application');
        Route::delete('/{loan_application}', [LoanApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,loan_application');
        Route::post('/{loan_application}/submit', [LoanApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,loan_application'); // EDITED HERE
    });

    // --- Start of Resource Management for User Applications ---
    // These routes are for general users to create applications and have the 'resource-management.' name prefix.
    // They are explicitly defined here to ensure correct naming and broader role access,
    // separate from the admin-specific 'resource-management' group.
    Route::group(['prefix' => 'my-loan-applications', 'as' => 'resource-management.my-loan-applications.'], function () {
        Route::get('/create', LoanApplicationFormLW::class)
            ->middleware('role:Admin|AM|CC|CR|HR')
            ->name('create'); // This will result in the full name 'resource-management.my-loan-applications.create'
    });

    Route::group(['prefix' => 'my-email-applications', 'as' => 'resource-management.my-email-applications.'], function () {
        Route::get('/create', EmailApplicationFormLW::class)
            ->middleware('role:Admin|AM|CC|CR|HR')
            ->name('create'); // This will result in the full name 'resource-management.my-email-applications.create'
    });
    // --- End of Resource Management for User Applications ---


    // Admin Sections (Resource Management) - This group remains for Admin/BPM Staff/IT Admin specific functionalities
    // This group applies '/resource-management' URL prefix and 'resource-management.' name prefix to its children.
    Route::prefix('resource-management')->name('resource-management.')->middleware(['role:Admin|BPM Staff|IT Admin'])->group(function () {

        Route::prefix('application-forms')->name('application-forms.')->group(function () {
            // Traditional Loan Application Form (if still in use)
            Route::get('/loan/create', [LoanApplicationController::class, 'createTraditionalForm'])
                ->name('loan.create')
                ->middleware('can:create,' . LoanApplication::class);

            // Email Application Form if accessed from Admin Resource Management context
            Route::get('/email/create', EmailApplicationFormLW::class)
                ->name('email.create')
                ->middleware('can:create,' . EmailApplication::class);
        });

        // BPM Staff specific routes
        Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPM Staff'])->group(function () {
            Route::get('/outstanding-loans', BpmOutstandingLoansLW::class)->name('outstanding-loans');
            Route::get('/issued-loans', BpmIssuedLoansLW::class)->name('issued-loans');
            Route::get('/loan-transactions', [LoanTransactionController::class, 'index'])->name('loan-transactions.index');
            Route::get('/loan-transactions/issue/{loanApplication}/form', fn(LoanApplication $loanApplication) => view('loan-transactions.issue', compact('loanApplication')))->name('loan-transactions.issue.form')->middleware('can:processIssuance,loanApplication');
            Route::get('/loan-transactions/return/{loanTransaction}/form', fn(LoanTransaction $loanTransaction) => view('loan-transactions.return', compact('loanTransaction')))->name('loan-transactions.return.form')->middleware('can:processReturn,loanTransaction.loanApplication');
            Route::get('/loan-transactions/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('loan-transactions.show')->middleware('can:view,loanTransaction');
        });

        // Equipment Admin (CRUD for Equipment)
        Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPM Staff'])->group(function () {
            Route::get('/', AdminEquipmentIndexLW::class)->name('index')->middleware('can:viewAny,' . Equipment::class);
            Route::get('/create', [AdminEquipmentController::class, 'create'])->name('create')->middleware('can:create,' . Equipment::class);
            Route::post('/', [AdminEquipmentController::class, 'store'])->name('store')->middleware('can:create,' . Equipment::class);
            Route::get('/{equipment}', [AdminEquipmentController::class, 'show'])->name('show')->middleware('can:view,equipment');
            Route::get('/{equipment}/edit', [AdminEquipmentController::class, 'edit'])->name('edit')->middleware('can:update,' . Equipment::class);
            Route::put('/{equipment}', [AdminEquipmentController::class, 'update'])->name('update')->middleware('can:update,' . Equipment::class);
            Route::delete('/{equipment}', [AdminEquipmentController::class, 'destroy'])->name('destroy')->middleware('can:delete,' . Equipment::class);
        });

        // Email Applications Admin (IT Admin processing)
        Route::prefix('email-applications-admin')->name('email-applications-admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
            Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index')->middleware('can:viewAnyAdmin,' . EmailApplication::class);
            Route::get('/{email_application}', [EmailAccountController::class, 'showForAdmin'])->name('show')->middleware('can:viewAdmin,email_application');
            Route::post('/{email_application}/process', [EmailAccountController::class, 'processApplication'])->name('process')->middleware('can:processByIT,email_application');
        });

        // Users Admin (if distinct from Settings > Users)
        Route::prefix('users-admin')->name('users-admin.')->middleware(['role:Admin'])->group(function () {
            Route::get('/', AdminUsersIndexLWResourceManagement::class)->name('index')->middleware('can:viewAny,' . User::class);
        });
    });

    // System Settings Sections (Primarily Admin roles)
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        // Users Management (Livewire CRUD)
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index')->middleware('can:viewAny,' . User::class);
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create')->middleware('can:create,' . User::class);
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show')->middleware('can:view,user');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit')->middleware('can:update,user');

        // Roles and Permissions Management (Livewire)
        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index')->middleware('permission:manage_roles');
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index')->middleware('permission:manage_permissions');

        // Grades Management
        Route::resource('grades', AdminGradeController::class)
            ->parameters(['grades' => 'grade'])
            ->middleware('can:viewAny,' . Grade::class);

        // Departments Management (Livewire)
        Route::get('/departments', SettingsDepartmentsIndexLW::class)
            ->name('departments.index')
            ->middleware('can:viewAny,' . Department::class);

        // Positions Management (Livewire)
        Route::get('/positions', SettingsPositionsIndexLW::class)
            ->name('positions.index')
            ->middleware('can:viewAny,' . Position::class);
    });


    // Reports Module
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPM Staff'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/equipment-inventory', [ReportController::class, 'equipmentInventory'])->name('equipment-inventory')->middleware('permission:view_equipment_reports');
        Route::get('/loan-applications', [ReportController::class, 'loanApplications'])->name('loan-applications')->middleware('permission:view_loan_reports');
        Route::get('/activity-log', UserActivityReport::class)->name('activity-log')->middleware('permission:view_user_activity_reports');
        Route::get('/email-accounts', [ReportController::class, 'emailAccounts'])->name('email-accounts')->middleware('permission:view_email_reports');
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history')->middleware('permission:view_loan_reports');
    });
>>>>>>> 94d7072 (EDIT 3/6/25 FILES)
});

<<<<<<< HEAD
// Fallback Route for 404 Not Found errors
Route::fallback(function () {
=======
Route::middleware(['web', 'auth:sanctum', 'verified'])->group(function () {
    // ... other authenticated routes ...

    // Example: Log Viewer Route
    // If your log viewer package handles its own routing and controllers,
    // you might need to find where its routes are registered to add your middleware,
    // or it might offer a configuration option for adding middleware.

    // If you are defining it manually or can override its middleware:
    Route::get('/log-viewer', [\Opcodes\LogViewer\Http\Controllers\IndexController::class, '__invoke']) // Example controller
        ->name('log-viewer.index') // This matches your menu config
        ->middleware(['authorize.logviewer']); // Apply your new authorization middleware

    // If the log viewer package uses a route group, you can apply it there:
    // Route::group(['prefix' => 'log-viewer', 'middleware' => ['authorize.logviewer']], function () {
    //     // Package routes would be defined here by the package, or you'd include them
    // });
});

// Fallback route
Route::fallback(function () { //
>>>>>>> 40cf877 (files edit for testing purposes with backups. edited verticalmenu components)
    return response()->view('errors.404', [], 404);
});
