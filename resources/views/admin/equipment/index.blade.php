{{-- resources/views/admin/equipment/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Peralatan ICT MOTAC'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
                <i class="bi bi-hdd-stack-fill me-2"></i>{{ __('Senarai Peralatan ICT') }}
            </h1>
            {{-- ADJUSTMENT: Added @can directive to protect this action button --}}
            @can('create', App\Models\Equipment::class)
                <a href="{{ route('resource-management.equipment-admin.create') }}"
                    class="btn btn-primary d-inline-flex align-items-center motac-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan Baharu') }}
                </a>
            @endcan
        </div>

        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm motac-card">
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 motac-card-header">
                <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-list-ul me-2"></i>{{ __('Inventori Peralatan') }}
                </h3>
                <form action="{{ route('resource-management.equipment-admin.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" style="min-width: 250px;"
                        placeholder="{{ __('Cari No. Tag, Jenama, Model...') }}" value="{{ request('search') }}"
                        aria-label="{{ __('Carian Peralatan') }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary motac-btn-outline d-inline-flex align-items-center">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request('search'))
                        <a href="{{ route('resource-management.equipment-admin.index') }}"
                            class="btn btn-sm btn-outline-secondary motac-btn-outline" title="{{ __('Set Semula Carian') }}">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="card-body p-0 motac-card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Tag Aset') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis & Model') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Status') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dipinjam Oleh') }}</th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipmentList as $item)
                                <tr>
                                    <td class="px-3 py-2 small text-dark fw-medium">
                                        <a href="{{ route('resource-management.equipment-admin.show', $item) }}" class="text-decoration-none text-primary-emphasis motac-table-link">
                                            {{ $item->tag_id ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        <span class="fw-medium text-dark">{{ $item->asset_type_label ?? 'N/A' }}</span>
                                        <div>{{ $item->brand ?? '' }} {{ $item->model ?? '' }}</div>
                                    </td>
                                    <td class="px-3 py-2 small text-center"><x-equipment-status-badge :status="$item->status" /></td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->department->name ?? __('Umum') }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($item->activeLoanTransactionItem?->loanTransaction?->loanApplication?->user)
                                            <a href="{{ route('settings.users.show', $item->activeLoanTransactionItem->loanTransaction->loanApplication->user) }}" class="text-decoration-none motac-table-link">
                                                {{ $item->activeLoanTransactionItem->loanTransaction->loanApplication->user->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $item)
                                                <a href="{{ route('resource-management.equipment-admin.show', $item) }}" class="btn btn-sm btn-icon btn-outline-info border-0 motac-btn-icon" title="{{ __('Lihat Butiran') }}"><i class="bi bi-eye-fill"></i></a>
                                            @endcan
                                            @can('update', $item)
                                                <a href="{{ route('resource-management.equipment-admin.edit', $item) }}" class="btn btn-sm btn-icon btn-outline-primary border-0 motac-btn-icon" title="{{ __('Kemaskini') }}"><i class="bi bi-pencil-fill"></i></a>
                                            @endcan
                                            @can('delete', $item)
                                                <form action="{{ route('resource-management.equipment-admin.destroy', $item) }}" method="POST" onsubmit="return confirm('{{ __('Amaran: Anda pasti ingin memadam peralatan ini (:tagId)? Tindakan ini tidak boleh diundur.', ['tagId' => $item->tag_id]) }}');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger border-0 motac-btn-icon" title="{{ __('Padam') }}"><i class="bi bi-trash3-fill"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-5 text-center">
                                        <div class="d-flex flex-column align-items-center text-muted small">
                                            <i class="bi bi-hdd-rack-fill fs-1 text-secondary mb-2"></i>
                                            <p class="mb-1">{{ __('Tiada rekod peralatan ICT ditemui.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($equipmentList instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipmentList->hasPages())
                    <div class="card-footer bg-light border-top py-3 motac-card-footer">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
