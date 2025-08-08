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

/**
 * Controller for legacy (non-Livewire) reports.
 * Most main reports are now handled by Livewire components, but
 * PDF export and some legacy routes remain here.
 */
class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Main reports index page (still used).
     */
    public function index(Request $request): View
    {
        Log::info('ReportController@index: Displaying main reports page.', ['user_id' => Auth::id()]);
        return view('reports.reports-index');
    }

    /**
     * Equipment Inventory Report (legacy controller, for PDF export or direct view fallback).
     * Now primary interface is Livewire, but PDF/export uses this controller.
     */
    public function equipmentInventory(Request $request): View|Response
    {
        $this->authorize('viewLoanReports', Equipment::class);

        // Filtering logic for export - must mirror Livewire component if used for PDF.
        $query = Equipment::with(['department']);

        // Filters
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('tag_id', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('asset_type') && $request->input('asset_type') !== '') {
            $query->where('asset_type', $request->input('asset_type'));
        }
        if ($request->filled('department_id') && $request->input('department_id') !== '') {
            $query->where('department_id', $request->input('department_id'));
        }

        // Pagination or export
        $equipmentList = $query->orderBy('tag_id')->paginate(20);

        // For filter dropdowns
        $assetTypes = Equipment::getAssetTypeOptions();
        $statuses = Equipment::getStatusOptions();
        $departments = Department::orderBy('name')->pluck('name', 'id')->toArray();

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.equipment-inventory-report', compact('equipmentList', 'assetTypes', 'statuses', 'departments'));
            return $pdf->download('equipment-inventory.pdf');
        }

        return view('reports.equipment-inventory-report', compact('equipmentList', 'assetTypes', 'statuses', 'departments'));
    }

    /**
     * Loan Applications Report (legacy controller, for PDF export or direct view fallback).
     * Main UI is Livewire, but PDF/export handled here.
     */
    public function loanApplications(Request $request): View|Response
    {
        $this->authorize('viewLoanReports', LoanApplication::class);

        $query = LoanApplication::with(['user.department', 'loanApplicationItems']);

        // Filters
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('purpose', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('department_id') && $request->input('department_id') !== '') {
            $query->whereHas('user.department', function ($q) use ($request) {
                $q->where('id', $request->input('department_id'));
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $loanApplications = $query->orderByDesc('created_at')->paginate(20);

        // For filter dropdowns
        $statusOptions = LoanApplication::getStatusOptions();
        $departmentOptions = Department::orderBy('name')->pluck('name', 'id')->toArray();

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.loan-applications-report', compact('loanApplications', 'statusOptions', 'departmentOptions'));
            return $pdf->download('loan-applications.pdf');
        }

        return view('reports.loan-applications-report', compact('loanApplications', 'statusOptions', 'departmentOptions'));
    }

    /**
     * Loan History Report (legacy controller, for PDF export or direct view fallback).
     */
    public function loanHistory(Request $request): View|Response
    {
        $this->authorize('viewLoanReports', LoanTransaction::class);

        $query = LoanTransaction::with([
            'loanApplication.user.department',
            'items.equipment'
        ]);

        // Filters
        if ($request->filled('user_id') && $request->input('user_id') !== '') {
            $query->whereHas('loanApplication.user', function ($q) use ($request) {
                $q->where('id', $request->input('user_id'));
            });
        }
        if ($request->filled('type') && $request->input('type') !== '') {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $loanTransactions = $query->orderByDesc('transaction_date')->paginate(20);

        // Users filter for dropdown
        $usersFilter = User::orderBy('name')->pluck('name', 'id')->toArray();
        $transactionTypes = [
            'issue' => __('Pengeluaran'),
            'return' => __('Pemulangan'),
        ];

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.loan-history-report', compact('loanTransactions', 'usersFilter', 'transactionTypes', 'request'));
            return $pdf->download('loan-history.pdf');
        }

        return view('reports.loan-history-report', compact('loanTransactions', 'usersFilter', 'transactionTypes', 'request'));
    }

    /**
     * Loan Status Summary Report (for pie/bar chart and summary).
     */
    public function loanStatusSummary(Request $request): View|Response
    {
        $this->authorize('viewLoanReports', LoanApplication::class);

        $data = LoanApplication::select(['status', DB::raw('count(*) as count')])
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
            $pdf = Pdf::loadView('reports.loan-status-summary-report', ['data' => $data]);
            return $pdf->download('loan-status-summary.pdf');
        }

        return view('reports.loan-status-summary-report', ['data' => $data]);
    }

    /**
     * Utilization Report (equipment utilization rate and summary).
     */
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

    /**
     * User Activity Report (legacy, fallback only).
     * Main UI is Livewire.
     */
    public function userActivityLog(Request $request): View
    {
        // Kept as a fallback for direct blade view, Livewire is primary.
        $users = User::with(['department'])
            ->withCount(['loanApplicationsAsApplicant', 'approvalsAsApprover'])
            ->orderByDesc('loan_applications_as_applicant_count')
            ->paginate(20);

        $pageTitle = __('reports.user_activity.title');
        return view('reports.user-activity-log-report', compact('users', 'pageTitle'));
    }

    /**
     * NEW: Helpdesk-specific reports (stub).
     */
    public function helpdeskTicketVolume(Request $request): View|Response
    {
        // Logic to be implemented for helpdesk module
        return view('reports.helpdesk-ticket-volume');
    }

    public function helpdeskResolutionTimes(Request $request): View|Response
    {
        // Logic to be implemented for helpdesk module
        return view('reports.helpdesk-resolution-times');
    }
}
