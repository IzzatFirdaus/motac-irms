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
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
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

        return view('livewire.dashboard.dashboard-wrapper');
    }

    /**
     * Gathers data and returns the view for the Admin dashboard.
     * @return View
     */
    private function showAdminDashboard(): View
    {
        $data = [
            'users_count' => User::count(),
            'pending_approvals_count' => LoanApplication::whereIn('status', [
                LoanApplication::STATUS_PENDING_SUPPORT,
                // CORRECTED: Using the new constant from the LoanApplication model
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
     * @return View
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
     * @return View
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
     * @param User $user
     * @return View
     */
    private function showApproverDashboard(User $user): View
    {
        $thirtyDaysAgo = now()->subDays(30);
        $data = [
            'approved_last_30_days' => 0, // Placeholder
            'rejected_last_30_days' => 0, // Placeholder
        ];
        return view('dashboard.approver', $data);
    }
}
