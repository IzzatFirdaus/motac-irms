{{-- resources/views/livewire/settings/users/users-form-fields.blade.php --}}
{{-- Shared form fields for create/edit user operations --}}
{{-- This partial is used by both users-create.blade.php and users-edit.blade.php --}}
{{-- Mode parameter ('create' or 'edit') controls which fields are shown --}}

<form wire:submit.prevent="saveUser">
    <div class="row g-3">
        {{-- Section 1: Basic Personal Information --}}
        {{-- Title (Gelaran) - Required field --}}
        <div class="col-md-4">
            <label for="title" class="form-label fw-medium">{{ __('Gelaran') }} <span class="text-danger">*</span></label>
            <select id="title" wire:model.blur="title" class="form-select @error('title') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Gelaran') }} --</option>
                @foreach ($titleOptions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Full Name - Required field --}}
        <div class="col-md-8">
            <label for="name" class="form-label fw-medium">{{ __('Nama Penuh') }} <span class="text-danger">*</span></label>
            <input type="text" id="name" wire:model.blur="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Masukkan nama penuh') }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Identification Number (NRIC) - Required field --}}
        <div class="col-md-6">
            <label for="identification_number" class="form-label fw-medium">{{ __('No. Kad Pengenalan') }} <span class="text-danger">*</span></label>
            <input type="text" id="identification_number" wire:model.blur="identification_number" class="form-control @error('identification_number') is-invalid @enderror" placeholder="{{ __('Cth: 800101010001') }}">
            @error('identification_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Passport Number - Optional field --}}
        <div class="col-md-6">
            <label for="passport_number" class="form-label fw-medium">{{ __('No. Pasport (Jika Ada)') }}</label>
            <input type="text" id="passport_number" wire:model.blur="passport_number" class="form-control @error('passport_number') is-invalid @enderror" placeholder="{{ __('Cth: A12345678') }}">
            @error('passport_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Section 2: Contact Information --}}
        {{-- Personal Email - Required field, used for login --}}
        <div class="col-md-6">
            <label for="personal_email" class="form-label fw-medium">{{ __('E-mel Peribadi (Untuk Log Masuk)') }} <span class="text-danger">*</span></label>
            <input type="email" id="personal_email" wire:model.blur="personal_email" class="form-control @error('personal_email') is-invalid @enderror" placeholder="{{ __('pengguna@example.com') }}">
            @error('personal_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- MOTAC Official Email - Optional field --}}
        <div class="col-md-6">
            <label for="motac_email" class="form-label fw-medium">{{ __('E-mel Rasmi MOTAC (Jika Ada)') }}</label>
            <input type="email" id="motac_email" wire:model.blur="motac_email" class="form-control @error('motac_email') is-invalid @enderror" placeholder="{{ __('pengguna@motac.gov.my') }}">
            @error('motac_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Mobile Number - Required field --}}
        <div class="col-md-6">
            <label for="mobile_number" class="form-label fw-medium">{{ __('No. Telefon Bimbit') }} <span class="text-danger">*</span></label>
            <input type="tel" id="mobile_number" wire:model.blur="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror" placeholder="{{ __('Cth: 0123456789') }}">
            @error('mobile_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Section 3: Organizational Information --}}
        {{-- Department - Required field --}}
        <div class="col-md-6">
            <label for="department_id" class="form-label fw-medium">{{ __('Jabatan/Bahagian') }} <span class="text-danger">*</span></label>
            <select id="department_id" wire:model.blur="department_id" class="form-select @error('department_id') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                @foreach ($departmentOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            @error('department_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Position - Required field --}}
        <div class="col-md-6">
            <label for="position_id" class="form-label fw-medium">{{ __('Jawatan') }} <span class="text-danger">*</span></label>
            <select id="position_id" wire:model.blur="position_id" class="form-select @error('position_id') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Jawatan') }} --</option>
                @foreach ($positionOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            @error('position_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Grade - Required field --}}
        <div class="col-md-6">
            <label for="grade_id" class="form-label fw-medium">{{ __('Gred') }} <span class="text-danger">*</span></label>
            <select id="grade_id" wire:model.blur="grade_id" class="form-select @error('grade_id') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Gred') }} --</option>
                @foreach ($gradeOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            @error('grade_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Level (Aras) - Optional field --}}
        <div class="col-md-6">
            <label for="level" class="form-label fw-medium">{{ __('Aras') }}</label>
            <select id="level" wire:model.blur="level" class="form-select @error('level') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Aras') }} --</option>
                @foreach ($levelOptions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @error('level')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Section 4: Service Information --}}
        {{-- Service Status - Required field --}}
        <div class="col-md-6">
            <label for="service_status" class="form-label fw-medium">{{ __('Taraf Perkhidmatan') }} <span class="text-danger">*</span></label>
            <select id="service_status" wire:model.blur="service_status" class="form-select @error('service_status') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Taraf Perkhidmatan') }} --</option>
                @foreach ($serviceStatusOptions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @error('service_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Appointment Type - Required field --}}
        <div class="col-md-6">
            <label for="appointment_type" class="form-label fw-medium">{{ __('Jenis Pelantikan') }} <span class="text-danger">*</span></label>
            <select id="appointment_type" wire:model.blur="appointment_type" class="form-select @error('appointment_type') is-invalid @enderror">
                <option value="">-- {{ __('Pilih Jenis Pelantikan') }} --</option>
                @foreach ($appointmentTypeOptions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @error('appointment_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Section 5: Previous Department Information (Optional) --}}
        {{-- Previous Department Name - Optional field --}}
        <div class="col-md-6">
            <label for="previous_department_name" class="form-label fw-medium">{{ __('Nama Jabatan Terdahulu (Jika Berkaitan)') }}</label>
            <input type="text" id="previous_department_name" wire:model.blur="previous_department_name" class="form-control @error('previous_department_name') is-invalid @enderror" placeholder="{{ __('Cth: Jabatan Teknologi Maklumat') }}">
            @error('previous_department_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Previous Department Email - Optional field --}}
        <div class="col-md-6">
            <label for="previous_department_email" class="form-label fw-medium">{{ __('E-mel Jabatan Terdahulu (Jika Berkaitan)') }}</label>
            <input type="email" id="previous_department_email" wire:model.blur="previous_department_email" class="form-control @error('previous_department_email') is-invalid @enderror" placeholder="{{ __('cth@jabatan.gov.my') }}">
            @error('previous_department_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Section 6: Authentication (Only shown on create mode) --}}
        {{-- Password fields are only displayed when creating a new user --}}
        @if($mode === 'create')
        <div class="col-md-6">
            <label for="password" class="form-label fw-medium">{{ __('Kata Laluan') }} <span class="text-danger">*</span></label>
            <input type="password" id="password" wire:model.blur="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Masukkan kata laluan') }}">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
                {{ __('Min. 8 aksara, mesti ada huruf besar, huruf kecil, nombor, dan simbol.') }}
            </div>
        </div>

        {{-- Password Confirmation - Required only on create --}}
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label fw-medium">{{ __('Sahkan Kata Laluan') }} <span class="text-danger">*</span></label>
            <input type="password" id="password_confirmation" wire:model.blur="password_confirmation" class="form-control" placeholder="{{ __('Sahkan kata laluan') }}">
            <div class="form-text">
                {{ __('Masukkan semula kata laluan untuk pengesahan.') }}
            </div>
        </div>
        @endif

        {{-- Section 7: User Roles and Permissions --}}
        {{-- Roles Selection - Multi-select field --}}
        <div class="col-md-6">
            <label for="selectedRoles" class="form-label fw-medium">{{ __('Peranan (Roles)') }}</label>
            <select id="selectedRoles" wire:model.blur="selectedRoles" class="form-select @error('selectedRoles') is-invalid @enderror" multiple size="5">
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

        {{-- Account Status - Required field --}}
        <div class="col-md-6">
            <label for="status" class="form-label fw-medium">{{ __('Status Akaun') }} <span class="text-danger">*</span></label>
            <select id="status" wire:model.blur="status" class="form-select @error('status') is-invalid @enderror">
                @foreach (App\Models\User::getStatusOptions() as $key => $value)
                    <option value="{{ $key }}">{{ __($value) }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
                {{ __('Tentukan status akaun pengguna - Aktif atau Tidak Aktif.') }}
            </div>
        </div>
    </div>

    {{-- Section 8: Form Action Buttons --}}
    {{-- Submit and Reset buttons with loading states --}}
    <div class="mt-4 pt-3 border-top text-end motac-modal-footer">
        {{-- Reset Form Button - Only show on create mode --}}
        @if($mode === 'create')
        <button type="button" wire:click="resetForm" class="motac-btn-outline me-2" aria-label="{{ __('Reset Borang') }}">
            <i class="fas fa-undo me-1" aria-hidden="true"></i> {{ __('Reset Borang') }}
        </button>
        @endif

        {{-- Submit Button - Different text based on mode --}}
        <button type="submit" class="motac-btn-primary px-4" wire:loading.attr="disabled" aria-live="polite">
            {{-- Normal state - show appropriate text based on create/edit mode --}}
            <span wire:loading.remove wire:target="saveUser">
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                {{ $mode === 'create' ? __('Simpan Pengguna') : __('Kemaskini Pengguna') }}
            </span>
            {{-- Loading state - show spinner and loading text --}}
            <span wire:loading wire:target="saveUser" class="d-inline-flex align-items-center">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ __('Menyimpan...') }}
            </span>
        </button>
    </div>
</form>
