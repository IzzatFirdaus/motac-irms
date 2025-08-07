{{-- resources/views/loan-applications/loan-application-edit.blade.php --}}
{{-- Edit an existing ICT loan application --}}
{{-- Code unchanged; only filename is updated, and this comment documents the file purpose. --}}

@extends('layouts.app')

@section('title', __('Kemaskini Permohonan Pinjaman ICT'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                {{-- FIX: Replaced 'text-dark' with 'text-body' to allow the theme to control text color.  --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-body mb-0 d-flex align-items-center"><i
                            class="bi bi-pencil-square me-2"></i>{{ __('Kemaskini Permohonan Pinjaman') }}
                        #{{ $loanApplication->id }}</h1>
                    <a href="{{ route('loan-applications.show', $loanApplication) }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-eye-fill me-1"></i> {{ __('Lihat Permohonan') }}
                    </a>
                </div>

                @include('_partials._alerts.alert-general')

                <form action="{{ route('loan-applications.update', $loanApplication) }}" method="POST"
                    id="loanApplicationEditForm">
                    @csrf
                    @method('PUT')

                    {{-- BAHAGIAN 1 | MAKLUMAT PEMOHON --}}
                    {{-- FIX: Removed hardcoded 'bg-light'. The .card and .card-header classes are styled by theme-motac.css.  --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('BAHAGIAN 1 | MAKLUMAT PEMOHON') }}</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="purpose" class="form-label fw-semibold">{{ __('Tujuan Permohonan') }}<span
                                        class="text-danger">*</span></label>
                                <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3"
                                    required>{{ old('purpose', $loanApplication->purpose) }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6"><label for="location"
                                        class="form-label fw-semibold">{{ __('Lokasi Penggunaan') }}<span
                                            class="text-danger">*</span></label><input type="text" name="location"
                                        id="location" class="form-control @error('location') is-invalid @enderror"
                                        value="{{ old('location', $loanApplication->location) }}" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6"><label for="return_location"
                                        class="form-label fw-semibold">{{ __('Lokasi Pemulangan') }}</label><input
                                        type="text" name="return_location" id="return_location"
                                        class="form-control @error('return_location') is-invalid @enderror"
                                        value="{{ old('return_location', $loanApplication->return_location) }}">
                                    @error('return_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6"><label for="loan_start_date"
                                        class="form-label fw-semibold">{{ __('Tarikh Mula') }}<span
                                            class="text-danger">*</span></label><input type="datetime-local"
                                        name="loan_start_date" id="loan_start_date"
                                        class="form-control @error('loan_start_date') is-invalid @enderror"
                                        value="{{ old('loan_start_date', $loanApplication->loan_start_date ? $loanApplication->loan_start_date->format('Y-m-d\TH:i') : '') }}"
                                        min="{{ now()->toDateTimeLocalString('minute') }}" required>
                                    @error('loan_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6"><label for="loan_end_date"
                                        class="form-label fw-semibold">{{ __('Tarikh Pulang') }}<span
                                            class="text-danger">*</span></label><input type="datetime-local"
                                        name="loan_end_date" id="loan_end_date"
                                        class="form-control @error('loan_end_date') is-invalid @enderror"
                                        value="{{ old('loan_end_date', $loanApplication->loan_end_date ? $loanApplication->loan_end_date->format('Y-m-d\TH:i') : '') }}"
                                        required>
                                    @error('loan_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NOTE: Other form sections for items, responsible/supporting officers would be added here in a full implementation. --}}
                    {{-- The same principles of removing hardcoded colors would apply to them. --}}

                    <div class="text-center mt-4 pt-3">
                        <button type="submit"
                            class="btn btn-primary btn-lg d-inline-flex align-items-center px-5">
                            <i class="bi bi-save-fill me-2"></i> {{ __('Kemaskini Permohonan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
