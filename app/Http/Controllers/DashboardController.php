<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\User;
use App\Models\LoanApplication;
use App\Models\EmailApplication;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request and route to the correct dashboard based on user role.
     */
    public function __invoke(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return $this->showAdminDashboard();
        }

        if ($user->hasRole('BPM Staff')) {
            return $this->showBpmDashboard();
        }

        if ($user->hasRole('IT Admin')) {
            return $this->showItAdminDashboard();
        }

        if (Approval::where('officer_id', $user->id)->where('status', 'pending')->exists()) {
            return $this->showApproverDashboard($user);
        }

        return $this->showUserDashboard($user);
    }

    /**
     * Gathers data and returns the view for the Admin dashboard.
     */
    private function showAdminDashboard(): View
    {
        $data = [
            'users_count' => User::count(),
            'pending_approvals_count' => Approval::where('status', 'pending')->count(),
            'equipment_available_count' => Equipment::where('status', 'available')->count(),
            'equipment_on_loan_count' => Equipment::where('status', 'on_loan')->count(),
            'email_completed_count' => EmailApplication::where('status', 'completed')->count(),
            'email_pending_count' => EmailApplication::whereIn('status', ['pending_support', 'pending_admin', 'processing'])->count(),
            'email_rejected_count' => EmailApplication::where('status', 'rejected')->count(),
            'loan_issued_count' => LoanApplication::where('status', 'issued')->count(),
            'loan_approved_pending_issuance_count' => LoanApplication::where('status', 'approved')->count(),
            'loan_returned_count' => LoanApplication::where('status', 'returned')->count(),
        ];
        return view('dashboard.admin', $data);
    }

    /**
     * Gathers data and returns the view for the BPM Staff dashboard.
     * This now returns the Livewire component view which handles its own data.
     */
    private function showBpmDashboard(): View
    {
        return view('dashboard.bpm');
    }

    /**
     * Gathers data and returns the view for the IT Admin dashboard.
     */
    private function showItAdminDashboard(): View
    {
        $data = [
            'pending_email_applications_count' => EmailApplication::where('status', 'pending_admin')->count(),
            'processing_email_applications_count' => EmailApplication::where('status', 'processing')->count(),
        ];
        return view('dashboard.itadmin', $data);
    }

    /**
     * Gathers data and returns the view for the Approver dashboard.
     */
    private function showApproverDashboard(User $user): View
    {
        $data = [
            'approved_last_30_days' => Approval::where('officer_id', $user->id)->where('status', 'approved')->where('updated_at', '>=', now()->subDays(30))->count(),
            'rejected_last_30_days' => Approval::where('officer_id', $user->id)->where('status', 'rejected')->where('updated_at', '>=', now()->subDays(30))->count(),
        ];
        return view('dashboard.approver', $data);
    }

    /**
     * Gathers data and returns the view for a general user.
     */
    private function showUserDashboard(User $user): View
    {
        $data = [
            'user' => $user,
            // FIX: Changed loanApplications() to the correct relationship name: loanApplicationsAsApplicant()
            'active_loans_count' => $user->loanApplicationsAsApplicant()->whereIn('status', [LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED])->count(),
            'pending_applications_count' => $user->loanApplicationsAsApplicant()->where('status', 'like', 'pending_%')->count(),
        ];
        return view('dashboard.user', $data);
    }
}
