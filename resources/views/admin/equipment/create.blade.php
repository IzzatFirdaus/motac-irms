{{-- resources/views/admin/equipment/create.blade.php --}}
@extends('layouts.app')

@section('title', __('Tambah Peralatan ICT Baharu'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-plus-circle-fill me-2"></i>{{ __('Tambah Peralatan ICT Baharu') }}
                    </h1>
                    <a href="{{ route('resource-management.equipment-admin.index') }}"
                        class="btn btn-sm btn-outline-secondary motac-btn-outline d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                    </a>
                </div>

                @include('_partials._alerts.alert-validation')

                <form action="{{ route('resource-management.equipment-admin.store') }}" method="POST"
                    class="needs-validation" novalidate>
                    @csrf
                    <div class="card shadow-sm motac-card">
                        <div class="card-header bg-light py-3 motac-card-header">
                            <h3 class="card-title h5 mb-0 fw-semibold">{{ __('Butiran Asas Peralatan') }}</h3>
                        </div>
                        <div class="card-body p-3 p-md-4 motac-card-body">
                            <div class="row g-3">
                                {{-- Asset Tag ID Field --}}
                                <div class="col-md-6">
                                    <label for="tag_id" class="form-label fw-medium">{{ __('No. Tag Aset') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="tag_id" id="tag_id" value="{{ old('tag_id') }}" required
                                        class="form-control form-control-sm @error('tag_id') is-invalid @enderror"
                                        placeholder="{{ __('Cth: MOTAC/BPM/ICT/LPT/2024/001') }}">
                                    @error('tag_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Asset Type Dropdown --}}
                                <div class="col-md-6">
                                    <label for="asset_type" class="form-label fw-medium">{{ __('Jenis Peralatan') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="asset_type" id="asset_type" required
                                        class="form-select form-select-sm @error('asset_type') is-invalid @enderror">
                                        <option value="" disabled selected>-- {{ __('Pilih Jenis') }} --</option>
                                        @foreach ($assetTypes ?? [] as $typeValue => $typeLabel)
                                            <option value="{{ $typeValue }}"
                                                {{ old('asset_type') == $typeValue ? 'selected' : '' }}>
                                                {{ __($typeLabel) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Brand Field --}}
                                <div class="col-md-6">
                                    <label for="brand" class="form-label fw-medium">{{ __('Jenama') }}</label>
                                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                                        class="form-control form-control-sm @error('brand') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Dell, HP, Acer, Apple') }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Model Field --}}
                                <div class="col-md-6">
                                    <label for="model" class="form-label fw-medium">{{ __('Model') }}</label>
                                    <input type="text" name="model" id="model" value="{{ old('model') }}"
                                        class="form-control form-control-sm @error('model') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Latitude 5420, ThinkPad X1 Carbon') }}">
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Serial Number Field --}}
                                <div class="col-md-6">
                                    <label for="serial_number" class="form-label fw-medium">{{ __('No. Siri') }}</label>
                                    <input type="text" name="serial_number" id="serial_number"
                                        value="{{ old('serial_number') }}"
                                        class="form-control form-control-sm @error('serial_number') is-invalid @enderror"
                                        placeholder="{{ __('Masukkan nombor siri unik') }}">
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Purchase Date Field --}}
                                <div class="col-md-6">
                                    <label for="purchase_date"
                                        class="form-label fw-medium">{{ __('Tarikh Pembelian') }}</label>
                                    <input type="date" name="purchase_date" id="purchase_date"
                                        value="{{ old('purchase_date') }}"
                                        class="form-control form-control-sm @error('purchase_date') is-invalid @enderror">
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Warranty Expiry Date Field --}}
                                <div class="col-md-6">
                                    <label for="warranty_expiry_date"
                                        class="form-label fw-medium">{{ __('Tarikh Tamat Waranti') }}</label>
                                    <input type="date" name="warranty_expiry_date" id="warranty_expiry_date"
                                        value="{{ old('warranty_expiry_date') }}"
                                        class="form-control form-control-sm @error('warranty_expiry_date') is-invalid @enderror">
                                    @error('warranty_expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Status (Operational) Dropdown --}}
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-medium">{{ __('Status Operasi') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="status" required
                                        class="form-select form-select-sm @error('status') is-invalid @enderror">
                                        <option value="" disabled
                                            {{ old('status', \App\Models\Equipment::STATUS_AVAILABLE) ? '' : 'selected' }}>
                                            -- {{ __('Pilih Status Operasi') }} --</option>
                                        @foreach ($statusOptions ?? [] as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}"
                                                {{ old('status', \App\Models\Equipment::STATUS_AVAILABLE) == $statusValue ? 'selected' : '' }}>
                                                {{ __($statusLabel) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Condition Status Dropdown --}}
                                <div class="col-md-6">
                                    <label for="condition_status"
                                        class="form-label fw-medium">{{ __('Status Kondisi Fizikal') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="condition_status" id="condition_status" required
                                        class="form-select form-select-sm @error('condition_status') is-invalid @enderror">
                                        <option value="" disabled
                                            {{ old('condition_status', \App\Models\Equipment::CONDITION_GOOD) ? '' : 'selected' }}>
                                            -- {{ __('Pilih Status Kondisi') }} --</option>
                                        @foreach ($conditionStatusOptions ?? [] as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}"
                                                {{ old('condition_status', \App\Models\Equipment::CONDITION_GOOD) == $statusValue ? 'selected' : '' }}>
                                                {{ __($statusLabel) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('condition_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Current Location Details Field --}}
                                <div class="col-md-6">
                                    <label for="current_location"
                                        class="form-label fw-medium">{{ __('Lokasi Semasa') }}</label>
                                    <input type="text" name="current_location" id="current_location"
                                        value="{{ old('current_location') }}"
                                        class="form-control form-control-sm @error('current_location') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Bilik Server BPM, Aras 10 Blok D') }}">
                                    @error('current_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Department Dropdown (Optional Owner) --}}
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label fw-medium">{{ __('Jabatan Pemilik') }}
                                        <small class="text-muted">({{ __('Jika ada') }})</small></label>
                                    <select name="department_id" id="department_id"
                                        class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                                        @isset($departments)
                                            @foreach ($departments as $departmentId => $departmentName)
                                                <option value="{{ $departmentId }}"
                                                    {{ old('department_id') == $departmentId ? 'selected' : '' }}>
                                                    {{ $departmentName }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="description"
                                        class="form-label fw-medium">{{ __('Keterangan Tambahan (Cth: Spesifikasi)') }}</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="form-control form-control-sm @error('description') is-invalid @enderror"
                                        placeholder="{{ __('Cth: CPU i5, 8GB RAM, 256GB SSD, Warna Perak') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label fw-medium">{{ __('Nota Dalaman') }}</label>
                                    <textarea name="notes" id="notes" rows="2"
                                        class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                        placeholder="{{ __('Sebarang nota atau catatan lain untuk rujukan pentadbir') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end bg-light py-3 border-top motac-card-footer">
                            <button type="submit"
                                class="btn btn-primary d-inline-flex align-items-center motac-btn-primary">
                                <i class="bi bi-save-fill me-2"></i> {{ __('Simpan Peralatan') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        // Example: Initialize Bootstrap validation or any other page-specific scripts
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })();
    </script>
@endpush
