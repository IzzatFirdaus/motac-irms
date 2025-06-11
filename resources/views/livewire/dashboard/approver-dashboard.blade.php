<div>
    {{-- This view now acts as the primary container for the approver's dashboard experience --}}
    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Papan Pemuka Pegawai Pelulus') }}</h1>
            {{-- This link allows easy access to the user's full approval history --}}
            @if (Route::has('approvals.history'))
                <a href="{{ route('approvals.history') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-clock-history me-1"></i>
                    {{ __('Lihat Sejarah Kelulusan') }}
                </a>
            @endif
        </div>

        {{-- Section for statistical summary --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">{{ __('Statistik Kelulusan Anda (30 Hari Terakhir)') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 text-success"></i>
                            {{ __('Jumlah Diluluskan:') }}
                            <strong class="text-dark ms-2">{{ $approved_last_30_days }}</strong>
                        </p>
                        <p class="mb-0 d-flex align-items-center">
                            <i class="bi bi-x-circle-fill me-2 text-danger"></i>
                            {{ __('Jumlah Ditolak:') }}
                            <strong class="text-dark ms-2">{{ $rejected_last_30_days }}</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">{{ __('Panduan Kelulusan') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            {{ __('Sila semak setiap permohonan dengan teliti sebelum membuat keputusan. Keputusan anda akan dimaklumkan kepada pemohon melalui e-mel.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section for the main approval task list --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">{{ __('Permohonan Menunggu Tindakan Anda') }}</h6>
                    </div>
                    <div class="card-body p-0">
                        {{-- REVISED: Removed the unnecessary key() directive to clear the editor warning. --}}
                        @livewire('resource-management.approval.dashboard', [
                            'filterStatus' => 'pending'
                        ])
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
