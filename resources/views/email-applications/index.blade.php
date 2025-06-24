{{-- resources/views/email-applications/index.blade.php --}}
@extends('layouts.app') {{-- Assuming a main layout file, adjust if needed --}}

@section('title', __('Senarai Permohonan E-mel Saya'))

@section('content')
    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="fs-3 fw-bold mb-0">{{ __('Senarai Permohonan E-mel Saya') }}</h1>
            {{-- The create route is handled by a Livewire component, so we link to that page --}}
            <a href="{{ route('email-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ __('Buat Permohonan Baru') }}
            </a>
        </div>

        {{-- Session-based alerts for user feedback --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Main Content Card --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h2 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-envelope-paper-fill me-2"></i>
                    {{ __('Sejarah Permohonan') }}
                </h2>
            </div>

            @if ($applications->isEmpty())
                {{-- Empty State: Displayed when no applications are found --}}
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-folder-x fs-1 text-secondary mb-3"></i>
                    <h5 class="mb-1">{{ __('Tiada Permohonan Ditemui') }}</h5>
                    <p class="small">{{ __('Anda belum membuat sebarang permohonan e-mel atau ID pengguna lagi.') }}</p>
                    <a href="{{ route('email-applications.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        {{ __('Mohon Sekarang') }}
                    </a>
                </div>
            @else
                {{-- Applications Table --}}
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium" style="width: 80px;">{{ __('#ID') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Tujuan / Catatan') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium" style="width: 15%;">{{ __('Status') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('E-mel Dicadang / Diluluskan') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium" style="width: 15%;">{{ __('Tarikh Hantar') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium text-end" style="width: 120px;">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                <tr>
                                    {{-- Application ID --}}
                                    <td class="py-2 px-3 fw-bold text-dark">#{{ $app->id }}</td>

                                    {{-- Purpose/Notes from the application_reason_notes field --}}
                                    <td class="py-2 px-3 small text-muted">
                                        {{ Str::limit(e($app->application_reason_notes), 60) ?: '-' }}
                                    </td>

                                    {{-- Status Badge (uses the model accessor for color and label) --}}
                                    <td class="py-2 px-3 small">
                                        <x-email-application-status-badge :application="$app" />
                                    </td>

                                    [cite_start]{{-- Proposed/Assigned Email. [cite: 103] --}}
                                    <td class="py-2 px-3 small text-muted">
                                        {{ e($app->final_assigned_email ?? $app->proposed_email ?? $app->group_email ?? '-') }}
                                    </td>

                                    {{-- Submission Date (created_at). Uses the global app config for formatting to prevent errors. [cite: 92] --}}
                                    <td class="py-2 px-3 small text-muted">
                                        {{ $app->created_at->format(config('app.datetime_format_my', 'd M Y, h:i A')) }}
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="py-2 px-3 text-end">
                                        <div class="d-inline-flex">
                                            {{-- View Details Button --}}
                                            <a href="{{ route('email-applications.show', $app->id) }}" class="btn btn-sm btn-outline-primary border-0 p-1" title="{{ __('Lihat Butiran') }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            {{-- Edit button is only shown if authorized by the EmailApplicationPolicy --}}
                                            @can('update', $app)
                                                <a href="{{ route('email-applications.edit', $app->id) }}" class="btn btn-sm btn-outline-secondary border-0 p-1 ms-1" title="{{ __('Edit Draf') }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan

                                            {{-- Delete button is only shown if authorized by the EmailApplicationPolicy --}}
                                            @can('delete', $app)
                                                <form action="{{ route('email-applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam draf permohonan ini?') }}');" class="ms-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="{{ __('Padam Draf') }}">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if ($applications->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3 d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
