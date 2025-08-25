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
                        <h5 class="card-title">{{ __('dashboard.quick_actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            {{-- Quick action for ICT Equipment Loan Application --}}
                            @if (Route::has('loan.applications.create'))
                                <div class="col-4 mb-3">
                                    <a href="{{ route('loan.applications.create') }}" class="d-block text-decoration-none text-dark">
                                        <i class="bi bi-laptop quick-action-bs-icon text-primary"></i>
                                        <span class="d-block">{{ __('dashboard.apply_for_loan') }}</span>
                                    </a>
                                </div>
                            @endif
                            {{-- Quick action for viewing My Loan Applications --}}
                            @if (Route::has('loan.applications.index'))
                                <div class="col-4 mb-3">
                                    <a href="{{ route('loan.applications.index') }}" class="d-block text-decoration-none text-dark">
                                        <i class="bi bi-clipboard-check quick-action-bs-icon text-success"></i>
                                        <span class="d-block">{{ __('dashboard.my_loan_applications') }}</span>
                                    </a>
                                </div>
                            @endif
                            {{-- NEW: Quick action for submitting a Helpdesk Ticket --}}
                            @if (Route::has('helpdesk.create'))
                                <div class="col-4 mb-3">
                                    <a href="{{ route('helpdesk.create') }}" class="d-block text-decoration-none text-dark">
                                        <i class="bi bi-life-preserver quick-action-bs-icon text-info"></i>
                                        <span class="d-block">{{ __('dashboard.submit_helpdesk_ticket') }}</span>
                                    </a>
                                </div>
                            @endif
                            {{-- NEW: Quick action for viewing My Helpdesk Tickets --}}
                            @if (Route::has('helpdesk.index'))
                                <div class="col-4 mb-3">
                                    <a href="{{ route('helpdesk.index') }}" class="d-block text-decoration-none text-dark">
                                        <i class="bi bi-ticket-detailed quick-action-bs-icon text-secondary"></i>
                                        <span class="d-block">{{ __('dashboard.my_helpdesk_tickets') }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics Card (Loan Stats) --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('dashboard.my_loan_stats') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ __('dashboard.pending_loans') }}
                                <span class="badge bg-warning icon-stat">{{ $pending_loans_count }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ __('dashboard.approved_loans') }}
                                <span class="badge bg-success icon-stat">{{ $approved_loans_count }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ __('dashboard.rejected_loans') }}
                                <span class="badge bg-danger icon-stat">{{ $rejected_loans_count }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ __('dashboard.total_loans') }}
                                <span class="badge bg-primary icon-stat">{{ $total_loans_count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Applications Section --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">{{ __('dashboard.recent_loan_applications') }}</h6>
                        @if (Route::has('loan.applications.index'))
                            <a href="{{ route('loan.applications.index') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.view_all_loan_applications') }}</a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if ($recent_applications->isEmpty())
                            <div class="alert alert-info text-center m-3">{{ __('dashboard.no_recent_loan_applications') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('common.application_no') }}</th>
                                            <th>{{ __('common.item_name') }}</th>
                                            <th>{{ __('common.loan_purpose') }}</th>
                                            <th>{{ __('common.status') }}</th>
                                            <th>{{ __('common.applied_on') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recent_applications as $application)
                                            <tr onclick="window.location='{{ route('loan.applications.show', $application->id) }}'">
                                                <td>{{ $application->application_number }}</td>
                                                <td>{{ $application->equipment->name ?? 'N/A' }}</td>
                                                <td>{{ $application->purpose }}</td>
                                                <td>
                                                    @php
                                                        $badgeClass = '';
                                                        switch ($application->status) {
                                                            case 'pending': $badgeClass = 'bg-warning'; break;
                                                            case 'approved': $badgeClass = 'bg-success'; break;
                                                            case 'rejected': $badgeClass = 'bg-danger'; break;
                                                            case 'issued': $badgeClass = 'bg-info'; break;
                                                            case 'returned': $badgeClass = 'bg-primary'; break;
                                                            default: $badgeClass = 'bg-secondary'; break;
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                                                </td>
                                                <td>{{ $application->created_at->format('d M Y') }}</td>
                                            </tr>
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
            document.addEventListener('DOMContentLoaded', () => {
                function updateMotacDashboardClock() {
                    const now = new Date();
                    const pageLocale = @json(app()->getLocale());
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
