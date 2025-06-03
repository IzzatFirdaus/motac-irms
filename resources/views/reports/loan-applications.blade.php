{{-- resources/views/reports/loan-applications.blade.php --}}
<x-app-layout>
    @section('title', __('Laporan Permohonan Pinjaman Peralatan ICT')) {{-- Added title --}}

    <div class="container-fluid py-4"> {{-- Added container-fluid and padding --}}
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-journal-arrow-down me-2"></i>{{-- Bootstrap Icon --}}
                        {{ __('Laporan Permohonan Pinjaman Peralatan ICT') }}
                    </h3>
                    {{-- Ensure this route name 'admin.reports.index' is correct as per your web.php --}}
                    @if (Route::has('reports.index')) {{-- Changed to reports.index assuming it's in reports group --}}
                        <div class="mt-2 mt-sm-0 flex-shrink-0">
                            <a href="{{ route('reports.index') }}"
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                                <i class="bi bi-arrow-left me-1"></i> {{-- Bootstrap Icon --}}
                                {{ __('Kembali ke Senarai Laporan') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-body">
                {{-- Corrected path to your general alert partial --}}
                @include('_partials._alerts.alert-general')

                <div class="table-responsive">
                    @if ($loanApplications->isNotEmpty())
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('ID') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Tujuan') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Tarikh Pinjaman') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanApplications as $application)
                                    <tr>
                                        <td class="px-3 py-2 small fw-medium text-dark">#{{ $application->id }}</td>
                                        <td class="px-3 py-2 small">{{ $application->user->name ?? ($application->user->full_name ?? 'N/A') }}</td>
                                        <td class="px-3 py-2 small text-muted">{{ Str::limit($application->purpose, 40) }}</td>
                                        <td class="px-3 py-2 small text-muted">{{ $application->loan_start_date?->translatedFormat('d M Y') }}</td>
                                        <td class="px-3 py-2 small">
                                            {{-- Ensure App\Helpers\Helpers::getStatusColorClass exists and handles these statuses --}}
                                            {{-- Or use a dedicated component like <x-loan-application-status-badge :status="$application->status" /> --}}
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status) }} fw-normal">
                                                {{ $application->status_translated ?? __(Str::title(str_replace('_', ' ', $application->status))) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($loanApplications->hasPages())
                            <div class="mt-3 pt-3 border-top d-flex justify-content-center">
                                {{ $loanApplications->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info d-flex align-items-center text-center" role="alert"> {{-- Added text-center --}}
                            <i class="bi bi-info-circle-fill me-2"></i> {{-- Bootstrap Icon --}}
                            <div>
                                {{ __('Tiada permohonan pinjaman ditemui untuk dipaparkan dalam laporan ini.') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
