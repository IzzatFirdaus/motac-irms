{{-- resources/views/livewire/dashboard/dashboard.blade.php --}}
<div>
    @section('title', $pageTitleValue)

    @php
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @push('page-style')
        <style>
            /* ... existing styles ... */
            .quick-action-img { /* This class was for the old img tags, can be removed or repurposed */
                max-height: 100px; /* Adjusted from 130px if icons are smaller */
                object-fit: contain;
                margin-bottom: 0.5rem;
            }

            /* ADDED: Styles for Bootstrap Icons in Quick Actions */
            .quick-action-bs-icon {
                font-size: 3.5rem; /* Adjust size as needed */
                display: block; /* Or inline-block if preferred */
                margin-bottom: 0.75rem; /* Spacing below icon */
                line-height: 1; /* Ensure icon is vertically centered if it has extra space */
            }
            /* ... existing styles ... */
        </style>
    @endpush

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- ... Welcome Card ... --}}
        <div class="row gy-4 mb-4">
            <div class="col-lg-12">
                <div class="card h-100 motac-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <h4 class="mb-0 me-2 motac-quick-action-title">{{ __('Selamat Datang Kembali,') }}
                                {{ $displayUserName }}!</h4>
                            <div class="ms-auto d-flex flex-column align-items-end">
                                <span id="motacDashboardDate" class="text-muted small"></span>
                                <span id="motacDashboardTime" class="text-muted small"></span>
                            </div>
                        </div>
                        <p class="mb-0 text-muted">{{ __('Semoga hari anda berjalan lancar dan produktif.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Row --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 motac-card">
                    <div class="card-header motac-card-header">
                        <h5 class="card-title mb-0">{{ __('Tindakan Pantas') }}</h5>
                    </div>
                    <div class="card-body motac-card-body">
                        <div class="row text-center">
                            @foreach ($quickActions as $action)
                                @php
                                    $canView = true;
                                    if (isset($action['role']) && Auth::check()) {
                                        $roles = is_array($action['role']) ? $action['role'] : [$action['role']];
                                        $canView = false;
                                        foreach ($roles as $role) {
                                            if (Auth::user()->hasRole($role)) {
                                                $canView = true;
                                                break;
                                            }
                                        }
                                    } elseif (isset($action['role']) && !Auth::check()) {
                                        $canView = false;
                                    }
                                @endphp
                                @if ($canView)
                                    <div class="col-6 col-md-4 mb-3">
                                        <a href="{{ route($action['route']) }}" class="d-block text-decoration-none">
                                            {{-- MODIFIED: Changed from <img> to <i> for Bootstrap Icons --}}
                                            <i class="{{ $action['icon'] }} quick-action-bs-icon"></i>
                                            <p class="text-muted mb-0 motac-quick-action-title">
                                                {{ __($action['name']) }}</p>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ICT Loan Status & Email/ID Status Row --}}
            {{-- ... (rest of the file remains the same as your last provided version) ... --}}
            <div class="col-lg-6 col-md-12">
                <div class="row g-4">
                    {{-- ICT Loan Status Card --}}
                    <div class="col-md-6">
                        <div class="card h-100 motac-card">
                            <div class="card-body text-center motac-card-body">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-laptop icon-stat text-primary me-2"></i>
                                    <h6 class="mb-0 motac-stat-title">{{ __('Status Pinjaman ICT') }}</h6>
                                </div>
                                <h3 class="mb-2 text-primary">{{ $pendingUserLoanApplicationsCount }}</h3>
                                <p class="mb-0 text-muted">{{ __('Permohonan ICT dalam Proses') }}</p>
                                <a href="{{ route('loan-applications.index', ['status' => 'pending']) }}"
                                    class="btn btn-sm btn-outline-primary mt-3 motac-btn-primary">
                                    {{ __('Lihat Permohonan Saya') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Email/ID Application Status Card --}}
                    <div class="col-md-6">
                        <div class="card h-100 motac-card">
                            <div class="card-body text-center motac-card-body">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-envelope icon-stat text-info me-2"></i>
                                    <h6 class="mb-0 motac-stat-title">{{ __('Status Permohonan Emel/ID') }}</h6>
                                </div>
                                <h3 class="mb-2 text-info">{{ $pendingUserEmailApplicationsCount }}</h3>
                                <p class="mb-0 text-muted">{{ __('Permohonan Emel/ID dalam Proses') }}</p>
                                <a href="{{ route('email-applications.index', ['status' => 'pending']) }}"
                                    class="btn btn-sm btn-outline-info mt-3 motac-btn-primary">
                                    {{ __('Lihat Permohonan Saya') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Active Loans Card --}}
                    <div class="col-12 mt-4">
                        <div class="card h-100 motac-card">
                            <div class="card-body text-center motac-card-body">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="bi bi-folder-check icon-stat text-success me-2"></i>
                                    <h6 class="mb-0 motac-stat-title">{{ __('Pinjaman ICT Aktif') }}</h6>
                                </div>
                                <h3 class="mb-2 text-success">{{ $activeUserLoansCount }}</h3>
                                <p class="mb-0 text-muted">{{ __('Pinjaman ICT yang sedang aktif.') }}</p>
                                <a href="{{ route('loan-applications.index', ['status' => 'active']) }}"
                                    class="btn btn-sm btn-outline-success mt-3 motac-btn-primary">
                                    {{ __('Urus Pinjaman Aktif') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaction History & Upcoming Returns Row --}}
    <div class="row match-height g-4 mb-4">
        <div class="col-lg-6 col-md-12">
            <div class="card h-100 motac-card">
                <div class="card-header d-flex justify-content-between align-items-center motac-card-header">
                    <h5 class="card-title mb-0">{{ __('Sejarah Transaksi Terkini') }}</h5>
                    <a href="{{ route('resource-management.bpm.loan-transactions.index') }}"
                        class="btn btn-primary btn-sm motac-btn-primary">
                        {{ __('Lihat Semua') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body motac-card-body">
                    @if ($latestLoanTransactions->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            {{ __('Tiada sejarah transaksi pinjaman terkini untuk dipaparkan.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Tarikh') }}</th>
                                        <th scope="col">{{ __('Perkara') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        <th scope="col">{{ __('Kuantiti') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestLoanTransactions as $transaction)
                                        <tr class="td-link">
                                            <td>
                                                <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}">
                                                    {{ $transaction->transaction_date->format('d/m/Y') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}">
                                                    {{ $transaction->item_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}">
                                                    {{ $transaction->status_label }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('resource-management.bpm.loan-transactions.show', $transaction->id) }}">
                                                    {{ $transaction->quantity }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card h-100 motac-card">
                <div class="card-header d-flex justify-content-between align-items-center motac-card-header">
                    <h5 class="card-title mb-0">{{ __('Pulangan ICT Akan Datang') }}</h5>
                    <a href="{{ route('resource-management.bpm.loan-transactions.index', ['status' => 'issued']) }}"
                        class="btn btn-primary btn-sm motac-btn-primary">
                        {{ __('Lihat Semua') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body motac-card-body">
                    @if ($upcomingReturns->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            {{ __('Tiada pinjaman ICT yang perlu dipulangkan dalam masa terdekat.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Tarikh Pulang Dijangka') }}</th>
                                        <th scope="col">{{ __('Perkara') }}</th>
                                        <th scope="col">{{ __('Kuantiti') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingReturns as $returnLoanApp)
                                        <tr class="td-link">
                                            <td>
                                                <a href="{{ route('loan-applications.show', $returnLoanApp->id) }}">
                                                    {{ $returnLoanApp->expected_return_date ? \Carbon\Carbon::parse($returnLoanApp->expected_return_date)->format('d/m/Y') : ($returnLoanApp->loan_end_date ? \Carbon\Carbon::parse($returnLoanApp->loan_end_date)->format('d/m/Y') : 'N/A') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $returnLoanApp->id) }}">
                                                    {{ $returnLoanApp->item_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $returnLoanApp->id) }}">
                                                    {{ $returnLoanApp->quantity }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $returnLoanApp->id) }}">
                                                    {{ $returnLoanApp->status_label ?? $returnLoanApp->status_name ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $returnLoanApp->status)) }}
                                                </a>
                                            </td>
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

    {{-- Recent Applications Row --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6 col-md-12">
            <div class="card h-100 motac-card">
                <div class="card-header d-flex justify-content-between align-items-center motac-card-header">
                    <h5 class="card-title mb-0">{{ __('Permohonan Pinjaman ICT Terkini') }}</h5>
                    <a href="{{ route('loan-applications.index') }}"
                        class="btn btn-primary btn-sm motac-btn-primary">
                        {{ __('Lihat Semua') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body motac-card-body">
                    @if ($userRecentLoanApplications->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            {{ __('Tiada permohonan pinjaman ICT terkini untuk dipaparkan.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Tarikh') }}</th>
                                        <th scope="col">{{ __('Perkara') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userRecentLoanApplications as $loanApplication)
                                        <tr class="td-link">
                                            <td>
                                                <a href="{{ route('loan-applications.show', $loanApplication->id) }}">
                                                    {{ $loanApplication->created_at->format('d/m/Y') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $loanApplication->id) }}">
                                                    {{ $loanApplication->item_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $loanApplication->id) }}">
                                                    {{ $loanApplication->status_label ?? $loanApplication->status_name ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $loanApplication->status)) }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card h-100 motac-card">
                <div class="card-header d-flex justify-content-between align-items-center motac-card-header">
                    <h5 class="card-title mb-0">{{ __('Permohonan Emel/ID Terkini') }}</h5>
                    <a href="{{ route('email-applications.index') }}"
                        class="btn btn-primary btn-sm motac-btn-primary">
                        {{ __('Lihat Semua') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body motac-card-body">
                    @if ($userRecentEmailApplications->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            {{ __('Tiada permohonan emel/ID terkini untuk dipaparkan.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Tarikh') }}</th>
                                        <th scope="col">{{ __('Jenis Permohonan') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userRecentEmailApplications as $emailApplication)
                                        <tr class="td-link">
                                            <td>
                                                <a href="{{ route('email-applications.show', $emailApplication->id) }}">
                                                    {{ $emailApplication->created_at->format('d/m/Y') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('email-applications.show', $emailApplication->id) }}">
                                                    {{ $emailApplication->application_type_label ?? ($emailApplication->proposed_email ? __('Permohonan Emel') : __('Permohonan ID Pengguna')) }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('email-applications.show', $emailApplication->id) }}">
                                                     {{ $emailApplication->status_label ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $emailApplication->status)) }}
                                                </a>
                                            </td>
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

    @push('page-script')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const pageLocale = @json(App::getLocale());
                function updateMotacDashboardClock() {
                    const now = new Date();
                    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: false, hourCycle: 'h23' };
                    const dateEl = document.getElementById('motacDashboardDate');
                    const timeEl = document.getElementById('motacDashboardTime');
                    if (dateEl) {
                        try { dateEl.textContent = now.toLocaleDateString(pageLocale + '-MY', dateOptions); }
                        catch (e) { dateEl.textContent = now.toLocaleDateString('en-GB', dateOptions); console.warn('Locale for date formatting (' + pageLocale + '-MY) might not be fully supported, using en-GB fallback.', e); }
                    }
                    if (timeEl) {
                        try { timeEl.textContent = now.toLocaleTimeString(pageLocale + '-MY', timeOptions); }
                        catch (e) { timeEl.textContent = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); console.warn('Locale for time formatting (' + pageLocale + '-MY with hourCycle) might not be fully supported, using en-GB fallback.', e); }
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
