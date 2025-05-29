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
