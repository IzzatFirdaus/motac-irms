{{-- resources/views/admin/users/user-create.blade.php --}}
@extends('layouts.app')

@section('title', __('Tambah Pengguna Baru'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ __('Tambah Pengguna Baru') }}</h1>
                    <a href="{{ route('admin.users.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Pengguna') }}
                    </a>
                </div>

                {{-- Display validation errors if any --}}
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

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
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
                                        value="{{ old('name', request()->query('name')) }}" required
                                        placeholder="{{ __('Nama untuk paparan sistem') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">{{ __('Emel (Untuk Login)') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email"
                                        class="form-control form-control-sm @error('email') is-invalid @enderror"
                                        value="{{ old('email', request()->query('email_guess')) }}" required
                                        placeholder="{{ __('Cth: pengguna@example.com') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">{{ __('Kata Laluan') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-sm @error('password') is-invalid @enderror"
                                        required autocomplete="new-password" placeholder="{{ __('Minimum 8 aksara') }}">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label fw-medium">{{ __('Sahkan Kata Laluan') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control form-control-sm" required autocomplete="new-password"
                                        placeholder="{{ __('Taip semula kata laluan') }}">
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
                                    <label for="title"
                                        class="form-label fw-medium">{{ __('Gelaran (Cth: Encik, Puan)') }}</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control form-control-sm @error('title') is-invalid @enderror"
                                        value="{{ old('title') }}" placeholder="{{ __('Cth: Encik') }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="full_name_input"
                                        class="form-label fw-medium">{{ __('Nama Penuh (Seperti IC/Passport)') }}</label>
                                    <input type="text" name="full_name" id="full_name_input"
                                        class="form-control form-control-sm @error('full_name') is-invalid @enderror"
                                        value="{{ old('full_name', request()->query('name')) }}"
                                        placeholder="{{ __('Nama penuh rasmi') }}">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="identification_number"
                                        class="form-label fw-medium">{{ __('No. Kad Pengenalan (NRIC)') }}</label>
                                    <input type="text" name="identification_number" id="identification_number"
                                        class="form-control form-control-sm @error('identification_number') is-invalid @enderror"
                                        value="{{ old('identification_number') }}"
                                        placeholder="{{ __('Cth: 900101010001') }}">
                                    @error('identification_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="passport_number"
                                        class="form-label fw-medium">{{ __('No. Passport (Jika Ada)') }}</label>
                                    <input type="text" name="passport_number" id="passport_number"
                                        class="form-control form-control-sm @error('passport_number') is-invalid @enderror"
                                        value="{{ old('passport_number') }}"
                                        placeholder="{{ __('Untuk bukan warganegara') }}">
                                    @error('passport_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="personal_email"
                                        class="form-label fw-medium">{{ __('Emel Peribadi') }}</label>
                                    <input type="email" name="personal_email" id="personal_email"
                                        class="form-control form-control-sm @error('personal_email') is-invalid @enderror"
                                        value="{{ old('personal_email') }}"
                                        placeholder="{{ __('Cth: peribadi@example.com') }}">
                                    @error('personal_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="motac_email"
                                        class="form-label fw-medium">{{ __('Emel Rasmi MOTAC') }}</label>
                                    <input type="email" name="motac_email" id="motac_email"
                                        class="form-control form-control-sm @error('motac_email') is-invalid @enderror"
                                        value="{{ old('motac_email') }}"
                                        placeholder="{{ __('Cth: nama@motac.gov.my') }}">
                                    @error('motac_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mobile_number"
                                        class="form-label fw-medium">{{ __('No. Telefon Bimbit') }}</label>
                                    <input type="tel" name="mobile_number" id="mobile_number"
                                        class="form-control form-control-sm @error('mobile_number') is-invalid @enderror"
                                        value="{{ old('mobile_number') }}" placeholder="{{ __('Cth: 0123456789') }}">
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="user_id_assigned"
                                        class="form-label fw-medium">{{ __('ID Pengguna Rangkaian (Jika Ada)') }}</label>
                                    <input type="text" name="user_id_assigned" id="user_id_assigned"
                                        class="form-control form-control-sm @error('user_id_assigned') is-invalid @enderror"
                                        value="{{ old('user_id_assigned') }}"
                                        placeholder="{{ __('ID Network/AD jika ada') }}">
                                    @error('user_id_assigned')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="department_id"
                                        class="form-label fw-medium">{{ __('Jabatan/Unit') }}</label>
                                    <select name="department_id" id="department_id"
                                        class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="position_id" class="form-label fw-medium">{{ __('Jawatan') }}</label>
                                    <select name="position_id" id="position_id"
                                        class="form-select form-select-sm @error('position_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jawatan') }} --</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="grade_id" class="form-label fw-medium">{{ __('Gred') }}</label>
                                    <select name="grade_id" id="grade_id"
                                        class="form-select form-select-sm @error('grade_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Gred') }} --</option>
                                        @foreach ($grades as $grade)
                                            <option value="{{ $grade->id }}"
                                                {{ old('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('grade_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="service_status"
                                        class="form-label fw-medium">{{ __('Taraf Perkhidmatan') }}</label>
                                    <select name="service_status" id="service_status"
                                        class="form-select form-select-sm @error('service_status') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Taraf Perkhidmatan') }} --</option>
                                        @foreach ($serviceStatuses as $statusKey => $statusName)
                                            <option value="{{ $statusKey }}"
                                                {{ old('service_status') == $statusKey ? 'selected' : '' }}>
                                                {{ __($statusName) }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="appointment_type"
                                        class="form-label fw-medium">{{ __('Jenis Pelantikan') }}</label>
                                    <select name="appointment_type" id="appointment_type"
                                        class="form-select form-select-sm @error('appointment_type') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jenis Pelantikan') }} --</option>
                                        @foreach ($appointmentTypes as $typeKey => $typeName)
                                            <option value="{{ $typeKey }}"
                                                {{ old('appointment_type') == $typeKey ? 'selected' : '' }}>
                                                {{ __($typeName) }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-medium">{{ __('Status Pengguna') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="status"
                                        class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                                        @foreach ($userStatuses as $statusKey => $statusName)
                                            <option value="{{ $statusKey }}"
                                                {{ old('status', 'active') == $statusKey ? 'selected' : '' }}>
                                                {{ __($statusName) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if (request()->has('employee_id'))
                                    <input type="hidden" name="employee_id"
                                        value="{{ request()->query('employee_id') }}">
                                @endif
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
                            <label
                                class="form-label fw-medium d-block mb-2">{{ __('Tetapkan Peranan (Pilih satu atau lebih)') }}:</label>
                            @forelse ($roles ?? [] as $role)
                                @if (is_object($role) && property_exists($role, 'id') && property_exists($role, 'name'))
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                            id="role_{{ $role->id }}" value="{{ $role->name }}"
                                            {{ is_array(old('roles')) && in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="role_{{ $role->id }}">{{ $role->name }}</label>
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
                                <i class="bi bi-person-plus-fill me-2"></i> {{ __('Cipta Pengguna') }}
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-x-lg me-1"></i>{{ __('Batal') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
