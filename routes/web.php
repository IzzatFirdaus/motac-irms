<?php

declare(strict_types=1);

// General Controllers
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
use App\Livewire\ResourceManagement\Approval\Dashboard as ApprovalDashboard;
use App\Livewire\ContactUs;
use App\Livewire\Dashboard;
use App\Livewire\ResourceManagement\EmailAccount\ApplicationForm as EmailApplicationFormLW;
use App\Livewire\ResourceManagement\LoanApplication\ApplicationForm as LoanRequestFormLW;
use App\Livewire\ResourceManagement\Admin\BPM\IssuedLoans;
use App\Livewire\ResourceManagement\Admin\BPM\OutstandingLoans;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
use App\Livewire\ResourceManagement\Admin\Grades\Index as AdminGradesIndexLW;
use App\Livewire\ResourceManagement\Admin\Users\Index as AdminUsersIndexLW;
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

// Models for Route Model Binding & Policies
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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Adhering to System Design (Rev. 3)
*/

// Publicly Accessible Routes
Route::get('lang/{locale}', LanguageController::class)->name('language.swap');
Route::get('/contact-us', ContactUs::class)->name('contact-us');
Route::post('/webhooks/deploy', [WebhookController::class, 'handleDeploy'])
    ->name('webhooks.deploy')
    ->middleware('validate.webhook.signature');

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
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::resource('equipment', EquipmentController::class)->only(['index', 'show'])
        ->parameters(['equipment' => 'equipment']);

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    });

    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index')->middleware(['permission:view_any_approvals']);
        Route::get('/dashboard', ApprovalDashboard::class)->name('dashboard')->middleware(['permission:view_approval_tasks']);
        Route::get('/history', [ApprovalController::class, 'showHistory'])->name('history')->middleware(['permission:view_approval_history']); // ADDED THIS ROUTE
        Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show')->middleware('can:view,approval');
        Route::post('/{approval}/decision', [ApprovalController::class, 'recordDecision'])->name('recordDecision')->middleware('can:process,approval');
    });

    Route::prefix('email-applications')->name('email-applications.')->group(function () {
        Route::get('/', EmailApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . EmailApplication::class);
        Route::get('/create', EmailApplicationFormLW::class)->name('create')->middleware('can:create,' . EmailApplication::class);
        Route::post('/', [EmailApplicationController::class, 'store'])->name('store')->middleware('can:create,' . EmailApplication::class);
        Route::get('/{email_application}', [EmailApplicationController::class, 'show'])->name('show')->middleware('can:view,email_application');
        Route::get('/{email_application}/edit', EmailApplicationFormLW::class)->name('edit')->middleware('can:update,email_application');
        Route::put('/{email_application}', [EmailApplicationController::class, 'update'])->name('update')->middleware('can:update,email_application');
        Route::delete('/{email_application}', [EmailApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,email_application');
        Route::post('/{email_application}/submit', [EmailApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,email_application');
    });

    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/', LoanApplicationsIndexLW::class)->name('index')->middleware('can:viewAny,' . LoanApplication::class);
        Route::get('/create', LoanRequestFormLW::class)->name('create')->middleware('can:create,' . LoanApplication::class);
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store')->middleware('can:create,' . LoanApplication::class);
        Route::get('/{loan_application}', [LoanApplicationController::class, 'show'])->name('show')->middleware('can:view,loan_application');
        Route::get('/{loan_application}/edit', LoanRequestFormLW::class)->name('edit')->middleware('can:update,loan_application');
        Route::put('/{loan_application}', [LoanApplicationController::class, 'update'])->name('update')->middleware('can:update,loan_application');
        Route::delete('/{loan_application}', [LoanApplicationController::class, 'destroy'])->name('destroy')->middleware('can:delete,loan_application');
        Route::post('/{loan_application}/submit', [LoanApplicationController::class, 'submitApplication'])->name('submit')->middleware('can:submit,loan_application');
    });

    Route::prefix('resource-management')->name('resource-management.')->middleware(['role:Admin|BPM Staff|IT Admin'])->group(function () {

        Route::prefix('bpm')->name('bpm.')->middleware(['role:Admin|BPM Staff'])->group(function () {
            Route::get('/outstanding-loans', OutstandingLoans::class)->name('outstanding-loans');
            Route::get('/issued-loans', IssuedLoans::class)->name('issued-loans');
            Route::get('/loan-transactions/issue/{loanApplication}/form', fn(LoanApplication $loanApplication) => view('resource-management.admin.bpm.issue-page', compact('loanApplication')))->name('loan-transactions.issue.form')->middleware('can:processIssuance,loanApplication');
            Route::get('/loan-transactions/return/{loanTransaction}/form', fn(LoanTransaction $loanTransaction) => view('resource-management.admin.bpm.return-page', compact('loanTransaction')))->name('loan-transactions.return.form')->middleware('can:processReturn,loanTransaction.loanApplication');
            Route::get('/loan-transactions/{loanTransaction}', [LoanTransactionController::class, 'show'])->name('loan-transactions.show')->middleware('can:view,loanTransaction');
        });

        Route::prefix('equipment-admin')->name('equipment-admin.')->middleware(['role:Admin|BPM Staff'])->group(function () {
            Route::get('/', AdminEquipmentIndexLW::class)->name('index')->middleware('can:viewAny,' . Equipment::class);
            Route::get('/create', fn() => view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => null]))->name('create')->middleware('can:create,' . Equipment::class);
            Route::get('/{equipment}/edit', fn(Equipment $equipment) => view('resource-management.admin.equipment.create-edit-page', ['equipmentId' => $equipment->id]))->name('edit')->middleware('can:update,equipment');
            // Note: No Route::resource for 'admin.equipment.*' here.
        });

        Route::prefix('email-applications-admin')->name('email-applications-admin.')->middleware(['role:Admin|IT Admin'])->group(function () {
            Route::get('/', [EmailAccountController::class, 'indexForAdmin'])->name('index')->middleware('can:viewAnyAdmin,' . EmailApplication::class);
            Route::get('/{email_application}', [EmailAccountController::class, 'showForAdmin'])->name('show')->middleware('can:viewAdmin,email_application');
            Route::post('/{email_application}/process', [EmailAccountController::class, 'processApplication'])->name('process')->middleware('can:processByIT,email_application');
        });

        Route::prefix('users-admin')->name('users-admin.')->middleware(['role:Admin'])->group(function () {
            Route::get('/', AdminUsersIndexLW::class)->name('index')->middleware('can:viewAny,' . User::class);
            // No '.show' route defined here.
        });
    });

    Route::prefix('settings')->name('settings.')->middleware(['role:Admin'])->group(function () {
        Route::get('/users', SettingsUsersLW::class)->name('users.index')->middleware('can:viewAny,' . User::class);
        Route::get('/users/create', CreateSettingsUserLW::class)->name('users.create')->middleware('can:create,' . User::class);
        Route::get('/users/{user}', ShowSettingsUserLW::class)->name('users.show')->middleware('can:view,user');
        Route::get('/users/{user}/edit', EditSettingsUserLW::class)->name('users.edit')->middleware('can:update,user');
        Route::get('/roles', SettingsRolesLW::class)->name('roles.index')->middleware('permission:manage_roles');
        Route::get('/permissions', SettingsPermissionsLW::class)->name('permissions.index')->middleware('permission:manage_permissions');
        Route::get('/grades', AdminGradesIndexLW::class)->name('grades.index')->middleware('can:viewAny,' . Grade::class);
        Route::get('/departments', AdminDepartmentsIndexLW::class)->name('departments.index')->middleware('can:viewAny,' . Department::class);
        Route::get('/positions', AdminPositionsIndexLW::class)->name('positions.index')->middleware('can:viewAny,' . Position::class);
    });

    Route::prefix('reports')->name('reports.')->middleware(['role:Admin|BPM Staff'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/equipment-inventory', [ReportController::class, 'equipmentInventory'])->name('equipment-inventory')->middleware('permission:view_equipment_reports');
        Route::get('/loan-applications', [ReportController::class, 'loanApplications'])->name('loan-applications')->middleware('permission:view_loan_reports');
        Route::get('/activity-log', [ReportController::class, 'activityLog'])->name('activity-log')->middleware('permission:view_user_activity_reports');
        Route::get('/email-accounts', [ReportController::class, 'emailAccounts'])->name('email-accounts')->middleware('permission:view_email_reports');
        Route::get('/loan-history', [ReportController::class, 'loanHistory'])->name('loan-history')->middleware('permission:view_loan_reports');
    });
});

Route::fallback(function () {
    abort(404, __('Laman Tidak Ditemui. Sila semak URL atau kembali ke papan pemuka.'));
});
