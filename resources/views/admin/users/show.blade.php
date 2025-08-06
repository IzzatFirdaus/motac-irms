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
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('Maklumat Asas Pengguna') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row g-3 small">
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nama Penuh:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->full_name ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nama Pengguna (Login):') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->name ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('E-mel:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->email ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('No. Telefon:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $user->phone_number ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jawatan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                @if ($user->position)
                                    {{ $user->position->name }}
                                @else
                                    -
                                @endif
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Gred Jawatan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                @if ($user->grade)
                                    {{ $user->grade->name }} ({{ $user->grade->level }})
                                @else
                                    -
                                @endif
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Jabatan:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                @if ($user->department)
                                    {{ $user->department->name }}
                                @else
                                    -
                                @endif
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Peranan Sistem:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                @empty
                                    -
                                @endforelse
                            </dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Pengguna:') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">
                                @if ($user->is_active)
                                    <span class="badge bg-success">{{ __('Aktif') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Tidak Aktif') }}</span>
                                @endif
                            </dd>
                            @if ($user->created_at)
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Dicipta Pada:') }}</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $user->created_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>
                            @endif
                            @if ($user->updated_at)
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Dikemaskini Pada') }}</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $user->updated_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>
                            @endif
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
