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
        // Middleware for roles/permissions should be handled at the route level in web.php
        // as per current web.php structure.
    }

    /**
     * Display a report on user activities.
     * Corresponds to route: reports.activity-log
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function activityLog(Request $request): View
    {
        $adminUserId = Auth::id();
        Log::info("ReportController@activityLog: Generating User Activity Report.", [
            'admin_user_id' => $adminUserId,
            'filters' => $request->all(),
            'ip_address' => $request->ip(),
        ]);

        $query = User::withCount([
            'emailApplications',
            'loanApplicationsAsApplicant as loan_applications_count', // Ensure 'loanApplicationsAsApplicant' relation exists
            'approvalsMade as approvals_count',                     // Ensure 'approvalsMade' relation exists
        ])->with(['department:id,name', 'grade:id,name', 'position:id,name']);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                  ->orWhere('email', 'like', '%'.$searchTerm.'%')
                  ->orWhere('identification_number', 'like', '%'.$searchTerm.'%');
            });
        }
        // Add other filters (department, grade, etc.) as needed

        $users = $query->latest('created_at')->paginate(config('pagination.reports_per_page', 20));

        Log::info("ReportController@activityLog: Fetched {$users->total()} users for report.", ['admin_user_id' => $adminUserId]);

        return view('reports.activity-log', compact('users', 'request'));
    }

    /**
     * Display an equipment inventory report.
     * Method name changed to match web.php action.
     * Corresponds to route: reports.equipment-inventory
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function equipmentInventory(Request $request): View // Renamed from equipment
    {
        $adminUserId = Auth::id();
        Log::info("ReportController@equipmentInventory: Generating Equipment Inventory Report.", [
            'admin_user_id' => $adminUserId,
            'filters' => $request->all(),
            'ip_address' => $request->ip(),
        ]);

        $query = Equipment::with([
            'equipmentCategory:id,name',
            'definedLocation:id,name', // Ensure 'definedLocation' is the correct relation name
            'department:id,name',
        ]);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('location_id') && $request->location_id !== 'all') {
            $query->where('location_id', $request->input('location_id')); // Assuming 'location_id' is the FK on Equipment table
        }
        if ($request->filled('equipment_category_id') && $request->equipment_category_id !== 'all') {
            $query->where('equipment_category_id', $request->input('equipment_category_id'));
        }
        if ($request->filled('asset_type') && $request->asset_type !== 'all') {
            $query->where('asset_type', $request->input('asset_type'));
        }
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('tag_id', 'like', '%'.$searchTerm.'%')
                  ->orWhere('serial_number', 'like', '%'.$searchTerm.'%')
                  ->orWhere('brand', 'like', '%'.$searchTerm.'%')
                  ->orWhere('model', 'like', '%'.$searchTerm.'%')
                  ->orWhere('item_code', 'like', '%'.$searchTerm.'%');
            });
        }

        $equipment = $query->orderBy('tag_id')->paginate(config('pagination.reports_per_page', 20));

        $locations = Location::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $statuses = Equipment::getStatusOptions();
        $assetTypes = Equipment::getAssetTypeOptions();

        Log::info("ReportController@equipmentInventory: Fetched {$equipment->total()} equipment items.", ['admin_user_id' => $adminUserId]);

        return view('reports.equipment-inventory', compact('equipment', 'locations', 'categories', 'statuses', 'assetTypes', 'request'));
    }

    /**
     * Display a loan history report.
     * Corresponds to route: reports.loan-history
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function loanHistory(Request $request): View
    {
        $adminUserId = Auth::id();
        Log::info("ReportController@loanHistory: Generating Loan History Report.", [
            'admin_user_id' => $adminUserId,
            'filters' => $request->all(),
            'ip_address' => $request->ip(),
        ]);

        $query = LoanTransaction::with([
            'loanApplication.user:id,name',
            'loanApplication.responsibleOfficer:id,name',
            'loanTransactionItems.equipment:id,tag_id,asset_type,brand,model',
            'issuingOfficer:id,name',
            'receivingOfficer:id,name',
            'returningOfficer:id,name',
            'returnAcceptingOfficer:id,name',
        ]);

        if ($request->filled('user_id')) {
            $userIdFilter = $request->input('user_id');
            $query->whereHas('loanApplication', function ($q) use ($userIdFilter) {
                $q->where('user_id', $userIdFilter);
            });
        }
        if ($request->filled('equipment_id')) {
            $equipmentIdFilter = $request->input('equipment_id');
            $query->whereHas('loanTransactionItems', function ($q) use ($equipmentIdFilter) {
                $q->where('equipment_id', $equipmentIdFilter);
            });
        }
        if ($request->filled('transaction_type') && $request->transaction_type !== 'all') {
            $query->where('type', $request->input('transaction_type'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $loanHistory = $query->latest('transaction_date')->paginate(config('pagination.reports_per_page', 20));

        $users = User::orderBy('name')->pluck('name', 'id');
        $equipmentList = Equipment::orderBy('tag_id')->select('id', 'tag_id', 'brand', 'model')->get()->mapWithKeys(function ($item) {
            return [$item->id => "{$item->tag_id} ({$item->brand} {$item->model})"]; // Added () for clarity
        });
        $transactionTypes = LoanTransaction::getTypesOptions();

        Log::info("ReportController@loanHistory: Fetched {$loanHistory->total()} loan transactions.", ['admin_user_id' => $adminUserId]);

        return view('reports.loan-history', compact('loanHistory', 'users', 'equipmentList', 'transactionTypes', 'request'));
    }

    /**
     * Display a report on email account applications.
     * Corresponds to route: reports.email-accounts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function emailAccounts(Request $request): View
    {
        $adminUserId = Auth::id();
        Log::info("ReportController@emailAccounts: Generating Email Accounts Report.", [
            'admin_user_id' => $adminUserId,
            'filters' => $request->all(),
            'ip_address' => $request->ip(),
        ]);

        $query = EmailApplication::with(['user:id,name,department_id', 'user.department:id,name']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('proposed_email', 'like', '%'.$searchTerm.'%')
                  ->orWhere('application_reason_notes', 'like', '%'.$searchTerm.'%')
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', '%'.$searchTerm.'%')
                                ->orWhere('email', 'like', '%'.$searchTerm.'%');
                  });
            });
        }

        $emailApplications = $query->latest('updated_at')->paginate(config('pagination.reports_per_page', 20));

        $usersFilter = User::orderBy('name')->pluck('name', 'id');
        $statuses = EmailApplication::getStatusOptions();

        Log::info("ReportController@emailAccounts: Fetched {$emailApplications->total()} email applications.", ['admin_user_id' => $adminUserId]);

        return view('reports.email-accounts', compact('emailApplications', 'usersFilter', 'statuses', 'request'));
    }

    /**
     * Display a report on loan applications.
     * Corresponds to route: reports.loan-applications
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function loanApplications(Request $request): View
    {
        $adminUserId = Auth::id();
        Log::info("ReportController@loanApplications: Generating Loan Applications Report.", [
            'admin_user_id' => $adminUserId,
            'filters' => $request->all(),
            'ip_address' => $request->ip(),
        ]);

        $query = LoanApplication::with([
            'user:id,name,department_id', 'user.department:id,name',
            'responsibleOfficer:id,name',
            'supportingOfficer:id,name',
            'approvals.officer:id,name', // For showing who approved/rejected at various stages
        ]);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from')); // Assuming filter by creation date
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('purpose', 'like', '%'.$searchTerm.'%')
                  ->orWhere('location', 'like', '%'.$searchTerm.'%') // Usage location
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', '%'.$searchTerm.'%');
                  });
            });
        }

        $loanApplications = $query->latest('updated_at')->paginate(config('pagination.reports_per_page', 20));

        $usersFilter = User::orderBy('name')->pluck('name', 'id');
        $statuses = LoanApplication::getStatusOptions();

        Log::info("ReportController@loanApplications: Fetched {$loanApplications->total()} loan applications.", ['admin_user_id' => $adminUserId]);

        return view('reports.loan-applications', compact('loanApplications', 'usersFilter', 'statuses', 'request'));
    }

    /**
     * Display the main reports index page (if one exists).
     * Corresponds to route: reports.index
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // This method might show a dashboard of available reports or links to them.
        // Ensure the view 'reports.index' exists.
        return view('reports.index');
    }
}
