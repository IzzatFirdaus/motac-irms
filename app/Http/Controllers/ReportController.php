<?php
// ReportController.php

namespace App\Http\Controllers;

use App\Models\Department;
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
  // }

  public function equipmentInventory(Request $request): View | Response
  {
    $this->authorize('viewLoanReports', Equipment::class);

    $query = Equipment::query();

    if ($request->filled('search')) {
      $searchTerm = $request->input('search');
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'like', "%{$searchTerm}%")
          ->orWhere('tag_id', 'like', "%{$searchTerm}%");
      });
    }

    if ($request->filled('type')) {
      $query->where('type', $request->input('type'));
    }

    if ($request->filled('status')) {
      $query->where('status', $request->input('status'));
    }

    $equipment = $query->paginate(10);
    $equipmentTypes = Equipment::getEquipmentTypes();
    $equipmentStatuses = Equipment::getEquipmentStatuses();

    if ($request->filled('export') && $request->input('export') === 'pdf') {
      $pdf = Pdf::loadView('reports.equipment-inventory-pdf', compact('equipment', 'equipmentTypes', 'equipmentStatuses'));
      return $pdf->download('equipment-inventory.pdf');
    }

    return view(
      'reports.equipment-inventory',
      compact('equipment', 'equipmentTypes', 'equipmentStatuses')
    );
  }

  public function loanApplications(Request $request): View | Response
  {
    $this->authorize('viewLoanReports', LoanApplication::class);

    $query = LoanApplication::query()->with(['applicant.department', 'approvals']);

    if ($request->filled('status') && $request->input('status') !== 'all') {
      $query->where('status', $request->input('status'));
    }

    if ($request->filled('start_date')) {
      $query->whereDate('created_at', '>=', $request->input('start_date'));
    }

    if ($request->filled('end_date')) {
      $query->whereDate('created_at', '<=', $request->input('end_date'));
    }

    if ($request->filled('department_id') && $request->input('department_id') !== 'all') {
      $query->whereHas('applicant', function ($q) use ($request) {
        $q->where('department_id', $request->input('department_id'));
      });
    }

    $loanApplications = $query->orderBy('created_at', 'desc')->paginate(10);
    $statuses = LoanApplication::getStatusOptions();
    $departments = Department::orderBy('name')->get();

    if ($request->filled('export') && $request->input('export') === 'pdf') {
      $pdf = Pdf::loadView('reports.loan-applications-pdf', compact('loanApplications', 'statuses', 'departments'));
      return $pdf->download('loan-applications.pdf');
    }

    return view(
      'reports.loan-applications',
      compact('loanApplications', 'statuses', 'departments')
    );
  }

  // REMOVED: emailAccounts method as per transformation plan
  // public function emailAccounts(Request $request): View | Response
  // {
  //   $this->authorize('viewEmailReports', EmailApplication::class);

  //   $query = EmailApplication::query()->with(['user.department', 'approvals']);

  //   if ($request->filled('status')) {
  //     $query->where('status', $request->input('status'));
  //   }

  //   if ($request->filled('start_date')) {
  //     $query->whereDate('created_at', '>=', $request->input('start_date'));
  //   }

  //   if ($request->filled('end_date')) {
  //     $query->whereDate('created_at', '<=', $request->input('end_date'));
  //   }

  //   if ($request->filled('department_id')) {
  //     $query->whereHas('user', function ($q) use ($request) {
  //       $q->where('department_id', $request->input('department_id'));
  //     });
  //   }

  //   $emailApplications = $query->orderBy('created_at', 'desc')->paginate(10);
  //   $statuses = EmailApplication::getStatusOptions();
  //   $departments = Department::orderBy('name')->get();

  //   if ($request->filled('export') && $request->input('export') === 'pdf') {
  //     $pdf = Pdf::loadView('reports.email-accounts-pdf', compact('emailApplications', 'statuses', 'departments'));
  //     return $pdf->download('email-accounts.pdf');
  //   }

  //   return view(
  //     'reports.email-accounts',
  //     compact('emailApplications', 'statuses', 'departments')
  //   );
  // }

  public function loanHistory(Request $request): View | Response
  {
    $this->authorize('viewLoanReports', LoanTransaction::class);

    $query = LoanTransaction::query()->with(['equipment', 'loanApplication.applicant']);

    if ($request->filled('user_id')) {
      $query->whereHas('loanApplication', function ($q) use ($request) {
        $q->where('applicant_id', $request->input('user_id'));
      });
    }

    if ($request->filled('transaction_type')) {
      if ($request->input('transaction_type') === 'issue') {
        $query->whereNotNull('issued_at');
      } elseif ($request->input('transaction_type') === 'return') {
        $query->whereNotNull('returned_at');
      }
    }

    if ($request->filled('start_date')) {
      $query->where(function ($q) use ($request) {
        $q->whereDate('issued_at', '>=', $request->input('start_date'))
          ->orWhereDate('returned_at', '>=', $request->input('start_date'));
      });
    }

    if ($request->filled('end_date')) {
      $query->where(function ($q) use ($request) {
        $q->whereDate('issued_at', '<=', $request->input('end_date'))
          ->orWhereDate('returned_at', '<=', $request->input('end_date'));
      });
    }

    $loanTransactions = $query->orderBy('created_at', 'desc')->paginate(10);
    $usersFilter = User::orderBy('name')->get(['id', 'name']);
    $transactionTypes = [
      'issue' => 'Issue',
      'return' => 'Return',
    ];

    if ($request->filled('export') && $request->input('export') === 'pdf') {
      $pdf = Pdf::loadView('reports.loan-history-pdf', compact('loanTransactions', 'usersFilter', 'transactionTypes', 'request'));
      return $pdf->download('loan-history.pdf');
    }

    return view(
      'reports.loan-history',
      compact('loanTransactions', 'usersFilter', 'transactionTypes', 'request')
    );
  }

  public function loanStatusSummary(Request $request): View | Response
  {
    $this->authorize('viewLoanReports', LoanApplication::class);

    $data = LoanApplication::query()
      ->select(['status', DB::raw('count(*) as count')])
      ->groupBy('status')
      ->pluck('count', 'status')
      ->mapWithKeys(fn ($count, $status) => [
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

  public function utilizationReport(Request $request): View | Response
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

  // NEW: Helpdesk-specific reports
  public function helpdeskTicketVolume(Request $request): View | Response
  {
      // This method will display a report on ticket volume
      // Authorization and actual logic for fetching data will be added during Helpdesk module implementation
      return view('reports.helpdesk-ticket-volume');
  }

  public function helpdeskResolutionTimes(Request $request): View | Response
  {
      // This method will display a report on helpdesk ticket resolution times
      // Authorization and actual logic for fetching data will be added during Helpdesk module implementation
      return view('reports.helpdesk-resolution-times');
  }
}
