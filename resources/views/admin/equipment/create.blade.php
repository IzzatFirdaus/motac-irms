{{-- resources/views/admin/equipment/create.blade.php --}}
@extends('layouts.app')

@section('title', __('equipment.add_new_title'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
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
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3">
                                {{-- Asset Tag ID Field --}}
                                <div class="col-md-6">
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
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end bg-light py-3 border-top">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-save-fill me-2"></i> {{ __('equipment.save_equipment') }}
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
