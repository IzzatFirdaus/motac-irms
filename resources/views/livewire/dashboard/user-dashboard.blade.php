{{-- resources/views/livewire/dashboard/user-dashboard.blade.php --}}
<div>
    @push('page-style')
        <style>
            .quick-action-bs-icon {
                font-size: 2.5rem;
                display: block;
                margin-bottom: 0.75rem;
                line-height: 1;
            }

            .icon-stat {
                font-size: 1.5rem;
            }

            .table-hover tbody tr {
                cursor: pointer;
            }

            .card-title {
                font-weight: 600;
            }
        </style>
    @endpush

    <div class="container-fluid flex-grow-1 container-p-y">
        {{-- Welcome Header --}}
        <div class="d-flex align-items-center mb-4">
            <h1 class="h3 mb-0 me-auto fw-bold">{{ __('dashboard.welcome') }}, {{ $displayUserName }}!</h1>
            <div class="ms-auto d-flex flex-column align-items-end">
                <span id="motacDashboardDate" class="text-muted small"></span>
                <span id="motacDashboardTime" class="text-muted small"></span>
            </div>
        </div>

        {{-- Top Row: Quick Actions & Stats --}}
        <div class="row gy-4 mb-4">
            {{-- Quick Actions Card --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('dashboard.quick_shortcuts') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-sm-4 col-6 mb-3">
                                <a href="{{ route('loan-applications.create') }}" class="d-block text-decoration-none">
                                    <i class="bi bi-card-checklist text-primary quick-action-bs-icon"></i>
                                    <p class="text-muted mb-0 small">{{ __('dashboard.ict_loan') }}</p>
                                </a>
                            </div>
                            <div class="col-sm-4 col-6 mb-3">
                                <a href="{{ route('email-applications.create') }}" class="d-block text-decoration-none">
                                    <i class="bi bi-envelope-plus-fill text-info quick-action-bs-icon"></i>
                                    <p class="text-muted mb-0 small">{{ __('dashboard.email_application') }}</p>
                                </a>
                            </div>
                            <div class="col-sm-4 col-6 mb-3">
                                <a href="{{ route('notifications.index') }}" class="d-block text-decoration-none">
                                    <i class="bi bi-bell-fill text-warning quick-action-bs-icon"></i>
                                    <p class="text-muted mb-0 small">{{ __('dashboard.notifications') }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="col-lg-5">
                <div class="row g-4">
                    <div class="col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-laptop icon-stat text-primary me-2"></i>
                                    <h6 class="mb-0 small">{{ __('dashboard.ict_loans_in_process') }}</h6>
                                </div>
                                <h3 class="mb-2 text-primary">{{ $pendingUserLoanApplicationsCount }}</h3>
                                <a href="{{ route('loan-applications.index', ['status_filter_key' => 'pending_all']) }}"
                                    class="btn btn-sm btn-outline-primary mt-3">{{ __('common.view') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-envelope icon-stat text-info me-2"></i>
                                    <h6 class="mb-0 small">{{ __('dashboard.email_id_in_process') }}</h6>
                                </div>
                                <h3 class="mb-2 text-info">{{ $pendingUserEmailApplicationsCount }}</h3>
                                <a href="{{ route('email-applications.index', ['status_filter_key' => 'pending_all']) }}"
                                    class="btn btn-sm btn-outline-info mt-3">{{ __('common.view') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tables, etc. --}}
    </div>
    {{-- Scripts --}}
</div>
