<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\Equipment;
use App\Models\Approval;
use Livewire\Component;

class AdminDashboard extends Component
{
    // Properties to hold the statistics
    public int $users_count = 0;
    public int $pending_approvals_count = 0;
    public int $equipment_available_count = 0;
    public int $equipment_on_loan_count = 0;

    public int $email_completed_count = 0;
    public int $email_pending_count = 0;
    public int $email_rejected_count = 0;

    public int $loan_issued_count = 0;
    public int $loan_approved_pending_issuance_count = 0;
    public int $loan_returned_count = 0;

    /**
     * Mount the component and fetch all necessary data.
     */
    public function mount(): void
    {
        $this->fetchDashboardStats();
    }

    /**
     * Fetch all statistics for the admin dashboard.
     */
    public function fetchDashboardStats(): void
    {
        // General Stats
        $this->users_count = User::count();
        $this->pending_approvals_count = Approval::where('status', 'pending')->count(); // Note: Assumes an Approval model exists
        $this->equipment_available_count = Equipment::where('status', 'available')->count();
        $this->equipment_on_loan_count = Equipment::where('status', 'on_loan')->count();

        // Email Application Stats
        $this->email_completed_count = EmailApplication::where('status', EmailApplication::STATUS_COMPLETED)->count();
        $this->email_rejected_count = EmailApplication::where('status', EmailApplication::STATUS_REJECTED)->count();
        $this->email_pending_count = EmailApplication::whereIn('status', [
            EmailApplication::STATUS_PENDING_SUPPORT,
            EmailApplication::STATUS_PENDING_ADMIN,
            EmailApplication::STATUS_PROCESSING
        ])->count();

        // Loan Application Stats
        $this->loan_issued_count = LoanApplication::whereIn('status', [LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED])->count();
        $this->loan_returned_count = LoanApplication::where('status', LoanApplication::STATUS_RETURNED)->count();
        $this->loan_approved_pending_issuance_count = LoanApplication::where('status', LoanApplication::STATUS_APPROVED)->count();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.dashboard.admin-dashboard');
    }
}
