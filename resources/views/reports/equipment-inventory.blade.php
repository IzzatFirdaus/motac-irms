@extends('layouts.app')

@section('title', __('reports.equipment_inventory.title'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-tools me-2"></i>{{ __('reports.equipment_inventory.title') }}
            </h1>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i>{{ __('reports.back_to_list') }}
            </a>
        </div>

        @include('_partials._alerts.alert-general')

        {{-- Optional Filters --}}
        @isset($departments)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('reports.equipment-inventory') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="search"
                                    class="form-label small">{{ __('reports.filters.search_placeholder') }}</label>
                                <input type="text" name="search" id="search" class="form-control form-control-sm"
                                    placeholder="{{ __('reports.filters.search_placeholder') }}"
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status"
                                    class="form-label small">{{ __('reports.equipment_inventory.table.op_status') }}</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">{{ __('common.all') }}</option>
                                    @foreach ($statuses as $key => $label)
                                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="asset_type"
                                    class="form-label small">{{ __('reports.equipment_inventory.table.asset_type') }}</label>
                                <select name="asset_type" class="form-select form-select-sm">
                                    <option value="">{{ __('common.all') }}</option>
                                    @foreach ($assetTypes as $key => $label)
                                        <option value="{{ $key }}" @selected(request('asset_type') === $key)>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="department_id"
                                    class="form-label small">{{ __('reports.equipment_inventory.table.department') }}</label>
                                <select name="department_id" class="form-select form-select-sm">
                                    <option value="">{{ __('common.all') }}</option>
                                    @foreach ($departments as $id => $name)
                                        <option value="{{ $id }}" @selected(request('department_id') == $id)>{{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit"
                                    class="btn btn-sm btn-primary">{{ __('reports.filters.filter_button') }}</button>
                                <a href="{{ route('reports.equipment-inventory') }}"
                                    class="btn btn-sm btn-outline-secondary">{{ __('common.reset_search') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endisset

        @if ($equipmentList->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ __('reports.equipment_inventory.no_results') }}
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h3 class="h5 fw-semibold mb-0">{{ __('reports.equipment_inventory.list_header') }}</h3>
                    <small
                        class="text-muted">{{ __('Memaparkan :from-:to daripada :total rekod', ['from' => $equipmentList->firstItem(), 'to' => $equipmentList->lastItem(), 'total' => $equipmentList->total()]) }}</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('reports.equipment_inventory.table.asset_tag_id') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.asset_type') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.brand') }} &
                                    {{ __('reports.equipment_inventory.table.model') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.serial_no') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.op_status') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.condition_status') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.department') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.current_user') }}</th>
                                <th>{{ __('reports.equipment_inventory.table.loan_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipmentList as $item)
                                @php
                                    $transaction = $item->activeLoanTransactionItem?->loanTransaction;
                                @endphp
                                <tr>
                                    <td>{{ $item->tag_id }}</td>
                                    <td>{{ $item->asset_type_label }}</td>
                                    <td>{{ $item->brand }} {{ $item->model }}</td>
                                    <td>{{ $item->serial_number }}</td>
                                    <td><span
                                            class="badge {{ $item->status_color_class }}">{{ $item->status_label }}</span>
                                    </td>
                                    <td><span
                                            class="badge {{ $item->condition_color_class }}">{{ $item->condition_status_label }}</span>
                                    </td>
                                    <td>{{ $item->department->name ?? '-' }}</td>
                                    <td>{{ $transaction?->loanApplication?->user?->name ?? '-' }}</td>
                                    <td>{{ $transaction?->issue_timestamp?->translatedFormat('d M Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($equipmentList->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
