{{-- resources/views/admin/equipment/index.blade.php --}}
@php
    use App\Helpers\Helpers;
@endphp
@extends('layouts.app')

@section('title', __('Senarai Peralatan ICT'))

@section('content')
    <div class="container-fluid py-4"> {{-- Using container-fluid for wider table display --}}

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">{{ __('Senarai Peralatan ICT') }}</h1>
            @can('create equipment')
                <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan Baru') }}
                </a>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <h3 class="h5 card-title fw-semibold mb-0">
                    {{ __('Peralatan Sedia Ada') }}
                </h3>
                {{-- Consider adding a search/filter form here if not already part of a Livewire component --}}
                {{-- Example:
            <form action="{{ route('admin.equipment.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Cari No. Tag, Jenama, Model...') }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Cari') }}</button>
            </form>
            --}}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Tag Aset') }}
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis Aset') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenama') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Model') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Operasi') }}
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Kondisi') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dipinjam Oleh') }}
                                </th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipment as $item)
                                <tr>
                                    <td class="px-3 py-2 small text-dark fw-medium">
                                        <a href="{{ route('admin.equipment.show', $item) }}"
                                            class="text-decoration-none text-primary-emphasis">
                                            {{ $item->tag_id ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $item->asset_type_translated ?? ($item->asset_type ? __(Str::title(str_replace('_', ' ', $item->asset_type))) : 'N/A') }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->brand ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->model ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">
                                        {{-- Use a Blade component or a helper for consistent status display if possible --}}
                                        <span
                                            class="badge rounded-pill {{ Helpers::getStatusColorClass($item->status ?? '') }}">
                                            {{ $item->status_translated ?? ($item->status ? __(Str::title(str_replace('_', ' ', $item->status))) : 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small">
                                        <span
                                            class="badge rounded-pill {{ Helpers::getStatusColorClass($item->condition_status ?? '') }}">
                                            {{ $item->condition_status_translated ?? ($item->condition_status ? __(Str::title(str_replace('_', ' ', $item->condition_status))) : 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->department->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($item->activeLoanTransaction?->loanApplication?->user)
                                            <a href="{{ route('resource-management.admin.users.show', $item->activeLoanTransaction->loanApplication->user) }}"
                                                class="text-decoration-none">
                                                {{ $item->activeLoanTransaction->loanApplication->user->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view equipment', $item)
                                                <a href="{{ route('admin.equipment.show', $item) }}"
                                                    class="btn btn-sm btn-outline-secondary border-0 p-1"
                                                    title="{{ __('Lihat') }}"><i class="bi bi-eye-fill"></i></a>
                                            @endcan
                                            @can('update equipment', $item)
                                                <a href="{{ route('admin.equipment.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-primary border-0 p-1"
                                                    title="{{ __('Kemaskini') }}"><i class="bi bi-pencil-fill"></i></a>
                                            @endcan
                                            @can('delete equipment', $item)
                                                <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam peralatan ini: :tagId?', ['tagId' => $item->tag_id]) }}');"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1"
                                                        title="{{ __('Padam') }}"><i class="bi bi-trash3-fill"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-5 text-center">
                                        <div class="d-flex flex-column align-items-center text-muted small">
                                            <i class="bi bi-hdd-stack-fill fs-1 text-secondary mb-2"></i>
                                            {{ __('Tiada rekod peralatan ICT ditemui.') }}
                                            @if (request('search'))
                                                {{ __('Cuba kata kunci carian yang berbeza.') }}
                                            @else
                                                {{ __('Sila tambah peralatan baru.') }}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($equipment instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipment->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3">
                        {{ $equipment->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
