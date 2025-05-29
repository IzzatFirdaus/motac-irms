{{-- resources/views/admin/grades/create.blade.php --}}
@extends('layouts.app') {{-- Ensure layouts.app is Bootstrap-compatible --}}

@section('title', __('Tambah Gred Baru'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7"> {{-- Controls the width of the form card --}}

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ __('Tambah Gred Baru') }}</h1>
<<<<<<< HEAD
                    {{-- Corrected route name --}}
                    <a href="{{ route('settings.grades.index') }}"
=======
                    <a href="{{ route('admin.grades.index') }}"
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
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
<<<<<<< HEAD
                {{-- Corrected route name --}}
                <form action="{{ route('settings.grades.store') }}" method="POST">
=======

                <form action="{{ route('admin.grades.store') }}" method="POST">
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
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
<<<<<<< HEAD
                                    placeholder="{{ __('Cth: N41, 41, JUSA C') }}">
=======
                                    placeholder="{{ __('Cth: Pegawai Tadbir N41, Juruteknik FT19') }}">
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

<<<<<<< HEAD
=======
                            {{-- The System Design for `grades` table has `name` (e.g., "41", "N19") and `level` (integer).
                             It does not explicitly show a `code` field for grades table.
                             If `code` is different from `name`, ensure it's in your migration.
                             The MyMail form (PDF pg. 27) combines scheme and number in the Gred options.
                             Let's assume 'name' is for display like "N41" and 'level' is the numerical part "41" for comparison.
                             If 'code' is meant to be the scheme like "N", "F", "JUSA", then the label should reflect that.
                             The current `name` field example "Pegawai Tadbir N41" seems more like a position name with grade.
                             Let's adjust `name` to be just the grade identifier (e.g., N41, 41, JUSA C)
                             and `level` to be the numeric part for sorting/comparison.
                             The field `code` is not in the system design for the `grades` table (PDF pg. 7).
                             It's present in `departments` table though. I'll remove 'code' for now unless it's a custom addition.
                        --}}

>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
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

<<<<<<< HEAD
=======

>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                            <div class="mb-3">
                                <label for="description" class="form-label fw-medium">{{ __('Keterangan') }}</label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control form-control-sm @error('description') is-invalid @enderror"
                                    placeholder="{{ __('Keterangan tambahan mengenai gred ini') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

<<<<<<< HEAD
=======
                            {{-- System Design fields for `grades` table (PDF pg. 7): name, level, min_approval_grade_id, is_approver_grade --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
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
<<<<<<< HEAD
=======
                                    {{-- Populate with existing grades, ensure $gradesList is passed from controller --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
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

<<<<<<< HEAD
                        </div>
                        <div class="card-footer text-end bg-light py-3">
                             {{-- Corrected route name --}}
                            <a href="{{ route('settings.grades.index') }}" class="btn btn-outline-secondary me-2">
=======
                            {{-- The `requires_approval` checkbox seems redundant if `is_approver_grade` and `min_approval_grade_id` cover the logic.
                              The system design does not explicitly list `requires_approval` boolean on the `grades` table.
                              It mentions `is_approver_grade` (boolean) and `min_approval_grade_id` (foreignId).
                              I'll remove the `requires_approval` checkbox to align better with the system design's `grades` table.
                         --}}

                        </div>
                        <div class="card-footer text-end bg-light py-3">
                            <a href="{{ route('admin.grades.index') }}" class="btn btn-outline-secondary me-2">
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
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
