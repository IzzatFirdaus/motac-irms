<<<<<<< HEAD
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
=======
{{--
    resources/views/reports/equipment-inventory.blade.php (Refactored to Bootstrap 5)

    This Blade view file displays a report of ICT Equipment.
    - Includes Bootstrap CSS via CDN.
    - Extends 'layouts.app' and places content within @section('content').
    - Uses Bootstrap 5 classes for styling.
--}}

@extends('layouts.app') {{-- Assuming this layout includes Bootstrap JS and handles <html>, <head>, <body> tags --}}

@section('title', __('Laporan Peralatan ICT'))

{{-- If layouts.app doesn't include Bootstrap CSS, you'd push it to a stack or include it here,
     but typically the main layout handles this. For this specific file's original structure,
     I will assume layouts.app needs it, or if this should be standalone, it would be in <head>.
     For @extends, it implies layouts.app is the master.
--}}

@push('styles')
    {{-- If Bootstrap is not globally available from layouts.app, uncomment this: --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> --}}
@endpush

@section('content')
    {{-- Main container for the content. --}}
    <div class="container-fluid px-lg-4 py-4">

        {{-- Page Title --}}
        <h1 class="h2 fw-bold mb-4">{{ __('Laporan Peralatan ICT') }}</h1>

        {{-- Display success messages --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Display error messages --}}
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Table to display equipment for the report --}}
        @if ($equipment->isEmpty())
            <div class="alert alert-info" role="alert">
                {{ __('Tiada peralatan ICT ditemui untuk laporan ini.') }}
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-uppercase small">{{ __('Asset Tag ID') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Jenis Aset') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Brand') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Model') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Nombor Siri') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Status Ketersediaan') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Status Kondisi') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Jabatan') }}</th>
                                    <th scope="col" class="text-uppercase small">{{ __('Jawatan') }}</th>
                                    <th scope="col" class="text-uppercase small">
                                        {{ __('Pengguna Semasa (Jika Dipinjam)') }}</th>
                                    <th scope="col" class="text-uppercase small">
                                        {{ __('Tarikh Pinjaman (Jika Dipinjam)') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipment as $item)
                                    <tr>
                                        <td>{{ optional($item)->tag_id ?? 'N/A' }}</td>
                                        <td>{{ optional($item)->asset_type ?? 'N/A' }}</td>
                                        <td>{{ optional($item)->brand ?? 'N/A' }}</td>
                                        <td>{{ optional($item)->model ?? 'N/A' }}</td>
                                        <td>{{ optional($item)->serial_number ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $availabilityStatusValue = optional($item)->availability_status;
                                                $availabilityStatusClassBootstrap = match ($availabilityStatusValue) {
                                                    'available' => 'badge bg-success',
                                                    'on_loan' => 'badge bg-warning text-dark',
                                                    'under_maintenance' => 'badge bg-info text-dark',
                                                    'disposed' => 'badge bg-secondary',
                                                    'lost', 'damaged' => 'badge bg-danger',
                                                    default => 'badge bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="{{ $availabilityStatusClassBootstrap }} rounded-pill">
                                                {{ __(optional($item)->availability_status_translated ?? ucfirst($availabilityStatusValue ?? 'N/A')) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $conditionStatusValue = optional($item)->condition_status;
                                                $conditionStatusClassBootstrap = match ($conditionStatusValue) {
                                                    'Good', 'new' => 'badge bg-success', // Assuming 'new' is also good
                                                    'Fine',
                                                    'fair'
                                                        => 'badge bg-warning text-dark', // Assuming 'fair' is like 'Fine'
                                                    'Bad',
                                                    'Damaged',
                                                    'minor_damage',
                                                    'major_damage',
                                                    'unserviceable'
                                                        => 'badge bg-danger',
                                                    'lost' => 'badge bg-dark', // Differentiating lost from damaged
                                                    default => 'badge bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="{{ $conditionStatusClassBootstrap }} rounded-pill">
                                                {{ __(optional($item)->condition_status_translated ?? ucfirst($conditionStatusValue ?? 'N/A')) }}
                                            </span>
                                        </td>
                                        <td>{{ optional(optional($item)->department)->name ?? 'N/A' }}</td>
                                        <td>{{ optional(optional($item)->position)->name ?? 'N/A' }}</td>
                                        <td>
                                            @if (optional($item)->activeLoanTransaction && optional(optional($item)->activeLoanTransaction)->user)
                                                {{ optional(optional($item)->activeLoanTransaction)->user->full_name ?? (optional(optional($item)->activeLoanTransaction)->user->name ?? 'N/A') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if (optional($item)->activeLoanTransaction)
                                                {{ optional(optional($item)->activeLoanTransaction)->issue_timestamp?->format('Y-m-d H:i') ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination links (only if using paginate() in controller) --}}
            {{-- @if ($equipment instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipment->hasPages()) --}}
            {{--    <div class="mt-4 d-flex justify-content-center"> --}}
            {{--        {{ $equipment->links() }} --}}
            {{--    </div> --}}
            {{-- @endif --}}
        @endif

        {{-- Optional: Back button (if needed and not handled by layout breadcrumbs) --}}
        {{--
        <div class="mt-4 text-center">
             <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                  <i class="ti ti-arrow-left me-1"></i>
                  {{ __('Kembali ke Laporan') }}
              </a>
        </div>
        --}}
    </div>
@endsection

@push('scripts')
    {{-- If Bootstrap JS is not globally available from layouts.app and needed for components like dropdowns, modals, etc. --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> --}}
@endpush
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
