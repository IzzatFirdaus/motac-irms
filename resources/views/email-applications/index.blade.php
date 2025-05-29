{{-- resources/views/my-applications/email/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Permohonan E-mel ICT'))

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 fw-bold mb-0">{{ __('Senarai Permohonan E-mel ICT') }}</h1>
            <a href="{{ route('resource-management.email-applications.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-2" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                </svg>
                {{ __('Permohonan Baru') }}
            </a>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($applications->isEmpty())
            <div class="alert alert-info" role="alert">
                {{ __('Tiada permohonan e-mel ICT ditemui.') }}
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0"> {{-- p-0 to make table flush with card edges --}}
                    <div class="table-responsive">
                        <table class="table table-hover mb-0"> {{-- Removed table-striped for manual hover effect or simplicity --}}
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="py-3 px-3 text-start text-uppercase small text-muted">{{ __('Tujuan Permohonan') }}</th>
                                    <th scope="col" class="py-3 px-3 text-start text-uppercase small text-muted">{{ __('Status') }}</th>
                                    <th scope="col" class="py-3 px-3 text-start text-uppercase small text-muted">{{ __('Cadangan E-mel / E-mel Akhir') }}</th>
                                    <th scope="col" class="py-3 px-3 text-start text-uppercase small text-muted">{{ __('Tarikh Hantar') }}</th>
                                    <th scope="col" class="py-3 px-3 text-start text-uppercase small text-muted">{{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $app)
                                    <tr>
                                        <td class="py-3 px-3 align-middle">{{ Str::limit($app->purpose, 50) }}</td>
                                        <td class="py-3 px-3 align-middle">
                                            <span class="badge rounded-pill {{ match ($app->status) {
                                                'draft' => 'bg-secondary',
                                                'pending_support', 'pending_admin', 'processing' => 'bg-warning text-dark',
                                                'approved' => 'bg-info text-dark',
                                                'completed' => 'bg-success',
                                                'rejected', 'provision_failed' => 'bg-danger',
                                                default => 'bg-light text-dark',
                                            } }}">
                                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 align-middle">{{ $app->final_assigned_email ?? ($app->proposed_email ?? '-') }}</td>
                                        <td class="py-3 px-3 align-middle">{{ $app->created_at->format('d M Y') }}</td>
                                        <td class="py-3 px-3 align-middle">
                                            <a href="{{ route('my-applications.email.show', $app->id) }}" class="btn btn-sm btn-outline-primary me-1">{{ __('Lihat') }}</a>
                                            @if ($app->status === 'draft')
                                                {{-- The edit route for draft should probably be the same component as create but with ID --}}
                                                {{-- Assuming resource-management.email-applications.edit route exists --}}
                                                <a href="{{ route('resource-management.email-applications.edit', $app->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($applications->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $applications->links() }} {{-- Laravel pagination should render Bootstrap-compatible links --}}
                </div>
            @endif
        @endif
    </div>
@endsection
