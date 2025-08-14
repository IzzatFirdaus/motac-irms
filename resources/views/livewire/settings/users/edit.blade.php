@extends('layouts.app')

@section('title', __('Kemaskini Pengguna'))

@section('content')
    {{-- !!! IMPORTANT: This is the ONLY root HTML element for Livewire !!! --}}
    <div>
        <div class="container-fluid px-lg-4 py-4">

            {{-- Page Header and Back Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-user-edit me-2"></i>{{ __('Kemaskini Pengguna') }}
                    @if ($user->exists)
                        <span class="text-muted fw-normal ms-2"> - {{ $user->name }}</span>
                    @endif
                </h1>
                <a href="{{ route('settings.users.index') }}" wire:navigate class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('Kembali ke Senarai Pengguna') }}
                </a>
            </div>

            {{-- Livewire Flash Messages --}}
            {{-- These alerts are part of the Livewire component's output, so they must be inside the root div. --}}
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
                </div>
            @endif

            <div class="card shadow-sm motac-card">
                <div class="card-header bg-light py-3 motac-card-header">
                    <h3 class="h5 card-title fw-semibold mb-0">{{ __('Borang Maklumat Pengguna') }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveUser">
                        <div class="row g-3">
                            {{-- Section: Maklumat Asas Pengguna (Basic User Information) --}}
                            <div class="col-md-6 mb-4">
                                <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Maklumat Asas Pengguna') }}</h6>

                                <div class="form-group mb-3">
                                    <label for="title" class="form-label fw-medium">{{ __('Gelaran') }}<span class="text-danger">*</span></label>
                                    <select class="form-select @error('title') is-invalid @enderror" id="title" wire:model.blur="title">
                                        <option value="">-- {{ __('Pilih Gelaran') }} --</option>
                                        @foreach($titleOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name" class="form-label fw-medium">{{ __('Nama Penuh') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="{{ __('Contoh: Annis Anwari') }}" wire:model.blur="name">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="identification_number" class="form-label fw-medium">{{ __('No. Kad Pengenalan') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('identification_number') is-invalid @enderror" id="identification_number" placeholder="{{ __('Cth: 800707-02-5044') }}" wire:model.blur="identification_number">
                                    @error('identification_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="passport_number" class="form-label fw-medium">{{ __('No. Pasport (Jika Ada)') }}</label>
                                    <input type="text" class="form-control @error('passport_number') is-invalid @enderror" id="passport_number" wire:model.blur="passport_number">
                                    @error('passport_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="mobile_number" class="form-label fw-medium">{{ __('No. Telefon Bimbit') }}<span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" placeholder="{{ __('Cth: 0123456789') }}" wire:model.blur="mobile_number">
                                    @error('mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="personal_email" class="form-label fw-medium">{{ __('E-mel Peribadi (Untuk Log Masuk)') }}<span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('personal_email') is-invalid @enderror" id="personal_email" placeholder="{{ __('pengguna@example.com') }}" wire:model.blur="personal_email">
                                    @error('personal_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="motac_email" class="form-label fw-medium">{{ __('E-mel Rasmi MOTAC (Jika Ada)') }}</label>
                                    <input type="email" class="form-control @error('motac_email') is-invalid @enderror" id="motac_email" placeholder="{{ __('pengguna@motac.gov.my') }}" wire:model.blur="motac_email">
                                    @error('motac_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Section: Maklumat Perkhidmatan & Organisasi (Service & Organization Info) --}}
                            <div class="col-md-6 mb-4">
                                <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Maklumat Perkhidmatan & Organisasi') }}</h6>

                                <div class="form-group mb-3">
                                    <label for="service_status" class="form-label fw-medium">{{ __('Taraf Perkhidmatan') }}<span class="text-danger">*</span></label>
                                    <select class="form-select @error('service_status') is-invalid @enderror" id="service_status" wire:model.blur="service_status">
                                        <option value="">-- {{ __('Pilih Taraf Perkhidmatan') }} --</option>
                                        @foreach($serviceStatusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="appointment_type" class="form-label fw-medium">{{ __('Jenis Pelantikan') }}<span class="text-danger">*</span></label>
                                    <select class="form-select @error('appointment_type') is-invalid @enderror" id="appointment_type" wire:model.blur="appointment_type">
                                        <option value="">-- {{ __('Pilih Jenis Pelantikan') }} --</option>
                                        @foreach($appointmentTypeOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="department_id" class="form-label fw-medium">{{ __('Jabatan/Bahagian') }}<span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" wire:model.blur="department_id">
                                        <option value="">-- {{ __('Pilih Jabatan') }} --</option>
                                        @foreach($departmentOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="position_id" class="form-label fw-medium">{{ __('Jawatan') }}<span class="text-danger">*</span></label>
                                    <select class="form-select @error('position_id') is-invalid @enderror" id="position_id" wire:model.blur="position_id">
                                        <option value="">-- {{ __('Pilih Jawatan') }} --</option>
                                        @foreach($positionOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="grade_id" class="form-label fw-medium">{{ __('Gred') }}<span class="text-danger">*</span></label>
                                    <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" wire:model.blur="grade_id">
                                        <option value="">-- {{ __('Pilih Gred') }} --</option>
                                        @foreach($gradeOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="level" class="form-label fw-medium">{{ __('Aras') }}</label>
                                    <select class="form-select @error('level') is-invalid @enderror" id="level" wire:model.blur="level">
                                        <option value="">-- {{ __('Pilih Aras') }} --</option>
                                        @foreach($levelOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="previous_department_name" class="form-label fw-medium">{{ __('Nama Jabatan Terdahulu (Jika Berkaitan)') }}</label>
                                    <input type="text" class="form-control @error('previous_department_name') is-invalid @enderror" id="previous_department_name" wire:model.blur="previous_department_name">
                                    @error('previous_department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="previous_department_email" class="form-label fw-medium">{{ __('E-mel Jabatan Terdahulu (Jika Berkaitan)') }}</label>
                                    <input type="email" class="form-control @error('previous_department_email') is-invalid @enderror" id="previous_department_email" wire:model.blur="previous_department_email">
                                    @error('previous_department_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="status" class="form-label fw-medium">{{ __('Status Akaun') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="status" wire:model.blur="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                        {{-- Assuming User model has getStatusOptions() or similar --}}
                                        @foreach (\App\Models\User::getStatusOptions() as $key => $value)
                                            <option value="{{ $key }}">{{ __($value) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section: Peranan Pengguna (User Roles) --}}
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Peranan Pengguna') }}</h6>
                            @error('selectedRoles') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            <div class="row">
                                @foreach($allRoles as $id => $name)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="role-{{ $id }}" value="{{ $id }}" wire:model.live="selectedRoles">
                                            <label class="form-check-label" for="role-{{ $id }}">
                                                {{ $name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('selectedRoles.*') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-4"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveUser">
                                    <i class="fas fa-save me-1"></i> {{ __('Kemaskini Pengguna') }}
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
@endsection
