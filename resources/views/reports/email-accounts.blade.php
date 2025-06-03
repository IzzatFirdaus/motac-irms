{{-- resources/views/reports/email-accounts.blade.php --}}
<x-app-layout>
    @section('title', __('Laporan Akaun E-mel ICT'))

    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-envelope-paper-fill me-2"></i>
                        {{ __('Laporan Akaun E-mel ICT') }}
                    </h3>
                    @if (Route::has('reports.index')) {{-- Assuming reports.index is the correct route for back link --}}
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
                    @if ($emailApplications->isNotEmpty())
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('ID') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Emel Dicadang') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Emel Diluluskan') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Tarikh Mohon') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emailApplications as $application)
                                    <tr>
                                        <td class="px-3 py-2 small fw-medium text-dark">#{{ $application->id }}</td>
                                        <td class="px-3 py-2 small">{{ $application->user->name ?? ($application->user->full_name ?? 'N/A') }}</td>
                                        <td class="px-3 py-2 small text-muted">{{ $application->proposed_email ?? '-' }}</td>
                                        <td class="px-3 py-2 small text-muted">{{ $application->final_assigned_email ?? '-' }}</td>
                                        <td class="px-3 py-2 small">
                                            {{-- CORRECTED: Added the second argument 'email_application' for type --}}
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status ?? 'default', 'email_application') }} fw-normal">
                                                {{ $application->status_label ?? __(Str::title(str_replace('_', ' ', $application->status))) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">{{ $application->created_at->translatedFormat('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($emailApplications->hasPages())
                            <div class="mt-3 pt-3 border-top d-flex justify-content-center">
                                {{ $emailApplications->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info d-flex align-items-center text-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                {{ __('Tiada permohonan e-mel ditemui untuk dipaparkan dalam laporan ini.') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
