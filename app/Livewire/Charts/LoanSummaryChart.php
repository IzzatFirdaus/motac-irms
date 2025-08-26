<?php

namespace App\Livewire\Charts;

use App\Models\LoanApplication;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * LoanSummaryChart.
 *
 * Pie chart component to display summary statistics for loan applications.
 * Updated for v4.0: No EmailApplication or legacy status references remain.
 * Only current LoanApplication statuses are represented.
 */
class LoanSummaryChart extends Component
{
    /**
     * Mount method for initializing the component.
     * Not strictly necessary as we use the computed property below, but kept for reference.
     */
    public function mount()
    {
        // NO state is set here; chart data is handled via the chartModel property below.
    }

    /**
     * Computed property to generate the PieChartModel for the summary chart.
     * This is referenced in the Livewire view with $this->chartModel.
     *
     * @return PieChartModel
     */
    public function getChartModelProperty()
    {
        // Query and group loan applications by status
        $stats = LoanApplication::query()
            ->select(DB::raw('status, count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Define labels for the loan statuses (no legacy or email statuses)
        $labels = [
            LoanApplication::STATUS_PENDING_SUPPORT => __('dashboard.status_pending_support'),
            LoanApplication::STATUS_APPROVED        => __('dashboard.status_approved'),
            LoanApplication::STATUS_ISSUED          => __('dashboard.status_on_loan'),
            LoanApplication::STATUS_RETURNED        => __('dashboard.status_returned'),
            LoanApplication::STATUS_REJECTED        => __('dashboard.status_rejected'),
        ];

        // Define colors for each status slice
        $colors = [
            LoanApplication::STATUS_PENDING_SUPPORT => '#ffc107', // Amber
            LoanApplication::STATUS_APPROVED        => '#0d6efd', // Blue
            LoanApplication::STATUS_ISSUED          => '#0dcaf0', // Cyan
            LoanApplication::STATUS_RETURNED        => '#198754', // Green
            LoanApplication::STATUS_REJECTED        => '#dc3545', // Red
        ];

        // Create and build the pie chart
        $pie = (new PieChartModel)
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

    /**
     * Render the Livewire view for this component.
     */
    public function render()
    {
        // The view can access $this->chartModel (calls getChartModelProperty)
        return view('livewire.charts.loan-summary-chart');
    }
}
