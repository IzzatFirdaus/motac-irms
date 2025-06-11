<?php

namespace App\Http\Controllers;

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

        if ($user->hasRole(['Approver', 'HOD'])) {
            return $this->showApproverDashboard($user);
        }

        // **THE FIX**: Call a dedicated method for the user dashboard.
        return $this->showUserDashboard($user);
    }

    /**
     * Gathers data and returns the view for the Admin dashboard.
     */
    private function showAdminDashboard(): View
    {
        $data = [
            'users_count' => User::count(),
            'pending_approvals_count' => LoanApplication::whereIn('status', [
                LoanApplication::STATUS_PENDING_SUPPORT,
                LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
                LoanApplication::STATUS_PENDING_BPM_REVIEW,
            ])->count(),
            'equipment_available_count' => Equipment::where('status', Equipment::STATUS_AVAILABLE)->count(),
            'equipment_on_loan_count' => Equipment::where('status', Equipment::STATUS_ON_LOAN)->count(),
            'email_completed_count' => EmailApplication::where('status', EmailApplication::STATUS_COMPLETED)->count(),
            'email_pending_count' => EmailApplication::whereIn('status', [
                EmailApplication::STATUS_PENDING_SUPPORT,
                EmailApplication::STATUS_PENDING_ADMIN,
                EmailApplication::STATUS_PROCESSING
            ])->count(),
            'email_rejected_count' => EmailApplication::where('status', EmailApplication::STATUS_REJECTED)->count(),
            'loan_issued_count' => LoanApplication::where('status', LoanApplication::STATUS_ISSUED)->count(),
            'loan_approved_pending_issuance_count' => LoanApplication::where('status', LoanApplication::STATUS_APPROVED)->count(),
            'loan_returned_count' => LoanApplication::where('status', LoanApplication::STATUS_RETURNED)->count(),
        ];
        return view('dashboard.admin', $data);
    }

    /**
     * Gathers data and returns the view for the BPM Staff dashboard.
     */
    private function showBpmDashboard(): View
    {
        $data = [
            'availableLaptopsCount' => Equipment::where('status', Equipment::STATUS_AVAILABLE)->where('asset_type', 'laptop')->count(),
            'availableProjectorsCount' => Equipment::where('status', Equipment::STATUS_AVAILABLE)->where('asset_type', 'projector')->count(),
            'availablePrintersCount' => Equipment::where('status', Equipment::STATUS_AVAILABLE)->where('asset_type', 'printer')->count(),
        ];
        return view('dashboard.bpm', $data);
    }

    /**
     * Gathers data and returns the view for the IT Admin dashboard.
     */
    private function showItAdminDashboard(): View
    {
        $data = [
            'pending_email_applications_count' => EmailApplication::where('status', EmailApplication::STATUS_PENDING_ADMIN)->count(),
            'processing_email_applications_count' => EmailApplication::where('status', EmailApplication::STATUS_PROCESSING)->count(),
        ];
        return view('dashboard.itadmin', $data);
    }

    /**
     * Gathers data and returns the view for the Approver dashboard.
     */
    private function showApproverDashboard(User $user): View
    {
        $data = [
            'approved_last_30_days' => 0, // Placeholder
            'rejected_last_30_days' => 0, // Placeholder
        ];
        return view('dashboard.approver', $data);
    }

    /**
     * **THE FIX**: New method to gather data and return the view for a general user.
     */
    private function showUserDashboard(User $user): View
    {
        // This data will be available on the user's dashboard.
        $data = [
            'user' => $user,
            'active_loans_count' => $user->loanApplications()->whereIn('status', [LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED])->count(),
            'pending_applications_count' => $user->loanApplications()->where('status', 'like', 'pending_%')->count(),
        ];
        // This now returns the correct view asserted in the test.
        return view('dashboard.user', $data);
    }
}
