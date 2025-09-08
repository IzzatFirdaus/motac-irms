<<<<<<< HEAD
{{-- resources/views/admin/equipment/create.blade.php --}}
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

            {{-- CORRECTED: Use the single, correct partial for all session and validation messages. --}}
            @include('partials.session-messages')

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
=======
{{-- Remove <!DOCTYPE html>, <html>, <head>, <script for Tailwind>, <style> block --}}
@extends('layouts.app') {{-- Assuming layouts.app has Bootstrap 5 linked --}}

@section('title', 'Tambah Peralatan ICT Baru') {{-- Added title section for consistency --}}

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}
    <div class="row justify-content-center">
        <div class="col-lg-8"> {{-- Control overall width --}}

            <h2 class="fs-3 fw-bold mb-4">Tambah Peralatan ICT Baru</h2>

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

            <form action="{{ route('equipment.store') }}" method="POST">
                @csrf

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Butiran Peralatan</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="asset_type" class="form-label">Jenis Aset*:</label>
                            <input type="text" name="asset_type" id="asset_type" class="form-control @error('asset_type') is-invalid @enderror" required value="{{ old('asset_type') }}">
                            @error('asset_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="brand" class="form-label">Jenama*:</label>
                            <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" required value="{{ old('brand') }}">
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label">Model*:</label>
                            <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" required value="{{ old('model') }}">
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="serial_number" class="form-label">Nombor Siri*:</label>
                            <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required value="{{ old('serial_number') }}">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tag_id" class="form-label">Tag ID MOTAC*:</label>
                            <input type="text" name="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror" required value="{{ old('tag_id') }}">
                            @error('tag_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Tarikh Pembelian:</label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date') }}">
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="warranty_expiry_date" class="form-label">Tarikh Tamat Waranti:</label>
                            <input type="date" name="warranty_expiry_date" id="warranty_expiry_date" class="form-control @error('warranty_expiry_date') is-invalid @enderror" value="{{ old('warranty_expiry_date') }}">
                            @error('warranty_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Awal*:</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                                <option value="under_maintenance" {{ old('status') == 'under_maintenance' ? 'selected' : '' }}>Dalam Penyelenggaraan (Under Maintenance)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="current_location" class="form-label">Lokasi Semasa:</label>
                            <input type="text" name="current_location" id="current_location" class="form-control @error('current_location') is-invalid @enderror" value="{{ old('current_location') }}">
                            @error('current_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan:</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> {{-- End card-body --}}
                </div> {{-- End card --}}

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Simpan Peralatan
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                    </svg>
                    Kembali ke Senarai Peralatan
                </a>
            </div>
>>>>>>> b3ca845 (code additions and edits)
        </div>
    </div>
</div>
@endsection
