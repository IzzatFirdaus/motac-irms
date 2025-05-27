<div>
    {{--
        Title is now set dynamically by the #[Title] attribute
        in the App\Livewire\Dashboard component's pageTitle() method.
    --}}

    @section('page-style')
        {{-- Styles specific to this dashboard page --}}
        <style>
            .match-height > [class*='col'] {
                display: flex;
                flex-flow: column;
            }

            .match-height > [class*='col'] > .card {
                flex: 1 1 auto;
            }

            /* Styling for hover effects on table rows (optional) */
            .table-hover tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.03); /* Subtle hover effect */
            }
            .td-link { /* Class for table cells that act as links */
                cursor: pointer;
            }
            .td-link:hover {
                color: {{ $configData['primaryColor'] ?? '#7367f0' }}; /* Use theme's primary color if available */
                text-decoration: underline;
            }
        </style>
    @endsection

    {{-- Include general alerts partial - System Design 6.3 --}}
    @include('_partials._alerts.alert-general')

    <div class="row match-height">
        {{-- Greeting Card --}}
        <div class="col-xl-4 mb-4 col-lg-5 col-12">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h4 class="card-title mb-1">{{ __('Hi,') }} {{ $displayUserName ?? __('User') }}! 👋</h4>
                    <small class="text-muted">{{ __('Start your day with a smile') }}</small>
                </div>

                <div class="d-flex align-items-end row h-100">
                    <div class="col-7">
                        <div class="card-body text-nowrap">
                            <h5 id="date" class="text-primary mt-3 mb-1"></h5> {{-- JS Clock - Generic --}}
                            <h5 id="time" class="text-primary mb-2"></h5> {{-- JS Clock - Generic --}}

                            {{-- Quick Actions Dropdown - User Dashboard "quick access to form submissions" --}}
                            <div class="btn-group dropend">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-menu-2 ti-xs me-1"></i>{{ __('Actions') }}
                                </button>
                                <ul class="dropdown-menu">
                                    @can('create loan_applications') {{-- Permission based on LoanApplicationPolicy create method --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('resource-management.application-forms.loan.create') }}">
                                            <i class="ti ti-briefcase ti-xs me-1"></i>{{ __('New ICT Loan Request') }}
                                        </a>
                                    </li>
                                    @endcan
                                    @can('create email_applications') {{-- Permission based on EmailApplicationPolicy create method --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('resource-management.application-forms.email.create') }}">
                                            <i class="ti ti-mail ti-xs me-1"></i>{{ __('New Email/ID Request') }}
                                        </a>
                                    </li>
                                    @endcan

                                    {{-- Check if any actionable links were rendered --}}
                                    @if (!Auth::user()->can('create loan_applications') && !Auth::user()->can('create email_applications'))
                                        <li><small class="dropdown-item text-muted">{{ __('No quick actions available') }}</small></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 text-center text-sm-left h-100 d-flex align-items-end">
                        <div class="card-body pb-0 px-0 px-md-4 w-100">
                            {{-- Ensure this asset exists and is MOTAC branded or a suitable generic image --}}
                            <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" class="img-fluid"
                                alt="{{ config('app.name') }} {{ __('Illustration') }}"
                                style="object-fit: contain; width: 100%; height: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MOTAC System Statistics Section - User Dashboard "application statuses" --}}
        <div class="col-xl-8 mb-4 col-lg-7 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Your Application Overview') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        {{-- ICT Loan Stats --}}
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-warning me-3 p-2"><i class="ti ti-briefcase ti-sm"></i></div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $pendingLoanRequestsCount ?? 0 }}</h5>
                                    <small>{{ __('Pending ICT Loans') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-info me-3 p-2"><i class="ti ti-checks ti-sm"></i></div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $activeLoansCount ?? 0 }}</h5>
                                    <small>{{ __('Active ICT Loans') }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Email/ID Stats --}}
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-danger me-3 p-2"><i class="ti ti-mail-forward ti-sm"></i></div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $pendingEmailRequestsCount ?? 0 }}</h5>
                                    <small>{{ __('Pending Email/ID Requests') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section for Recent Loan Applications & Email Applications --}}
    <div class="row mt-4">
        {{-- Recent ICT Loan Applications --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <h5 class="card-header">{{ __('Recent ICT Loan Requests') }}</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Purpose') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Submitted Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($userRecentLoanApplications as $loanApp)
                                <tr>
                                    <td class="td-link">
                                        <a href="{{ route('resource-management.my-applications.loan-applications.show', $loanApp->id) }}">
                                            <strong>#{{ $loanApp->id }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($loanApp->purpose, 35) }}</td>
                                    <td>
                                        {{-- Using Helper for status class - System Design 6.3 --}}
                                        <span class="badge {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($loanApp->status) }}">
                                            {{ $loanApp->statusTranslated ?? __(Str::title(str_replace('_', ' ', $loanApp->status))) }}
                                        </span>
                                    </td>
                                    {{-- Using submitted_at for "Submitted Date" or created_at if submission date isn't always set --}}
                                    <td>{{ $loanApp->submitted_at ? $loanApp->submitted_at->translatedFormat(config('app.date_format_my', 'd/m/Y')) : $loanApp->created_at->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        {{ __('No recent ICT loan requests found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Email/ID Applications --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <h5 class="card-header">{{ __('Recent Email/ID Requests') }}</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Proposed Email') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Submitted Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($userRecentEmailApplications as $emailApp)
                                <tr>
                                    <td class="td-link">
                                        <a href="{{ route('resource-management.my-applications.email-applications.show', $emailApp->id) }}">
                                            <strong>#{{ $emailApp->id }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $emailApp->proposed_email ?? __('N/A') }}</td>
                                    <td>
                                        {{-- Using Helper for status class - System Design 6.3 --}}
                                        <span class="badge {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($emailApp->status) }}">
                                            {{ $emailApp->statusTranslated ?? __(Str::title(str_replace('_', ' ', $emailApp->status))) }}
                                        </span>
                                    </td>
                                    <td>{{ $emailApp->created_at->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}</td>
                                    {{-- Email apps might not have a separate 'submitted_at' if draft->pending is immediate upon full cert. --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        {{ __('No recent Email/ID requests found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Any HRMS specific modals or sections like "Today Leaves", "Changelog" are intentionally kept commented out / removed --}}

    @push('custom-scripts')
        <script>
            // JavaScript Clock
            function updateClock() {
                const now = new Date();
                // Using app.locale for date localization, ensure it's set correctly (e.g., 'ms-MY' for Bahasa Melayu)
                const currentAppLocale = document.documentElement.lang || 'en-US'; // Fallback to en-US
                let dateLocale = 'en-GB'; // Default to a common format day/month/year
                if (currentAppLocale.startsWith('ms') || currentAppLocale.startsWith('my')) { // 'my' is Laravel locale, 'ms' is common for JS
                    dateLocale = 'ms-MY';
                } else if (currentAppLocale.startsWith('ar')) {
                    dateLocale = 'ar-SA'; // Example Arabic locale for date
                }

                const dateOptions = {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                };
                const timeOptions = { // Keep time in 24hr for clarity unless specified otherwise
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                };

                try {
                    const formattedDate = now.toLocaleDateString(dateLocale, dateOptions);
                    const dateEl = document.getElementById('date');
                    if (dateEl) dateEl.innerHTML = formattedDate;
                } catch (e) {
                    console.error("Error formatting date with locale " + dateLocale + ": " + e.message);
                    // Fallback to simpler formatting if locale causes issues
                    const fallbackDate = now.getFullYear() + '-' + (now.getMonth() + 1).toString().padStart(2, '0') + '-' + now.getDate().toString().padStart(2, '0');
                    const dateEl = document.getElementById('date');
                    if (dateEl) dateEl.innerHTML = fallbackDate;
                }


                const formattedTime = now.toLocaleTimeString('en-US', timeOptions); // Time format is often kept universal
                const timeEl = document.getElementById('time');

                if (timeEl) timeEl.innerHTML = formattedTime;
            }

            if (document.getElementById('date') && document.getElementById('time')) {
                setInterval(updateClock, 1000);
                updateClock(); // Initial call
            }
        </script>
    @endpush
</div>
