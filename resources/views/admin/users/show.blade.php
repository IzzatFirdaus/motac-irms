{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Pengguna') . ': ' . ($user->name ?? ($user->full_name ?? 'N/A')))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-2">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Butiran Pengguna') }}: <span
                            class="text-primary">{{ $user->name ?? ($user->full_name ?? 'N/A') }}</span>
                    </h1>
                    <div>
                        <a href="{{ route('admin.users.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center me-2">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                        </a>
                        @can('update', $user)
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-pencil-square me-1"></i>{{ __('Kemaskini') }}
                            </a>
                        @endcan
                    </div>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h3 class="h5 card-title fw-semibold mb-0"><i
                                class="bi bi-key-fill me-2"></i>{{ __('Maklumat Akaun Pengguna') }}</h3>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <dl class="row g-2 small">
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nama Paparan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Emel (Login):') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->email ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Peranan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                @forelse ($user->getRoleNames() as $roleName)
                                    <span
                                        class="badge bg-secondary-subtle text-secondary-emphasis fw-normal me-1">{{ $roleName }}</span>
                                @empty
                                    {{ __('Tiada Peranan') }}
                                @endforelse
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Akaun:') }}</dt>
                            <dd class="col-sm-8 col-lg-9">
                                <span
                                    class="badge rounded-pill {{ $user->status === 'active' ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                    {{ __(Str::title($user->status ?? 'N/A')) }}
                                </span>
                            </dd>

                            @if ($user->email_verified_at)
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Emel Disahkan Pada:') }}</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $user->email_verified_at?->translatedFormat('d M Y, H:i A') ?? '-' }}</dd>
                            @endif

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Dicipta Pada:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $user->created_at?->translatedFormat('d M Y, H:i A') ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Dikemaskini Pada:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $user->updated_at?->translatedFormat('d M Y, H:i A') ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h3 class="h5 card-title fw-semibold mb-0"><i
                                class="bi bi-person-lines-fill me-2"></i>{{ __('Butiran Tambahan MOTAC') }}</h3>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <dl class="row g-2 small">
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Gelaran:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->title ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nama Penuh (IC/Passport):') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->full_name ?? ($user->name ?? 'N/A') }}</dd>
                            {{-- Fallback to name if full_name is null --}}

                            {{-- Employee ID from Employee model if linked, not directly on User model as per design --}}
                            @if ($user->employee)
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('ID Pekerja (HRMS):') }}</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    <a href="{{ route('resource-management.admin.employees.show_profile', $user->employee) }}"
                                        class="text-decoration-none">
                                        {{ $user->employee->employee_id ?? 'N/A' }} <i
                                            class="bi bi-box-arrow-up-right small"></i>
                                    </a>
                                </dd>
                            @endif

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Kad Pengenalan (NRIC):') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $user->identification_number ?? ($user->nric ?? '-') }}</dd> {{-- Prioritize identification_number as per design --}}

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Passport:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->passport_number ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Telefon Bimbit:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->mobile_number ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Emel Peribadi:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->personal_email ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Emel Rasmi MOTAC:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->motac_email ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('ID Pengguna Rangkaian:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->user_id_assigned ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jabatan/Unit:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->department->name ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jawatan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->position->name ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Gred:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->grade->name ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Taraf Perkhidmatan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $user->service_status_label ?? ($user->service_status ? __(Str::title(str_replace('_', ' ', $user->service_status))) : '-') }}
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jenis Pelantikan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                {{ $user->appointment_type_label ?? ($user->appointment_type ? __(Str::title(str_replace('_', ' ', $user->appointment_type))) : '-') }}
                            </dd>
                        </dl>
                    </div>
                    @can('delete', $user)
                        <div class="card-footer bg-light text-end py-3 border-top">
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam pengguna :name? Tindakan ini tidak boleh diundur.', ['name' => $user->name]) }}');"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                    <i class="bi bi-trash3-fill me-1"></i> {{ __('Padam Pengguna') }}
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
