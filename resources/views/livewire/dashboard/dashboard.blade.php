{{-- resources/views/livewire/dashboard/dashboard.blade.php --}}
<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @push('page-style')
        <style>
            .quick-action-bs-icon { font-size: 3.5rem; display: block; margin-bottom: 0.75rem; line-height: 1; }
            .icon-stat { font-size: 1.75rem; }
            .table-hover tbody tr { cursor: pointer; }
        </style>
    @endpush

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4 mb-4">
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <h4 class="mb-0 me-2">{{ __('dashboard.welcome_back') }} {{ $displayUserName }}!</h4>
                            <div class="ms-auto d-flex flex-column align-items-end">
                                <span id="motacDashboardDate" class="text-muted small"></span>
                                <span id="motacDashboardTime" class="text-muted small"></span>
                            </div>
                        </div>
                        <p class="mb-0 text-muted">{{ __('dashboard.productive_day') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-md-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('dashboard.quick_actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            @foreach ($quickActions as $action)
                                @php
                                    $canViewQuickAction = Auth::check() && Auth::user()->hasAnyRole((array) ($action['role'] ?? []));
                                @endphp
                                @if ($canViewQuickAction && Route::has($action['route']))
                                    <div class="col-6 col-sm-4 col-md-4 mb-3">
                                        <a href="{{ route($action['route']) }}" class="d-block text-decoration-none">
                                            <i class="{{ $action['icon'] }} quick-action-bs-icon"></i>
                                            <p class="text-muted mb-0 small">{{ __($action['name']) }}</p>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="row g-4">
                    <div class="col-md-6 col-sm-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-laptop icon-stat text-primary me-2"></i>
                                    <h6 class="mb-0 small">{{ __('dashboard.ict_loans_in_process') }}</h6>
                                </div>
                                <h3 class="mb-2 text-primary">{{ $pendingUserLoanApplicationsCount }}</h3>
                                <a href="{{ route('loan-applications.index', ['status_filter_key' => 'pending_all']) }}" class="btn btn-sm btn-outline-primary mt-3">{{ __('common.view') }}</a>
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
                                <a href="{{ route('email-applications.index', ['status_filter_key' => 'pending_all']) }}" class="btn btn-sm btn-outline-info mt-3">{{ __('common.view') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-folder-check icon-stat text-success me-2"></i>
                                    <h6 class="mb-0 small">{{ __('dashboard.active_ict_loans') }}</h6>
                                </div>
                                <h3 class="mb-2 text-success">{{ $activeUserLoansCount }}</h3>
                                <a href="{{ route('loan-applications.index', ['status_filter_key' => 'active_loans']) }}" class="btn btn-sm btn-outline-success mt-3">{{ __('common.manage') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('dashboard.recent_transactions') }}</h5>
                        @if (Auth::user()->hasAnyRole(['Admin', 'BPM Staff']) && Route::has('resource-management.bpm.loan-transactions.index'))
                            <a href="{{ route('resource-management.bpm.loan-transactions.index') }}" class="btn btn-primary btn-sm">{{ __('common.all') }} <i class="bi bi-arrow-right"></i></a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($latestLoanTransactions->isEmpty())
                            <p class="text-muted text-center">{{ __('dashboard.no_recent_transactions') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.date') }}</th>
                                            <th>{{ __('dashboard.subject') }}</th>
                                            <th>{{ __('dashboard.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestLoanTransactions as $transaction)
                                            @if (Route::has('resource-management.bpm.loan-transactions.show'))
                                                <tr onclick="window.location='{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}';">
                                                    <td>{{ \App\Helpers\Helpers::formatDate($transaction->transaction_date, 'date_my') }}</td>
                                                    <td>{{ Str::limit(optional($transaction->loanApplication)->purpose ?: $transaction->item_name, 35) }}</td>
                                                    <td><x-loan-transaction-status-badge :status="$transaction->status" /></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('dashboard.upcoming_returns') }}</h5>
                        @if (Route::has('loan-applications.index'))
                            <a href="{{ route('loan-applications.index', ['filter' => 'upcoming_returns']) }}" class="btn btn-primary btn-sm">{{ __('common.all') }} <i class="bi bi-arrow-right"></i></a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($upcomingReturns->isEmpty())
                            <p class="text-muted text-center">{{ __('dashboard.no_upcoming_returns') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.return_date') }}</th>
                                            <th>{{ __('dashboard.subject') }}</th>
                                            <th>{{ __('dashboard.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingReturns as $returnLoanApp)
                                            @if (Route::has('loan-applications.show'))
                                                <tr onclick="window.location='{{ route('loan-applications.show', $returnLoanApp->id) }}';">
                                                    <td>{{ \App\Helpers\Helpers::formatDate($returnLoanApp->loan_end_date, 'date_my', __('common.not_available')) }}</td>
                                                    {{-- THE FIX IS APPLIED ON THE NEXT LINE --}}
                                                    <td>{{ Str::limit($returnLoanApp->purpose, 35) }}</td>
                                                    <td><x-resource-status-panel :resource="$returnLoanApp" statusAttribute="status" type="loan_application" :showIcon="true" /></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('dashboard.your_recent_loan_apps') }}</h5>
                        @if (Route::has('loan-applications.index'))
                            <a href="{{ route('loan-applications.index') }}" class="btn btn-primary btn-sm">{{ __('common.see_all') }} <i class="bi bi-arrow-right"></i></a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($userRecentLoanApplications->isEmpty())
                            <p class="text-muted text-center">{{ __('dashboard.no_recent_loan_apps') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.apply_date') }}</th>
                                            <th>{{ __('dashboard.subject') }}</th>
                                            <th>{{ __('dashboard.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userRecentLoanApplications as $loanApplication)
                                            @if (Route::has('loan-applications.show'))
                                                <tr onclick="window.location='{{ route('loan-applications.show', $loanApplication->id) }}';">
                                                    <td>{{ \App\Helpers\Helpers::formatDate($loanApplication->created_at, 'date_my') }}</td>
                                                    <td>{{ Str::limit($loanApplication->purpose, 35) }}</td>
                                                    <td><x-resource-status-panel :resource="$loanApplication" statusAttribute="status" type="loan_application" :showIcon="true" /></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('dashboard.your_recent_email_apps') }}</h5>
                        @if (Route::has('email-applications.index'))
                            <a href="{{ route('email-applications.index') }}" class="btn btn-primary btn-sm">{{ __('common.see_all') }} <i class="bi bi-arrow-right"></i></a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($userRecentEmailApplications->isEmpty())
                            <p class="text-muted text-center">{{ __('dashboard.no_recent_email_apps') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.apply_date') }}</th>
                                            <th>{{ __('dashboard.type') }}</th>
                                            <th>{{ __('dashboard.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userRecentEmailApplications as $emailApplication)
                                            @if (Route::has('email-applications.show'))
                                                <tr onclick="window.location='{{ route('email-applications.show', $emailApplication->id) }}';">
                                                    <td>{{ \App\Helpers\Helpers::formatDate($emailApplication->created_at, 'date_my') }}</td>
                                                    <td>{{ $emailApplication->application_type_label }}</td>
                                                    <td><x-resource-status-panel :resource="$emailApplication" statusAttribute="status" type="email_application" :showIcon="true" /></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page-script')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const pageLocale = @json(App::getLocale());
                function updateMotacDashboardClock() {
                    const now = new Date();
                    const displayLocale = pageLocale === 'ms' ? 'ms-MY' : 'en-GB';
                    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                    const dateEl = document.getElementById('motacDashboardDate');
                    const timeEl = document.getElementById('motacDashboardTime');
                    if(dateEl) dateEl.textContent = now.toLocaleDateString(displayLocale, dateOptions);
                    if(timeEl) timeEl.textContent = now.toLocaleTimeString(displayLocale, timeOptions);
                }
                if (document.getElementById('motacDashboardDate') && document.getElementById('motacDashboardTime')) {
                    updateMotacDashboardClock();
                    setInterval(updateMotacDashboardClock, 1000);
                }
            });
        </script>
    @endpush
</div>
