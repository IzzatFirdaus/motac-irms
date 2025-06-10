@extends('layouts.app')

@section('title', __('reports.inventory_report_title'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-archive-fill me-2"></i>{{ __('reports.inventory_report_title') }}
            </h1>
            @if (Route::has('reports.index'))
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i>
                    {{ __('reports.back_to_reports') }}
                </a>
            @endif
        </div>

        @include('_partials._alerts.alert-general')

        {{-- ADDED: Filter and Search Section --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('reports.equipment-inventory') }}" method="GET" class="needs-validation">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label small">{{ __('Carian Kata Kunci') }}</label>
                            <input type="text" name="search" id="search" class="form-control form-control-sm"
                                placeholder="{{ __('reports.search_placeholder') }}" value="{{ $request->input('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label small">{{ __('reports.op_status') }}</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ $request->input('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="asset_type" class="form-label small">{{ __('reports.asset_type') }}</label>
                            <select name="asset_type" id="asset_type" class="form-select form-select-sm">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach ($assetTypes as $value => $label)
                                    <option value="{{ $value }}" {{ $request->input('asset_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                             <label for="department_id" class="form-label small">{{ __('reports.department') }}</label>
                             <select name="department_id" id="department_id" class="form-select form-select-sm">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($departments as $id => $name)
                                    <option value="{{ $id }}" {{ $request->input('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                             </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-sm btn-primary w-100">{{ __('Tapis') }}</button>
                            <a href="{{ route('reports.equipment-inventory') }}" class="btn btn-sm btn-link text-muted w-100">{{ __('common.reset_search') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($equipmentList->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                 <i class="bi bi-info-circle-fill me-2"></i>{{ __('reports.no_equipment_for_report') }}
            </div>
        @else
            <div class="card shadow-sm">
                 <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h3 class="h5 card-title fw-semibold mb-0">{{__('reports.equipment_list')}}</h3>
                    <small class="text-muted">{{ __('Memaparkan :from-:to daripada :total rekod', ['from' => $equipmentList->firstItem(), 'to' => $equipmentList->lastItem(), 'total' => $equipmentList->total()]) }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.asset_tag_id') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.asset_type') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.brand') }} & {{ __('reports.model') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.op_status') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.condition_status') }}</th>
                                    <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('reports.current_user') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipmentList as $item)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark fw-medium font-monospace">{{ $item->tag_id ?? __('common.not_available') }}</td>
                                        <td class="px-3 py-2 small">{{ $item->asset_type_label }}</td>
                                        <td class="px-3 py-2 small">{{ $item->brand ?? '' }} {{ $item->model ?? '' }}</td>
                                        <td class="px-3 py-2 small">
                                            <span class="badge rounded-pill {{ $item->status_color_class }} fw-normal">{{ $item->status_label }}</span>
                                        </td>
                                        <td class="px-3 py-2 small">
                                            <span class="badge rounded-pill {{ $item->condition_color_class }} fw-normal">{{ $item->condition_status_label }}</span>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">{{ $item->activeLoanTransactionItem?->loanTransaction?->loanApplication?->user?->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
