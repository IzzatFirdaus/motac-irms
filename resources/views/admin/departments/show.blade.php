{{-- resources/views/equipment/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Peralatan: :name', ['name' => $equipment->brand . ' ' . $equipment->model]))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h2 fw-bold text-dark mb-0">
                    {{ __('Butiran Peralatan ICT') }}
                </h2>
                <a href="{{ route('equipment.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Peralatan') }}
                </a>
            </div>

            <x-alert-bootstrap /> {{-- For any session messages --}}

            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                     <h3 class="h5 card-title mb-0 fw-semibold">
                        {{ $equipment->brand ?? '' }} {{ $equipment->model ?? 'Peralatan ICT' }}
                        <small class="text-muted fs-6">(Tag: {{ $equipment->tag_id ?? 'N/A' }})</small>
                    </h3>
                </div>
                <div class="card-body p-3 p-md-4">
                    <dl class="row small g-3"> {{-- Definition list styling --}}
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenis Aset:') }}</dt>
                        <dd class="col-sm-8 text-dark">{{ $equipment->asset_type_label }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenama:') }}</dt>
                        <dd class="col-sm-8 text-dark">{{ $equipment->brand ?? '-' }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Model:') }}</dt>
                        <dd class="col-sm-8 text-dark">{{ $equipment->model ?? '-' }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Tag ID MOTAC:') }}</dt>
                        <dd class="col-sm-8 text-dark font-monospace">{{ $equipment->tag_id ?? '-' }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Nombor Siri:') }}</dt>
                        <dd class="col-sm-8 text-dark font-monospace">{{ $equipment->serial_number ?? '-' }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status Operasi:') }}</dt>
                        <dd class="col-sm-8 text-dark">
                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($equipment->status) }}">
                                {{ $equipment->status_label }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status Keadaan Fizikal:') }}</dt>
                        <dd class="col-sm-8 text-dark">
                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($equipment->condition_status) }}">
                                {{ $equipment->condition_status_label }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Lokasi Semasa (Jika Diketahui Umum):') }}</dt>
                        <dd class="col-sm-8 text-dark">{{ $equipment->current_location ?? '-' }}</dd>

                        @if($equipment->description) {{-- Show only if public description is available --}}
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Deskripsi Umum:') }}</dt>
                        <dd class="col-sm-8 text-dark" style="white-space: pre-wrap;">{{ $equipment->description }}</dd>
                        @endif

                        @if($equipment->notes && Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('BPM Staff'))) {{-- Show notes only to relevant roles --}}
                        <dt class="col-sm-4 fw-medium text-muted pt-2 border-top mt-2">{{ __('Catatan Tambahan (Admin):') }}</dt>
                        <dd class="col-sm-8 text-dark pt-2 border-top mt-2" style="white-space: pre-wrap;">{{ $equipment->notes }}</dd>
                        @endif
                    </dl>
                </div>
                {{-- No action buttons like 'Edit' for public view --}}
            </div>
        </div>
    </div>
</div>
@endsection
