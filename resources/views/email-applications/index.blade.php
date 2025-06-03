{{-- resources/views/my-applications/email/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Permohonan E-mel Saya'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="fs-3 fw-bold mb-0">{{ __('Senarai Permohonan E-mel Saya') }}</h1>
            {{-- Ensure this route name is correct and exists in web.php --}}
            <a href="{{ route('email-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ __('Buat Permohonan Baru') }}
            </a>
        </div>

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

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h2 class="h5 card-title fw-semibold mb-0">{{ __('Permohonan Saya') }}</h2>
            </div>
            @if ($applications->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-folder-x fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Permohonan') }}</h5>
                    <p class="small">{{ __('Anda belum membuat sebarang permohonan e-mel / ID pengguna lagi.') }}</p>
                    <a href="{{ route('email-applications.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        {{ __('Mohon Sekarang') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                    {{ __('ID') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                    {{ __('Tujuan / Catatan') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                    {{ __('Status') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                    {{ __('E-mel Dicadang/Diluluskan') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">
                                    {{ __('Tarikh Hantar') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium text-end">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                <tr>
                                    <td class="py-2 px-3 small text-dark">#{{ $app->id }}</td>
                                    <td class="py-2 px-3 small text-muted">
                                        {{ Str::limit(e($app->purpose ?? $app->application_reason_notes), 50) }}</td>
                                    <td class="py-2 px-3 small">
                                        {{-- Assuming you have a status badge component or a helper --}}
                                        <x-email-application-status-badge :status="$app->status" />
                                    </td>
                                    <td class="py-2 px-3 small text-muted">
                                        {{ e($app->final_assigned_email ?? ($app->proposed_email ?? ($app->group_email ?? '-'))) }}
                                    </td>
                                    <td class="py-2 px-3 small text-muted">
                                        {{ $app->created_at->translatedFormat('d M Y, H:i') }}</td>
                                    <td class="py-2 px-3 text-end">
                                        {{-- Ensure 'email-applications.show' and 'email-applications.edit' are the correct route names --}}
                                        <a href="{{ route('email-applications.show', $app->id) }}"
                                            class="btn btn-sm btn-outline-primary border-0 p-1"
                                            title="{{ __('Lihat Butiran') }}">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @if ($app->isDraft())
                                            {{-- Assuming an isDraft() method on the model --}}
                                            <a href="{{ route('email-applications.edit', $app->id) }}"
                                                class="btn btn-sm btn-outline-secondary border-0 p-1 ms-1"
                                                title="{{ __('Edit Draf') }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($applications->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3 d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
