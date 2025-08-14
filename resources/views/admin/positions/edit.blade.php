{{-- resources/views/admin/positions/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Kemaskini Jawatan') . ': ' . ($position->name ?? 'N/A'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Kemaskini Jawatan') }}: <span class="text-primary">{{ $position->name ?? 'N/A' }}</span>
                    </h1>
                    <a href="{{ route('admin.positions.index') }}"
                        class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Kembali ke Senarai Jawatan') }}
                    </a>
                </div>


                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading fw-bold">{{ __('Ralat Pengesahan!') }}</h5>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li><small>{{ $error }}</small></li>
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

                <form action="{{ route('admin.positions.update', $position) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold">{{ __('Butiran Jawatan') }}</h3>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">{{ __('Nama Jawatan') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-sm @error('name') is-invalid @enderror" required
                                    value="{{ old('name', $position->name) }}"
                                    placeholder="{{ __('Masukkan nama jawatan') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="code"
                                    class="form-label fw-medium">{{ __('Kod Jawatan (Jika Ada)') }}</label>
                                <input type="text" name="code" id="code"
                                    class="form-control form-control-sm @error('code') is-invalid @enderror"
                                    value="{{ old('code', $position->code) }}"
                                    placeholder="{{ __('Masukkan kod jawatan (jika ada)') }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-medium">{{ __('Keterangan') }}</label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control form-control-sm @error('description') is-invalid @enderror"
                                    placeholder="{{ __('Masukkan keterangan jawatan') }}">{{ old('description', $position->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- grade_id Field (from System Design PDF pg. 6) --}}
                            <div class="mb-3">
                                <label for="grade_id"
                                    class="form-label fw-medium">{{ __('Gred Berkaitan (Jika Ada)') }}</label>
                                <select name="grade_id" id="grade_id"
                                    class="form-select form-select-sm @error('grade_id') is-invalid @enderror">
                                    <option value="">-- {{ __('Pilih Gred') }} --</option>
                                    {{-- Assuming $grades is passed from controller --}}
                                    @isset($grades)
                                        @foreach ($grades as $grade)
                                            <option value="{{ $grade->id }}"
                                                {{ old('grade_id', $position->grade_id) == $grade->id ? 'selected' : '' }}>
                                                {{ $grade->name }} ({{ __('Tahap') }} {{ $grade->level }})
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('grade_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- is_active Field (from System Design PDF pg. 6) --}}
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox"
                                        role="switch" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $position->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Jawatan Aktif') }}</label>
                                    <div class="form-text small">
                                        {{ __('Nyahaktifkan jika jawatan ini tidak lagi digunakan.') }}</div>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end bg-light py-3">
                            <a href="{{ route('admin.positions.show', $position) }}"
                                class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center px-4">
                                <i class="bi bi-save-fill me-2"></i>
                                {{ __('Kemaskini Jawatan') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
