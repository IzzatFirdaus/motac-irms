{{-- resources/views/admin/departments/department-index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Jabatan/Unit'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Jabatan/Unit') }}</h2>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary d-inline-flex align-items-center">
            <i class="bi bi-plus-circle-fill me-2"></i>{{ __('Tambah Jabatan/Unit Baru') }}
        </a>
    </div>

    <x-alert-bootstrap /> {{-- Display session messages and errors --}}

    {{-- Department/unit search/filter form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.departments.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Cari Jabatan/Unit...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="branch_type" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Jenis Cawangan') }}</option>
                            <option value="{{ App\Models\Department::BRANCH_TYPE_HQ }}" {{ request('branch_type') == App\Models\Department::BRANCH_TYPE_HQ ? 'selected' : '' }}>
                                {{ __(Str::headline(App\Models\Department::BRANCH_TYPE_HQ)) }}
                            </option>
                            <option value="{{ App\Models\Department::BRANCH_TYPE_STATE }}" {{ request('branch_type') == App\Models\Department::BRANCH_TYPE_STATE ? 'selected' : '' }}>
                                {{ __(Str::headline(App\Models\Department::BRANCH_TYPE_STATE)) }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">{{ __('Cari') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($departments->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            {{ __('Tiada jabatan/unit ditemui yang sepadan dengan carian anda.') }}
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Nama Jabatan/Unit') }}</th>
                        <th>{{ __('Jenis Cawangan') }}</th>
                        <th>{{ __('Kod') }}</th>
                        <th>{{ __('Ketua Jabatan/Unit') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $dept)
                    <tr>
                        <td>{{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}</td>
                        <td>
                            <a href="{{ route('admin.departments.show', $dept) }}" class="fw-semibold text-primary text-decoration-none">
                                {{ $dept->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ __(Str::headline($dept->branch_type)) }}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace">{{ $dept->code ?: '-' }}</span>
                        </td>
                        <td>
                            @if($dept->headOfDepartment)
                                <span>{{ $dept->headOfDepartment->name }}</span>
                                <span class="text-muted small d-block">{{ $dept->headOfDepartment->email }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $dept->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $dept->is_active ? __('Aktif') : __('Tidak Aktif') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.departments.show', $dept) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Lihat Butiran') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Kemaskini') }}">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($departments->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $departments->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
