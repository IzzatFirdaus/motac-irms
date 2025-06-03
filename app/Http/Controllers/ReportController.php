<?php

namespace App\Http\Controllers;

use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    // Route-level middleware for roles/permissions (e.g., 'role:Admin|BPM Staff') is defined in web.php
  }

  /**
   * Display a report on user activities.
   * SDD Ref: [cite: 1]
   */
  public function activityLog(Request $request): View
  {
    $requestingUserId = Auth::id();
    Log::info("ReportController@activityLog: Generating User Activity Report.", ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);
    $query = User::withCount([
      'emailApplications',
      'loanApplicationsAsApplicant as loan_applications_count', // Ensure this alias matches what view expects or use as is
      'approvalsMade as approvals_count', // Ensure this alias matches what view expects or use as is
    ])->with(['department:id,name', 'grade:id,name,level', 'position:id,name']);

    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'like', '%' . $searchTerm . '%')
          ->orWhere('email', 'like', '%' . $searchTerm . '%')
          ->orWhere('identification_number', 'like', '%' . $searchTerm . '%');
      });
    }
    $users = $query->latest('created_at')->paginate(config('pagination.reports_per_page', 20));
    Log::info("ReportController@activityLog: Fetched {$users->total()} users.", ['requesting_user_id' => $requestingUserId]);
    // Assuming view 'reports.activity-log' uses $users variable
    return view('reports.activity-log', compact('users', 'request'));
  }

  /**
   * Display an equipment inventory report.
   * SDD Ref: [cite: 1]
   */
  public function equipmentInventory(Request $request): View
  {
    $requestingUserId = Auth::id();
    Log::info("ReportController@equipmentInventory: Generating Equipment Inventory Report.", ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);
    $query = Equipment::with(['equipmentCategory:id,name', 'definedLocation:id,name', 'department:id,name']);

    if ($request->filled('status') && $request->status !== 'all') $query->where('status', $request->input('status'));
    if ($request->filled('location_id') && $request->location_id !== 'all') $query->where('location_id', $request->input('location_id'));
    if ($request->filled('equipment_category_id') && $request->equipment_category_id !== 'all') $query->where('equipment_category_id', $request->input('equipment_category_id'));
    if ($request->filled('asset_type') && $request->asset_type !== 'all') $query->where('asset_type', $request->input('asset_type'));
    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where(fn($q) => $q->where('tag_id', 'like', "%{$searchTerm}%")->orWhere('serial_number', 'like', "%{$searchTerm}%")->orWhere('brand', 'like', "%{$searchTerm}%")->orWhere('model', 'like', "%{$searchTerm}%")->orWhere('item_code', 'like', "%{$searchTerm}%"));
    }
    $equipmentList = $query->orderBy('tag_id')->paginate(config('pagination.reports_per_page', 20)); // Changed variable name for clarity
    $locations = Location::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    $statuses = method_exists(Equipment::class, 'getStatusOptions') ? Equipment::getStatusOptions() : [];
    $assetTypes = method_exists(Equipment::class, 'getAssetTypeOptions') ? Equipment::getAssetTypeOptions() : [];
    Log::info("ReportController@equipmentInventory: Fetched {$equipmentList->total()} equipment items.", ['requesting_user_id' => $requestingUserId]);
    // Pass as 'equipment' if view expects $equipment, or 'equipmentList' if view expects $equipmentList
    return view('reports.equipment-inventory', compact('equipmentList', 'locations', 'categories', 'statuses', 'assetTypes', 'request'));
  }

  /**
   * Display a loan history report.
   * SDD Ref: [cite: 1]
   */
  public function loanHistory(Request $request): View
  {
    $requestingUserId = Auth::id();
    Log::info("ReportController@loanHistory: Generating Loan History Report.", ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);
    $query = LoanTransaction::with([
      'loanApplication.user:id,name',
      'loanApplication.responsibleOfficer:id,name',
      'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model', // Ensure Equipment model has brand_model_serial accessor or use individual fields
      'issuingOfficer:id,name',
      'receivingOfficer:id,name',
      'returningOfficer:id,name',
      'returnAcceptingOfficer:id,name',
    ]);

    if ($request->filled('user_id')) $query->whereHas('loanApplication', fn($q) => $q->where('user_id', $request->input('user_id')));
    if ($request->filled('equipment_id')) $query->whereHas('loanTransactionItems', fn($q) => $q->where('equipment_id', $request->input('equipment_id')));
    if ($request->filled('transaction_type') && $request->transaction_type !== 'all') $query->where('type', $request->input('transaction_type'));
    if ($request->filled('date_from')) $query->whereDate('transaction_date', '>=', $request->input('date_from'));
    if ($request->filled('date_to')) $query->whereDate('transaction_date', '<=', $request->input('date_to'));

    // Changed variable name from $loanHistory to $loanTransactions to match view expectation
    $loanTransactions = $query->latest('transaction_date')->paginate(config('pagination.reports_per_page', 20));

    $users = User::orderBy('name')->pluck('name', 'id');
    $equipmentList = Equipment::orderBy('tag_id')->select('id', 'tag_id', 'brand', 'model')->get()->mapWithKeys(fn($item) => [$item->id => "{$item->tag_id} ({$item->brand} {$item->model})"]);
    $transactionTypes = method_exists(LoanTransaction::class, 'getTypesOptions') ? LoanTransaction::getTypesOptions() : [];

    Log::info("ReportController@loanHistory: Fetched {$loanTransactions->total()} loan transactions.", ['requesting_user_id' => $requestingUserId]);

    // Pass the data to the view with the key 'loanTransactions'
    return view('reports.loan-history', compact('loanTransactions', 'users', 'equipmentList', 'transactionTypes', 'request'));
  }

  /**
   * Display a report on email account applications.
   * SDD Ref: [cite: 1]
   */
  public function emailAccounts(Request $request): View
  {
    $requestingUserId = Auth::id();
    Log::info("ReportController@emailAccounts: Generating Email Accounts Report.", ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);
    $query = EmailApplication::with(['user:id,name,department_id', 'user.department:id,name']);

    if ($request->filled('status') && $request->status !== 'all') $query->where('status', $request->input('status'));
    if ($request->filled('user_id')) $query->where('user_id', $request->input('user_id'));
    if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->input('date_from'));
    if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->input('date_to'));
    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where(fn($q) => $q->where('proposed_email', 'like', "%{$searchTerm}%")->orWhere('application_reason_notes', 'like', "%{$searchTerm}%")
        ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$searchTerm}%")->orWhere('email', 'like', "%{$searchTerm}%")));
    }
    $emailApplications = $query->latest('updated_at')->paginate(config('pagination.reports_per_page', 20));
    $usersFilter = User::orderBy('name')->pluck('name', 'id');
    $statuses = method_exists(EmailApplication::class, 'getStatusOptions') ? EmailApplication::getStatusOptions() : []; // Added check for method existence
    Log::info("ReportController@emailAccounts: Fetched {$emailApplications->total()} email applications.", ['requesting_user_id' => $requestingUserId]);
    // Assuming view 'reports.email-accounts' uses $emailApplications, $usersFilter, $statuses
    return view('reports.email-accounts', compact('emailApplications', 'usersFilter', 'statuses', 'request'));
  }

  /**
   * Display a report on loan applications.
   * SDD Ref: [cite: 1]
   */
  public function loanApplications(Request $request): View
  {
    $requestingUserId = Auth::id();
    Log::info("ReportController@loanApplications: Generating Loan Applications Report.", ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);
    $query = LoanApplication::with([
      'user:id,name,department_id',
      'user.department:id,name',
      'responsibleOfficer:id,name',
      'supportingOfficer:id,name',
      'approvals.officer:id,name',
    ]);

    if ($request->filled('status') && $request->status !== 'all') $query->where('status', $request->input('status'));
    if ($request->filled('user_id')) $query->where('user_id', $request->input('user_id'));
    if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->input('date_from'));
    if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->input('date_to'));
    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where(fn($q) => $q->where('purpose', 'like', "%{$searchTerm}%")->orWhere('location', 'like', "%{$searchTerm}%")
        ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$searchTerm}%")));
    }
    $loanApplications = $query->latest('updated_at')->paginate(config('pagination.reports_per_page', 20));
    $usersFilter = User::orderBy('name')->pluck('name', 'id');
    $statuses = method_exists(LoanApplication::class, 'getStatusOptions') ? LoanApplication::getStatusOptions() : []; // Added check for method existence
    Log::info("ReportController@loanApplications: Fetched {$loanApplications->total()} loan applications.", ['requesting_user_id' => $requestingUserId]);
    // Assuming view 'reports.loan-applications' uses $loanApplications, $usersFilter, $statuses
    return view('reports.loan-applications', compact('loanApplications', 'usersFilter', 'statuses', 'request'));
  }

  /**
   * Display the main reports index page.
   * SDD Ref: [cite: 1]
   */
  public function index(Request $request): View
  {
    Log::info("ReportController@index: Displaying main reports page.", ['user_id' => Auth::id()]);
    // Assuming view path is 'reports.index' as per SDD
    return view('reports.index');
  }
}
