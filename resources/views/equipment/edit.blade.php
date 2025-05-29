<<<<<<< HEAD
{{-- resources/views/equipment/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Edit Peralatan ICT #') . $equipment->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 class="fs-3 fw-bold mb-4 text-dark">{{ __('Edit Peralatan ICT #') }}{{ $equipment->id }}
                <span class="text-muted fs-5">({{ __('Tag') }}: {{ e($equipment->tag_id ?? 'N/A') }})</span>
            </h2>

            @include('partials.session-messages')
            @include('partials.validation-errors')

            {{-- Assumed admin route for updating equipment --}}
            <form action="{{ route('resource-management.equipment-admin.update', $equipment) }}" method="POST">
=======
{{-- Remove <!DOCTYPE html>, <html>, <head>, <script for Tailwind>, <style> block --}}
@extends('layouts.app') {{-- Assuming layouts.app has Bootstrap 5 linked --}}

@section('title', 'Edit Peralatan ICT #' . $equipment->id) {{-- Added title section --}}

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}
    <div class="row justify-content-center">
        <div class="col-lg-8"> {{-- Control overall width --}}

            <h2 class="fs-3 fw-bold mb-4">Edit Peralatan ICT #{{ $equipment->id }} (Tag: {{ $equipment->tag_id ?? 'N/A' }})</h2>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Ralat Pengesahan:</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('equipment.update', $equipment) }}" method="POST">
>>>>>>> b3ca845 (code additions and edits)
                @csrf
                @method('PUT')

                <div class="card shadow-sm mb-4">
<<<<<<< HEAD
                    <div class="card-header bg-light py-3">
                        <h3 class="h5 card-title fw-semibold mb-0">{{ __('Butiran Asas Peralatan') }}</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="asset_type" class="form-label">{{ __('Jenis Aset') }} <span class="text-danger">*</span></label>
                                <select name="asset_type" id="asset_type" class="form-select @error('asset_type') is-invalid @enderror" required>
                                    <option value="">-- {{ __('Pilih Jenis Aset') }} --</option>
                                    @foreach (\App\Models\Equipment::getAssetTypeOptions() as $value => $label) {{-- Assumes helper method on model [cite: 363, 380] --}}
                                        <option value="{{ $value }}" {{ old('asset_type', $equipment->asset_type) == $value ? 'selected' : '' }}>{{ e($label) }}</option>
                                    @endforeach
                                </select>
                                @error('asset_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="brand" class="form-label">{{ __('Jenama') }} <span class="text-danger">*</span></label>
                                <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" required value="{{ old('brand', $equipment->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="model" class="form-label">{{ __('Model') }} <span class="text-danger">*</span></label>
                                <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" required value="{{ old('model', $equipment->model) }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="serial_number" class="form-label">{{ __('Nombor Siri') }} <span class="text-danger">*</span></label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required value="{{ old('serial_number', $equipment->serial_number) }}">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tag_id" class="form-label">{{ __('Tag ID MOTAC') }} <span class="text-danger">*</span></label>
                                <input type="text" name="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror" required value="{{ old('tag_id', $equipment->tag_id) }}">
                                @error('tag_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="item_code" class="form-label">{{ __('Kod Item (KEW.PA-2/KEW.PA-3)') }}</label>
                                <input type="text" name="item_code" id="item_code" class="form-control @error('item_code') is-invalid @enderror" value="{{ old('item_code', $equipment->item_code) }}">
                                @error('item_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="purchase_date" class="form-label">{{ __('Tarikh Pembelian') }}</label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="warranty_expiry_date" class="form-label">{{ __('Tarikh Tamat Waranti') }}</label>
                                <input type="date" name="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') is-invalid @enderror" value="{{ old('warranty_expiry_date', $equipment->warranty_expiry_date?->format('Y-m-d')) }}">
                                @error('warranty_expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('Status Operasi') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                     <option value="">-- {{ __('Pilih Status Operasi') }} --</option>
                                     {{-- Full status options for editing. System design enum [cite: 363, 380] --}}
                                     @foreach (\App\Models\Equipment::getStatusOptions() as $value => $label) {{-- Assumes a helper method in model --}}
                                        <option value="{{ $value }}" {{ old('status', $equipment->status) == $value ? 'selected' : '' }}>{{ e($label) }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="condition_status" class="form-label">{{ __('Keadaan Fizikal') }} <span class="text-danger">*</span></label>
                                <select name="condition_status" id="condition_status" class="form-select @error('condition_status') is-invalid @enderror" required>
                                     <option value="">-- {{ __('Pilih Keadaan Fizikal') }} --</option>
                                     @foreach (\App\Models\Equipment::getConditionStatusOptions() as $value => $label) {{-- Assumes a helper method in model [cite: 363, 380] --}}
                                        <option value="{{ $value }}" {{ old('condition_status', $equipment->condition_status) == $value ? 'selected' : '' }}>{{ e($label) }}</option>
                                    @endforeach
                                </select>
                                @error('condition_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="current_location" class="form-label">{{ __('Lokasi Semasa') }}</label>
                                <input type="text" name="current_location" id="current_location" class="form-control @error('current_location') is-invalid @enderror" value="{{ old('current_location', $equipment->current_location) }}">
                                @error('current_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">{{ __('Catatan Tambahan') }}</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $equipment->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
=======
                    <div class="card-header">
                        <h4 class="card-title mb-0">Butiran Peralatan</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="asset_type" class="form-label">Jenis Aset*:</label>
                            <input type="text" name="asset_type" id="asset_type" class="form-control @error('asset_type') is-invalid @enderror" required value="{{ old('asset_type', $equipment->asset_type) }}">
                            @error('asset_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="brand" class="form-label">Jenama*:</label>
                            <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" required value="{{ old('brand', $equipment->brand) }}">
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label">Model*:</label>
                            <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" required value="{{ old('model', $equipment->model) }}">
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="serial_number" class="form-label">Nombor Siri*:</label>
                            <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required value="{{ old('serial_number', $equipment->serial_number) }}">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tag_id" class="form-label">Tag ID MOTAC*:</label>
                            <input type="text" name="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror" required value="{{ old('tag_id', $equipment->tag_id) }}">
                            @error('tag_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Tarikh Pembelian:</label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}">
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="warranty_expiry_date" class="form-label">Tarikh Tamat Waranti:</label>
                            <input type="date" name="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') is-invalid @enderror" value="{{ old('warranty_expiry_date', $equipment->warranty_expiry_date?->format('Y-m-d')) }}">
                            @error('warranty_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status*:</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="available" {{ old('status', $equipment->status) == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                                <option value="on_loan" {{ old('status', $equipment->status) == 'on_loan' ? 'selected' : '' }}>Sedang Dipinjam (On Loan)</option>
                                <option value="under_maintenance" {{ old('status', $equipment->status) == 'under_maintenance' ? 'selected' : '' }}>Dalam Penyelenggaraan (Under Maintenance)</option>
                                <option value="disposed" {{ old('status', $equipment->status) == 'disposed' ? 'selected' : '' }}>Dilupuskan (Disposed)</option>
                                <option value="lost" {{ old('status', $equipment->status) == 'lost' ? 'selected' : '' }}>Hilang (Lost)</option>
                                <option value="damaged" {{ old('status', $equipment->status) == 'damaged' ? 'selected' : '' }}>Rosak (Damaged)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="current_location" class="form-label">Lokasi Semasa:</label>
                            <input type="text" name="current_location" id="current_location" class="form-control @error('current_location') is-invalid @enderror" value="{{ old('current_location', $equipment->current_location) }}">
                            @error('current_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan:</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $equipment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
>>>>>>> b3ca845 (code additions and edits)
                        </div>
                    </div> {{-- End card-body --}}
                </div> {{-- End card --}}

<<<<<<< HEAD
                <div class="d-flex justify-content-center mt-4 gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="bi bi-save-fill me-2"></i>
                        {{ __('Kemaskini Peralatan') }}
                    </button>
                     {{-- Assumed admin route for equipment index --}}
                    <a href="{{ route('resource-management.equipment-admin.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                         <i class="bi bi-x-circle me-2"></i>
                        {{ __('Batal') }}
                    </a>
=======
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Kemaskini Peralatan
                    </button>
>>>>>>> b3ca845 (code additions and edits)
                </div>
            </form>

            <div class="mt-4 text-center">
<<<<<<< HEAD
                <a href="{{ route('resource-management.equipment-admin.index') }}" class="btn btn-link text-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left-circle-fill me-2"></i>
                    {{ __('Kembali ke Senarai Peralatan Pentadbir') }}
=======
                <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                    </svg>
                    Kembali ke Senarai Peralatan
>>>>>>> b3ca845 (code additions and edits)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
