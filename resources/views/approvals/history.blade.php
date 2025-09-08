{{-- resources/views/approvals/history.blade.php --}}
<<<<<<< HEAD
@extends('layouts.app')
=======
@extends('layouts.app') {{-- Ensure layouts.app is Bootstrap-compatible --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('title', __('Sejarah Kelulusan'))

@section('content')
<<<<<<< HEAD
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center"><i class="bi bi-clock-history me-2"></i>{{ __('Sejarah Kelulusan') }}</h1>
            <a href="{{ route('approvals.dashboard') }}"
                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
=======
    <div class="container py-4"> {{-- Bootstrap container --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Sejarah Kelulusan') }}</h1>
            <a href="{{ route('approval.dashboard') }}" {{-- Make sure this route is correct for your app's approval dashboard --}}
                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                <i class="bi bi-speedometer2 me-1"></i>
                {{ __('Papan Pemuka Kelulusan') }}
            </a>
        </div>

<<<<<<< HEAD
        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm motac-card">
            <div class="card-header bg-light py-3 motac-card-header">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Rekod Kelulusan Lepas') }}</h3>
            </div>
            @if ($approvals->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-collection fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Sejarah Ditemui') }}</h5>
                    <p class="small">{{ __('Anda belum membuat sebarang keputusan kelulusan.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">ID</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Permohonan') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Keputusan') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">{{ __('Tindakan') }}</th>
=======

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Rekod Kelulusan Lepas') }}</h3>
            </div>
            @if ($approvals->isEmpty())
                <div class="card-body">
                    <div class="alert alert-info text-center mb-0" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada kelulusan dalam sejarah pada masa ini.') }}
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle"> {{-- Added align-middle --}}
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">ID</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Jenis Permohonan') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Pemohon') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tarikh Kelulusan Dibuat') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Status') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Pegawai Pelulus') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tarikh Keputusan Sebenar') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">
                                    {{ __('Tindakan') }}</th>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvals as $approval)
<<<<<<< HEAD
                                @php $approvableItem = $approval->approvable; @endphp
                                <tr>
                                    <td class="px-3 py-2 small text-dark">#{{ $approval->id ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($approvableItem)
                                            <i class="bi {{ $approvableItem instanceof \App\Models\EmailApplication ? 'bi-envelope-at text-info' : 'bi-laptop text-success' }} me-1"></i>
                                            {{ $approvableItem instanceof \App\Models\EmailApplication ? __('Permohonan Emel') : __('Pinjaman ICT') }}
                                            (#{{ $approvableItem->id }})
                                        @else
                                            <i class="bi bi-question-circle me-1 text-secondary"></i>{{ __('Jenis Tidak Diketahui') }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ optional($approvableItem->user)->name ?? __('Tidak Diketahui') }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ optional($approval->approval_timestamp)->translatedFormat('d M Y, H:i A') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small"><x-approval-status-badge :status="$approval->status" /></td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $approval)
                                                <a href="{{ route('approvals.show', $approval) }}" class="btn btn-sm btn-icon btn-outline-secondary motac-btn-icon" title="{{ __('Lihat Butiran Kelulusan') }}">
                                                    <i class="bi bi-search"></i>
                                                </a>
                                            @endcan
                                            {{-- ADJUSTMENT: Added @can directive to secure this link --}}
                                            @if ($approvableItem)
                                                @can('view', $approvableItem)
                                                    @php
                                                        $route = $approvableItem instanceof \App\Models\EmailApplication ? 'email-applications.show' : 'loan-applications.show';
                                                    @endphp
                                                    <a href="{{ route($route, $approvableItem->id) }}" class="btn btn-sm btn-icon btn-outline-primary motac-btn-icon" title="{{ __('Lihat Permohonan Asal') }}">
                                                        <i class="bi bi-file-earmark-text-fill"></i>
                                                    </a>
                                                @endcan
=======
                                @php($approvableItem = $approval->approvable) {{-- Changed variable name for clarity --}}
                                <tr>
                                    <td class="px-3 py-2 small text-dark">#{{ $approval->id ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($approvableItem instanceof \App\Models\EmailApplication)
                                            <i class="bi bi-envelope-at me-1 text-info"></i>{{ __('Permohonan Emel') }}
                                        @elseif ($approvableItem instanceof \App\Models\LoanApplication)
                                            <i class="bi bi-laptop me-1 text-success"></i>{{ __('Pinjaman ICT') }}
                                        @else
                                            <i
                                                class="bi bi-question-circle me-1 text-secondary"></i>{{ __('Jenis Tidak Diketahui') }}
                                        @endif
                                        (#{{ $approvableItem->id ?? 'N/A' }})
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $approvableItem->user->name ?? ($approvableItem->user->full_name ?? 'N/A') }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $approval->created_at?->translatedFormat('d M Y, H:i A') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">
                                        {{-- Assuming x-approval-status-badge or similar component handles this well --}}
                                        <x-approval-status-badge :status="$approval->status" />
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $approval->officer->name ?? ($approval->officer->full_name ?? 'N/A') }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional($approval->approval_timestamp)->translatedFormat('d M Y, H:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $approval)
                                                <a href="{{ route('approvals.show', $approval) }}"
                                                    class="btn btn-sm btn-outline-secondary border-0 p-1"
                                                    title="{{ __('Lihat Butiran Kelulusan') }}">
                                                    <i class="bi bi-receipt-cutoff"></i>
                                                </a>
                                            @endcan
                                            @if ($approvableItem)
                                                @php
                                                    $applicationDetailRoute = '#!'; // Default
                                                    if ($approvableItem instanceof \App\Models\EmailApplication) {
                                                        $applicationDetailRoute = route(
                                                            'resource-management.my-applications.email-applications.show',
                                                            $approvableItem->id,
                                                        );
                                                    } elseif ($approvableItem instanceof \App\Models\LoanApplication) {
                                                        $applicationDetailRoute = route(
                                                            'resource-management.my-applications.loan-applications.show',
                                                            $approvableItem->id,
                                                        );
                                                    }
                                                @endphp
                                                @if (Route::has(Str::after($applicationDetailRoute, url('/'))))
                                                    {{-- Check if route exists before linking --}}
                                                    <a href="{{ $applicationDetailRoute }}"
                                                        class="btn btn-sm btn-outline-primary border-0 p-1"
                                                        title="{{ __('Lihat Permohonan Asal') }}">
                                                        <i class="bi bi-file-earmark-text-fill"></i>
                                                    </a>
                                                @endif
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($approvals->hasPages())
<<<<<<< HEAD
                    <div class="card-footer bg-light border-top py-3 d-flex justify-content-center">
                        {{ $approvals->links() }}
=======
                    <div class="card-footer bg-light border-top-0 py-3 d-flex justify-content-center">
                        {{ $approvals->links() }} {{-- Ensure Laravel pagination is set to Bootstrap in AppServiceProvider --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
