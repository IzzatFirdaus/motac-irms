<?php

namespace App\Http\Controllers;

use App\Models\EmailApplication; // Make sure this is present if emailAccounts method is re-added
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Corrected from auth() to Auth::id() previously
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function activityLog(Request $request): View
    {
        Log::info('Generating User Activity Report.', [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        $query = User::withCount([
            'emailApplications',
            'loanApplications',
            'approvals',
        ]);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search'); // Changed
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('full_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$searchTerm.'%');
            });
        }
        // Example for other filters in activityLog if it were still for Spatie's Activity model
        // if ($request->filled('user_id')) {
        //     $query->where('causer_id', $request->input('user_id')); // Changed
        // }
        // if ($request->filled('log_name')) {
        //     $query->where('log_name', $request->input('log_name')); // Changed
        // }
        // if ($request->filled('event')) {
        //     $query->where('event', $request->input('event')); // Changed
        // }

        $users = $query->latest('created_at')->paginate(20);

        Log::info('Successfully fetched data for User Activity Report.', [
            'user_id' => Auth::id(),
            'count' => $users->total(),
        ]);

        return view('reports.activity-log', compact('users', 'request'));
    }

    public function equipment(Request $request): View
    {
        Log::info('Generating Equipment Inventory Report.', [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        $query = Equipment::with(['category', 'location']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status')); // Changed
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id')); // Changed
        }
        if ($request->filled('category_id')) {
            $query->where('equipment_category_id', $request->input('category_id')); // Changed
        }
        if ($request->filled('search')) {
            $searchTerm = $request->input('search'); // Changed
            $query
                ->where('name', 'like', '%'.$searchTerm.'%')
                ->orWhere('serial_number', 'like', '%'.$searchTerm.'%');
        }

        $equipment = $query->paginate(15);
        $locations = Location::all();
        $categories = EquipmentCategory::all();

        Log::info('Successfully fetched data for Equipment Inventory Report.', [
            'user_id' => Auth::id(),
            'count' => $equipment->total(),
        ]);

        return view(
            'reports.equipment-inventory',
            compact('equipment', 'locations', 'categories', 'request')
        );
    }

    public function loanHistory(Request $request): View
    {
        Log::info('Generating Loan History Report.', [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        $query = LoanTransaction::with([
            'loanApplication.user',
            'loanApplication.responsibleOfficer',
            'items.equipment',
            'issuingOfficer',
            'receivingOfficer',
            'returningOfficer',
            'returnAcceptingOfficer',
        ])->whereIn('type', [
            LoanTransaction::TYPE_ISSUE,
            LoanTransaction::TYPE_RETURN,
        ]);

        if ($request->filled('user_id')) {
            $userId = $request->input('user_id'); // Changed
            $query->whereHas('loanApplication', function ($q) use ($userId): void {
                $q->where('user_id', $userId);
            });
        }
        if ($request->filled('equipment_id')) {
            $equipmentId = $request->input('equipment_id'); // Changed
            $query->whereHas('items', function ($q) use ($equipmentId): void {
                $q->where('equipment_id', $equipmentId);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from')); // Changed
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to')); // Changed
        }

        $loanHistory = $query->latest()->paginate(20);
        $users = User::all();
        $equipmentList = Equipment::all();

        Log::info('Successfully fetched data for Loan History Report.', [
            'user_id' => Auth::id(),
            'count' => $loanHistory->total(),
        ]);

        return view(
            'reports.loan-history',
            compact('loanHistory', 'users', 'equipmentList', 'request')
        );
    }

    // Ensure this method is defined if the route exists in web.php
    public function emailAccounts(Request $request): View
    {
        Log::info('Generating Email Accounts Report.', [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        $query = EmailApplication::with(['user.department']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status')); // Changed
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id')); // Changed
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from')); // Changed
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to')); // Changed
        }
        if ($request->filled('search')) {
            $searchTerm = $request->input('search'); // Changed
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('subject', 'like', '%'.$searchTerm.'%')
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm): void {
                        $userQuery->where('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('email', 'like', '%'.$searchTerm.'%');
                    });
            });
        }

        $emailApplications = $query->latest()->paginate(20);
        $usersFilter = User::orderBy('name')->get();
        $statuses = method_exists(EmailApplication::class, 'getStatuses')
          ? EmailApplication::getStatuses()
          : ['pending', 'approved', 'rejected', 'processed', 'failed'];

        Log::info('Successfully fetched data for Email Accounts Report.', [
            'user_id' => Auth::id(),
            'count' => $emailApplications->total(),
        ]);

        return view(
            'reports.email-accounts',
            compact('emailApplications', 'usersFilter', 'statuses', 'request')
        );
    }

    public function loanApplications(Request $request): View
    {
        Log::info('Generating Loan Applications Report.', [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);

        $query = LoanApplication::with([
            'user.department',
            'responsibleOfficer',
            'approvals.officer',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status')); // Changed
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id')); // Changed
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from')); // Changed
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to')); // Changed
        }

        $loanApplications = $query->latest()->paginate(20);
        $usersFilter = User::all();
        $statuses = LoanApplication::getStatuses();

        Log::info('Successfully fetched data for Loan Applications Report.', [
            'user_id' => Auth::id(),
            'count' => $loanApplications->total(),
        ]);

        return view(
            'reports.loan-applications',
            compact('loanApplications', 'usersFilter', 'statuses', 'request')
        );
    }
}
