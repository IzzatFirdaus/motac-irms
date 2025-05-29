{{-- resources/views/loan-transactions/outstanding.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Pinjaman Menunggu Pengeluaran'))

@section('content')
    <div class="container py-4">
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
                                        {{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanApplications as $application)
                                    <tr>
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
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end align-middle">
                                            <a href="{{ route('loan-transactions.issue.form', $application) }}"
                                                class="btn btn-sm btn-info d-inline-flex align-items-center">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                {{ __('Keluarkan Peralatan') }}
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
