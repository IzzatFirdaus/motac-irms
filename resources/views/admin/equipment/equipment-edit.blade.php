{{-- resources/views/admin/equipment/equipment-edit.blade.php --}}
@extends('layouts.app')

@section('title', __('equipment.edit_title') . ': #' . ($equipment->tag_id ?? $equipment->id))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="bi bi-pencil-square me-2"></i>{{ __('equipment.edit_title') }} <span
                        class="text-primary ms-2">#{{ $equipment->tag_id ?? $equipment->id }}</span>
                </h1>
                <a href="{{ route('resource-management.equipment-admin.show', $equipment) }}"
                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-eye-fill me-1"></i> {{ __('equipment.view_details') }}
                </a>
            </div>

            @include('_partials._alerts.alert-general')

            <form action="{{ route('resource-management.equipment-admin.update', $equipment) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h3 class="card-title h5 mb-0 fw-semibold">{{ __('equipment.equipment_details') }}</h3>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tag_id" class="form-label fw-medium">{{ __('equipment.asset_tag') }} <span class="text-danger">*</span></label>
                                <input type="text" name="tag_id" id="tag_id" value="{{ old('tag_id', $equipment->tag_id) }}" required class="form-control form-control-sm @error('tag_id') is-invalid @enderror">
                                @error('tag_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="asset_type" class="form-label fw-medium">{{ __('equipment.asset_type') }} <span class="text-danger">*</span></label>
                                <select name="asset_type" id="asset_type" required class="form-select form-select-sm @error('asset_type') is-invalid @enderror">
                                    <option value="">-- {{ __('equipment.select_asset_type') }} --</option>
                                    @foreach ($assetTypes as $typeValue => $typeLabel)
                                        <option value="{{ $typeValue }}" {{ old('asset_type', $equipment->asset_type) == $typeValue ? 'selected' : '' }}>{{ __($typeLabel) }}</option>
                                    @endforeach
                                </select>
                                @error('asset_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="brand" class="form-label fw-medium">{{ __('equipment.brand') }}</label>
                                <input type="text" name="brand" id="brand" value="{{ old('brand', $equipment->brand) }}" class="form-control form-control-sm @error('brand') is-invalid @enderror" placeholder="{{ __('equipment.brand_placeholder') }}">
                                @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="model" class="form-label fw-medium">{{ __('equipment.model') }}</label>
                                <input type="text" name="model" id="model" value="{{ old('model', $equipment->model) }}" class="form-control form-control-sm @error('model') is-invalid @enderror" placeholder="{{ __('equipment.model_placeholder') }}">
                                @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="serial_number" class="form-label fw-medium">{{ __('equipment.serial_number') }}</label>
                                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}" class="form-control form-control-sm @error('serial_number') is-invalid @enderror" placeholder="{{ __('equipment.serial_number_placeholder') }}">
                                @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="purchase_date" class="form-label fw-medium">{{ __('equipment.purchase_date') }}</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror">
                                @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="warranty_expiry_date" class="form-label fw-medium">{{ __('equipment.warranty_expiry_date') }}</label>
                                <input type="date" name="warranty_expiry_date" id="warranty_expiry_date" value="{{ old('warranty_expiry_date', $equipment->warranty_expiry_date?->format('Y-m-d')) }}" class="form-control form-control-sm @error('warranty_expiry_date') is-invalid @enderror">
                                @error('warranty_expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-medium">{{ __('equipment.operational_status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" required class="form-select form-select-sm @error('status') is-invalid @enderror">
                                    <option value="">-- {{ __('equipment.select_operational_status') }} --</option>
                                    @foreach ($statusOptions as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}" {{ old('status', $equipment->status) == $statusValue ? 'selected' : '' }}>{{ __($statusLabel) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="condition_status" class="form-label fw-medium">{{ __('equipment.condition_status') }} <span class="text-danger">*</span></label>
                                <select name="condition_status" id="condition_status" required class="form-select form-select-sm @error('condition_status') is-invalid @enderror">
                                    <option value="">-- {{ __('equipment.select_condition_status') }} --</option>
                                    @foreach ($conditionStatusOptions as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}" {{ old('condition_status', $equipment->condition_status) == $statusValue ? 'selected' : '' }}>{{ __($statusLabel) }}</option>
                                    @endforeach
                                </select>
                                @error('condition_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="current_location" class="form-label fw-medium">{{ __('equipment.current_location') }}</label>
                                <input type="text" name="current_location" id="current_location" value="{{ old('current_location', $equipment->current_location) }}" class="form-control form-control-sm @error('current_location') is-invalid @enderror" placeholder="{{ __('equipment.current_location_placeholder') }}">
                                @error('current_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="department_id" class="form-label fw-medium">{{ __('equipment.owner_department') }} <small class="text-muted">{{ __('equipment.owner_department_optional') }}</small></label>
                                <select name="department_id" id="department_id" class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                    <option value="">-- {{ __('equipment.select_department') }} --</option>
                                    @isset($departments)
                                        @foreach ($departments as $id => $name)
                                            <option value="{{ $id }}" {{ old('department_id', $equipment->department_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-medium">{{ __('equipment.description') }}</label>
                                <textarea name="description" id="description" rows="3" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="{{ __('equipment.description_placeholder') }}">{{ old('description', $equipment->description) }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label fw-medium">{{ __('equipment.internal_notes') }}</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control form-control-sm @error('notes') is-invalid @enderror" placeholder="{{ __('equipment.internal_notes_placeholder') }}">{{ old('notes', $equipment->notes) }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-light py-3 border-top">
                        <a href="{{ route('resource-management.equipment-admin.show', $equipment) }}" class="btn btn-outline-secondary me-2">{{ __('common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="bi bi-save-fill me-2"></i> {{ __('equipment.update_equipment') }}
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
        // Bootstrap form validation
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
