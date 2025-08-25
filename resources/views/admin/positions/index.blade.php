{{-- resources/views/admin/positions/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Jawatan'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Jawatan') }}</h1>
            @can('create', App\Models\Position::class)
<<<<<<< HEAD
                {{-- Route name 'settings.positions.create' assumed based on settings pattern --}}
                <a href="{{ route('settings.positions.create') }}" class="btn btn-primary d-inline-flex align-items-center">
=======
                <a href="{{ route('admin.positions.create') }}" class="btn btn-primary d-inline-flex align-items-center">
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Jawatan Baru') }}
                </a>
            @endcan
        </div>

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

        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <h3 class="h5 card-title fw-semibold mb-0">
                    {{ __('Jawatan Berdaftar') }}
                </h3>
<<<<<<< HEAD
=======
                {{-- Add search/filter form here if needed --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
            </div>
            @if ($positions->isEmpty())
                <div class="card-body">
                    <div class="alert alert-info text-center mb-0" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada rekod jawatan ditemui.') }}
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Jawatan') }}
                                </th>
<<<<<<< HEAD
=======
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Kod') }}</th>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Gred Berkaitan') }}
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="min-width: 250px;">
                                    {{ __('Keterangan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium text-center px-3 py-2">
                                    {{ __('Status') }}</th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($positions as $position)
                                <tr>
                                    <td class="px-3 py-2 small text-dark fw-medium">
<<<<<<< HEAD
                                        {{-- Route name 'settings.positions.show' assumed --}}
                                        <a href="{{ route('settings.positions.show', $position) }}"
=======
                                        <a href="{{ route('admin.positions.show', $position) }}"
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                            class="text-decoration-none text-primary-emphasis">
                                            {{ $position->name ?? 'N/A' }}
                                        </a>
                                    </td>
<<<<<<< HEAD
=======
                                    <td class="px-3 py-2 small text-muted font-monospace">{{ $position->code ?? '-' }}</td>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                    <td class="px-3 py-2 small text-muted">{{ $position->grade->name ?? '-' }}</td>
                                    <td class="px-3 py-2 small text-muted" style="white-space: normal;">
                                        {{ Str::limit($position->description ?? '-', 70) }}
                                    </td>
                                    <td class="px-3 py-2 small text-center">
<<<<<<< HEAD
                                        {{-- FIX: Using high-contrast badges --}}
                                        @if ($position->is_active)
                                            <span class="badge rounded-pill text-bg-success">{{ __('Aktif') }}</span>
                                        @else
                                            <span class="badge rounded-pill text-bg-danger">{{ __('Tidak Aktif') }}</span>
=======
                                        @if ($position->is_active)
                                            <span
                                                class="badge rounded-pill bg-success-subtle text-success-emphasis">{{ __('Aktif') }}</span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-danger-subtle text-danger-emphasis">{{ __('Tidak Aktif') }}</span>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $position)
<<<<<<< HEAD
                                                {{-- Route name 'settings.positions.show' assumed --}}
                                                <a href="{{ route('settings.positions.show', $position) }}"
=======
                                                <a href="{{ route('admin.positions.show', $position) }}"
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                                    class="btn btn-sm btn-outline-secondary border-0 p-1"
                                                    title="{{ __('Lihat') }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            @endcan
                                            @can('update', $position)
<<<<<<< HEAD
                                                {{-- Route name 'settings.positions.edit' assumed --}}
                                                <a href="{{ route('settings.positions.edit', $position) }}"
=======
                                                <a href="{{ route('admin.positions.edit', $position) }}"
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                                    class="btn btn-sm btn-outline-primary border-0 p-1"
                                                    title="{{ __('Kemaskini') }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $position)
<<<<<<< HEAD
                                                {{-- Route name 'settings.positions.destroy' assumed --}}
                                                <form method="POST"
                                                    action="{{ route('settings.positions.destroy', $position) }}"
=======
                                                <form method="POST" action="{{ route('admin.positions.destroy', $position) }}"
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                                    onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam jawatan :name?', ['name' => $position->name]) }}');"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1"
                                                        title="{{ __('Padam') }}">
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
                @if ($positions->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3">
                        {{ $positions->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
