{{-- resources/views/livewire/settings/users/create-user.blade.php --}}
<div>
    {{-- If using event-based title updates from the Livewire component, this @section is not needed. --}}
    {{-- @section('title', __('Tambah Pengguna Baru')) --}}

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h2 class="h4 fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        {{ __('Tambah Pengguna Baru') }}
                    </h2>
                    <a href="{{ route('settings.users.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Pengguna') }}
                    </a>
                </div>

                {{-- Ensure this path points to your general alert display partial --}}
                @include('_partials._alerts.alert-general')

                <div class="card shadow-sm motac-card">
                    <div class="card-header motac-card-header py-3">
                        <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                            <i class="bi bi-person-vcard-fill me-2"></i>
                            {{ __('Butiran Pengguna') }}
                        </h5>
                    </div>
                    <div class="card-body p-4 motac-card-body">
                        <form wire:submit.prevent="saveUser">
                            @csrf {{-- Not strictly necessary for Livewire submit --}}

                            <div class="row g-3">
                                {{-- Title --}}
                                <div class="col-md-4">
                                    <label for="title" class="form-label fw-medium">{{ __('Gelaran') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="title" wire:model="title"
                                        class="form-select @error('title') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Gelaran') }} --</option>
                                        @foreach ($titleOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Name --}}
                                <div class="col-md-8">
                                    <label for="name" class="form-label fw-medium">{{ __('Nama Penuh') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="name" wire:model.defer="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="{{ __('Masukkan nama penuh') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Identification Number (NRIC) --}}
                                <div class="col-md-6">
                                    <label for="identification_number"
                                        class="form-label fw-medium">{{ __('No. Kad Pengenalan') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="identification_number"
                                        wire:model.defer="identification_number"
                                        class="form-control @error('identification_number') is-invalid @enderror"
                                        placeholder="{{ __('Cth: 800101010001') }}">
                                    @error('identification_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Passport Number --}}
                                <div class="col-md-6">
                                    <label for="passport_number"
                                        class="form-label fw-medium">{{ __('No. Pasport (Jika Ada)') }}</label>
                                    <input type="text" id="passport_number" wire:model.defer="passport_number"
                                        class="form-control @error('passport_number') is-invalid @enderror">
                                    @error('passport_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Personal Email --}}
                                <div class="col-md-6">
                                    <label for="personal_email"
                                        class="form-label fw-medium">{{ __('E-mel Peribadi (Untuk Log Masuk)') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" id="personal_email" wire:model.defer="personal_email"
                                        class="form-control @error('personal_email') is-invalid @enderror"
                                        placeholder="{{ __('pengguna@example.com') }}">
                                    @error('personal_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- MOTAC Email --}}
                                <div class="col-md-6">
                                    <label for="motac_email"
                                        class="form-label fw-medium">{{ __('E-mel Rasmi MOTAC (Jika Ada)') }}</label>
                                    <input type="email" id="motac_email" wire:model.defer="motac_email"
                                        class="form-control @error('motac_email') is-invalid @enderror"
                                        placeholder="{{ __('pengguna@motac.gov.my') }}">
                                    @error('motac_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mobile Number --}}
                                <div class="col-md-6">
                                    <label for="mobile_number"
                                        class="form-label fw-medium">{{ __('No. Telefon Bimbit') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" id="mobile_number" wire:model.defer="mobile_number"
                                        class="form-control @error('mobile_number') is-invalid @enderror"
                                        placeholder="{{ __('Cth: 0123456789') }}">
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Department --}}
                                <div class="col-md-6">
                                    <label for="department_id"
                                        class="form-label fw-medium">{{ __('Jabatan/Bahagian') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="department_id" wire:model="department_id"
                                        class="form-select @error('department_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                                        @foreach ($departmentOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Position --}}
                                <div class="col-md-6">
                                    <label for="position_id" class="form-label fw-medium">{{ __('Jawatan') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="position_id" wire:model="position_id"
                                        class="form-select @error('position_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jawatan') }} --</option>
                                        @foreach ($positionOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Grade --}}
                                <div class="col-md-6">
                                    <label for="grade_id" class="form-label fw-medium">{{ __('Gred') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="grade_id" wire:model="grade_id"
                                        class="form-select @error('grade_id') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Gred') }} --</option>
                                        @foreach ($gradeOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('grade_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Level (Aras) --}}
                                <div class="col-md-6">
                                    <label for="level" class="form-label fw-medium">{{ __('Aras') }}</label>
                                    <select id="level" wire:model="level"
                                        class="form-select @error('level') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Aras') }} --</option>
                                        @foreach ($levelOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Service Status --}}
                                <div class="col-md-6">
                                    <label for="service_status"
                                        class="form-label fw-medium">{{ __('Taraf Perkhidmatan') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="service_status" wire:model="service_status"
                                        class="form-select @error('service_status') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Taraf Perkhidmatan') }} --</option>
                                        @foreach ($serviceStatusOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Appointment Type --}}
                                <div class="col-md-6">
                                    <label for="appointment_type"
                                        class="form-label fw-medium">{{ __('Jenis Pelantikan') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="appointment_type" wire:model="appointment_type"
                                        class="form-select @error('appointment_type') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jenis Pelantikan') }} --</option>
                                        @foreach ($appointmentTypeOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Previous Department Name (Conditional based on appointment_type) --}}
                                <div class="col-md-6">
                                    <label for="previous_department_name"
                                        class="form-label fw-medium">{{ __('Nama Jabatan Terdahulu (Jika Berkaitan)') }}</label>
                                    <input type="text" id="previous_department_name"
                                        wire:model.defer="previous_department_name"
                                        class="form-control @error('previous_department_name') is-invalid @enderror">
                                    @error('previous_department_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="previous_department_email"
                                        class="form-label fw-medium">{{ __('E-mel Jabatan Terdahulu (Jika Berkaitan)') }}</label>
                                    <input type="email" id="previous_department_email"
                                        wire:model.defer="previous_department_email"
                                        class="form-control @error('previous_department_email') is-invalid @enderror">
                                    @error('previous_department_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">{{ __('Kata Laluan') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password" id="password" wire:model="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        {{ __('Min. 8 aksara, mesti ada huruf besar, huruf kecil, nombor, dan simbol.') }}
                                    </div>
                                </div>

                                {{-- Password Confirmation --}}
                                <div class="col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label fw-medium">{{ __('Sahkan Kata Laluan') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password" id="password_confirmation"
                                        wire:model="password_confirmation" class="form-control">
                                </div>

                                {{-- Roles --}}
                                <div class="col-md-6">
                                    <label for="selectedRoles"
                                        class="form-label fw-medium">{{ __('Peranan (Roles)') }}</label>
                                    <select id="selectedRoles" wire:model="selectedRoles"
                                        class="form-select @error('selectedRoles') is-invalid @enderror" multiple
                                        size="3">
                                        @foreach ($allRoles as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedRoles')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('selectedRoles.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        {{ __('Pilih satu atau lebih peranan. Tahan Ctrl (atau Cmd pada Mac) untuk memilih beberapa.') }}
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-medium">{{ __('Status Akaun') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="status" wire:model="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                        {{-- Assuming User model has getStatusOptions() or similar --}}
                                        @foreach (App\Models\User::getStatusOptions() as $key => $value)
                                            <option value="{{ $key }}">{{ __($value) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top text-end">
                                <button type="button" wire:click="resetForm"
                                    class="btn btn-outline-secondary me-2 motac-btn-outline">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset Borang') }}
                                </button>
                                <button type="submit" class="btn btn-primary px-4 motac-btn-primary"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="saveUser">
                                        <i class="bi bi-save-fill me-1"></i> {{ __('Simpan Pengguna') }}
                                    </span>
                                    <span wire:loading wire:target="saveUser"
                                        class="d-inline-flex align-items-center">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"
                                            aria-hidden="true"></span>
                                        {{ __('Menyimpan...') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
