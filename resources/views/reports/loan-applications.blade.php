{{-- resources/views/reports/loan-applications.blade.php --}}
<x-app-layout>
    @section('title', __('Laporan Permohonan Pinjaman Peralatan ICT'))

    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-journal-arrow-down me-2"></i>
                        {{ __('Laporan Permohonan Pinjaman Peralatan ICT') }}
                    </h3>
                    @if (Route::has('reports.index'))
                        <div class="mt-2 mt-sm-0 flex-shrink-0">
                            <a href="{{ route('reports.index') }}"
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                                <i class="bi bi-arrow-left me-1"></i>
                                {{ __('Kembali ke Senarai Laporan') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-body">
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
                                            {{-- CORRECTED: Added 'loan_application' as the second argument --}}
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status ?? 'default', 'loan_application') }} fw-normal">
                                                {{-- Assuming $application has a status_label accessor or you fallback --}}
                                                {{ $application->status_label ?? __(Str::title(str_replace('_', ' ', $application->status))) }}
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
                        <div class="alert alert-info d-flex align-items-center text-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
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
