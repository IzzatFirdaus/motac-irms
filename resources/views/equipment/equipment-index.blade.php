{{-- resources/views/equipment/equipment-index.blade.php --}}
{{-- Public-facing Equipment Inventory Index. Renamed from index.blade.php for clarity and consistency. --}}
@extends('layouts.app')

@section('title', __('Senarai Peralatan ICT'))

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="fs-2 fw-bold text-dark mb-0">{{ __('Senarai Peralatan ICT Tersedia') }}</h1>
        {{-- "Tambah Peralatan Baru" button is intentionally removed for public user view --}}
        {{-- Admin creation is handled via resource-management.equipment-admin.create route --}}
    </div>

    {{-- General alert partial for flash messages, validation, etc. --}}
    @include('_partials._alerts.alert-general')

    {{-- Show info alert if no equipment is available --}}
    @if (!isset($equipmentList) || $equipmentList->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            {{ __('Tiada peralatan ICT ditemui dalam inventori pada masa ini.') }}
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                 <h2 class="h5 card-title fw-semibold mb-0">{{ __('Inventori Peralatan') }}</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Jenis Aset') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Jenama & Model') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Tag ID MOTAC') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Nombor Siri') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Status') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Lokasi Semasa') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium text-end">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipmentList as $item)
                                <tr>
                                    <td class="py-2 px-3 small">{{ e($item->asset_type_label ?? $item->asset_type) }}</td>
                                    <td class="py-2 px-3 small">{{ e($item->brand ?? 'N/A') }} {{ e($item->model ?? '') }}</td>
                                    <td class="py-2 px-3 small font-monospace">{{ e($item->tag_id ?? 'N/A') }}</td>
                                    <td class="py-2 px-3 small font-monospace">{{ e($item->serial_number ?? 'N/A') }}</td>
                                    <td class="py-2 px-3 small">
                                        <x-equipment-status-badge :status="$item->status" />
                                    </td>
                                    <td class="py-2 px-3 small">{{ e($item->current_location ?? 'N/A') }}</td>
                                    <td class="py-2 px-3 text-end">
                                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                            <i class="bi bi-eye-fill me-1"></i>{{ __('Lihat') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination if available --}}
        @if ($equipmentList->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $equipmentList->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
