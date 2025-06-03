@extends('layouts.app')

@section('title', __('Maklumat Pengguna')) {{-- Static title, as per recent fix in Livewire component --}}

@section('content')
    {{-- !!! IMPORTANT: This is the ONLY root HTML element for Livewire component [settings.users.show] !!! --}}
    <div>
        <div class="container-fluid px-lg-4 py-4">

            {{-- Page Header and Back Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-user-circle me-2"></i>{{ __('Maklumat Pengguna') }}
                    @if ($userToShow->exists)
                        <span class="text-muted fw-normal ms-2"> - {{ $userToShow->name }}</span>
                    @endif
                </h1>
                <a href="{{ route('settings.users.index') }}" wire:navigate class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('Kembali ke Senarai Pengguna') }}
                </a>
            </div>

            <div class="card shadow-sm motac-card">
                <div class="card-header bg-light py-3 motac-card-header">
                    <h3 class="h5 card-title fw-semibold mb-0">{{ __('Butiran Pengguna') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Profile Photo --}}
                        <div class="col-md-3 text-center mb-4">
                            <img src="{{ $userToShow->profile_photo_url }}" alt="{{ $userToShow->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="mb-1">{{ $userToShow->name }}</h5>
                            <p class="text-muted small">{{ $userToShow->email }}</p>
                            @if ($userToShow->motac_email)
                                <p class="text-muted small mb-0">{{ $userToShow->motac_email }} (MOTAC)</p>
                            @endif
                            <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($userToShow->status ?? '', 'user_status') }} fw-normal mt-2">
                                {{ \App\Models\User::getStatusOptions()[$userToShow->status] ?? $userToShow->status }}
                            </span>
                            <hr class="d-md-none">
                        </div>

                        {{-- User Details --}}
                        <div class="col-md-9">
                            <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Maklumat Peribadi') }}</h6>
                            <dl class="row mb-4">
                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Gelaran') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ \App\Models\User::getTitleOptions()[$userToShow->title] ?? $userToShow->title }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('No. Kad Pengenalan') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ $userToShow->identification_number ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('No. Passport') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ $userToShow->passport_number ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('No. Telefon Bimbit') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ $userToShow->mobile_number ?? 'N/A' }}</dd>
                            </dl>

                            <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Maklumat Organisasi') }}</h6>
                            <dl class="row mb-4">
                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Taraf Perkhidmatan') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ \App\Models\User::getServiceStatusOptions()[$userToShow->service_status] ?? $userToShow->service_status }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Pelantikan') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ \App\Models\User::getAppointmentTypeOptions()[$userToShow->appointment_type] ?? $userToShow->appointment_type }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jabatan') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ optional($userToShow->department)->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jawatan') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ optional($userToShow->position)->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Gred') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ optional($userToShow->grade)->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Aras') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ $userToShow->level ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('Jabatan Terdahulu') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ $userToShow->previous_department_name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 col-lg-3 text-muted">{{ __('E-mel Jabatan Terdahulu') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ $userToShow->previous_department_email ?? 'N/A' }}</dd>
                            </dl>

                            <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Peranan Pengguna') }}</h6>
                            <div class="mb-4">
                                @forelse ($userToShow->roles as $role)
                                    <span class="badge bg-primary-subtle text-primary fw-normal me-2 mb-1">{{ $role->name }}</span>
                                @empty
                                    <span class="badge bg-warning-subtle text-warning fw-normal">{{ __('Tiada Peranan Ditugaskan') }}</span>
                                @endforelse
                            </div>

                            <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">{{ __('Log Audit') }}</h6>
                            <dl class="row mb-0 small text-muted">
                                <dt class="col-sm-4 col-lg-3">{{ __('Dicipta Oleh') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ optional($userToShow->creator)->name ?? 'Sistem' }} pada {{ optional($userToShow->created_at)->translatedFormat('d M Y, h:i A') }}</dd>

                                <dt class="col-sm-4 col-lg-3">{{ __('Dikemaskini Oleh') }}:</dt>
                                <dd class="col-sm-8 col-lg-9">{{ optional($userToShow->updater)->name ?? 'Sistem' }} pada {{ optional($userToShow->updated_at)->translatedFormat('d M Y, h:i A') }}</dd>

                                @if ($userToShow->deleted_at)
                                    <dt class="col-sm-4 col-lg-3">{{ __('Dipadam Oleh') }}:</dt>
                                    <dd class="col-sm-8 col-lg-9">{{ optional($userToShow->deleter)->name ?? 'Sistem' }} pada {{ optional($userToShow->deleted_at)->translatedFormat('d M Y, h:i A') }}</dd>
                                @endif
                            </dl>

                            <div class="d-flex justify-content-end mt-4">
                                @can('update', $userToShow)
                                    <a href="{{ route('settings.users.edit', $userToShow->id) }}" wire:navigate class="btn btn-primary me-2">
                                        <i class="fas fa-edit me-1"></i> {{ __('Edit Pengguna') }}
                                    </a>
                                @endcan
                                @can('delete', $userToShow)
                                    @if (Auth::user()->id !== $userToShow->id) {{-- Prevent deleting self --}}
                                        {{-- Dispatch to open a global delete modal. The actual deletion method is on the Index component. --}}
                                        <button wire:click="$dispatch('open-delete-modal', { id: {{ $userToShow->id }}, itemDescription: '{{ __('pengguna') }} {{ $userToShow->name }}', deleteMethod: 'deleteUser' })" class="btn btn-danger">
                                            <i class="fas fa-trash-alt me-1"></i> {{ __('Padam Pengguna') }}
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- The delete modal itself will be in index.blade.php or your main layout, listening for the 'open-delete-modal' event --}}
    </div>
@endsection
