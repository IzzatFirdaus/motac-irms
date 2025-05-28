{{-- resources/views/livewire/dashboard/dashboard.blade.php --}}
<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses(); // Get theme config
    @endphp

    {{-- Page title is handled by #[Title] in the Livewire component --}}

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

    {{-- CORRECTED PATH: Removed 'layouts/' from the include path --}}
    @include('_partials._alerts.alert-general')

    <div class="row match-height g-4">
        <div class="col-xl-5 col-lg-6 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h4 class="card-title mb-1">{{ __('Hai,') }} {{ $displayUserName }}! 👋</h4>
                    <small class="text-muted">{{ __('Mulakan hari anda dengan semakan ringkas di sini.') }}</small>
                </div>
                <div class="d-flex align-items-end row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 id="date" class="text-primary mt-2 mb-1 small fw-semibold"></h5>
                            <h5 id="time" class="text-primary mb-3 small fw-semibold"></h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-bolt ti-xs me-1"></i>{{ __('Tindakan Pantas') }}
                                </button>
                                <ul class="dropdown-menu">
                                    @can('create', App\Models\LoanApplication::class)
                                    <li><a class="dropdown-item" href="{{ route('loan-applications.create') }}"><i class="ti ti-device-laptop ti-xs me-2"></i>{{ __('Mohon Pinjaman ICT') }}</a></li>
                                    @endcan
                                    @can('create', App\Models\EmailApplication::class)
                                    <li><a class="dropdown-item" href="{{ route('email-applications.create') }}"><i class="ti ti-mail-forward ti-xs me-2"></i>{{ __('Mohon E-mel/ID') }}</a></li>
                                    @endcan
                                    @if(!Auth::user()?->can('create', App\Models\LoanApplication::class) && !Auth::user()?->can('create', App\Models\EmailApplication::class))
                                       <li><span class="dropdown-item text-muted small">{{ __('Tiada tindakan pantas tersedia.') }}</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left h-100 d-flex align-items-end">
                        <div class="card-body pb-0 px-0 px-md-4 w-100">
                            <img src="{{ asset('assets/img/illustrations/motac_dashboard_hero.svg') }}"
                                 class="img-fluid quick-action-img" alt="{{ __('Ilustrasi Ruang Kerja MOTAC') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Ringkasan Permohonan Anda') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3 text-center">
                        <div class="col-md-4 col-6">
                            <div class="d-flex flex-column align-items-center">
                                <div class="badge rounded-pill bg-label-warning p-2 mb-2"><i class="ti ti-device-laptop icon-stat"></i></div>
                                <h5 class="mb-0 display-6">{{ $pendingUserLoanApplicationsCount }}</h5>
                                <small>{{ __('Pinjaman ICT Menunggu') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex flex-column align-items-center">
                                <div class="badge rounded-pill bg-label-info p-2 mb-2"><i class="ti ti-transform icon-stat"></i></div>
                                <h5 class="mb-0 display-6">{{ $activeUserLoansCount }}</h5>
                                <small>{{ __('Pinjaman ICT Aktif') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 mt-3 mt-md-0">
                            <div class="d-flex flex-column align-items-center">
                                <div class="badge rounded-pill bg-label-danger p-2 mb-2"><i class="ti ti-mail icon-stat"></i></div>
                                <h5 class="mb-0 display-6">{{ $pendingUserEmailApplicationsCount }}</h5>
                                <small>{{ __('E-mel/ID Menunggu') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="card-title m-0 me-2">{{ __('Permohonan Pinjaman ICT Terkini') }}</h5>
                    <a href="{{ route('loan-applications.index') }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-list-details ti-xs me-1"></i>{{ __('Lihat Semua') }}</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead><tr><th>{{__('ID')}}</th><th>{{__('Tujuan')}}</th><th>{{__('Status')}}</th><th>{{__('Tarikh Mohon')}}</th></tr></thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($userRecentLoanApplications as $loanApp)
                                <tr>
                                    <td class="td-link"><a href="{{ route('loan-applications.show', $loanApp->id) }}"><strong>#{{ $loanApp->id }}</strong></a></td>
                                    <td>{{ Str::limit($loanApp->purpose, 30) }}</td>
                                    <td><span class="badge {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($loanApp->status) }}">{{ __(Str::title(str_replace('_', ' ', $loanApp->status))) }}</span></td>
                                    <td>{{ ($loanApp->submitted_at ?? $loanApp->created_at)->translatedFormat(config('app.date_format_my_short', 'd M Y')) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted small">{{ __('Tiada permohonan pinjaman ICT terkini.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h5 class="card-title m-0 me-2">{{ __('Permohonan E-mel/ID Terkini') }}</h5>
                    <a href="{{ route('email-applications.index') }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-list-details ti-xs me-1"></i>{{ __('Lihat Semua') }}</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead><tr><th>{{__('ID')}}</th><th>{{__('E-mel Dicadang')}}</th><th>{{__('Status')}}</th><th>{{__('Tarikh Mohon')}}</th></tr></thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($userRecentEmailApplications as $emailApp)
                                <tr>
                                    <td class="td-link"><a href="{{ route('email-applications.show', $emailApp->id) }}"><strong>#{{ $emailApp->id }}</strong></a></td>
                                    <td>{{ $emailApp->proposed_email ?? __('N/A') }}</td>
                                    <td><span class="badge {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($emailApp->status) }}">{{ __(Str::title(str_replace('_', ' ', $emailApp->status))) }}</span></td>
                                    <td>{{ $emailApp->created_at->translatedFormat(config('app.date_format_my_short', 'd M Y')) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted small">{{ __('Tiada permohonan e-mel/ID terkini.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('page-script')
        <script>
            function updateClock() {
                const now = new Date();
                const currentAppLocale = document.documentElement.lang || 'ms';
                let dateLocale = 'ms-MY';
                if (currentAppLocale.startsWith('en')) { dateLocale = 'en-GB'; }
                else if (currentAppLocale.startsWith('ar')) { dateLocale = 'ar-SA'; }

                const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };

                const dateEl = document.getElementById('date');
                if (dateEl) {
                    try { dateEl.innerHTML = now.toLocaleDateString(dateLocale, dateOptions); }
                    catch (e) { dateEl.innerHTML = now.toDateString(); }
                }
                const timeEl = document.getElementById('time');
                if (timeEl) { timeEl.innerHTML = now.toLocaleTimeString('en-GB', timeOptions); }
            }
            if (document.getElementById('date') && document.getElementById('time')) {
                setInterval(updateClock, 1000);
                updateClock();
            }
        </script>
    @endpush
</div>
