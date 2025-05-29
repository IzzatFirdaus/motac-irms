{{-- resources/views/admin/equipment/create.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('equipment.add_new_title'))
=======
@section('title', __('Tambah Peralatan Baru'))
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
<<<<<<< HEAD
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-plus-circle-fill me-2"></i>{{ __('equipment.add_new_title') }}
                    </h1>
                    <a href="{{ route('resource-management.equipment-admin.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('equipment.back_to_list') }}
                    </a>
                </div>

                {{-- CORRECTED: Use the single, correct partial for all session and validation messages. --}}
                @include('_partials._alerts.alert-general')

                <form action="{{ route('resource-management.equipment-admin.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold">{{ __('equipment.basic_details') }}</h3>
=======

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Tambah Peralatan Baru') }}
                    </h1>
                    <a href="{{ route('admin.equipment.index') }}"
                        class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                    </a>
                </div>


                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading fw-bold">{{ __('Sila perbetulkan ralat di bawah:') }}</h5>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li><small>{{ $error }}</small></li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.equipment.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold">{{ __('Butiran Peralatan') }}</h3>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3">
                                {{-- Asset Tag ID Field --}}
                                <div class="col-md-6">
<<<<<<< HEAD
                                    <label for="tag_id" class="form-label fw-medium">{{ __('equipment.asset_tag') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="tag_id" id="tag_id" value="{{ old('tag_id') }}" required class="form-control form-control-sm @error('tag_id') is-invalid @enderror" placeholder="{{ __('equipment.asset_tag_placeholder') }}">
                                    @error('tag_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Asset Type Dropdown --}}
                                <div class="col-md-6">
                                    <label for="asset_type" class="form-label fw-medium">{{ __('equipment.asset_type') }} <span class="text-danger">*</span></label>
                                    <select name="asset_type" id="asset_type" required class="form-select form-select-sm @error('asset_type') is-invalid @enderror">
                                        <option value="" disabled selected>-- {{ __('equipment.select_asset_type') }} --</option>
                                        @foreach ($assetTypes ?? [] as $typeValue => $typeLabel)
                                            <option value="{{ $typeValue }}" {{ old('asset_type') == $typeValue ? 'selected' : '' }}>{{ __($typeLabel) }}</option>
                                        @endforeach
                                    </select>
                                    @error('asset_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Brand Field --}}
                                <div class="col-md-6">
                                    <label for="brand" class="form-label fw-medium">{{ __('equipment.brand') }}</label>
                                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="form-control form-control-sm @error('brand') is-invalid @enderror" placeholder="{{ __('equipment.brand_placeholder') }}">
                                    @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Model Field --}}
                                <div class="col-md-6">
                                    <label for="model" class="form-label fw-medium">{{ __('equipment.model') }}</label>
                                    <input type="text" name="model" id="model" value="{{ old('model') }}" class="form-control form-control-sm @error('model') is-invalid @enderror" placeholder="{{ __('equipment.model_placeholder') }}">
                                    @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Serial Number Field --}}
                                <div class="col-md-6">
                                    <label for="serial_number" class="form-label fw-medium">{{ __('equipment.serial_number') }}</label>
                                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="form-control form-control-sm @error('serial_number') is-invalid @enderror" placeholder="{{ __('equipment.serial_number_placeholder') }}">
                                    @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Purchase Date Field --}}
                                <div class="col-md-6">
                                    <label for="purchase_date" class="form-label fw-medium">{{ __('equipment.purchase_date') }}</label>
                                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror">
                                    @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Warranty Expiry Date Field --}}
                                <div class="col-md-6">
                                    <label for="warranty_expiry_date" class="form-label fw-medium">{{ __('equipment.warranty_expiry_date') }}</label>
                                    <input type="date" name="warranty_expiry_date" id="warranty_expiry_date" value="{{ old('warranty_expiry_date') }}" class="form-control form-control-sm @error('warranty_expiry_date') is-invalid @enderror">
                                    @error('warranty_expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Status (Operational) Dropdown --}}
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-medium">{{ __('equipment.operational_status') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" required class="form-select form-select-sm @error('status') is-invalid @enderror">
                                        <option value="" disabled {{ old('status', \App\Models\Equipment::STATUS_AVAILABLE) ? '' : 'selected' }}>-- {{ __('equipment.select_operational_status') }} --</option>
                                        @foreach ($statusOptions ?? [] as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}" {{ old('status', \App\Models\Equipment::STATUS_AVAILABLE) == $statusValue ? 'selected' : '' }}>{{ __($statusLabel) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Condition Status Dropdown --}}
                                <div class="col-md-6">
                                    <label for="condition_status" class="form-label fw-medium">{{ __('equipment.condition_status') }} <span class="text-danger">*</span></label>
                                    <select name="condition_status" id="condition_status" required class="form-select form-select-sm @error('condition_status') is-invalid @enderror">
                                        <option value="" disabled {{ old('condition_status', \App\Models\Equipment::CONDITION_GOOD) ? '' : 'selected' }}>-- {{ __('equipment.select_condition_status') }} --</option>
                                        @foreach ($conditionStatusOptions ?? [] as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}" {{ old('condition_status', \App\Models\Equipment::CONDITION_GOOD) == $statusValue ? 'selected' : '' }}>{{ __($statusLabel) }}</option>
                                        @endforeach
                                    </select>
                                    @error('condition_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Current Location Details Field --}}
                                <div class="col-md-6">
                                    <label for="current_location" class="form-label fw-medium">{{ __('equipment.current_location') }}</label>
                                    <input type="text" name="current_location" id="current_location" value="{{ old('current_location') }}" class="form-control form-control-sm @error('current_location') is-invalid @enderror" placeholder="{{ __('equipment.current_location_placeholder') }}">
                                    @error('current_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Department Dropdown --}}
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label fw-medium">{{ __('equipment.owner_department') }} <small class="text-muted">({{ __('equipment.owner_department_optional') }})</small></label>
                                    <select name="department_id" id="department_id" class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                        <option value="">-- {{ __('equipment.select_department') }} --</option>
                                        @isset($departments) @foreach ($departments as $departmentId => $departmentName)
                                            <option value="{{ $departmentId }}" {{ old('department_id') == $departmentId ? 'selected' : '' }}>{{ $departmentName }}</option>
                                        @endforeach @endisset
                                    </select>
                                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-medium">{{ __('equipment.description') }}</label>
                                    <textarea name="description" id="description" rows="3" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="{{ __('equipment.description_placeholder') }}">{{ old('description') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label fw-medium">{{ __('equipment.internal_notes') }}</label>
                                    <textarea name="notes" id="notes" rows="2" class="form-control form-control-sm @error('notes') is-invalid @enderror" placeholder="{{ __('equipment.internal_notes_placeholder') }}">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
=======
                                    <label for="tag_id" class="form-label fw-medium">{{ __('No. Tag Aset') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="tag_id" id="tag_id" value="{{ old('tag_id') }}" required
                                        class="form-control form-control-sm @error('tag_id') is-invalid @enderror"
                                        placeholder="{{ __('Cth: MOTAC/ICT/LPT/2024/001') }}">
                                    @error('tag_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Asset Type Dropdown --}}
                                <div class="col-md-6">
                                    <label for="asset_type" class="form-label fw-medium">{{ __('Jenis Aset') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="asset_type" id="asset_type" required
                                        class="form-select form-select-sm @error('asset_type') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jenis') }} --</option>
                                        {{-- Assuming $equipmentTypes is an array of 'value' => 'Label' from controller --}}
                                        {{-- Based on System Design equipment.asset_type enum --}}
                                        @foreach ($equipmentTypes as $typeValue => $typeLabel)
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
                                        placeholder="{{ __('Cth: Dell, HP, Acer') }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Model Field --}}
                                <div class="col-md-6">
                                    <label for="model" class="form-label fw-medium">{{ __('Model') }}</label>
                                    <input type="text" name="model" id="model" value="{{ old('model') }}"
                                        class="form-control form-control-sm @error('model') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Latitude 5400, ProBook 440 G9') }}">
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Serial Number Field --}}
                                <div class="col-12">
                                    <label for="serial_number" class="form-label fw-medium">{{ __('No. Siri') }}</label>
                                    <input type="text" name="serial_number" id="serial_number"
                                        value="{{ old('serial_number') }}"
                                        class="form-control form-control-sm @error('serial_number') is-invalid @enderror"
                                        placeholder="{{ __('Masukkan nombor siri unik peralatan') }}">
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description Textarea --}}
                                <div class="col-12">
                                    <label for="description"
                                        class="form-label fw-medium">{{ __('Keterangan Tambahan') }}</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="form-control form-control-sm @error('description') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Spesifikasi ringkas, warna, atau sebarang maklumat berkaitan') }}">{{ old('description') }}</textarea>
                                    @error('description')
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
                                        <option value="">-- {{ __('Pilih Status') }} --</option>
                                        {{-- Assuming $statusOptions is an array of 'value' => 'Label' from controller --}}
                                        {{-- Based on System Design equipment.status enum --}}
                                        @foreach ($statusOptions as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}"
                                                {{ old('status', 'available') == $statusValue ? 'selected' : '' }}>
                                                {{-- Default to 'available' --}}
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
                                        <option value="">-- {{ __('Pilih Kondisi') }} --</option>
                                        {{-- Assuming $conditionStatusOptions is an array of 'value' => 'Label' from controller --}}
                                        {{-- Based on System Design equipment.condition_status enum --}}
                                        @foreach ($conditionStatusOptions as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}"
                                                {{ old('condition_status', 'good') == $statusValue ? 'selected' : '' }}>
                                                {{-- Default to 'good' --}}
                                                {{ __($statusLabel) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('condition_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Current Location Details Field --}}
                                <div class="col-12">
                                    <label for="current_location"
                                        class="form-label fw-medium">{{ __('Lokasi Semasa') }}</label>
                                    <input type="text" name="current_location" id="current_location"
                                        value="{{ old('current_location') }}"
                                        class="form-control form-control-sm @error('current_location') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Bilik Server, Aras 10, Blok D') }}">
                                    @error('current_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Department Dropdown --}}
                                <div class="col-md-6">
                                    <label for="department_id"
                                        class="form-label fw-medium">{{ __('Jabatan Pemilik (Jika ada)') }}</label>
                                    <select name="department_id" id="department_id"
                                        class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                                        @isset($departments)
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Center Dropdown --}}
                                <div class="col-md-6">
                                    <label for="center_id"
                                        class="form-label fw-medium">{{ __('Pusat (Jika berkaitan)') }}</label>
                                    <select name="center_id" id="center_id"
                                        class="form-select form-select-sm @error('center_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Pusat') }} --</option>
                                        @isset($centers)
                                            @foreach ($centers as $center)
                                                <option value="{{ $center->id }}"
                                                    {{ old('center_id') == $center->id ? 'selected' : '' }}>
                                                    {{ $center->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('center_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Notes Textarea --}}
                                <div class="col-12">
                                    <label for="notes" class="form-label fw-medium">{{ __('Nota Tambahan') }}</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                        placeholder="{{ __('Sebarang nota atau catatan lain') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end bg-light py-3 border-top">
<<<<<<< HEAD
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-save-fill me-2"></i> {{ __('equipment.save_equipment') }}
=======
                            <a href="{{ route('admin.equipment.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-save-fill me-2"></i> {{ __('Simpan Peralatan') }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<<<<<<< HEAD

@push('page-script')
    <script>
        // Example: Initialize Bootstrap validation
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
=======
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
