{{-- resources/views/reports/utilization-report.blade.php --}}
{{-- Utilization Report Page --}}

@extends('layouts.app')

@section('title', __('reports.utilization.title'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold">{{ __('reports.utilization.title') }}</h2>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-2 shadow-sm"
                title="{{ __('Export to PDF') }}">
                <i class="bi bi-file-earmark-pdf-fill"></i>
                <i class="bi bi-download"></i>
                {{ __('Export PDF') }}
            </a>
        </div>

        <p class="mb-4 text-muted">{{ __('reports.utilization.description') }}</p>

        <div class="row">
            <div class="col-md-6">
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-light fw-semibold">{{ __('reports.utilization.labels.utilization_rate') }}
                    </div>
                    <div class="card-body">
                        <h3 class="display-6 text-primary">{{ number_format($utilizationRate, 2) }}%</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-light fw-semibold">{{ __('reports.utilization.labels.status_summary') }}
                    </div>
                    <div class="card-body">
                        @forelse ($summary as $status => $count)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                        @empty
                            <p class="text-muted">{{ __('reports.utilization.no_results') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
