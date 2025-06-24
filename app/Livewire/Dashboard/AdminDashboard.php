<?php

namespace App\Livewire\Dashboard;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
     * @var array Data for the loan status doughnut chart.
     */
    public array $loan_status_chart_data = [];

    /**
     * Mount the component and fetch all necessary data.
     */
    public function mount(): void
    {
        $this->fetchDashboardStats();
        $this->prepareLoanStatusChart();
    }

    /**
     * Fetch all statistics for the main cards on the admin dashboard.
     */
    public function fetchDashboardStats(): void
    {
        // General Stats
        $this->users_count = User::count();
        $this->pending_approvals_count = Approval::where('status', 'pending')->count();
        $this->equipment_available_count = Equipment::where('status', 'available')->count();
        $this->equipment_on_loan_count = Equipment::where('status', 'on_loan')->count();

        // Email Application Stats
        $this->email_completed_count = EmailApplication::where('status', EmailApplication::STATUS_COMPLETED)->count();
        $this->email_rejected_count = EmailApplication::where('status', EmailApplication::STATUS_REJECTED)->count();
        $this->email_pending_count = EmailApplication::whereIn('status', [
            EmailApplication::STATUS_PENDING_SUPPORT,
            EmailApplication::STATUS_PENDING_ADMIN,
            EmailApplication::STATUS_PROCESSING,
        ])->count();

        // Loan Application Stats
        $this->loan_issued_count = LoanApplication::whereIn('status', [LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED])->count();
        $this->loan_returned_count = LoanApplication::where('status', LoanApplication::STATUS_RETURNED)->count();
        $this->loan_approved_pending_issuance_count = LoanApplication::where('status', LoanApplication::STATUS_APPROVED)->count();
    }

    /**
     * Prepare data for the loan application status doughnut chart.
     */
    public function prepareLoanStatusChart(): void
    {
        $stats = LoanApplication::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Use the new, specific keys from the dashboard language file.
        $labels = [
            LoanApplication::STATUS_PENDING_SUPPORT => __('dashboard.status_pending_support'),
            LoanApplication::STATUS_APPROVED => __('dashboard.status_approved'),
            LoanApplication::STATUS_ISSUED => __('dashboard.status_on_loan'),
            LoanApplication::STATUS_RETURNED => __('dashboard.status_returned'),
            LoanApplication::STATUS_REJECTED => __('dashboard.status_rejected'),
        ];

        // Define the color palette for the chart
        $colors = [
            LoanApplication::STATUS_PENDING_SUPPORT => '#ffc107',
            LoanApplication::STATUS_APPROVED => '#0d6efd',
            LoanApplication::STATUS_ISSUED => '#0dcaf0',
            LoanApplication::STATUS_RETURNED => '#198754',
            // FIXED: Removed the extra single quote from the key below
            LoanApplication::STATUS_REJECTED => '#dc3545',
        ];

        $filteredStats = array_intersect_key($stats, $labels);

        $this->loan_status_chart_data = [
            'labels' => array_values(array_intersect_key($labels, $filteredStats)),
            'datasets' => [[
                'label' => __('dashboard.loan_stats_title'),
                'data' => array_values($filteredStats),
                'backgroundColor' => array_values(array_intersect_key($colors, $filteredStats)),
                'hoverOffset' => 8,
                'borderColor' => '#fff',
                'borderWidth' => 2,
            ]]
        ];
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        return view('livewire.dashboard.admin-dashboard');
    }
}
