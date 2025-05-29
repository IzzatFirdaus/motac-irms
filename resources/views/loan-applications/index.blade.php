@extends('layouts.app')

@section('title', __('Senarai Permohonan Pinjaman Peralatan ICT'))

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Permohonan Pinjaman Peralatan ICT') }}</h2>
        <a href="{{ route('resource-management.loan-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
            <i class="bi bi-plus-lg me-1"></i> {{-- Example Bootstrap Icon --}}
            {{ __('Permohonan Baru') }}
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($applications->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada permohonan pinjaman peralatan ICT ditemui.') }}
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0"> {{-- Bootstrap table classes --}}
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tujuan Permohonan') }}</th>
                            <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Pinjaman') }}</th>
                            <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Dijangka Pulang') }}</th>
                            <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                            <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Hantar') }}</th>
                            <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            <tr>
                                <td class="px-3 py-2 small text-dark align-middle">{{ Str::limit($app->purpose, 50) }}</td>
                                <td class="px-3 py-2 small text-muted align-middle">{{ $app->loan_start_date?->format('d M Y') ?? 'N/A' }}</td>
                                <td class="px-3 py-2 small text-muted align-middle">{{ $app->loan_end_date?->format('d M Y') ?? 'N/A' }}</td>
                                <td class="px-3 py-2 small align-middle">
                                    {{-- Use Bootstrap badge classes via Helper --}}
                                    <span class="badge rounded-pill {{ App\Helpers\Helpers::getBootstrapStatusColorClass($app->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 small text-muted align-middle">{{ $app->created_at->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-end align-middle">
                                    <a href="{{ route('my-applications.loan.show', $app) }}" class="btn btn-sm btn-outline-primary border-0" title="Lihat">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    {{-- @if ($app->status === 'draft')
                                    <a href="{{ route('resource-management.loan-applications.edit', $app) }}" class="btn btn-sm btn-outline-secondary border-0 ms-1" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @endif --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($applications->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $applications->links() }} {{-- Ensure Laravel pagination is configured for Bootstrap --}}
            </div>
        @endif
    @endif
</div>
@endsection
