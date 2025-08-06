{{--
    resources/views/livewire/charts/loan-summary-chart.blade.php
    Display a pie chart summarizing loan application statuses.
--}}

<div>
    {{-- Livewire Pie Chart for Loan Summary --}}
    <livewire:livewire-pie-chart
        :pie-chart-model="$this->chartModel"
        height="260"
        width="100%"
    />
</div>
