{{-- resources/views/livewire/dashboard.blade.php --}}
<div>
    @section('title', __('Papan Pemuka'))

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0 fw-bold">{{ __('Selamat Datang, :name', ['name' => $displayUserName]) }}</h2>
                <p class="text-muted">{{ __('Papan Pemuka Sistem Pengurusan Sumber Bersepadu MOTAC') }}</p>
            </div>
        </div>
        @if($isNormalUser)
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold text-primary mb-1">
                                <i class="bi bi-inboxes me-2"></i>{{ __('Permohonan Pinjaman Anda (Belum Selesai)') }}
                            </h6>
                            <h2 class="display-5 fw-bold">{{ $pendingUserLoanApplicationsCount }}</h2>
                        </div>
                        <div>
                            <a href="{{ route('loan-applications.index') }}" class="btn btn-outline-primary btn-sm mt-2">
                                {{ __('Lihat Semua Permohonan') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>{{ __('Permohonan Terkini Anda') }}
                    </div>
                    <div class="card-body p-3">
                        @if($userRecentLoanApplications->isEmpty())
                            <p class="text-muted">{{ __('Tiada permohonan terkini.') }}</p>
                        @else
                            <table class="table table-sm mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Tujuan') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Dikemaskini') }}</th>
                                        <th>{{ __('Tindakan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userRecentLoanApplications as $application)
                                    <tr>
                                        <td>#{{ $application->id }}</td>
                                        <td>{{ Str::limit($application->purpose, 30, '...') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ __(Str::title(str_replace('_', ' ', $application->status))) }}</span>
                                        </td>
                                        <td>{{ $application->updated_at?->translatedFormat(config('app.datetime_format_my_short', 'd M Y, H:i')) }}</td>
                                        <td>
                                            <a href="{{ route('loan-applications.show', $application->id) }}" class="btn btn-xs btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
            <div class="row mt-4">
                <div class="col">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Papan pemuka pentadbir/pegawai akan menampilkan laporan dan akses pantas tugasan sistem selepas integrasi modul-modul pentadbiran.') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
