{{-- resources/views/admin/departments/department-create.blade.php --}}
@extends('layouts.app') {{-- Ensure layouts.app loads Bootstrap 5 CSS & JS --}}

@section('title', __('Tambah Jabatan/Unit Baru'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <h2 class="h2 fw-bold mb-4 text-dark">{{ __('Tambah Jabatan/Unit Baru') }}</h2>

            <x-alert-bootstrap /> {{-- Global Bootstrap alert for session messages or validation --}}

            <form action="{{ route('admin.departments.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h4 class="card-title h5 mb-0 fw-semibold">{{ __('Butiran Jabatan/Unit') }}</h4>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">{{ __('Nama Jabatan/Unit') }}<span class="text-danger">*</span>:</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required value="{{ old('name') }}"
                                   aria-describedby="nameHelp">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="nameHelp" class="form-text text-muted">{{ __('Nama penuh dan rasmi jabatan atau unit.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="branch_type" class="form-label fw-bold">{{ __('Jenis Cawangan') }}<span class="text-danger">*</span>:</label>
                            <select name="branch_type" id="branch_type"
                                    class="form-select @error('branch_type') is-invalid @enderror" required>
                                <option value="">{{ __('-- Sila Pilih Jenis Cawangan --') }}</option>
                                {{-- Populate from App\Models\Department constants --}}
                                <option value="{{ App\Models\Department::BRANCH_TYPE_HQ }}" {{ old('branch_type') == App\Models\Department::BRANCH_TYPE_HQ ? 'selected' : '' }}>
                                    {{ __(Str::headline(App\Models\Department::BRANCH_TYPE_HQ)) }}
                                </option>
                                <option value="{{ App\Models\Department::BRANCH_TYPE_STATE }}" {{ old('branch_type') == App\Models\Department::BRANCH_TYPE_STATE ? 'selected' : '' }}>
                                    {{ __(Str::headline(App\Models\Department::BRANCH_TYPE_STATE)) }}
                                </option>
                            </select>
                            @error('branch_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label fw-bold">{{ __('Kod Jabatan/Unit') }}:</label>
                            <input type="text" name="code" id="code"
                                   class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code') }}"
                                   aria-describedby="codeHelp">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="codeHelp" class="form-text text-muted">{{ __('Kod unik untuk jabatan, jika ada.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">{{ __('Keterangan') }}:</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Active/Inactive --}}
                        <div class="mb-3">
                            <label for="is_active" class="form-label fw-bold">{{ __('Status') }}:</label>
                            <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>{{ __('Tidak Aktif') }}</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Head of Department --}}
                        <div class="mb-3">
                            <label for="head_of_department_id" class="form-label fw-bold">{{ __('Ketua Jabatan/Unit (Jika Ada)') }}:</label>
                            <select name="head_of_department_id" id="head_of_department_id" class="form-select @error('head_of_department_id') is-invalid @enderror">
                                <option value="">{{ __('-- Tiada Ketua Jabatan Dipilih --') }}</option>
                                {{-- $users is passed from controller --}}
                                @isset($users)
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('head_of_department_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('head_of_department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                             <small id="hodHelp" class="form-text text-muted">{{ __('Pilih ketua jabatan/unit dari senarai pengguna sistem.') }}</small>
                        </div>

                    </div>
                    <div class="card-footer text-center bg-light py-3">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center px-4">
                            <i class="bi bi-plus-circle-fill me-2"></i>
                            {{ __('Simpan Jabatan/Unit') }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-2"></i>
                    {{ __('Kembali ke Senarai Jabatan/Unit') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
