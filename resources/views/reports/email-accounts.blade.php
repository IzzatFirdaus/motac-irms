@extends('layouts.app')

@section('title', __('reports.email_report_title'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-envelope-paper-fill me-2"></i>{{ __('reports.email_report_title') }}
            </h1>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i>{{ __('reports.back_to_reports') }}
            </a>
        </div>

        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('reports.email-accounts') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label small">{{ __('Carian Kata Kunci') }}</label>
                            <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="{{ __('reports.search_by_user_email') }}" value="{{ $request->input('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="user_id" class="form-label small">{{ __('reports.applicant') }}</label>
                            <select name="user_id" id="user_id" class="form-select form-select-sm">
                                <option value="all">{{ __('common.all') }}</option>
                                @foreach($usersFilter as $id => $name)
                                    <option value="{{ $id }}" {{ $request->input('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label small">{{ __('common.status') }}</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="all">{{ __('common.all') }}</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ $request->input('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label small">{{ __('reports.date_from') }}</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $request->input('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label small">{{ __('reports.date_to') }}</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $request->input('date_to') }}">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Tapis') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h3 class="h5 card-title fw-semibold mb-0">{{__('reports.email_list')}}</h3>
                @if($emailApplications->total() > 0)
                <small class="text-muted">{{ __('Memaparkan :from-:to daripada :total rekod', ['from' => $emailApplications->firstItem(), 'to' => $emailApplications->lastItem(), 'total' => $emailApplications->total()]) }}</small>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.applicant') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.application_type') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.application_date') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.proposed_email') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.assigned_email') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('common.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($emailApplications as $application)
                                <tr>
                                    <td class="px-3 py-2 small">
                                        {{ $application->user->name ?? __('common.not_available') }}
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $application->user->department->name ?? '' }}</div>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $application->application_type_label }}</td>
                                    <td class="px-3 py-2 small">{{ $application->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="px-3 py-2 small">{{ $application->proposed_email ?? '-' }}</td>
                                    <td class="px-3 py-2 small text-primary fw-medium">{{ $application->final_assigned_email ?? $application->final_assigned_user_id ?? '-' }}</td>
                                    <td class="px-3 py-2 small"><x-resource-status-panel :resource="$application" statusAttribute="status" type="email_application" :showIcon="true" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">{{ __('reports.no_email_apps_found') }}</td>
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
