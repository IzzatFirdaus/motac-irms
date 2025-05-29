<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @push('page-style')
        <style>
            .match-height > [class*='col'] { display: flex; flex-direction: column; }
            .match-height > [class*='col'] > .card { flex: 1 1 auto; }
            .td-link a { color: inherit; text-decoration: none; }
            .td-link a:hover { color: var(--bs-primary); text-decoration: underline; }
            .icon-stat { font-size: 1.6rem; }
            .quick-action-img { max-height: 130px; object-fit: contain; margin-bottom: 0.5rem; }
            .card-header .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        </style>
    @endpush

    {{-- Alerts --}}
    @include('_partials._alerts.alert-general')

    {{-- Top Summary --}}
    <div class="row match-height g-4">
        {{-- Welcome & Quick Actions --}}
        <div class="col-xl-5 col-lg-6 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h4 class="card-title mb-1">{{ __('Hai,') }} {{ $displayUserName }}! 👋</h4>
                    <small class="text-muted">{{ __('Mulakan hari anda dengan semakan ringkas di sini.') }}</small>
                </div>
                <div class="d-flex align-items-end row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 id="date" class="text-primary mt-2 mb-1 small fw-semibold" role="status" aria-live="polite"></h5>
                            <h5 id="time" class="text-primary mb-3 small fw-semibold" role="status" aria-live="polite"></h5>

                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown">
                                    <i class="ti ti-bolt ti-xs me-1"></i>{{ __('Tindakan Pantas') }}
                                </button>
                                <ul class="dropdown-menu">
                                    @can('create', App\Models\LoanApplication::class)
                                        <li>
                                            {{-- MODIFIED ROUTE BELOW --}}
                                            <a class="dropdown-item" href="{{ route('resource-management.application-forms.loan.create') }}">
                                                <i class="ti ti-device-laptop ti-xs me-2"></i>{{ __('Mohon Pinjaman ICT') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('create', App\Models\EmailApplication::class)
                                        <li>
                                            {{-- MODIFIED ROUTE BELOW --}}
                                            <a class="dropdown-item" href="{{ route('resource-management.application-forms.email.create') }}">
                                                <i class="ti ti-mail-forward ti-xs me-2"></i>{{ __('Mohon E-mel/ID') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @unless(Auth::user()?->can('create', App\Models\LoanApplication::class) || Auth::user()?->can('create', App\Models\EmailApplication::class))
                                        <li><span class="dropdown-item text-muted small">{{ __('Tiada tindakan pantas tersedia.') }}</span></li>
                                    @endunless
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center d-flex align-items-end">
                        <div class="card-body pb-0 px-0 px-md-4 w-100">
                            <img src="{{ asset('assets/img/illustrations/motac_dashboard_hero.svg') }}"
                                 class="img-fluid quick-action-img"
                                 alt="{{ __('Ilustrasi Ruang Kerja MOTAC') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Summary Counts --}}
        <div class="col-xl-7 col-lg-6 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Ringkasan Permohonan Anda') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3 text-center">
                        @foreach ([
                            ['count' => $pendingUserLoanApplicationsCount, 'icon' => 'ti-device-laptop', 'label' => 'Pinjaman ICT Menunggu', 'color' => 'warning'],
                            ['count' => $activeUserLoansCount, 'icon' => 'ti-transform', 'label' => 'Pinjaman ICT Aktif', 'color' => 'info'],
                            ['count' => $pendingUserEmailApplicationsCount, 'icon' => 'ti-mail', 'label' => 'E-mel/ID Menunggu', 'color' => 'danger'],
                        ] as $item)
                            <div class="col-md-4 col-6">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="badge rounded-pill bg-label-{{ $item['color'] }} p-2 mb-2">
                                        <i class="ti {{ $item['icon'] }} icon-stat"></i>
                                    </div>
                                    <h5 class="mb-0 display-6">{{ $item['count'] }}</h5>
                                    <small>{{ __($item['label']) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Applications --}}
    <div class="row mt-4 g-4">
        @foreach ([
            [
                'title' => 'Permohonan Pinjaman ICT Terkini',
                'routeIndex' => 'loan-applications.index',
                'items' => $userRecentLoanApplications,
                'fields' => ['purpose', 'status', 'submitted_at'],
                'routeShow' => 'loan-applications.show',
                'columns' => [__('ID'), __('Tujuan'), __('Status'), __('Tarikh Mohon')],
                'empty' => __('Tiada permohonan pinjaman ICT terkini.')
            ],
            [
                'title' => 'Permohonan E-mel/ID Terkini',
                'routeIndex' => 'email-applications.index',
                'items' => $userRecentEmailApplications,
                'fields' => ['proposed_email', 'status', 'created_at'],
                'routeShow' => 'email-applications.show',
                'columns' => [__('ID'), __('E-mel Dicadang'), __('Status'), __('Tarikh Mohon')],
                'empty' => __('Tiada permohonan e-mel/ID terkini.')
            ]
        ] as $box)
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between py-2">
                        <h5 class="card-title m-0 me-2">{{ __($box['title']) }}</h5>
                        <a href="{{ route($box['routeIndex']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-list-details ti-xs me-1"></i>{{ __('Lihat Semua') }}
                        </a>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    @foreach ($box['columns'] as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($box['items'] as $item)
                                    <tr>
                                        <td class="td-link">
                                            <a href="{{ route($box['routeShow'], $item->id) }}"><strong>#{{ $item->id }}</strong></a>
                                        </td>
                                        <td>{{ Str::limit($item->{$box['fields'][0]} ?? __('N/A'), 30) }}</td>
                                        <td>
                                            <span class="badge {{ \App\Helpers\Helpers::getStatusColorClass($item->{$box['fields'][1]}) }}">
                                                {{ __(Str::title(str_replace('_', ' ', $item->{$box['fields'][1]}))) }}
                                            </span>
                                        </td>
                                        <td>{{ \App\Helpers\Helpers::formatDate($item->{$box['fields'][2]}) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted small">{{ $box['empty'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('page-script')
        <script>
            function updateClock() {
                const now = new Date();
                const locale = document.documentElement.lang || 'ms-MY'; // Default to 'ms-MY'
                const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }; // Use en-GB for 24hr format

                const dateEl = document.getElementById('date');
                const timeEl = document.getElementById('time');

                if (dateEl) {
                    try {
                        // Attempt to use the page's locale for date
                        dateEl.innerHTML = now.toLocaleDateString(locale, dateOptions);
                    } catch (e) {
                        // Fallback for browsers that might not support 'ms-MY' fully, or if locale is invalid
                        const fallbackDateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                        dateEl.innerHTML = now.toLocaleDateString('en-US', fallbackDateOptions); // A widely supported fallback
                    }
                }

                if (timeEl) {
                     // Using 'en-GB' is a common trick to get a 24-hour format like HH:MM:SS
                    timeEl.innerHTML = now.toLocaleTimeString('en-GB', timeOptions);
                }
            }

            if (document.getElementById('date') && document.getElementById('time')) {
                updateClock();
                setInterval(updateClock, 1000);
            }
        </script>
    @endpush
</div>
