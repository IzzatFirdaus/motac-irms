<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction; // Added use statement
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
        // ... activityLog method remains the same ...
        $requestingUserId = Auth::id();
        Log::info('ReportController@activityLog: Generating User Activity Report.', ['requesting_user_id' => $requestingUserId, 'filters' => $request->all()]);

        $query = User::withCount([
            'emailApplications',
            'loanApplicationsAsApplicant as loan_applications_count',
            'approvalsMade as approvals_count',
        ])->with(['department:id,name', 'grade:id,name,level', 'position:id,name']);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', sprintf('%%%s%%', $searchTerm))
                ->orWhere('email', 'like', sprintf('%%%s%%', $searchTerm));
        }

        $users = $query->paginate(config('pagination.reports_per_page', 20));

        return view('reports.activity-log', ['users' => $users, 'request' => $request]);
    }

    /**
     * Generate and display the equipment inventory report with filtering.
     */
    public function equipmentInventory(Request $request): View
    {
        // ... equipmentInventory method remains the same ...
        $query = Equipment::query()
            ->with([
                'department:id,name',
                'activeLoanTransactionItem.loanTransaction.loanApplication.user:id,name',
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
            $query->where(function ($q) use ($search): void {
                $q->where('tag_id', 'like', sprintf('%%%s%%', $search))
                    ->orWhere('brand', 'like', sprintf('%%%s%%', 'model', 'like', sprintf('%%%s%%', $search)))
                    ->orWhere('serial_number', 'like', sprintf('%%%s%%', $search));
            });
        }
        $equipmentList = $query->orderBy('tag_id', 'asc')->paginate(20)->withQueryString();
        $statuses = Equipment::getStatusOptions();
        $assetTypes = Equipment::getAssetTypeOptions();
        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('reports.equipment-inventory', compact('equipmentList', 'statuses', 'assetTypes', 'departments', 'request'));
    }

    /**
     * Generate and display the loan applications report.
     */
    public function loanApplications(Request $request): View
    {
        // ... loanApplications method remains the same ...
        $this->authorize('viewLoanReports', LoanApplication::class);

        $query = LoanApplication::query()
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
        // ... emailAccounts method remains the same ...
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
        $emailApplications = $query->latest('updated_at')->paginate(20)->withQueryString();
        $usersFilter = User::orderBy('name')->pluck('name', 'id');
        $statuses = EmailApplication::getStatusOptions();

        return view('reports.email-accounts', compact('emailApplications', 'usersFilter', 'statuses', 'request'));
    }

    /**
     * FIXED: This new method generates the loan history report.
     * It fetches all loan transactions with relevant filters.
     */
    public function loanHistory(Request $request): View
    {
        $this->authorize('viewLoanReports', LoanApplication::class);

        $query = LoanTransaction::with([
            'loanApplication:id,user_id',
            'loanApplication.user:id,name',
            'issuingOfficer:id,name',
            'receivingOfficer:id,name',
            'items.equipment:id,brand,model,tag_id'
        ]);

        // Add filters based on request
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

        // Data for filter dropdowns
        $usersFilter = User::whereHas('loanApplicationsAsApplicant')->orderBy('name')->pluck('name', 'id');
        $transactionTypes = LoanTransaction::getTypeOptions();

        return view('reports.loan-history', compact('loanTransactions', 'usersFilter', 'transactionTypes', 'request'));
    }
}
