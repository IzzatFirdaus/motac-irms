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
                @csrf
                @method('PUT')

                <div class="card shadow-sm mb-4">
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
                        </div>
                    </div> {{-- End card-body --}}
                </div> {{-- End card --}}

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Kemaskini Peralatan
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
        </div>
    </div>
</div>
@endsection
