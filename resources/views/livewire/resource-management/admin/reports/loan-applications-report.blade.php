@extends('layouts.app')

@section('title', __('reports.loan_apps_title'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-journal-text me-2"></i>{{ __('reports.loan_apps_title') }}
            </h1>
            @if (Route::has('reports.index'))
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('reports.back_to_reports') }}
                </a>
            @endif
        </div>

        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('reports.loan_apps_list') }}</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.applicant') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('dashboard.subject') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.loan_dates') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('common.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loanApplications as $application)
                                <tr>
                                    <td class="px-3 py-2 small fw-medium">#{{ $application->id }}</td>
                                    <td class="px-3 py-2 small">{{ $application->user->name ?? __('common.not_available') }}</td>
                                    <td class="px-3 py-2 small" style="white-space: normal; min-width: 250px;">{{ Str::limit($application->purpose, 70) }}</td>
                                    <td class="px-3 py-2 small">{{ $application->loan_start_date?->translatedFormat('d/m/Y') }} - {{ $application->loan_end_date?->translatedFormat('d/m/Y') }}</td>
                                    <td class="px-3 py-2 small">
                                        {{-- CORRECTED: Using the model accessor for a high-contrast badge --}}
                                        <span class="badge rounded-pill {{ $application->status_color_class }}">{{ $application->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        {{ __('reports.no_loan_apps_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($loanApplications->hasPages())
                <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                    {{ $loanApplications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
