{{-- resources/views/equipment/index.blade.php --}}
@extends('layouts.app') {{-- Ensure Bootstrap 5 is loaded --}}

@section('title', __('Senarai Peralatan ICT Tersedia'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Peralatan ICT') }}</h2>
        {{-- No 'Tambah Baru' button for public view --}}
    </div>

    <x-alert-bootstrap /> {{-- For any session messages passed to this route --}}

    {{-- Filters could be added here if needed for public view --}}
    {{-- Example:
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('equipment.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Cari Peralatan...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="asset_type" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Jenis Aset') }}</option>
                            @foreach(App\Models\Equipment::$ASSET_TYPES_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ request('asset_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">{{ __('Cari') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    --}}

    @if ($equipment->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            {{ __('Tiada peralatan ICT ditemui yang sepadan dengan carian anda atau tersedia pada masa ini.') }}
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($equipment as $item)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    {{-- Optionally, add an image placeholder or actual image if you have one --}}
                    {{-- <img src="..." class="card-img-top" alt="{{ $item->name }}"> --}}
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title h6 fw-bold text-primary">{{ $item->brand ?? '' }} {{ $item->model ?? 'Peralatan ICT' }}</h5>
                        <p class="card-text small text-muted mb-1">
                            <strong>{{ __('Jenis Aset:') }}</strong> {{ $item->asset_type_label }}<br>
                            <strong>{{ __('Tag ID:') }}</strong> {{ $item->tag_id ?? __('N/A') }}
                        </p>
                        <div class="mt-auto">
                             <span class="badge rounded-pill {{ $item->status == App\Models\Equipment::STATUS_AVAILABLE ? 'text-bg-success' : 'text-bg-secondary' }} me-2">
                                {{ $item->status_label }}
                            </span>
                             <span class="badge rounded-pill text-bg-light">
                                {{ $item->condition_status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-center py-2">
                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> {{ __('Lihat Butiran') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if ($equipment->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $equipment->links() }} {{-- Ensure Bootstrap pagination styling --}}
            </div>
        @endif
    @endif
</div>
@endsection
