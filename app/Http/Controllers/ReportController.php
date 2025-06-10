<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use App\Models\Equipment;
use Illuminate\View\View;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\EmailApplication;
use App\Models\EquipmentCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Display the main reports index page.
   */
  public function index(Request $request): View
  {
    Log::info('ReportController@index: Displaying main reports page.', ['user_id' => Auth::id()]);
    return view('reports.index');
  }

  /**
   * Display a report on user activities.
   */
  public function activityLog(Request $request): View
  {
    $requestingUserId = Auth::id();
    Log::info('ReportController@activityLog: Generating User Activity Report.', ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);

    $query = User::withCount([
      'emailApplications',
      'loanApplicationsAsApplicant as loan_applications_count',
      'approvalsMade as approvals_count',
    ])->with(['department:id,name', 'grade:id,name,level', 'position:id,name']);

    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where('name', 'like', "%{$searchTerm}%")
        ->orWhere('email', 'like', "%{$searchTerm}%");
    }

    $users = $query->paginate(config('pagination.reports_per_page', 20));

    return view('reports.activity-log', compact('users', 'request'));
  }

  /**
   * Generate and display the equipment inventory report with filtering.
   */
  public function equipmentInventory(Request $request): View
  {
    $query = Equipment::query()
      // Eager load the entire relationship chain to fix the LazyLoadingViolationException.
      ->with([
        'department:id,name',
        'activeLoanTransactionItem.loanTransaction.loanApplication.user:id,name'
      ]);

    // Apply filters from the request
    if ($request->filled('status')) {
      $query->where('status', $request->input('status'));
    }
    if ($request->filled('asset_type')) {
      $query->where('asset_type', $request->input('asset_type'));
    }
    if ($request->filled('department_id')) {
      $query->where('department_id', $request->input('department_id'));
    }
    if ($request->filled('search')) {
      $search = $request->input('search');
      $query->where(function ($q) use ($search) {
        $q->where('tag_id', 'like', "%{$search}%")
          ->orWhere('brand', 'like', "%{$search}%")
          ->orWhere('model', 'like', "%{$search}%")
          ->orWhere('serial_number', 'like', "%{$search}%");
      });
    }

    $equipmentList = $query->orderBy('tag_id', 'asc')->paginate(20)->withQueryString();

    // Fetch data needed for the filter dropdowns
    $statuses = Equipment::getStatusOptions();
    $assetTypes = Equipment::getAssetTypeOptions();
    $departments = Department::orderBy('name')->pluck('name', 'id');

    return view('reports.equipment-inventory', [
      'equipmentList' => $equipmentList,
      'statuses' => $statuses,
      'assetTypes' => $assetTypes,
      'departments' => $departments,
      'request' => $request, // Pass the request to retain filter input values
    ]);
  }

  /**
   * Generate and display the loan applications report.
   */
  public function loanApplications(Request $request): View
  {
    $this->authorize('viewLoanReports', LoanApplication::class); // Assumes a policy permission

    $query = LoanApplication::query()
      // EDITED: Changed 'department:id,name' to the correct nested relationship 'user.department:id,name'
      // Also ensure the user relationship selects the foreign key 'department_id' needed for the nested load.
      ->with(['user:id,name,department_id', 'user.department:id,name']);

    // Apply filters
    if ($request->filled('status') && $request->input('status') !== 'all') {
      $query->where('status', $request->input('status'));
    }
    if ($request->filled('user_id') && $request->input('user_id') !== 'all') {
      $query->where('user_id', $request->input('user_id'));
    }
    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->input('date_from'));
    }
    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->input('date_to'));
    }

    $loanApplications = $query->latest('updated_at')->paginate(20)->withQueryString();

    $usersFilter = User::orderBy('name')->pluck('name', 'id');
    $statuses = LoanApplication::getStatusOptions();

    return view('reports.loan-applications', compact('loanApplications', 'usersFilter', 'statuses', 'request'));
  }

  /**
   * Display a report on email account applications.
   */
  public function emailAccounts(Request $request): View
  {
    $this->authorize('viewEmailReports', EmailApplication::class); // Assumes a policy permission

    $query = EmailApplication::query()
      ->with(['user:id,name,department_id', 'user.department:id,name']);

    if ($request->filled('status') && $request->input('status') !== 'all') {
      $query->where('status', $request->input('status'));
    }
    if ($request->filled('user_id') && $request->input('user_id') !== 'all') {
      $query->where('user_id', $request->input('user_id'));
    }
    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->input('date_from'));
    }
    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->input('date_to'));
    }

    $emailApplications = $query->latest('updated_at')->paginate(20)->withQueryString();

    $usersFilter = User::orderBy('name')->pluck('name', 'id');
    $statuses = EmailApplication::getStatusOptions();

    return view('reports.email-accounts', compact('emailApplications', 'usersFilter', 'statuses', 'request'));
  }
}
