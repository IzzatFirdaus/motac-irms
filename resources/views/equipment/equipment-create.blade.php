{{-- resources/views/equipment/equipment-create.blade.php --}}
{{-- Create Equipment Form - Renamed from create.blade.php for clarity and consistency. --}}
@extends('layouts.app')

@section('title', __('Tambah Peralatan ICT Baru'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="bi bi-plus-circle-fill me-2"></i>{{ __('Tambah Peralatan ICT Baru') }}
                </h1>
                <a href="{{ route('resource-management.equipment-admin.index') }}"
                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                </a>
            </div>

            {{-- Show all session and validation messages --}}
            @include('equipment.partials.equipment-session-messages')

            <form action="{{ route('resource-management.equipment-admin.store') }}" method="POST">
                @csrf

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h3 class="h5 card-title fw-semibold mb-0">{{ __('Butiran Asas Peralatan') }}</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Asset Type --}}
                            <div class="col-md-6 mb-3">
                                <label for="asset_type" class="form-label">{{ __('Jenis Aset') }} <span class="text-danger">*</span></label>
                                <select name="asset_type" id="asset_type" class="form-select @error('asset_type') is-invalid @enderror" required>
                                    <option value="">-- {{ __('Pilih Jenis Aset') }} --</option>
                                    @foreach (\App\Models\Equipment::getAssetTypeOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ old('asset_type') == $value ? 'selected' : '' }}>{{ e($label) }}</option>
                                    @endforeach
                                </select>
                                @error('asset_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Brand --}}
                            <div class="col-md-6 mb-3">
                                <label for="brand" class="form-label">{{ __('Jenama') }} <span class="text-danger">*</span></label>
                                <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" required value="{{ old('brand') }}" placeholder="Contoh: Dell, HP, Acer">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Model --}}
                            <div class="col-md-6 mb-3">
                                <label for="model" class="form-label">{{ __('Model') }} <span class="text-danger">*</span></label>
                                <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" required value="{{ old('model') }}" placeholder="Contoh: Latitude 5420, ProBook 440 G8">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Serial Number --}}
                            <div class="col-md-6 mb-3">
                                <label for="serial_number" class="form-label">{{ __('Nombor Siri') }} <span class="text-danger">*</span></label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required value="{{ old('serial_number') }}">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tag ID --}}
                            <div class="col-md-6 mb-3">
                                <label for="tag_id" class="form-label">{{ __('Tag ID MOTAC') }} <span class="text-danger">*</span></label>
                                <input type="text" name="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror" required value="{{ old('tag_id') }}" placeholder="Contoh: MOTAC.K.12345">
                                @error('tag_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Item Code --}}
                            <div class="col-md-6 mb-3">
                                <label for="item_code" class="form-label">{{ __('Kod Item (KEW.PA-2/KEW.PA-3)') }}</label>
                                <input type="text" name="item_code" id="item_code" class="form-control @error('item_code') is-invalid @enderror" value="{{ old('item_code') }}" placeholder="Contoh: MYLPT001">
                                @error('item_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Purchase Date --}}
                            <div class="col-md-6 mb-3">
                                <label for="purchase_date" class="form-label">{{ __('Tarikh Pembelian') }}</label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date') }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Warranty Expiry Date --}}
                            <div class="col-md-6 mb-3">
                                <label for="warranty_expiry_date" class="form-label">{{ __('Tarikh Tamat Waranti') }}</label>
                                <input type="date" name="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') is-invalid @enderror" value="{{ old('warranty_expiry_date') }}">
                                @error('warranty_expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Initial Operational Status --}}
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('Status Operasi Awal') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach (\App\Models\Equipment::getInitialStatusOptions() as $value => $label)
                                         <option value="{{ $value }}" {{ old('status', \App\Models\Equipment::STATUS_AVAILABLE) == $value ? 'selected' : '' }}>{{ e($label) }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Initial Condition Status --}}
                            <div class="col-md-6 mb-3">
                                <label for="condition_status" class="form-label">{{ __('Keadaan Fizikal Awal') }} <span class="text-danger">*</span></label>
                                <select name="condition_status" id="condition_status" class="form-select @error('condition_status') is-invalid @enderror" required>
                                     @foreach (\App\Models\Equipment::getConditionStatusOptions() as $value => $label)
                                         <option value="{{ $value }}" {{ old('condition_status', \App\Models\Equipment::CONDITION_NEW) == $value ? 'selected' : '' }}>{{ e($label) }}</option>
                                    @endforeach
                                </select>
                                @error('condition_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Current Location --}}
                            <div class="col-12 mb-3">
                                <label for="current_location" class="form-label">{{ __('Lokasi Semasa (Stor/Unit)') }}</label>
                                <input type="text" name="current_location" id="current_location" class="form-control @error('current_location') is-invalid @enderror" value="{{ old('current_location') }}">
                                @error('current_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">{{ __('Catatan Tambahan') }}</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Contoh: Spesifikasi tambahan, aksesori, dll.">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4 gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ __('Simpan Peralatan') }}
                    </button>
                    <a href="{{ route('resource-management.equipment-admin.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-x-circle me-2"></i>
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
