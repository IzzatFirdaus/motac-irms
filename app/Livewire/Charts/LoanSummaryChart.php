<?php

namespace App\Livewire\Charts;

use Asantibanez\LivewireCharts\Models\PieChartModel;
use Livewire\Component;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\DB;

class LoanSummaryChart extends Component
{
  //public $chartModel;

  public function mount()
  {
    $stats = LoanApplication::query()
      ->select('status', DB::raw('count(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status')
      ->toArray();

    $labels = [
      LoanApplication::STATUS_PENDING_SUPPORT => __('dashboard.status_pending_support'),
      LoanApplication::STATUS_APPROVED => __('dashboard.status_approved'),
      LoanApplication::STATUS_ISSUED => __('dashboard.status_on_loan'),
      LoanApplication::STATUS_RETURNED => __('dashboard.status_returned'),
      LoanApplication::STATUS_REJECTED => __('dashboard.status_rejected'),
    ];

    $colors = [
      LoanApplication::STATUS_PENDING_SUPPORT => '#ffc107',
      LoanApplication::STATUS_APPROVED => '#0d6efd',
      LoanApplication::STATUS_ISSUED => '#0dcaf0',
      LoanApplication::STATUS_RETURNED => '#198754',
      LoanApplication::STATUS_REJECTED => '#dc3545',
    ];

    $pie = (new PieChartModel())
      ->setTitle(__('dashboard.loan_stats_title'));

    foreach ($stats as $status => $count) {
      if (isset($labels[$status])) {
        $pie->addSlice(
          $labels[$status],
          $count,
          $colors[$status]
        );
      }
    }

    //$this->chartModel = $pie;
  }

  public function getChartModelProperty()
  {
    $stats = LoanApplication::query()
      ->select('status', DB::raw('count(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status')
      ->toArray();

    $labels = [
      LoanApplication::STATUS_PENDING_SUPPORT => __('dashboard.status_pending_support'),
      LoanApplication::STATUS_APPROVED => __('dashboard.status_approved'),
      LoanApplication::STATUS_ISSUED => __('dashboard.status_on_loan'),
      LoanApplication::STATUS_RETURNED => __('dashboard.status_returned'),
      LoanApplication::STATUS_REJECTED => __('dashboard.status_rejected'),
    ];

    $colors = [
      LoanApplication::STATUS_PENDING_SUPPORT => '#ffc107',
      LoanApplication::STATUS_APPROVED => '#0d6efd',
      LoanApplication::STATUS_ISSUED => '#0dcaf0',
      LoanApplication::STATUS_RETURNED => '#198754',
      LoanApplication::STATUS_REJECTED => '#dc3545',
    ];

    $pie = (new PieChartModel())
      ->setTitle(__('dashboard.loan_stats_title'));

    foreach ($stats as $status => $count) {
      if (isset($labels[$status])) {
        $pie->addSlice(
          $labels[$status],
          $count,
          $colors[$status]
        );
      }
    }

    return $pie;
  }


  public function render()
  {
    return view('livewire.charts.loan-summary-chart');
  }
}
