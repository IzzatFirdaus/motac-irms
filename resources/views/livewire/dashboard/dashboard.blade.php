{{-- resources/views/livewire/dashboard/dashboard.blade.php --}}
<div>
    @section('title', $pageTitleValue) {{-- Use the public property from the component for the title --}}

    @php
        // $configData is usually available if layouts/app.blade.php sets it globally,
        // but good practice to ensure it's available if directly accessed by sub-components or includes.
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @push('page-style')
        {{-- Page-specific styles --}}
        <style>
            .match-height > [class*='col'] { display: flex; flex-direction: column; }
            .match-height > [class*='col'] > .card { flex: 1 1 auto; /* Ensure cards in a row take equal height */ }

            .td-link a { color: inherit; text-decoration: none; }
            .td-link a:hover { color: var(--motac-primary, #0055A4); text-decoration: underline; } /* Use MOTAC primary color var */

            .icon-stat { font-size: 1.6rem; /* Consistent icon size in stat badges */ }
            .quick-action-img { max-height: 130px; object-fit: contain; margin-bottom: 0.5rem; }

            .card-header .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; } /* Styling for small buttons in card headers */

            /* Ensuring Noto Sans is applied if not globally forced, and line height */
            .card-title, .card-header h5, .card-body small, .table th, .table td, .btn, .dropdown-item {
                font-family: 'Noto Sans', sans-serif; /* Design Language 2.2 */
            }
            body, .text-muted, p { /* General text considerations */
                line-height: 1.6; /* Design Language 2.2 */
            }
            .badge { /* Ensure badges also use Noto Sans */
                font-family: 'Noto Sans', sans-serif; /* Design Language 2.2 */
                font-weight: 500; /* Medium weight for labels as per Design Language 2.2 */
            }
        </style>
    @endpush

    {{-- Alerts: For displaying session flash messages (success, error, etc.) --}}
    @include('_partials._alerts.alert-general')

    {{-- Top Row: Welcome Message, Date/Time, Quick Actions, and Summary Counts --}}
    <div class="row match-height g-4 mb-4">
        {{-- Welcome & Quick Actions Card --}}
        <div class="col-xl-5 col-lg-6 col-md-6 col-12">
            <div class="card h-100 motac-card">
                <div class="card-header pb-0 motac-card-header">
                    <h4 class="card-title mb-1">{{ __('Hai,') }} {{ $displayUserName }}! 👋</h4>
                    <small class="text-muted">{{ __('Mulakan hari anda dengan semakan ringkas di sini.') }}</small>
                </div>
                <div class="d-flex align-items-end row h-100">
                    <div class="col-sm-7">
                        <div class="card-body motac-card-body">
                            {{-- Live Date and Time --}}
                            <h5 id="motacDashboardDate" class="text-primary mt-2 mb-1 small fw-semibold" role="status" aria-live="polite"></h5>
                            <h5 id="motacDashboardTime" class="text-primary mb-3 small fw-semibold" role="status" aria-live="polite"></h5>

                            {{-- Quick Actions Dropdown --}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle motac-btn-primary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-lightning-charge fs-6 me-1"></i>{{ __('Tindakan Pantas') }}
                                </button>
                                <ul class="dropdown-menu">
                                    @can('create', App\Models\LoanApplication::class)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('loan-applications.create') }}">
                                                <i class="bi bi-laptop fs-6 me-2"></i>{{ __('Mohon Pinjaman ICT') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('create', App\Models\EmailApplication::class)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('email-applications.create') }}">
                                                <i class="bi bi-envelope-plus fs-6 me-2"></i>{{ __('Mohon E-mel/ID') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @if(!Auth::user()?->can('create', App\Models\LoanApplication::class) && !Auth::user()?->can('create', App\Models\EmailApplication::class))
                                        <li><span class="dropdown-item text-muted small">{{ __('Tiada tindakan pantas tersedia.') }}</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center d-flex align-items-end">
                        <div class="card-body pb-0 px-0 px-md-4 w-100 motac-card-body">
                            <img src="{{ asset('assets/img/illustrations/motac_dashboard_hero.svg') }}"
                                 class="img-fluid quick-action-img"
                                 alt="{{ __('Ilustrasi papan pemuka untuk Sistem MOTAC') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Summary Counts Card --}}
        <div class="col-xl-7 col-lg-6 col-md-6 col-12">
            <div class="card h-100 motac-card">
                <div class="card-header motac-card-header">
                    <h5 class="card-title mb-0">{{ __('Ringkasan Permohonan Anda') }}</h5>
                </div>
                <div class="card-body motac-card-body">
                    <div class="row gy-3 text-center">
                        @php
                            $statItems = [
                                ['count' => $pendingUserLoanApplicationsCount, 'icon' => 'bi-laptop', 'labelKey' => 'Pinjaman ICT Menunggu', 'colorClass' => \App\Helpers\Helpers::getStatusColorClass('pending_support', 'loan'), 'route' => route('loan-applications.index', ['status' => 'pending_support'])],
                                ['count' => $activeUserLoansCount, 'icon' => 'bi-check2-all', 'labelKey' => 'Pinjaman ICT Aktif', 'colorClass' => \App\Helpers\Helpers::getStatusColorClass('issued', 'loan'), 'route' => route('loan-applications.index', ['status' => 'issued'])],
                                ['count' => $pendingUserEmailApplicationsCount, 'icon' => 'bi-envelope-exclamation', 'labelKey' => 'E-mel/ID Menunggu', 'colorClass' => \App\Helpers\Helpers::getStatusColorClass('pending_support', 'email'), 'route' => route('email-applications.index', ['status' => 'pending_support'])],
                            ];
                        @endphp
                        @foreach ($statItems as $item)
                            <div class="col-md-4 col-6">
                                <a href="{{ $item['route'] ?? '#' }}" class="text-decoration-none d-block">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="badge rounded-pill p-2 mb-2 {{ $item['colorClass'] }}">
                                            <i class="bi {{ $item['icon'] }} icon-stat"></i>
                                        </div>
                                        <h5 class="mb-0 display-6" aria-label="{{ $item['count'] }} {{ __($item['labelKey']) }}">{{ $item['count'] }}</h5>
                                        <small class="text-muted">{{ __($item['labelKey']) }}</small>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Applications Tables --}}
    <div class="row g-4">
        @php
            $applicationSections = [
                [
                    'titleKey' => 'Permohonan Pinjaman ICT Terkini',
                    'routeIndex' => 'loan-applications.index',
                    'items' => $userRecentLoanApplications,
                    'fields' => ['purpose', 'status', 'updated_at'], // Using 'updated_at' as per component logic
                    'routeShow' => 'loan-applications.show',
                    'columns' => [__('ID'), __('Tujuan Ringkas'), __('Status'), __('Tarikh Kemaskini')], // Changed label
                    'emptyMessageKey' => 'Tiada permohonan pinjaman ICT terkini.',
                    'itemType' => 'loan'
                ],
                [
                    'titleKey' => 'Permohonan E-mel/ID Terkini',
                    'routeIndex' => 'email-applications.index',
                    'items' => $userRecentEmailApplications,
                    'fields' => ['proposed_email', 'status', 'updated_at'], // Using 'updated_at' as per component logic
                    'routeShow' => 'email-applications.show',
                    'columns' => [__('ID'), __('E-mel Dicadang'), __('Status'), __('Tarikh Kemaskini')], // Changed label
                    'emptyMessageKey' => 'Tiada permohonan e-mel/ID terkini.',
                    'itemType' => 'email'
                ]
            ];
        @endphp

        @foreach ($applicationSections as $section)
            <div class="col-lg-6">
                <div class="card h-100 motac-card">
                    <div class="card-header d-flex align-items-center justify-content-between py-3 motac-card-header">
                        <h5 class="card-title m-0 me-2">{{ __($section['titleKey']) }}</h5>
                        @if(Route::has($section['routeIndex']))
                            <a href="{{ route($section['routeIndex']) }}" class="btn btn-sm btn-outline-primary motac-btn-outline">
                                <i class="bi bi-view-list fs-6 me-1"></i>{{ __('Lihat Semua') }}
                            </a>
                        @endif
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    @foreach ($section['columns'] as $column)
                                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($section['items'] as $item)
                                    <tr>
                                        <td class="px-3 py-2 td-link">
                                            @if(Route::has($section['routeShow']))
                                            <a href="{{ route($section['routeShow'], $item->id) }}"><strong>#{{ $item->id }}</strong></a>
                                            @else
                                            <strong>#{{ $item->id }}</strong>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small">{{ Str::limit($item->{$section['fields'][0]} ?? __('N/A'), 35) }}</td>
                                        <td class="px-3 py-2 small">
                                            <span class="badge {{ \App\Helpers\Helpers::getStatusColorClass($item->{$section['fields'][1]}, $section['itemType']) }}">
                                                {{ __(Str::title(str_replace('_', ' ', $item->{$section['fields'][1]}))) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ $item->{$section['fields'][2]} ? \App\Helpers\Helpers::formatDate($item->{$section['fields'][2]}) : __('N/A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($section['columns']) }}" class="text-center py-4 text-muted small">
                                            <i class="bi bi-folder-x fs-3 mb-2 d-block"></i>
                                            {{ __($section['emptyMessageKey']) }}
                                        </td>
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
            document.addEventListener('DOMContentLoaded', function () {
                function updateMotacDashboardClock() {
                    const now = new Date();
                    const pageLocale = document.documentElement.lang || 'ms';

                    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23' };

                    const dateEl = document.getElementById('motacDashboardDate');
                    const timeEl = document.getElementById('motacDashboardTime');

                    if (dateEl) {
                        try {
                            dateEl.textContent = now.toLocaleDateString(pageLocale + '-MY', dateOptions);
                        } catch (e) {
                            dateEl.textContent = now.toLocaleDateString('en-GB', dateOptions);
                            console.warn('Locale for date formatting (' + pageLocale + '-MY) might not be fully supported, using en-GB fallback.', e);
                        }
                    }

                    if (timeEl) {
                        try {
                            timeEl.textContent = now.toLocaleTimeString(pageLocale + '-MY', timeOptions);
                        } catch (e) {
                            timeEl.textContent = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
                            console.warn('Locale for time formatting (' + pageLocale + '-MY with hourCycle) might not be fully supported, using en-GB fallback.', e);
                        }
                    }
                }

                if (document.getElementById('motacDashboardDate') && document.getElementById('motacDashboardTime')) {
                    updateMotacDashboardClock();
                    setInterval(updateMotacDashboardClock, 1000);
                }
            });
        </script>
    @endpush
</div>
