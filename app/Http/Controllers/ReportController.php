<?php

// ReportController.php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

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

        // Filtering logic uses model scopes to keep controller thin
        $equipmentList = Equipment::with(['department'])
            ->search((string) $request->input('search', ''))
            ->filterStatus($request->input('status'))
            ->filterAssetType($request->input('asset_type'))
            ->filterDepartment($request->integer('department_id'))
            ->orderBy('tag_id')
            ->paginate(20);

        // For filter dropdowns
        $assetTypes  = Equipment::getAssetTypeOptions();
        $statuses    = Equipment::getStatusOptions();
        $departments = Department::orderBy('name')->pluck('name', 'id')->toArray();

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.equipment-inventory-report', ['equipmentList' => $equipmentList, 'assetTypes' => $assetTypes, 'statuses' => $statuses, 'departments' => $departments]);

            return $pdf->download('equipment-inventory.pdf');
        }

        return view('reports.equipment-inventory-report', ['equipmentList' => $equipmentList, 'assetTypes' => $assetTypes, 'statuses' => $statuses, 'departments' => $departments]);
    }

    /**
     * Loan Applications Report (legacy controller, for PDF export or direct view fallback).
     * Main UI is Livewire, but PDF/export handled here.
     */
    public function loanApplications(Request $request): View|Response
    {
        $this->authorize('viewLoanReports', LoanApplication::class);

        $loanApplications = LoanApplication::with(['user.department', 'loanApplicationItems'])
            ->search((string) $request->input('search', ''))
            ->filterStatus($request->input('status'))
            ->filterDepartment($request->integer('department_id'))
            ->filterCreatedBetween($request->input('date_from'), $request->input('date_to'))
            ->orderByDesc('created_at')
            ->paginate(20);

        // For filter dropdowns
        $statusOptions     = LoanApplication::getStatusOptions();
        $departmentOptions = Department::orderBy('name')->pluck('name', 'id')->toArray();

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.loan-applications-report', ['loanApplications' => $loanApplications, 'statusOptions' => $statusOptions, 'departmentOptions' => $departmentOptions]);

            return $pdf->download('loan-applications.pdf');
        }

        return view('reports.loan-applications-report', ['loanApplications' => $loanApplications, 'statusOptions' => $statusOptions, 'departmentOptions' => $departmentOptions]);
    }

    /**
     * Loan History Report (legacy controller, for PDF export or direct view fallback).
     */
    public function loanHistory(Request $request): View|Response
    {
        $this->authorize('viewLoanReports', LoanTransaction::class);

        $loanTransactions = LoanTransaction::with([
            'loanApplication.user.department',
            'items.equipment',
        ])
            ->filterUser($request->integer('user_id'))
            ->filterType($request->input('type'))
            ->filterDateBetween($request->input('date_from'), $request->input('date_to'))
            ->orderByDesc('transaction_date')
            ->paginate(20);

        // Users filter for dropdown
        $usersFilter      = User::orderBy('name')->pluck('name', 'id')->toArray();
        $transactionTypes = [
            'issue'  => __('Pengeluaran'),
            'return' => __('Pemulangan'),
        ];

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.loan-history-report', ['loanTransactions' => $loanTransactions, 'usersFilter' => $usersFilter, 'transactionTypes' => $transactionTypes, 'request' => $request]);

            return $pdf->download('loan-history.pdf');
        }

        return view('reports.loan-history-report', ['loanTransactions' => $loanTransactions, 'usersFilter' => $usersFilter, 'transactionTypes' => $transactionTypes, 'request' => $request]);
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
                ],
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

        $summary         = Equipment::getStatusSummary();
        $utilizationRate = Equipment::getUtilizationRate();

        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.utilization-report', ['summary' => $summary, 'utilizationRate' => $utilizationRate]);

            return $pdf->download('utilization-report.pdf');
        }

        return view('reports.utilization-report', ['summary' => $summary, 'utilizationRate' => $utilizationRate]);
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

        return view('reports.user-activity-log-report', ['users' => $users, 'pageTitle' => $pageTitle]);
    }

    /**
     * NEW: Helpdesk-specific reports (stub).
     * This is a fallback/action for /reports/helpdesk-tickets route.
     */
    public function helpdeskTickets(Request $request): View|Response
    {
        // Logic to be implemented for helpdesk module
        // For now, just render the stub view
        return view('reports.helpdesk-tickets');
    }

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
