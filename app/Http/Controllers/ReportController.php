<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Helpers\Helpers;

class ReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request): View
  {
    Log::info('ReportController@index: Displaying main reports page.', ['user_id' => Auth::id()]);
    return view('reports.index');
  }

  /**
   * This method is no longer directly used for the user activity log route
   * as it is now handled by a Livewire component (UserActivityReport).
   * You can remove this method if it's not used elsewhere.
   */
  // public function activityLog(Request $request): View
  // {
  //   $query = User::withCount([
  //     'emailApplications',
  //     'loanApplicationsAsApplicant',
  //     'approvalsMade',
  //   ])->with(['department:id,name', 'roles:id,name']);

  //   if ($request->filled('search')) {
  //     $searchTerm = $request->input('search');
  //     $query->where(function ($q) use ($searchTerm) {
  //       $q->where('name', 'like', "%{$searchTerm}%")
  //         ->orWhere('email', 'like', "%{$searchTerm}%");
  //     });
  //   }

  //   if ($request->filled('department_id')) {
  //     $query->where('department_id', $request->input('department_id'));
  //   }

  //   if ($request->filled('role_name')) {
  //     $query->whereHas('roles', function ($q) use ($request) {
  //       $q->where('name', $request->input('role_name'));
  //     });
  //   }

  //   $users = $query->paginate(20)->withQueryString();
  //   $departmentOptions = Department::orderBy('name')->pluck('name', 'id')->toArray();
  //   $roleOptions = config('constants.roles', []);

  //   $pageTitle = __('reports.user_activity.title');

  //   return view('reports.user-activity-log', compact(
  //     'users',
  //     'request',
  //     'departmentOptions',
  //     'roleOptions',
  //     'pageTitle'
  //   ));
  // }

  public function equipmentInventory(Request $request): View
  {
    $query = Equipment::query()
      ->with([
        'department:id,name',
        'activeLoanTransactionItem.loanTransaction.loanApplication.user:id,name',
      ]);

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

    $equipmentList = $query->orderBy('tag_id')->paginate(20)->withQueryString();
    $statuses = Equipment::getStatusOptions();
    $assetTypes = Equipment::getAssetTypeOptions();
    $departments = Department::orderBy('name')->pluck('name', 'id');

    return view('reports.equipment-inventory', compact(
      'equipmentList', 'statuses', 'assetTypes', 'departments', 'request'
    ));
  }

  public function loanApplications(Request $request): View
  {
    $this->authorize('viewLoanReports', LoanApplication::class);

    $query = LoanApplication::query()
      ->with(['user:id,name,department_id', 'user.department:id,name', 'loanApplicationItems']);

    if ($request->filled('status') && $request->input('status') !== 'all') {
      $query->where('status', $request->input('status'));
    }

    if ($request->filled('department_id')) {
      $query->whereHas('user.department', function ($q) use ($request) {
        $q->where('id', $request->input('department_id'));
      });
    }

    if ($request->filled('date_from')) {
      $query->whereDate('loan_start_date', '>=', $request->input('date_from'));
    }

    if ($request->filled('date_to')) {
      $query->whereDate('loan_start_date', '<=', $request->input('date_to'));
    }

    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where(function ($q) use ($searchTerm) {
        $q->where('id', 'like', "%{$searchTerm}%")
          ->orWhere('purpose', 'like', "%{$searchTerm}%")
          ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$searchTerm}%"));
      });
    }

    $loanApplications = $query->latest()->paginate(20)->withQueryString();
    $statusOptions = LoanApplication::getStatusOptions();
    $departmentOptions = Department::pluck('name', 'id')->toArray();

    return view('reports.loan-applications', compact(
      'loanApplications', 'request', 'statusOptions', 'departmentOptions'
    ));
  }

  public function emailAccounts(Request $request): View
  {
    $this->authorize('viewEmailReports', EmailApplication::class);

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

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%"))
          ->orWhere('proposed_email', 'like', "%$search%");
      });
    }

    $emailApplications = $query->latest()->paginate(20)->withQueryString();
    $usersFilter = User::orderBy('name')->pluck('name', 'id');
    $statuses = EmailApplication::getStatusOptions();

    return view('reports.email-accounts', compact(
      'emailApplications', 'usersFilter', 'statuses', 'request'
    ));
  }

  public function loanHistory(Request $request): View
  {
    $this->authorize('viewLoanReports', LoanApplication::class);

    $query = LoanTransaction::with([
      'loanApplication:id,user_id',
      'loanApplication.user:id,name,department_id',
      'items.equipment:id,brand,model,tag_id',
    ]);

    if ($request->filled('user_id')) {
      $query->whereHas('loanApplication', function ($q) use ($request) {
        $q->where('user_id', $request->input('user_id'));
      });
    }

    if ($request->filled('type')) {
      $query->where('type', $request->input('type'));
    }

    if ($request->filled('date_from')) {
      $query->whereDate('transaction_date', '>=', $request->input('date_from'));
    }

    if ($request->filled('date_to')) {
      $query->whereDate('transaction_date', '<=', $request->input('date_to'));
    }

    $loanTransactions = $query->latest('transaction_date')->paginate(20)->withQueryString();
    $usersFilter = User::orderBy('name')->pluck('name', 'id');
    $transactionTypes = LoanTransaction::getTypeOptions();

    return view('reports.loan-history', compact(
      'loanTransactions', 'usersFilter', 'transactionTypes', 'request'
    ));
  }

  public function loanStatusSummary(Request $request): View|Response
  {
    $this->authorize('viewLoanReports', LoanApplication::class);

    $data = LoanApplication::query()
      ->select('status', DB::raw('count(*) as count'))
      ->groupBy('status')
      ->pluck('count', 'status')
      ->mapWithKeys(fn($count, $status) => [
        $status => [
          'label' => LoanApplication::getStatusOptions()[$status] ?? ucfirst(str_replace('_', ' ', $status)),
          'count' => $count,
        ]
      ])
      ->toArray();

    if ($request->filled('export') && $request->input('export') === 'pdf') {
      $pdf = Pdf::loadView('reports.loan-status-summary', ['data' => $data]);
      return $pdf->download('loan-status-summary.pdf');
    }

    return view('reports.loan-status-summary', ['data' => $data]);
  }

  public function utilizationReport(Request $request): View|Response
  {
    $this->authorize('viewLoanReports', Equipment::class);

    $summary = Equipment::getStatusSummary();
    $utilizationRate = Equipment::getUtilizationRate();

    if ($request->filled('export') && $request->input('export') === 'pdf') {
      $pdf = Pdf::loadView('reports.utilization-report', compact('summary', 'utilizationRate'));
      return $pdf->download('utilization-report.pdf');
    }

    return view('reports.utilization-report', compact('summary', 'utilizationRate'));
  }
}
