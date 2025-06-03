{{-- resources/views/admin/grades/create.blade.php --}}
@extends('layouts.app') {{-- Ensure layouts.app is Bootstrap-compatible --}}

@section('title', __('Tambah Gred Baru'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7"> {{-- Controls the width of the form card --}}

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ __('Tambah Gred Baru') }}</h1>
                    {{-- Corrected route name --}}
                    <a href="{{ route('settings.grades.index') }}"
                        class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Kembali ke Senarai Gred') }}
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
                {{-- Corrected route name --}}
                <form action="{{ route('settings.grades.store') }}" method="POST">
                    @csrf
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold">{{ __('Butiran Gred') }}</h3>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">{{ __('Nama Gred') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-sm @error('name') is-invalid @enderror" required
                                    value="{{ old('name') }}"
                                    placeholder="{{ __('Cth: N41, 41, JUSA C') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="level" class="form-label fw-medium">{{ __('Tahap Gred (Angka)') }}<span
                                        class="text-danger">*</span></label>
                                <input type="number" name="level" id="level"
                                    class="form-control form-control-sm @error('level') is-invalid @enderror" required
                                    value="{{ old('level') }}"
                                    placeholder="{{ __('Cth: 41, 19, 54 (untuk perbandingan)') }}">
                                <div class="form-text small">
                                    {{ __('Angka sahaja untuk tujuan perbandingan dan tapisan kelulusan.') }}</div>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-medium">{{ __('Keterangan') }}</label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control form-control-sm @error('description') is-invalid @enderror"
                                    placeholder="{{ __('Keterangan tambahan mengenai gred ini') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_approver_grade" id="is_approver_grade" value="1"
                                        class="form-check-input @error('is_approver_grade') is-invalid @enderror"
                                        {{ old('is_approver_grade') ? 'checked' : '' }} role="switch">
                                    <label class="form-check-label" for="is_approver_grade">
                                        {{ __('Adakah ini gred pelulus?') }}
                                    </label>
                                    <div class="form-text small">
                                        {{ __('Tandakan jika gred ini biasanya memegang peranan sebagai pelulus dalam sistem.') }}
                                    </div>
                                    @error('is_approver_grade')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="min_approval_grade_id"
                                    class="form-label fw-medium">{{ __('Gred Minimum Untuk Meluluskan Gred Ini (Jika Ada)') }}</label>
                                <select name="min_approval_grade_id" id="min_approval_grade_id"
                                    class="form-select form-select-sm @error('min_approval_grade_id') is-invalid @enderror">
                                    <option value="">-- {{ __('Tiada / Tidak Perlu') }} --</option>
                                    @isset($gradesList)
                                        @foreach ($gradesList as $g)
                                            <option value="{{ $g->id }}"
                                                {{ old('min_approval_grade_id') == $g->id ? 'selected' : '' }}>
                                                {{ $g->name }} ({{ __('Tahap') }} {{ $g->level }})
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <div class="form-text small">
                                    {{ __('Jika gred ini memerlukan kelulusan dari gred yang lebih tinggi, pilih gred tersebut di sini.') }}
                                </div>
                                @error('min_approval_grade_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="card-footer text-end bg-light py-3">
                             {{-- Corrected route name --}}
                            <a href="{{ route('settings.grades.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center px-4">
                                <i class="bi bi-save-fill me-2"></i>
                                {{ __('Simpan Gred') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
