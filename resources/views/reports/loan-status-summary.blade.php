@extends('layouts.app')

@section('title', __('reports.loan_status_summary.title'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold">{{ __('reports.loan_status_summary.title') }}</h2>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-2 shadow-sm"
                title="{{ __('Export to PDF') }}">
                <i class="bi bi-file-earmark-pdf-fill"></i>
                <i class="bi bi-download"></i>
                {{ __('Export PDF') }}
            </a>
        </div>


        <p class="mb-4 text-muted">{{ __('reports.loan_status_summary.description') }}</p>

        <div class="card shadow-sm">
            <div class="card-body">
                @if (!empty($data))
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('reports.loan_status_summary.labels.status') }}</th>
                                <th>{{ __('reports.loan_status_summary.labels.count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $status => $item)
                                <tr>
                                    <td class="text-capitalize">{{ $item['label'] }}</td>
                                    <td><strong>{{ $item['count'] }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">{{ __('reports.loan_status_summary.no_results') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
