{{-- resources/views/admin/departments/department-show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Jabatan/Unit: :name', ['name' => $department->name]))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h2 fw-bold text-dark mb-0">
                    {{ __('Butiran Jabatan/Unit') }}
                </h2>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali ke Senarai Jabatan/Unit') }}
                </a>
            </div>

            <x-alert-bootstrap /> {{-- For session messages --}}

            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h3 class="h5 card-title mb-0 fw-semibold">
                        {{ $department->name }}
                        @if($department->code)
                            <small class="text-muted fs-6">({{ $department->code }})</small>
                        @endif
                    </h3>
                </div>
                <div class="card-body p-3 p-md-4">
                    <dl class="row small g-3">
                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Jenis Cawangan:') }}</dt>
                        <dd class="col-sm-8 text-dark">{{ __(Str::headline($department->branch_type)) }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Status:') }}</dt>
                        <dd class="col-sm-8 text-dark">
                            <span class="badge rounded-pill {{ $department->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $department->is_active ? __('Aktif') : __('Tidak Aktif') }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Kod Jabatan/Unit:') }}</dt>
                        <dd class="col-sm-8 text-dark font-monospace">{{ $department->code ?: '-' }}</dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Ketua Jabatan/Unit:') }}</dt>
                        <dd class="col-sm-8 text-dark">
                            @if($department->headOfDepartment)
                                {{ $department->headOfDepartment->name }}
                                <span class="text-muted small d-block">{{ $department->headOfDepartment->email }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 fw-medium text-muted">{{ __('Keterangan:') }}</dt>
                        <dd class="col-sm-8 text-dark" style="white-space: pre-wrap;">
                            {{ $department->description ?: '-' }}
                        </dd>
                    </dl>
                </div>
                <div class="card-footer bg-light text-center py-2">
                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="bi bi-pencil-square me-1"></i>
                        {{ __('Kemaskini Jabatan/Unit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
