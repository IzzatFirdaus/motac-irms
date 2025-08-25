{{-- resources/views/livewire/dashboard/admin-dashboard.blade.php --}}
{{-- Admin Dashboard for MOTAC IRMS --}}

@push('page-style')
<style>
    .motac-quick-link {
        transition: all 0.2s ease-in-out;
        background-color: var(--bs-tertiary-bg);
    }
    .motac-quick-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        background-color: var(--bs-body-bg);
    }
</style>
@endpush

<div>
    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-24">
            <h1 class="heading-medium fw-semibold text-black-900 mb-0">{{ __('dashboard.admin_title') }}</h1>
        </div>

        {{-- Stat Cards --}}
        <div class="row">
            @include('_partials.stat-card', [
                'label' => __('dashboard.total_users'),
                'value' => $users_count,
                'icon' => 'bi-people-fill',
                'color' => 'primary-500',
                'link' => route('settings.users.index'),
            ])
            @include('_partials.stat-card', [
                'label' => __('dashboard.pending_approvals'),
                'value' => $pending_approvals_count,
                'icon' => 'bi-hourglass-split',
                'color' => 'warning-500',
                'link' => route('approvals.dashboard'),
            ])
            @include('_partials.stat-card', [
                'label' => __('dashboard.available_equipment'),
                'value' => $equipment_available_count,
                'icon' => 'bi-box-seam-fill',
                'color' => 'info-500',
                'link' => route('admin.equipment.index'),
            ])
            @include('_partials.stat-card', [
                'label' => __('dashboard.loaned_equipment'),
                'value' => $equipment_on_loan_count,
                'icon' => 'bi-truck',
                'color' => 'success-500',
                'link' => route('admin.equipment.issued-loans'),
            ])
        </div>

        <div class="row">
            @include('_partials.stat-card', [
                'label' => __('dashboard.active_loans'),
                'value' => $total_active_loans_count,
                'icon' => 'bi-journal-text',
                'color' => 'primary-500',
                'link' => route('reports.loan-status-summary'),
            ])
            @include('_partials.stat-card', [
                'label' => __('dashboard.overdue_loans'),
                'value' => $overdue_loans_count,
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => 'danger-500',
                'link' => route('reports.loan-status-summary'),
            ])
            @include('_partials.stat-card', [
                'label' => __('dashboard.utilization_rate'),
                'value' => $equipment_utilization_rate . '%',
                'icon' => 'bi-graph-up',
                'color' => 'secondary-500',
                'link' => route('reports.utilization-report'),
            ])
        </div>

        {{-- Equipment Status + Loan Chart --}}
        <div class="row">
            <div class="col-lg-4 mb-24 py-16">
                <div class="card shadow-sm motac-card h-100">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-boxes me-2 text-primary"></i>
                        <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.equipment_status_summary') }}</h6>
                    </div>
                    <div class="card-body motac-card-body">
                        @foreach ($equipment_status_summary as $status => $count)
                            <p class="mb-2 d-flex justify-content-between border-bottom pb-2">
                                <span>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                <strong class="text-dark">{{ $count }}</strong>
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-24 py-16">
                <div class="card shadow-sm motac-card h-100">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-pie-chart-fill me-2 text-primary"></i>
                        <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.loan_stats_title') }}</h6>
                </div>
                <div class="card-body motac-card-body">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            @if (!empty($loan_status_chart_data['labels']))
                                <canvas id="loanStatusChart" height="260"></canvas>
                            @else
                                <div class="text-muted text-center py-5">
                                    <i class="bi bi-graph-up fs-1 mb-2"></i>
                                    <p class="mb-0">{{ __('dashboard.no_loan_data_available') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <h6 class="text-center mb-3 fw-semibold">{{ __('dashboard.loan_summary') }}</h6>
                            <p class="mb-2 d-flex justify-content-between border-bottom pb-2">
                                <span><i class="bi bi-truck me-2 text-info"></i>{{ __('dashboard.on_loan') }}</span>
                                <strong class="text-dark">{{ $loan_issued_count ?? 0 }}</strong>
                            </p>
                            <p class="mb-2 d-flex justify-content-between border-bottom pb-2">
                                <span><i class="bi bi-patch-check-fill me-2 text-primary"></i>{{ __('dashboard.approved_pending_issuance') }}</span>
                                <strong class="text-dark">{{ $loan_approved_pending_issuance_count ?? 0 }}</strong>
                            </p>
                            <p class="mb-0 d-flex justify-content-between">
                                <span><i class="bi bi-box-arrow-in-left me-2 text-success"></i>{{ __('dashboard.returned') }}</span>
                                <strong class="text-dark">{{ $loan_returned_count ?? 0 }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Tasks --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-24 motac-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-list-check me-2"></i>{{ __('dashboard.latest_tasks_title') }}
                    </h6>
                    @if (Route::has('approvals.dashboard'))
                        <a href="{{ route('approvals.dashboard') }}" class="btn btn-sm btn-outline-primary motac-btn-outline">{{ __('dashboard.view_all_tasks') }}</a>
                    @endif
                </div>
                <div class="card-body motac-card-body p-0">
                    {{-- Livewire component for approval dashboard (latest tasks, limit 5, hide filters) --}}
                    @livewire('resource-management.approval.approval-dashboard', ['displayLimit' => 5, 'showFilters' => false])
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Render loan status chart using Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($loan_status_chart_data);
        const ctx = document.getElementById('loanStatusChart');
        if (ctx && data.labels.length > 0) {
            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 15,
                                boxWidth: 12,
                                font: { size: 14 }
                            }
                        },
                        title: { display: false }
                    },
                    cutout: '60%'
                }
            });
        }
    });
</script>
@endpush
