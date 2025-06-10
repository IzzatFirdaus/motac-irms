{{-- resources/views/admin/equipment/index.blade.php --}}
@extends('layouts.app')

@section('title', __('equipment.list_title'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
                <i class="bi bi-hdd-stack-fill me-2"></i>{{ __('equipment.list_title') }}
            </h1>
            @can('create', App\Models\Equipment::class)
                <a href="{{ route('resource-management.equipment-admin.create') }}"
                    class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('equipment.add_new_title') }}
                </a>
            @endcan
        </div>

        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-list-ul me-2"></i>{{ __('equipment.inventory_title') }}
                </h3>
                <form action="{{ route('resource-management.equipment-admin.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" style="min-width: 250px;"
                        placeholder="{{ __('equipment.search_placeholder') }}" value="{{ request('search') }}"
                        aria-label="{{ __('equipment.search_placeholder') }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request('search'))
                        <a href="{{ route('resource-management.equipment-admin.index') }}"
                            class="btn btn-sm btn-outline-secondary" title="{{ __('common.reset_search') }}">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('equipment.asset_tag') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('equipment.type_and_model') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('common.status') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('common.department') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('equipment.loaned_to') }}</th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipmentList as $item)
                                <tr>
                                    <td class="px-3 py-2 small text-dark fw-medium">
                                        <a href="{{ route('resource-management.equipment-admin.show', $item) }}" class="text-decoration-none">{{ $item->tag_id ?? __('common.not_available') }}</a>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        <span class="fw-medium text-dark">{{ $item->asset_type_label ?? __('common.not_available') }}</span>
                                        <div>{{ $item->brand ?? '' }} {{ $item->model ?? '' }}</div>
                                    </td>
                                    <td class="px-3 py-2 small text-center"><x-equipment-status-badge :status="$item->status" /></td>
                                    <td class="px-3 py-2 small text-muted">{{ $item->department->name ?? __('Umum') }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($user = $item->activeLoanTransactionItem?->loanTransaction?->loanApplication?->user)
                                            <a href="{{ route('settings.users.show', $user) }}" class="text-decoration-none">{{ $user->name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $item)
                                                <a href="{{ route('resource-management.equipment-admin.show', $item) }}" class="btn btn-sm btn-icon btn-outline-info border-0" title="{{ __('equipment.view_details') }}"><i class="bi bi-eye-fill"></i></a>
                                            @endcan
                                            @can('update', $item)
                                                <a href="{{ route('resource-management.equipment-admin.edit', $item) }}" class="btn btn-sm btn-icon btn-outline-primary border-0" title="{{ __('common.update') }}"><i class="bi bi-pencil-fill"></i></a>
                                            @endcan
                                            @can('delete', $item)
                                                <form action="{{ route('resource-management.equipment-admin.destroy', $item) }}" method="POST" onsubmit="return confirm('{{ __('equipment.delete_confirm', ['tagId' => $item->tag_id]) }}');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('common.delete') }}"><i class="bi bi-trash3-fill"></i></button>
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
                                            <p class="mb-1">{{ __('equipment.no_records_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($equipmentList instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipmentList->hasPages())
                    <div class="card-footer bg-light border-top py-3">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
