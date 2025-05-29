{{-- resources/views/admin/positions/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Jawatan') . ': ' . ($position->name ?? 'N/A'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('Butiran Jawatan') }}: <span class="text-primary">{{ $position->name ?? 'N/A' }}</span>
                    </h1>
                    <div>
                        <a href="{{ route('admin.positions.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center me-2">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai') }}
                        </a>
                        @can('update', $position)
                            <a href="{{ route('admin.positions.edit', $position) }}"
                                class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-pencil-square me-1"></i>{{ __('Kemaskini') }}
                            </a>
                        @endcan
                    </div>
                </div>

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

                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h3 class="h5 card-title fw-semibold mb-0">{{ __('Maklumat Jawatan') }}</h3>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <dl class="row g-2 small">
                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Nama Jawatan') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $position->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Kod Jawatan') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $position->code ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Keterangan') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark" style="white-space: pre-wrap;">
                                {{ $position->description ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Gred Berkaitan') }}</dt>
                            <dd class="col-sm-8 col-lg-9 text-dark">{{ $position->grade?->name ?? '-' }}</dd>

                            <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Status Jawatan') }}</dt>
                            <dd class="col-sm-8 col-lg-9">
                                @if ($position->is_active)
                                    <span
                                        class="badge rounded-pill bg-success-subtle text-success-emphasis">{{ __('Aktif') }}</span>
                                @else
                                    <span
                                        class="badge rounded-pill bg-danger-subtle text-danger-emphasis">{{ __('Tidak Aktif') }}</span>
                                @endif
                            </dd>

                            @if ($position->created_at)
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Dicipta Pada') }}</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $position->created_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>
                            @endif
                            @if ($position->updated_at)
                                <dt class="col-sm-4 col-lg-3 fw-medium text-muted">{{ __('Dikemaskini Pada') }}</dt>
                                <dd class="col-sm-8 col-lg-9 text-dark">
                                    {{ $position->updated_at?->translatedFormat('d M Y, h:i A') ?? '-' }}</dd>
                            @endif
                        </dl>
                    </div>
                    @can('delete', $position)
                        <div class="card-footer bg-light text-end py-3 border-top">
                            <form method="POST" action="{{ route('admin.positions.destroy', $position) }}"
                                onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam jawatan :name? Tindakan ini tidak boleh diundur.', ['name' => $position->name]) }}');"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                    <i class="bi bi-trash3-fill me-1"></i> {{ __('Padam Jawatan') }}
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
