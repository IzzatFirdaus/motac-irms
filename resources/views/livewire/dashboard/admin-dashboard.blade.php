<div>
    @push('page-style')
        {{-- Custom styles for the quick action links --}}
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

    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">{{ __('dashboard.admin_title') }}</h1>
        </div>

        {{-- Main Statistics Row --}}
        <div class="row">
            {{-- Total Users Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-primary border-4 shadow-sm h-100 py-2 motac-card">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">{{ __('dashboard.total_users') }}</div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $users_count ?? '0' }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-people-fill fs-2 text-gray-300"></i></div>
                        </div>
                        @if (Route::has('settings.users.index'))
                            <a href="{{ route('settings.users.index') }}" class="stretched-link" title="{{ __('dashboard.manage_users') }}"></a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pending Approvals Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-warning border-4 shadow-sm h-100 py-2 motac-card">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">{{ __('dashboard.pending_approvals') }}</div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $pending_approvals_count ?? '0' }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-hourglass-split fs-2 text-gray-300"></i></div>
                        </div>
                        @if (Route::has('approvals.dashboard'))
                            <a href="{{ route('approvals.dashboard') }}" class="stretched-link" title="{{ __('dashboard.view_all_tasks') }}"></a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Available Equipment Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-info border-4 shadow-sm h-100 py-2 motac-card">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">{{ __('dashboard.available_equipment') }}</div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_available_count ?? '0' }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-box-seam-fill fs-2 text-gray-300"></i></div>
                        </div>
                        @if (Route::has('admin.equipment.index'))
                            <a href="{{ route('admin.equipment.index') }}" class="stretched-link" title="{{ __('dashboard.manage_inventory') }}"></a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Loaned Equipment Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-success border-4 shadow-sm h-100 py-2 motac-card">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">{{ __('dashboard.loaned_equipment') }}</div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $equipment_on_loan_count ?? '0' }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-truck fs-2 text-gray-300"></i></div>
                        </div>
                        @if (Route::has('resource-management.bpm.issued-loans'))
                            <a href="{{ route('resource-management.bpm.issued-loans') }}" class="stretched-link" title="{{ __('dashboard.view_active_loans') }}"></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Quick Actions Row --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-lightning-charge-fill me-2"></i>{{ __('dashboard.quick_actions') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="{{ route('settings.users.index') }}" class="text-decoration-none d-block p-3 border rounded-3 motac-quick-link">
                                    <i class="bi bi-people-fill fs-2 text-primary"></i>
                                    <div class="mt-1 small fw-semibold">{{ __('dashboard.manage_users') }}</div>
                                </a>
                            </div>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="{{ route('admin.equipment.index') }}" class="text-decoration-none d-block p-3 border rounded-3 motac-quick-link">
                                    <i class="bi bi-box-seam-fill fs-2 text-info"></i>
                                    <div class="mt-1 small fw-semibold">{{ __('dashboard.manage_inventory') }}</div>
                                </a>
                            </div>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="{{ route('approvals.dashboard') }}" class="text-decoration-none d-block p-3 border rounded-3 motac-quick-link">
                                    <i class="bi bi-list-check fs-2 text-warning"></i>
                                    <div class="mt-1 small fw-semibold">{{ __('dashboard.view_all_tasks') }}</div>
                                </a>
                            </div>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="{{ route('reports.index') }}" class="text-decoration-none d-block p-3 border rounded-3 motac-quick-link">
                                    <i class="bi bi-file-earmark-bar-graph-fill fs-2 text-success"></i>
                                    <div class="mt-1 small fw-semibold">{{ __('dashboard.reports_title') }}</div>
                                </a>
                            </div>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="{{ route('settings.index') }}" class="text-decoration-none d-block p-3 border rounded-3 motac-quick-link">
                                    <i class="bi bi-gear-fill fs-2 text-secondary"></i>
                                    <div class="mt-1 small fw-semibold">{{ __('dashboard.settings_title') }}</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Application Statistics and Chart Row --}}
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm motac-card h-100">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-envelope-paper-fill me-2 text-primary"></i>
                        <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.email_stats_title') }}</h6>
                    </div>
                    <div class="card-body motac-card-body d-flex flex-column justify-content-center">
                        <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-check-circle-fill me-2 text-success"></i>{{ __('dashboard.completed') }}</span> <strong class="text-dark">{{ $email_completed_count ?? 0 }}</strong></p>
                        <p class="mb-2 d-flex justify-content-between"><span><i class="bi bi-hourglass-top me-2 text-warning"></i>{{ __('dashboard.in_process') }}</span> <strong class="text-dark">{{ $email_pending_count ?? 0 }}</strong></p>
                        <p class="mb-0 d-flex justify-content-between"><span><i class="bi bi-x-circle-fill me-2 text-danger"></i>{{ __('dashboard.rejected') }}</span> <strong class="text-dark">{{ $email_rejected_count ?? 0 }}</strong></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm motac-card h-100">
                    <div class="card-header py-3 motac-card-header d-flex align-items-center">
                        <i class="bi bi-pie-chart-fill me-2 text-primary"></i>
                        <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.loan_stats_title') }}</h6>
                    </div>
                    <div class="card-body motac-card-body">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="chart-container" style="position: relative; height:250px; width:100%">
                                    <canvas id="loanStatusChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <h6 class="text-center mb-3 fw-semibold">{{ __('dashboard.loan_summary') }}</h6>
                                <p class="mb-2 d-flex justify-content-between border-bottom pb-2"><span><i class="bi bi-truck me-2 text-info"></i>{{ __('dashboard.on_loan') }}</span> <strong class="text-dark">{{ $loan_issued_count ?? 0 }}</strong></p>
                                <p class="mb-2 d-flex justify-content-between border-bottom pb-2"><span><i class="bi bi-patch-check-fill me-2 text-primary"></i>{{ __('dashboard.approved_pending_issuance') }}</span> <strong class="text-dark">{{ $loan_approved_pending_issuance_count ?? 0 }}</strong></p>
                                <p class="mb-0 d-flex justify-content-between"><span><i class="bi bi-box-arrow-in-left me-2 text-success"></i>{{ __('dashboard.returned') }}</span> <strong class="text-dark">{{ $loan_returned_count ?? 0 }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity / Approval Tasks --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4 motac-card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between motac-card-header">
                        <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                            <i class="bi bi-list-check me-2"></i>{{ __('dashboard.latest_tasks_title') }}
                        </h6>
                        @if (Route::has('approvals.dashboard'))
                            <a href="{{route('approvals.dashboard')}}" class="btn btn-sm btn-outline-primary motac-btn-outline">{{__('dashboard.view_all_tasks')}}</a>
                        @endif
                    </div>
                    <div class="card-body motac-card-body p-0">
                        @livewire('resource-management.approval.dashboard', ['displayLimit' => 5, 'showFilters' => false])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page-script')
        {{-- Add Chart.js from a CDN and the script to render our chart --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:load', function () {
                // Data for the chart is injected from the Livewire component
                const loanChartData = @json($loan_status_chart_data);

                const ctx = document.getElementById('loanStatusChart');
                if (ctx && loanChartData.labels.length > 0) {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: loanChartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 15,
                                        boxWidth: 12,
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                title: {
                                    display: false,
                                    text: 'Loan Application Status'
                                }
                            },
                            cutout: '60%' // Creates the doughnut hole
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
