{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', __('User Details') . ': ' . ($user->full_name ?? 'N/A'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <h1 class="h2 fw-bold text-dark mb-4">{{ __('User Details') }}: {{ $user->full_name ?? 'N/A' }}</h1>

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-lg rounded-3">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-semibold text-dark mb-3 border-bottom pb-2">{{ __('User Information') }}</h3>
                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Name:') }}</div>
                                <p class="text-dark mb-0">{{ $user->full_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('NRIC:') }}</div>
                                <p class="text-dark mb-0">{{ $user->identification_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('MOTAC Email:') }}</div>
                                <p class="text-dark mb-0">{{ $user->motac_email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Department:') }}</div>
                                <p class="text-dark mb-0">{{ $user->department->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Grade:') }}</div>
                                <p class="text-dark mb-0">{{ $user->grade->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Service Status:') }}</div>
                                <p class="text-dark mb-0 text-capitalize">
                                    {{ str_replace('_', ' ', $user->service_status ?? '-') }}</p>
                            </div>
                            {{-- Example for roles --}}
                            {{-- @if ($user->roles->isNotEmpty())
                        <div class="col-md-12">
                            <div class="text-muted fw-medium">{{ __('Roles:') }}</div>
                            <p class="text-dark mb-0">{{ $user->roles->pluck('name')->join(', ') }}</p>
                        </div>
                        @endif --}}
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Back to Users List') }}
                    </a>
                    {{-- @can('update', $user)
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info d-inline-flex align-items-center ms-2">
                    <i class="bi bi-pencil-square me-1"></i>
                    {{ __('Edit User') }}
                </a>
                @endcan --}}
                </div>
            </div>
        </div>
    </div>
@endsection
