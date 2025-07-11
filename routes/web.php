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
use App\Livewire\Dashboard as DashboardLW;
use App\Livewire\Dashboard\AdminDashboard as AdminDashboardLW;
use App\Livewire\ResourceManagement\Admin\Equipment\Index as AdminEquipmentIndexLW;
// CORRECTED: Ensure UserActivityReport Livewire component is imported
use App\Livewire\ResourceManagement\Admin\Reports\UserActivityReport;
use App\Livewire\ResourceManagement\Approval\Dashboard as ApprovalDashboardLW;
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
});

// Fallback Route for 404 Not Found errors
Route::fallback(function () {
  return response()->view('errors.404', [], 404);
});
