{{-- resources/views/loan-applications/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Permohonan Pinjaman ICT Saya'))

@section('content')
    <div class="container py-4">
        {{-- FIX: Replaced 'text-dark' with 'text-body' to allow the theme to control text color.  --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-body mb-0 d-flex align-items-center">
                <i class="bi bi-card-list me-2"></i>{{ __('Senarai Permohonan Pinjaman Saya') }}
            </h1>
            {{-- The @can directive correctly protects the create button.  --}}
            @can('create', App\Models\LoanApplication::class)
                <a href="{{ route('loan-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    {{ __('Buat Permohonan Baru') }}
                </a>
            @endcan
        </div>

        @include('_partials._alerts.alert-general')

        {{-- FIX: Removed hardcoded 'bg-light' and non-standard classes. The .card and .card-header classes are now styled by theme-motac.css.  --}}
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Sejarah Permohonan Pinjaman Anda') }}</h3>
            </div>
            @if ($applications->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-folder-x fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Permohonan Ditemui') }}</h5>
                    <p class="small">{{ __('Anda belum membuat sebarang permohonan pinjaman peralatan ICT.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    {{-- FIX: Added 'table-dark' to make the striped table dark-mode aware and removed 'table-light' from thead. This allows theme-motac.css to work correctly.  --}}
                    <table class="table table-hover table-striped table-dark mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">ID</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tujuan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Pinjaman') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                <tr>
                                    {{-- FIX: Replaced 'text-dark' with 'text-body' to respect the theme.  --}}
                                    <td class="px-3 py-2 small text-body fw-medium">#{{ $app->id }}</td>
                                    <td class="px-3 py-2 small text-body">{{ Str::limit($app->purpose, 50) }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ optional($app->loan_start_date)->translatedFormat('d M Y') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small"><x-resource-status-panel :resource="$app" statusAttribute="status" type="loan_application" /></td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('loan-applications.show', $app) }}" class="btn btn-sm btn-icon btn-outline-primary border-0" title="{{ __('Lihat Butiran') }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @can('update', $app)
                                                <a href="{{ route('loan-applications.edit', $app) }}" class="btn btn-sm btn-icon btn-outline-secondary border-0" title="{{ __('Kemaskini Draf') }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $app)
                                                <form action="{{ route('loan-applications.destroy', $app) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Adakah anda pasti untuk memadam draf permohonan ini?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam Draf') }}">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
             @if ($applications->hasPages())
                {{-- FIX: Removed 'bg-light'. The .card-footer class is themed by the CSS.  --}}
                <div class="card-footer border-top py-3 d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
