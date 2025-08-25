<<<<<<< HEAD
@extends('layouts.app')

@section('title', __('reports.email_applications.title'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
            <i class="bi bi-envelope-paper-fill me-2"></i>{{ __('reports.email_applications.title') }}
        </h1>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>{{ __('reports.back_to_list') }}
        </a>
    </div>

    @include('_partials._alerts.alert-general')

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('reports.email-accounts') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label small">{{ __('reports.filters.search_placeholder') }}</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm"
                               placeholder="{{ __('reports.search_placeholder') }}"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="user_id" class="form-label small">{{ __('reports.filters.user') }}</label>
                        <select name="user_id" id="user_id" class="form-select form-select-sm">
                            <option value="">{{ __('reports.filters.all_users') }}</option>
                            @foreach ($usersFilter as $id => $name)
                                <option value="{{ $id }}" @selected(request('user_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label small">{{ __('common.status') }}</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="">{{ __('common.all') }}</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label small">{{ __('reports.filters.date_from') }}</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label small">{{ __('reports.filters.date_to') }}</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="submit" class="btn btn-sm btn-primary w-100">{{ __('reports.filters.filter_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('reports.email_applications.table.applicant') }}</th>
                            <th>{{ __('reports.email_applications.table.application_type') }}</th>
                            <th>{{ __('reports.email_applications.table.application_date') }}</th>
                            <th>{{ __('reports.email_applications.table.proposed_email') }}</th>
                            <th>{{ __('reports.email_applications.table.assigned_email') }}</th>
                            <th>{{ __('reports.email_applications.table.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($emailApplications as $application)
                            <tr>
                                <td>
                                    {{ $application->user->name ?? __('common.not_available') }}
                                    <div class="text-muted small">{{ $application->user->department->name ?? '-' }}</div>
                                </td>
                                <td>{{ $application->application_type_label }}</td>
                                <td>{{ $application->created_at->translatedFormat('d M Y') }}</td>
                                <td>{{ $application->proposed_email ?? '-' }}</td>
                                <td class="text-primary fw-medium">{{ $application->final_assigned_email ?? '-' }}</td>
                                <td>
                                    <x-resource-status-panel :resource="$application" statusAttribute="status" type="email_application" :showIcon="true" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">{{ __('reports.email_applications.no_results') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($emailApplications->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                {{ $emailApplications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
=======
<x-app-layout>
    {{-- The outer container is managed by x-app-layout --}}

    <div class="card shadow-sm mb-4">
        {{-- Card Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mt-2">
                    <h3 class="h5 mb-0">
                        {{ __('Laporan Akaun Emel ICT') }}
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
                @if ($emailApplications->isNotEmpty())
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-uppercase small">{{ __('ID') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Pemohon') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Emel Dicadang') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Status') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Tarikh') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($emailApplications as $application)
                                <tr>
                                    <td class="fw-medium">{{ $application->id }}</td>
                                    <td>{{ $application->user->full_name ?? $application->user->name }}</td>
                                    <td>{{ $application->proposed_email }}</td>
                                    <td>
                                        @php
                                            // Assuming Helpers::getStatusColorClass() can be adapted
                                            // to return Bootstrap text/badge classes or you map them here.
                                            // Example: 'text-success', 'badge bg-warning text-dark'
                                            $statusClass = \App\Helpers\Helpers::getStatusColorClass(
                                                $application->status,
                                            );
                                            // If it returns Tailwind, map it:
                                            // $statusClass = match($statusClass) {
                                            //    'text-green-600' => 'text-success',
                                            //    'text-yellow-600' => 'text-warning',
                                            //     default => 'text-secondary',
                                            // };
                                        @endphp
                                        <span class="{{ $statusClass }} fw-semibold">
                                            {{ $application->status }} {{-- Consider using a more presentable status name --}}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($emailApplications->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $emailApplications->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                            aria-label="Warning:">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                            {{ __('Tiada permohonan emel ditemui.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
