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
