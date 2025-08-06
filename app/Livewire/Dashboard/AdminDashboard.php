<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;
use App\Models\Approval;
use App\Models\Equipment;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\DB; // Ensure DB facade is imported

class AdminDashboard extends Component
{
  public int $users_count = 0;
  public int $pending_approvals_count = 0;
  public int $equipment_available_count = 0;
  public int $equipment_on_loan_count = 0;
  public int $loan_issued_count = 0;
  public int $loan_approved_pending_issuance_count = 0;
  public int $loan_returned_count = 0;

  public int $total_active_loans_count = 0;
  public int $overdue_loans_count = 0;
  public float $equipment_utilization_rate = 0.0;
  public array $equipment_status_summary = [];

  public array $loan_status_chart_data = [];

  public function mount(): void
  {
    $this->fetchDashboardStats();
    $this->prepareLoanStatusChart();
  }

  public function fetchDashboardStats(): void
  {
    $this->users_count = User::count();
    $this->pending_approvals_count = Approval::where('status', 'pending')->count();
    $this->equipment_available_count = Equipment::where('status', Equipment::STATUS_AVAILABLE)->count();
    // CORRECTED: The constant was STATUS_LOANED, which does not exist.
    // It has been changed to the correct constant, STATUS_ON_LOAN.
    $this->equipment_on_loan_count = Equipment::where('status', Equipment::STATUS_ON_LOAN)->count();

    $this->loan_issued_count = LoanApplication::where('status', LoanApplication::STATUS_ISSUED)->count();
    $this->loan_approved_pending_issuance_count = LoanApplication::where('status', LoanApplication::STATUS_APPROVED)->count();
    $this->loan_returned_count = LoanApplication::where('status', LoanApplication::STATUS_RETURNED)->count();

    $this->total_active_loans_count = LoanApplication::whereIn('status', [
      LoanApplication::STATUS_APPROVED,
      LoanApplication::STATUS_ISSUED,
      LoanApplication::STATUS_PARTIALLY_ISSUED,
      LoanApplication::STATUS_OVERDUE,
    ])->count();

    $this->overdue_loans_count = LoanApplication::where('status', LoanApplication::STATUS_OVERDUE)->count();

    $this->equipment_utilization_rate = Equipment::getUtilizationRate(); // Assuming this static method exists

    $this->equipment_status_summary = Equipment::getStatusSummary(); // Assuming this static method exists
  }

  public function prepareLoanStatusChart(): void
  {
    $stats = LoanApplication::select([
        'status',
        DB::raw('count(*) as total')
    ])
    ->groupBy('status')
    ->pluck('total', 'status')
    ->toArray();

    $labels = [
      LoanApplication::STATUS_PENDING_SUPPORT => __('dashboard.status_pending_support'),
      LoanApplication::STATUS_APPROVED => __('dashboard.status_approved'),
      LoanApplication::STATUS_ISSUED => __('dashboard.status_on_loan'),
      LoanApplication::STATUS_PARTIALLY_ISSUED => __('dashboard.status_on_loan'),
      LoanApplication::STATUS_RETURNED => __('dashboard.status_returned'),
      LoanApplication::STATUS_REJECTED => __('dashboard.status_rejected'),
      LoanApplication::STATUS_OVERDUE => __('dashboard.status_overdue'),
    ];

    $colors = [
      LoanApplication::STATUS_PENDING_SUPPORT => '#ffc107',
      LoanApplication::STATUS_APPROVED => '#0d6efd',
      LoanApplication::STATUS_ISSUED => '#0dcaf0',
      LoanApplication::STATUS_PARTIALLY_ISSUED => '#0dcaf0',
      LoanApplication::STATUS_RETURNED => '#198754',
      LoanApplication::STATUS_REJECTED => '#dc3545',
      LoanApplication::STATUS_OVERDUE => '#6610f2',
    ];

    $filtered = array_filter($stats, fn($v, $k) => isset($labels[$k]), ARRAY_FILTER_USE_BOTH);

    $this->loan_status_chart_data = [
      'labels' => array_map(fn($key) => $labels[$key], array_keys($filtered)),
      'datasets' => [[
        'label' => __('dashboard.loan_stats_title'),
        'data' => array_values($filtered),
        'backgroundColor' => array_map(fn($key) => $colors[$key], array_keys($filtered)),
        'borderColor' => array_map(fn($key) => $colors[$key], array_keys($filtered)),
        'borderWidth' => 1,
      ]],
    ];
  }

  public function render()
  {
    return view('livewire.dashboard.admin-dashboard');
  }
}
