{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('Butiran Pengguna') . ': ' . ($user->full_name ?? $user->name ?? __('Tidak Diketahui')))
=======
@section('title', __('User Details') . ': ' . ($user->full_name ?? 'N/A'))
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
<<<<<<< HEAD
            <div class="col-lg-9 col-xl-8">

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">
                        {{ __('Profil Pengguna') }}
                    </h1>
                    {{-- Back button --}}
                    <a href="{{ url()->previous(route('users.index')) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Pengguna') }}
                    </a>
                </div>

                @include('partials.session-messages')

                <div class="card shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary bg-gradient text-white p-4">
                        <div class="d-flex align-items-center">
                            {{-- Assuming profile_photo_path is available, or a default avatar logic --}}
                            <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ __('Foto Profil') }} {{ $user->full_name ?? $user->name }}" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.5);">
                            <div>
                                <h2 class="h4 fw-bold mb-0">{{ $user->full_name ?? $user->name ?? __('Tidak Diketahui') }}</h2>
                                <p class="mb-0 small">{{ optional($user->position)->name ?? __('Jawatan Tidak Dinyatakan') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h3 class="h5 fw-semibold text-dark mb-3 border-bottom pb-2 pt-2">{{ __('Maklumat Peribadi & Perkhidmatan') }}</h3>
                        <dl class="row g-3 small">
                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Nama Penuh') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ $user->full_name ?? $user->name ?? __('N/A') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('No. Kad Pengenalan') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ $user->identification_number ?? __('N/A') }}</dd>

                            @if($user->passport_number)
                            <dt class="col-sm-4 text-muted fw-medium">{{ __('No. Pasport') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ $user->passport_number }}</dd>
                            @endif

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('E-mel Peribadi') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ $user->email ?? __('N/A') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('E-mel MOTAC') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ $user->motac_email ?? __('Tiada') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('No. Telefon Bimbit') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ $user->mobile_number ?? __('N/A') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Jabatan') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ optional($user->department)->name ?? __('Tidak Dinyatakan') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Jawatan') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ optional($user->position)->name ?? __('Tidak Dinyatakan') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Gred') }}</dt>
                            <dd class="col-sm-8 text-dark">{{ optional($user->grade)->name ?? __('Tidak Dinyatakan') }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Taraf Perkhidmatan') }}</dt>
                            <dd class="col-sm-8 text-dark text-capitalize">
                                {{-- Assuming a helper or accessor on User model for display name --}}
                                {{ $user->service_status_label ?? str_replace('_', ' ', $user->service_status ?? '-') }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Jenis Pelantikan') }}</dt>
                            <dd class="col-sm-8 text-dark text-capitalize">
                                {{ $user->appointment_type_label ?? str_replace('_', ' ', $user->appointment_type ?? '-') }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-medium">{{ __('Status Akaun') }}</dt>
                            <dd class="col-sm-8 text-dark">
                                <span class="badge rounded-pill {{ $user->status === \App\Models\User::STATUS_ACTIVE ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $user->status === \App\Models\User::STATUS_ACTIVE ? __('Aktif') : __('Tidak Aktif') }}
                                </span>
                            </dd>

                            @if ($user->roles->isNotEmpty())
                                <dt class="col-sm-4 text-muted fw-medium">{{ __('Peranan Sistem') }}</dt>
                                <dd class="col-sm-8 text-dark">
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-info text-dark me-1">{{ e($role->name) }}</span>
                                    @endforeach
                                </dd>
                            @endif
                        </dl>
=======
            <div class="col-lg-8">

                <h1 class="h2 fw-bold text-dark mb-4">{{ __('User Details') }}: {{ $user->full_name ?? 'N/A' }}</h1>

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

                <div class="card shadow-lg rounded-3">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-semibold text-dark mb-3 border-bottom pb-2">{{ __('User Information') }}</h3>
                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Name:') }}</div>
                                <p class="text-dark mb-0">{{ $user->full_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('NRIC:') }}</div>
                                <p class="text-dark mb-0">{{ $user->identification_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('MOTAC Email:') }}</div>
                                <p class="text-dark mb-0">{{ $user->motac_email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Department:') }}</div>
                                <p class="text-dark mb-0">{{ $user->department->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Grade:') }}</div>
                                <p class="text-dark mb-0">{{ $user->grade->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fw-medium">{{ __('Service Status:') }}</div>
                                <p class="text-dark mb-0 text-capitalize">
                                    {{ str_replace('_', ' ', $user->service_status ?? '-') }}</p>
                            </div>
                            {{-- Example for roles --}}
                            {{-- @if ($user->roles->isNotEmpty())
                        <div class="col-md-12">
                            <div class="text-muted fw-medium">{{ __('Roles:') }}</div>
                            <p class="text-dark mb-0">{{ $user->roles->pluck('name')->join(', ') }}</p>
                        </div>
                        @endif --}}
                        </div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </div>
                </div>

                <div class="text-center mt-4">
<<<<<<< HEAD
                    {{-- Edit User button typically available in admin context (Settings panel) --}}
                    {{-- @can('update', $user)
                    <a href="{{ route('settings.users.edit', $user->id) }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="bi bi-pencil-square me-1"></i>
                        {{ __('Edit Pengguna (Admin)') }}
                    </a>
                    @endcan --}}
=======
                    <a href="{{ route('users.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Back to Users List') }}
                    </a>
                    {{-- @can('update', $user)
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info d-inline-flex align-items-center ms-2">
                    <i class="bi bi-pencil-square me-1"></i>
                    {{ __('Edit User') }}
                </a>
                @endcan --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                </div>
            </div>
        </div>
    </div>
@endsection
