{{-- resources/views/livewire/resource-management/admin/reports/loan-applications-report.blade.php --}}
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

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <form wire:submit.prevent="applyFilters">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="searchTermLoan" class="form-label">{{ __('Carian (ID, Nama, Tujuan)') }}</label>
                            <input wire:model.live.debounce.300ms="searchTerm" id="searchTermLoan" type="text" class="form-control form-control-sm" placeholder="{{ __('Carian...') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="filterStatus" class="form-label">{{ __('Status') }}</label>
                            <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                                <option value="">{{ __('Semua Status') }}</option>
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterDepartmentId" class="form-label">{{ __('Jabatan') }}</label>
                            <select wire:model.live="filterDepartmentId" id="filterDepartmentId" class="form-select form-select-sm">
                                <option value="">{{ __('Semua Jabatan') }}</option>
                                @foreach($departmentOptions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterDateFrom" class="form-label">{{ __('Tarikh Dari') }}</label>
                            <input wire:model.live="filterDateFrom" id="filterDateFrom" type="date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label for="filterDateTo" class="form-label">{{ __('Tarikh Hingga') }}</label>
                            <input wire:model.live="filterDateTo" id="filterDateTo" type="date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                                <i class="bi bi-search"></i>
                            </button>
                            <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Loan Applications Table --}}
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
                            @forelse ($reportData as $application)
                                <tr>
                                    <td class="px-3 py-2 small fw-medium">#{{ $application->id }}</td>
                                    <td class="px-3 py-2 small">{{ $application->user->name ?? __('common.not_available') }}</td>
                                    <td class="px-3 py-2 small" style="white-space: normal; min-width: 250px;">{{ Str::limit($application->purpose, 70) }}</td>
                                    <td class="px-3 py-2 small">{{ $application->loan_start_date?->translatedFormat('d/m/Y') }} - {{ $application->loan_end_date?->translatedFormat('d/m/Y') }}</td>
                                    <td class="px-3 py-2 small">
                                        {{-- Use badge for status --}}
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
            @if ($reportData->hasPages())
                <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                    {{ $reportData->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
