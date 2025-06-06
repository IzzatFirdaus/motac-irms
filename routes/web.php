<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file defines the web-based routes for the MOTAC Integrated
| Resource Management System. It adheres to the System Design (Rev. 3.5),
| utilizing controllers, Livewire components, and appropriate middleware
| for authentication and authorization.
|
*/

// General Controllers
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailApplicationController;
use App\Http\Controllers\EquipmentController; // For public equipment views
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebhookController;

// Admin Controllers
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;

// Livewire Components (Grouped for clarity)
// Dashboard & Public
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\ContactUs as ContactUsLW;

// Resource Management - Application Forms (Using LW suffix for clarity)
use App\Livewire\ResourceManagement\EmailAccount\ApplicationForm as EmailApplicationFormLW;
use App\Livewire\ResourceManagement\LoanApplication\ApplicationForm as LoanApplicationFormLW;

// Resource Management - My Applications
use App\Livewire\ResourceManagement\MyApplications\Email\Index as MyEmailApplicationsIndexLW;
use App\Livewire\ResourceManagement\MyApplications\Loan\Index as MyLoanApplicationsIndexLW;

// Resource Management - Approvals
use App\Livewire\ResourceManagement\Approval\Dashboard as ApprovalDashboardLW;

// Resource Management - Admin/BPM
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans as BpmIssuedLoansLW;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans as BpmOutstandingLoansLW;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Admin\Users\Index as AdminUsersIndexLWResourceManagement;
// Assuming ProcessReturn Livewire component will be loaded via a controller method rendering a wrapper view.
// use App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn as BpmProcessReturnLW; // Not directly routed

// Livewire Report Components
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport;


// Settings Livewire Components
use App\Livewire\Settings\Users\Index as SettingsUsersIndexLW;
use App\Livewire\Settings\Users\Create as SettingsUsersCreateLW;
use App\Livewire\Settings\Users\Show as SettingsUsersShowLW;
use App\Livewire\Settings\Users\Edit as SettingsUsersEditLW;
use App\Livewire\Settings\Departments\Index as SettingsDepartmentsIndexLW;
use App\Livewire\Settings\Permissions\Index as SettingsPermissionsIndexLW;
use App\Livewire\Settings\Positions\Index as SettingsPositionsIndexLW;
use App\Livewire\Settings\Roles\Index as SettingsRolesIndexLW;

// Models
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\CommonMark\GithubFlavoredMarkdownConverter;

// Publicly Accessible Routes
Route::get('lang/{locale}', LanguageController::class)->name('language.swap');
Route::get('/contact-us', ContactUsLW::class)->name('contact-us');
Route::post('/webhooks/deploy', [WebhookController::class, 'handleDeploy'])
    ->name('webhooks.deploy')
    ->middleware('validate.webhook.signature');

// Legal Documents
Route::get('/terms-of-service', function () {
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


// Authenticated User Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::redirect('/', '/dashboard', 301);
    Route::get('/dashboard', DashboardLW::class)->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware(['role:Admin'])->group(function () { //
        Route::get('/dashboard', SettingsUsersIndexLW::class)->name('dashboard');
    });

    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']); //

    Route::prefix('notifications')->name('notifications.')->group(function () { //
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    });

    Route::prefix('approvals')->name('approvals.')->group(function () { //
        Route::get('/', [ApprovalController::class, 'index'])->name('index')->middleware('permission:view_any_approvals,web');
        Route::get('/dashboard', ApprovalDashboardLW::class)->name('dashboard')->middleware('permission:view_approval_tasks,web');
        Route::get('/history', [ApprovalController::class, 'showHistory'])->name('history')->middleware('permission:view_approval_history,web');
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show')->middleware('can:view,approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision')->middleware('can:update,approval');
    });

    Route::prefix('email-applications')->name('email-applications.')->group(function () { //
        Route::get('/', MyEmailApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . EmailApplication::class);
        Route::get('/create', EmailApplicationFormLW::class)->name('create')->middleware('can:create,' . EmailApplication::class);
        Route::get('/{email_application}', [EmailApplicationController::class, 'show'])->name('show')->middleware('can:view,email_application');
        Route::get('/{email_application}/edit', EmailApplicationFormLW::class)->name('edit')->middleware('can:update,email_application');
        Route::delete('/{email_application}', [EmailApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,' . EmailApplication::class);
        Route::post('/{email_application}/submit', [EmailApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,' . EmailApplication::class);
    });

    Route::prefix('loan-applications')->name('loan-applications.')->group(function () { //
        Route::get('/', MyLoanApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . LoanApplication::class);
        Route::get('/create', LoanApplicationFormLW::class)->name('create')->middleware('can:create,' . LoanApplication::class);
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store')->middleware('can:create,' . LoanApplication::class);
        Route::get('/{loan_application}', [LoanApplicationController::class, 'show'])->name('show')->middleware('can:view,loan_application');
        Route::get('/{loan_application}/edit', LoanApplicationFormLW::class)->name('edit')->middleware('can:update,loan_application');
        Route::delete('/{loan_application}', [LoanApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,loan_application');
        Route::post('/{loan_application}/submit', [LoanApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,loan_application'); // EDITED HERE
    });

    // User Application Creation Routes (Simplified)
    Route::group(['prefix' => 'my-loan-applications', 'as' => 'resource-management.my-loan-applications.'], function () {
        Route::get('/create', LoanApplicationFormLW::class)->name('create')->middleware('can:create,' . LoanApplication::class);
    });
    Route::group(['prefix' => 'my-email-applications', 'as' => 'resource-management.my-email-applications.'], function () {
        Route::get('/create', EmailApplicationFormLW::class)->name('create')->middleware('can:create,' . EmailApplication::class);
    });

    // Admin Sections (Resource Management)
    Route::prefix('resource-management')->name('resource-management.')->middleware(['role:Admin|BPM Staff|IT Admin'])->group(function () { //
        Route::prefix('application-forms')->name('application-forms.')->group(function () {
            Route::get('/loan/create', [LoanApplicationController::class, 'createTraditionalForm']) // Assuming a traditional form exists
                ->name('loan.create')
                ->middleware('can:create,' . LoanApplication::class);
            Route::get('/email/create', EmailApplicationFormLW::class)
                ->name('email.create')
                ->middleware('can:create,' . EmailApplication::class);
        });

        Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPM Staff'])->group(function () { //
            Route::get('/outstanding-loans', BpmOutstandingLoansLW::class)->name('outstanding-loans');
            Route::get('/issued-loans', BpmIssuedLoansLW::class)->name('issued-loans');
            Route::get('/loan-transactions', [LoanTransactionController::class, 'index'])->name('loan-transactions.index');

            // Routes for Issuance (assuming showIssueForm and storeIssue exist in LoanTransactionController)
            Route::get('/loan-transactions/issue/{loanApplication}/form', [LoanTransactionController::class, 'showIssueForm'])
                 ->name('loan-transactions.issue.form') // Simplified name
                 ->middleware('can:createIssue,App\Models\LoanTransaction,loanApplication'); // Policy: createIssue(User $user, LoanApplication $loanApplication)

            Route::post('/loan-transactions/issue/{loanApplication}', [LoanTransactionController::class, 'storeIssue'])
                 ->name('loan-transactions.storeIssue') // Simplified name
                 ->middleware('can:createIssue,App\Models\LoanTransaction,loanApplication');

            // Routes for Return
            Route::get('/loan-transactions/return/{loanTransaction}/form', [LoanTransactionController::class, 'returnForm'])
                ->name('loan-transactions.return.form') // Simplified name
                // Middleware will try to authorize 'processReturn' on LoanApplicationPolicy using $loanTransaction->loanApplication
                ->middleware('can:processReturn,loanTransaction.loanApplication');

            Route::post('/loan-transactions/return/{loanTransaction}', [LoanTransactionController::class, 'storeReturn'])
                ->name('loan-transactions.storeReturn') // Simplified name
                // Middleware will try to authorize 'processReturn' on LoanApplicationPolicy using $loanTransaction->loanApplication
                ->middleware('can:processReturn,loanTransaction.loanApplication');

            Route::get('/loan-transactions/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('loan-transactions.show')->middleware('can:view,loanTransaction');
        });

        Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPM Staff'])->group(function () { //
            Route::get('/', AdminEquipmentIndexLW::class)->name('index')->middleware('can:viewAny,' . Equipment::class);
            Route::get('/create', [AdminEquipmentController::class, 'create'])->name('create')->middleware('can:create,' . Equipment::class);
            Route::post('/', [AdminEquipmentController::class, 'store'])->name('store')->middleware('can:create,' . Equipment::class);
            Route::get('/{equipment}', [AdminEquipmentController::class, 'show'])->name('show')->middleware('can:view,equipment');
            Route::get('/{equipment}/edit', [AdminEquipmentController::class, 'edit'])->name('edit')->middleware('can:update,' . Equipment::class);
            Route::put('/{equipment}', [AdminEquipmentController::class, 'update'])->name('update')->middleware('can:update,' . Equipment::class);
            Route::delete('/{equipment}', [AdminEquipmentController::class, 'destroy'])->name('destroy')->middleware('can:delete,' . Equipment::class);
        });

        Route::prefix('email-applications-admin')->name('email-applications-admin.')->middleware(['role:Admin|IT Admin'])->group(function () { //
            Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index')->middleware('can:viewAnyAdmin,' . EmailApplication::class);
            Route::get('/{email_application}', [EmailAccountController::class, 'showForAdmin'])->name('show')->middleware('can:viewAdmin,email_application');
            Route::post('/{email_application}/process', [EmailAccountController::class, 'processApplication'])->name('process')->middleware('can:processByIT,' . EmailApplication::class);
        });

        Route::prefix('users-admin')->name('users-admin.')->middleware(['role:Admin'])->group(function () { //
            Route::get('/', AdminUsersIndexLWResourceManagement::class)->name('index')->middleware('can:viewAny,' . User::class);
        });
    });

    // System Settings Sections
    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () { //
        Route::get('/users', SettingsUsersIndexLW::class)->name('users.index')->middleware('can:viewAny,' . User::class);
        Route::get('/users/create', SettingsUsersCreateLW::class)->name('users.create')->middleware('can:create,' . User::class);
        Route::get('/users/{user}', SettingsUsersShowLW::class)->name('users.show')->middleware('can:view,user');
        Route::get('/users/{user}/edit', SettingsUsersEditLW::class)->name('users.edit')->middleware('can:update,user');

        Route::get('/roles', SettingsRolesIndexLW::class)->name('roles.index')->middleware('permission:manage_roles,web'); //
        Route::get('/permissions', SettingsPermissionsIndexLW::class)->name('permissions.index')->middleware('permission:manage_permissions,web'); //

        Route::resource('grades', AdminGradeController::class) //
            ->parameters(['grades' => 'grade']) // Ensures {grade} parameter name
            ->middleware(['can:viewAny,' . Grade::class]); // General policy for resource

        Route::get('/departments', SettingsDepartmentsIndexLW::class) //
            ->name('departments.index')
            ->middleware(['can:viewAny,' . Department::class]);

        Route::get('/positions', SettingsPositionsIndexLW::class) //
            ->name('positions.index')
            ->middleware(['can:viewAny,' . Position::class]);
    });

    // Reports Module
    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPM Staff'])->group(function () { //
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/equipment-inventory', [ReportController::class, 'equipmentInventory'])->name('equipment-inventory')->middleware('permission:view_equipment_reports,web');
        Route::get('/loan-applications', [ReportController::class, 'loanApplications'])->name('loan-applications')->middleware('permission:view_loan_reports,web');
        Route::get('/activity-log', UserActivityReport::class)->name('activity-log')->middleware('permission:view_user_activity_reports,web');
        Route::get('/email-accounts', [ReportController::class, 'emailAccounts'])->name('email-accounts')->middleware('permission:view_email_reports,web');
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history')->middleware('permission:view_loan_reports,web');
    });
});

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
    return response()->view('errors.404', [], 404);
});
