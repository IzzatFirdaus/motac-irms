@extends('layouts.app')

{{-- Use the passed $pageTitle variable for the section title, with a fallback --}}
@section('title', $pageTitle ?? __('reports.user_activity.title'))

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                {{-- Use the passed $pageTitle variable for the header, with a fallback --}}
                <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-check-fill me-2"></i>{{ $pageTitle ?? __('reports.user_activity.title') }}
                </h3>
                <a href="{{ route('reports.index') }}"
                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('reports.back_to_list') }}
                </a>
            </div>

            <div class="card-body">
                @include('_partials._alerts.alert-general')

                @if ($users->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th class="text-center">{{ __('Email Apps') }}</th>
                                    <th class="text-center">{{ __('Loan Apps') }}</th>
                                    <th class="text-center">{{ __('Approvals') }}</th>
                                    <th>{{ __('Registered') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="fw-medium">{{ $user->id }}</td>
                                        <td>{{ $user->full_name ?? $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">{{ $user->email_applications_count ?? 0 }}</td>
                                        <td class="text-center">{{ $user->loan_applications_as_applicant_count ?? 0 }}</td>
                                        <td class="text-center">{{ $user->approvals_made_count ?? 0 }}</td>
                                        <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning text-center mt-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('No user activity data available.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
