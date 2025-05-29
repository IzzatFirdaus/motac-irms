<<<<<<< HEAD
resources\views\reports\loan-applications.blade.php
@extends('layouts.app')

@section('title', __('reports.loan_applications.title'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-journal-arrow-down me-2"></i>{{ __('reports.loan_applications.title') }}
            </h1>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i>{{ __('reports.back_to_list') }}
            </a>
        </div>

        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-body p-3 p-md-4">
                <form method="GET" action="{{ route('reports.loan-applications') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3">
                            <label class="form-label small">{{ __('reports.filters.search_placeholder') }}</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm" placeholder="{{ __('Masukkan kata kunci...') }}">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label small">{{ __('reports.filters.transaction_type') }}</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">{{ __('reports.filters.all_types') }}</option>
                                @foreach ($statusOptions as $key => $label)
                                    <option value="{{ $key }}" @selected(request('status') == $key)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label small">{{ __('reports.loan_applications.table.department') }}</label>
                            <select name="department_id" class="form-select form-select-sm">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach ($departmentOptions as $id => $name)
                                    <option value="{{ $id }}" @selected(request('department_id') == $id)>{{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label small">{{ __('reports.filters.date_from') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label small">{{ __('reports.filters.date_to') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-lg-1 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 motac-btn-primary">
                                <i class="bi bi-search me-1"></i>{{ __('Saring') }}
                            </button>
                            <a href="{{ route('reports.loan-applications') }}"
                                class="btn btn-outline-secondary btn-sm w-100 motac-btn-outline"
                                title="{{ __('Reset') }}">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm motac-card">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('reports.loan_applications.table.applicant') }}</th>
                            <th>{{ __('reports.loan_applications.table.department') }}</th>
                            <th>{{ __('Tujuan') }}</th>
                            <th>{{ __('Item Dimohon') }}</th>
                            <th>{{ __('reports.loan_applications.table.loan_dates') }}</th>
                            <th>{{ __('reports.loan_applications.table.return_date') }}</th>
                            <th>{{ __('reports.loan_applications.table.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loanApplications as $item)
                            <tr>
                                <td>#{{ $item->id }}</td>
                                <td>{{ $item->user->name ?? 'N/A' }}</td>
                                <td>{{ $item->user->department->name ?? '-' }}</td>
                                <td>{{ Str::limit($item->purpose, 100) }}</td>
                                <td>
                                    {{-- Applied the new class 'list-square-bullet' here --}}
                                    <ul class="list-unstyled mb-0 list-square-bullet">
                                        @foreach ($item->loanApplicationItems as $appItem)
                                            {{-- Removed the literal '•' here as CSS will handle the bullet --}}
                                            <li>{{ $appItem->equipment_type_label ?? 'N/A' }} ({{ __('Qty') }}:
                                                {{ $appItem->quantity_requested }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $item->loan_start_date?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td>{{ $item->loan_end_date?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td>
                                    <span
                                        class="badge {{ App\Helpers\Helpers::getStatusColorClass($item->status, 'loan_application') }}">
                                        {{ $statusOptions[$item->status] ?? $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    {{ __('reports.loan_applications.no_results') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($loanApplications->hasPages())
                <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                    {{ $loanApplications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

{{-- Add this section for custom CSS --}}
@push('styles')
<style>
    /* Custom CSS for the thin square bullet point */
    .list-square-bullet {
        list-style-type: none; /* Remove default bullets */
        padding-left: 1.5em; /* Add padding for custom bullet */
    }

    .list-square-bullet li::before {
        content: "\25A2"; /* Unicode for WHITE SQUARE (hollow square) */
        display: inline-block;
        width: 1em; /* Adjust width if needed */
        margin-left: -1.5em; /* Pull the bullet point to the left */
        font-weight: bold; /* Make the square appear thicker */
        line-height: 1; /* Align vertically with text */
        vertical-align: middle; /* Adjust vertical alignment */
        margin-right: 0.5em; /* Space between bullet and text */
    }
</style>
@endpush
=======
<x-app-layout>
    {{-- The outer container is managed by x-app-layout --}}

    <div class="card shadow-sm mb-4">
        {{-- Card Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mt-2">
                    <h3 class="h5 mb-0">
                        {{ __('Laporan Permohonan Pinjaman Peralatan ICT') }}
                    </h3>
                </div>
                @if (Route::has('admin.reports.index'))
                    <div class="mt-2 flex-shrink-0">
                        <a href="{{ route('admin.reports.index') }}"
                           class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i>
                            {{ __('Kembali ke Laporan') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">
            {{-- General alerts partial - ensure this partial is also Bootstrap styled --}}
            @include('_partials._alerts.alert-general')

            <div class="table-responsive">
                @if ($loanApplications->isNotEmpty())
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-uppercase small">{{ __('ID') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Pemohon') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Tujuan') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Tarikh Pinjaman') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loanApplications as $application)
                                <tr>
                                    <td class="fw-medium">{{ $application->id }}</td>
                                    <td>{{ $application->user->full_name ?? $application->user->name }}</td>
                                    <td>{{ Str::limit($application->purpose, 30) }}</td>
                                    <td>{{ $application->loan_start_date?->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            // Assuming Helpers::getStatusColorClass() can be adapted
                                            // to return Bootstrap text/badge classes.
                                            $statusClass = \App\Helpers\Helpers::getStatusColorClass($application->status);
                                        @endphp
                                        <span class="{{ $statusClass }} fw-semibold">
                                            {{ $application->status }} {{-- Consider using a more presentable status name --}}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($loanApplications->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $loanApplications->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <div>
                            {{ __('Tiada permohonan pinjaman ditemui.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
