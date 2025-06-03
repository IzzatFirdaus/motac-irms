{{-- resources/views/reports/equipment-inventory.blade.php --}}
@extends('layouts.app')

@section('title', __('Laporan Inventori Peralatan ICT'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-archive-fill me-2"></i>{{ __('Laporan Inventori Peralatan ICT') }}
            </h1>
            @if (Route::has('admin.reports.index'))
                <a href="{{ route('admin.reports.index') }}"
                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                    <i class="bi bi-arrow-left me-1"></i>
                    {{ __('Kembali ke Senarai Laporan') }}
                </a>
            @endif
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
            </div>
        @endif

        {{-- CORRECTED LINE: Using $equipmentList instead of $equipment --}}
        @if ($equipmentList->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                 <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada peralatan ICT ditemui untuk laporan ini.') }}
            </div>
        @else
            <div class="card shadow-sm motac-card">
                 <div class="card-header bg-light py-3 motac-card-header">
                    <h3 class="h5 card-title fw-semibold mb-0">{{__('Senarai Peralatan')}}</h3>
                </div>
                <div class="card-body p-0"> {{-- p-0 to make table flush --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tag ID Aset') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis Aset') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenama') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Model') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Siri') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Operasi') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status Kondisi') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pengguna Semasa') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Pinjam') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- CORRECTED LINE: Iterating over $equipmentList --}}
                                @foreach ($equipmentList as $item)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark fw-medium">{{ optional($item)->tag_id ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">{{ $item->asset_type_translated ?? (optional($item)->asset_type ? __(Str::title(str_replace('_',' ',optional($item)->asset_type))) : 'N/A') }}</td>
                                        <td class="px-3 py-2 small">{{ optional($item)->brand ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">{{ optional($item)->model ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small font-monospace">{{ optional($item)->serial_number ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($item->status ?? '', 'equipment_status') }} fw-normal">
                                                {{ $item->status_translated ?? (optional($item)->status ? __(Str::title(str_replace('_',' ',optional($item)->status))) : 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small">
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($item->condition_status ?? '', 'equipment_condition') }} fw-normal">
                                                {{ $item->condition_status_translated ?? (optional($item)->condition_status ? __(Str::title(str_replace('_',' ',optional($item)->condition_status))) : 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small">{{ optional(optional($item)->department)->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small">
                                            @if (optional($item)->activeLoanTransaction && optional(optional($item)->activeLoanTransaction->loanApplication)->user)
                                                {{ optional(optional($item)->activeLoanTransaction->loanApplication->user)->name ?? 'N/A' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small">
                                            @if (optional($item)->activeLoanTransaction)
                                                {{ optional(optional($item)->activeLoanTransaction)->issue_timestamp?->translatedFormat('d M Y') ?? 'N/A' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- CORRECTED LINE: Using $equipmentList for pagination --}}
                 @if ($equipmentList instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipmentList->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                        {{ $equipmentList->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
