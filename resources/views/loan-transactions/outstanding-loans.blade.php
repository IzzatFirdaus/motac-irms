<<<<<<< HEAD
{{-- resources/views/loan-transactions/outstanding-loans.blade.php --}}
=======
{{-- resources/views/loan-transactions/outstanding.blade.php --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
@extends('layouts.app')

@section('title', __('Senarai Pinjaman Menunggu Pengeluaran'))

@section('content')
    <div class="container py-4">
<<<<<<< HEAD
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Pinjaman Menunggu Pengeluaran') }}</h1>
        </div>

        @include('_partials._alerts.alert-general') {{-- CORRECTED INCLUDE PATH --}}

        @if ($loanApplications->isEmpty())
            <div class="alert alert-info text-center shadow-sm rounded-3" role="alert">
                <i class="bi bi-info-circle-fill fs-3 me-2 align-middle"></i>
                <span class="align-middle">{{ __('Tiada permohonan pinjaman menunggu pengeluaran pada masa ini.') }}</span>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 card-title fw-semibold mb-0">{{ __('Permohonan Diluluskan & Sedia Untuk Pengeluaran') }}
                    </h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Permohonan #') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Pemohon') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tujuan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tarikh Jangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Item Diluluskan (Kuantiti)') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">
=======
        <h2 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Pinjaman Menunggu Pengeluaran') }}</h2>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($loanApplications->isEmpty())
            <div class="alert alert-info" role="alert">
                {{ __('Tiada permohonan pinjaman menunggu pengeluaran pada masa ini.') }}
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Permohonan #') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Pemohon') }}
                                    </th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Tujuan') }}
                                    </th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Tarikh Dijangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Item Diluluskan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium text-end">
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                        {{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanApplications as $application)
                                    <tr>
<<<<<<< HEAD
                                        <td class="px-3 py-2 small text-dark fw-medium">
                                            {{-- This route 'loan-applications.show' is global and correct --}}
                                            <a href="{{ route('loan-applications.show', $application->id) }}"
                                                class="text-decoration-none" title="{{ __('Lihat Permohonan') }}">
                                                #{{ $application->id }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            @if ($application->user)
                                                {{-- This route 'users.show' is global and correct --}}
                                                <a href="{{ route('users.show', $application->user->id) }}"
                                                    class="text-decoration-none" title="{{ __('Lihat Profil Pemohon') }}">
                                                    {{ e($application->user->name ?? __('N/A')) }}
                                                </a>
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ Str::limit(e($application->purpose), 40) }}</td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional($application->loan_end_date)->translatedFormat('d M Y') ?? __('N/A') }}
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            @if ($application->loanApplicationItems->where('quantity_approved', '>', 0)->isNotEmpty())
                                                <ul class="list-unstyled mb-0 ps-2">
                                                    @foreach ($application->loanApplicationItems->where('quantity_approved', '>', 0) as $item)
                                                        <li><i
                                                                class="bi bi-check-circle text-success me-1"></i>{{ e(\App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) ?? __('N/A') }}
                                                            ({{ $item->quantity_approved ?? __('N/A') }})</li>
=======
                                        <td class="align-middle">
                                            <a
                                                href="{{ route('loan-applications.show', $application->id) }}">{{ $application->id }}</a>
                                        </td>
                                        <td class="align-middle">
                                            @if ($application->user)
                                                <a href="{{ route('users.show', $application->user->id) }}">
                                                    {{ $application->user->name ?? 'N/A' }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ Str::limit($application->purpose, 40) }}</td>
                                        <td class="align-middle">
                                            {{ $application->loan_end_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                        <td class="align-middle small">
                                            @if ($application->items->where('quantity_approved', '>', 0)->isNotEmpty())
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($application->items->where('quantity_approved', '>', 0) as $item)
                                                        <li>{{ $item->equipment_type ?? 'N/A' }} (Diluluskan:
                                                            {{ $item->quantity_approved ?? 'N/A' }})</li>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
<<<<<<< HEAD
                                        <td class="px-3 py-2 text-end">
                                            {{-- CORRECTED ROUTE NAME --}}
                                            <a href="{{ route('resource-management.bpm.loan-transactions.issue.form', $application) }}"
                                                class="btn btn-sm btn-warning text-dark d-inline-flex align-items-center">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                {{ __('Proses Pengeluaran') }}
=======
                                        <td class="text-end align-middle">
                                            <a href="{{ route('loan-transactions.issue.form', $application) }}"
                                                class="btn btn-sm btn-info d-inline-flex align-items-center">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                {{ __('Keluarkan Peralatan') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($loanApplications->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $loanApplications->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
<<<<<<< HEAD
"""))
=======
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
