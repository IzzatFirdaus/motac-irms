{{-- resources/views/admin/users/user-edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Kemaskini Pengguna') . ': ' . ($user->name ?? 'N/A'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom gap-2">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Kemaskini Pengguna') }}: <span
                            class="text-primary">{{ $user->name ?? ($user->full_name ?? 'N/A') }}</span>
                    </h1>
                    <a href="{{ route('admin.users.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Pengguna') }}
                    </a>
                </div>

                {{-- Display validation errors and session messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading fw-bold"><i
                                class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Ralat Pengesahan!') }}</h5>
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
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Section: Account Info --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold"><i
                                    class="bi bi-key-fill me-2"></i>{{ __('Maklumat Akaun') }}</h3>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium">{{ __('Nama Paparan') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control form-control-sm @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">{{ __('Emel (Untuk Login)') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email"
                                        class="form-control form-control-sm @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required readonly>
                                    <div class="form-text small">
                                        {{ __('Emel login tidak boleh diubah selepas pendaftaran.') }}</div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password"
                                        class="form-label fw-medium">{{ __('Kata Laluan Baru (Biarkan kosong jika tidak mahu tukar)') }}</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-sm @error('password') is-invalid @enderror"
                                        autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label fw-medium">{{ __('Sahkan Kata Laluan Baru') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control form-control-sm" autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Additional User Info --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold"><i
                                    class="bi bi-person-lines-fill me-2"></i>{{ __('Butiran Tambahan Pengguna (MOTAC)') }}
                            </h3>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="title_edit"
                                        class="form-label fw-medium">{{ __('Gelaran (Cth: Encik, Puan)') }}</label>
                                    <input type="text" name="title" id="title_edit"
                                        class="form-control form-control-sm @error('title') is-invalid @enderror"
                                        value="{{ old('title', $user->title) }}" placeholder="{{ __('Cth: Encik') }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="full_name_edit" class="form-label fw-medium">{{ __('Nama Penuh') }}</label>
                                    <input type="text" name="full_name" id="full_name_edit"
                                        class="form-control form-control-sm @error('full_name') is-invalid @enderror"
                                        value="{{ old('full_name', $user->full_name) }}"
                                        placeholder="{{ __('Nama penuh rasmi') }}">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="identification_number_edit"
                                        class="form-label fw-medium">{{ __('No. Kad Pengenalan (NRIC)') }}</label>
                                    <input type="text" name="identification_number" id="identification_number_edit"
                                        class="form-control form-control-sm @error('identification_number') is-invalid @enderror"
                                        value="{{ old('identification_number', $user->identification_number) }}"
                                        placeholder="{{ __('Cth: 900101010001') }}">
                                    @error('identification_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="passport_number_edit"
                                        class="form-label fw-medium">{{ __('No. Passport (Jika Ada)') }}</label>
                                    <input type="text" name="passport_number" id="passport_number_edit"
                                        class="form-control form-control-sm @error('passport_number') is-invalid @enderror"
                                        value="{{ old('passport_number', $user->passport_number) }}"
                                        placeholder="{{ __('Untuk bukan warganegara') }}">
                                    @error('passport_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mobile_number_edit"
                                        class="form-label fw-medium">{{ __('No. Telefon Bimbit') }}</label>
                                    <input type="tel" name="mobile_number" id="mobile_number_edit"
                                        class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                        value="{{ old('mobile_number', $user->mobile_number) }}"
                                        placeholder="{{ __('Cth: 0123456789') }}">
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="personal_email_edit"
                                        class="form-label fw-medium">{{ __('Emel Peribadi') }}</label>
                                    <input type="email" name="personal_email" id="personal_email_edit"
                                        class="form-control form-control-sm @error('personal_email') is-invalid @enderror"
                                        value="{{ old('personal_email', $user->personal_email) }}"
                                        placeholder="{{ __('Cth: peribadi@example.com') }}">
                                    @error('personal_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="motac_email_edit"
                                        class="form-label fw-medium">{{ __('Emel Rasmi MOTAC') }}</label>
                                    <input type="email" name="motac_email" id="motac_email_edit"
                                        class="form-control form-control-sm @error('motac_email') is-invalid @enderror"
                                        value="{{ old('motac_email', $user->motac_email) }}"
                                        placeholder="{{ __('Cth: nama@motac.gov.my') }}">
                                    @error('motac_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="user_id_assigned_edit"
                                        class="form-label fw-medium">{{ __('ID Pengguna Rangkaian') }}</label>
                                    <input type="text" name="user_id_assigned" id="user_id_assigned_edit"
                                        class="form-control form-control-sm @error('user_id_assigned') is-invalid @enderror"
                                        value="{{ old('user_id_assigned', $user->user_id_assigned) }}"
                                        placeholder="{{ __('ID Network/AD jika ada') }}">
                                    @error('user_id_assigned')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="department_id_edit"
                                        class="form-label fw-medium">{{ __('Jabatan/Unit') }}</label>
                                    <select name="department_id" id="department_id_edit"
                                        class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="position_id_edit"
                                        class="form-label fw-medium">{{ __('Jawatan') }}</label>
                                    <select name="position_id" id="position_id_edit"
                                        class="form-select form-select-sm @error('position_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jawatan') }} --</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="grade_id_edit" class="form-label fw-medium">{{ __('Gred') }}</label>
                                    <select name="grade_id" id="grade_id_edit"
                                        class="form-select form-select-sm @error('grade_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Gred') }} --</option>
                                        @foreach ($grades as $grade)
                                            <option value="{{ $grade->id }}"
                                                {{ old('grade_id', $user->grade_id) == $grade->id ? 'selected' : '' }}>
                                                {{ $grade->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('grade_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="service_status_edit"
                                        class="form-label fw-medium">{{ __('Taraf Perkhidmatan') }}</label>
                                    <select name="service_status" id="service_status_edit"
                                        class="form-select form-select-sm @error('service_status') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Taraf Perkhidmatan') }} --</option>
                                        @foreach ($serviceStatuses as $statusKey => $statusName)
                                            <option value="{{ $statusKey }}"
                                                {{ old('service_status', $user->service_status) == $statusKey ? 'selected' : '' }}>
                                                {{ __($statusName) }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="appointment_type_edit"
                                        class="form-label fw-medium">{{ __('Jenis Pelantikan') }}</label>
                                    <select name="appointment_type" id="appointment_type_edit"
                                        class="form-select form-select-sm @error('appointment_type') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jenis Pelantikan') }} --</option>
                                        @foreach ($appointmentTypes as $typeKey => $typeName)
                                            <option value="{{ $typeKey }}"
                                                {{ old('appointment_type', $user->appointment_type) == $typeKey ? 'selected' : '' }}>
                                                {{ __($typeName) }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status_edit"
                                        class="form-label fw-medium">{{ __('Status Pengguna') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="status_edit"
                                        class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                                        @foreach ($userStatuses as $statusKey => $statusName)
                                            <option value="{{ $statusKey }}"
                                                {{ old('status', $user->status) == $statusKey ? 'selected' : '' }}>
                                                {{ __($statusName) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: User Roles --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h3 class="card-title h5 mb-0 fw-semibold"><i
                                    class="bi bi-shield-check me-2"></i>{{ __('Peranan Pengguna') }}</h3>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <label class="form-label fw-medium d-block mb-2">{{ __('Tetapkan Peranan') }}:</label>
                            @forelse ($roles as $role)
                                @if (is_object($role) && property_exists($role, 'id') && property_exists($role, 'name'))
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                            id="role_edit_{{ $role->id }}" value="{{ $role->name }}"
                                            {{ in_array($role->name, old('roles', $user->getRoleNames()->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="role_edit_{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                @endif
                            @empty
                                <p class="text-muted small">{{ __('Tiada peranan sistem yang telah dikonfigurasi.') }}</p>
                            @endforelse
                            @error('roles')
                                <div class="d-block text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @error('roles.*')
                                <div class="d-block text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="card-footer text-center bg-light py-3 border-top">
                            <button type="submit" class="btn btn-success d-inline-flex align-items-center px-4 py-2">
                                <i class="bi bi-save-fill me-2"></i> {{ __('Kemaskini Pengguna') }}
                            </button>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
