{{-- resources/views/my-applications/loan/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Permohonan Pinjaman Peralatan ICT Saya'))

@section('content')
    <div class="container py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Permohonan Pinjaman Saya') }}</h1>
            <a href="{{ route('loan-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ __('Buat Permohonan Baru') }}
            </a>
        </div>

        @include('partials.alert-messages')

        @if ($applications->isEmpty())
            <div class="alert alert-info text-center shadow-sm" role="alert">
                <div class="d-flex flex-column align-items-center">
                    <i class="bi bi-folder-x fs-1 text-secondary mb-2"></i>
                    <h5 class="alert-heading fw-semibold">{{ __('Tiada Permohonan Ditemui') }}</h5>
                    <p class="mb-0">{{ __('Anda belum membuat sebarang permohonan pinjaman peralatan ICT.') }}</p>
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 card-title fw-semibold mb-0">{{ __('Sejarah Permohonan Pinjaman') }}</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">ID</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tujuan Permohonan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tarikh Pinjaman') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tarikh Dijangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Status') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                        {{ __('Tarikh Hantar/Kemas Kini') }}</th>
                                    <th scope="col"
                                        class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">
                                        {{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $app)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark fw-medium">#{{ $app->id }}</td>
                                        <td class="px-3 py-2 small text-dark">
                                            {{ Str::limit($app->purpose, 45) }}</td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional($app->loan_start_date)->translatedFormat('d M Y, H:i') ?? __('N/A') }}</td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ optional($app->loan_end_date)->translatedFormat('d M Y, H:i') ?? __('N/A') }}</td>
                                        <td class="px-3 py-2 small">
                                            <x-loan-application-status-badge :status="$app->status" />
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{ $app->updated_at->translatedFormat('d M Y, H:i') }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="{{__('Tindakan Permohonan')}}">
                                                <a href="{{ route('loan-applications.show', $app) }}"
                                                    class="btn btn-outline-primary border-end-0" {{-- Removed border-0 for group --}}
                                                    title="{{ __('Lihat Butiran') }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                @can('update', $app) {{-- Policy check [cite: 330] --}}
                                                    <a href="{{ route('loan-applications.edit', $app) }}"
                                                        class="btn btn-outline-secondary border-end-0"
                                                        title="{{ __('Kemaskini Draf') }}">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $app) {{-- Policy check [cite: 330] --}}
                                                    <form action="{{ route('loan-applications.destroy', $app) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('{{ __('Adakah anda pasti untuk memadam draf permohonan ini?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger"
                                                            title="{{ __('Padam Draf') }}">
                                                            <i class="bi bi-trash3-fill"></i>
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
                </div>
            </div>

            @if ($applications->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
