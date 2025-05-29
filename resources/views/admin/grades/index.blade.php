{{-- resources/views/admin/grades/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Gred Jawatan'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Gred Jawatan') }}</h1>
            @can('create', App\Models\Grade::class)
                <a href="{{ route('admin.grades.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Gred Baru') }}
                </a>
            @endcan
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
            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <h3 class="h5 card-title fw-semibold mb-0">
                    {{ __('Gred Berdaftar') }}
                </h3>
                {{-- Add search/filter form here if needed --}}
            </div>
            @if ($grades->isEmpty())
                <div class="card-body">
                    <div class="alert alert-info text-center mb-0" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada rekod gred ditemui.') }}
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Gred') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tahap (Angka)') }}
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="min-width: 250px;">
                                    {{ __('Keterangan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium text-center px-3 py-2">
                                    {{ __('Gred Pelulus?') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Diluluskan Oleh Gred Min.') }}</th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grades as $grade)
                                <tr>
                                    <td class="px-3 py-2 small text-dark fw-medium">
                                        <a href="{{ route('admin.grades.show', $grade) }}"
                                            class="text-decoration-none text-primary-emphasis">
                                            {{ $grade->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $grade->level ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small text-muted" style="white-space: normal;">
                                        {{ Str::limit($grade->description ?? '-', 70) }}
                                    </td>
                                    <td class="px-3 py-2 small text-center">
                                        @if ($grade->is_approver_grade)
                                            <span
                                                class="badge rounded-pill bg-success-subtle text-success-emphasis">{{ __('Ya') }}</span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-danger-subtle text-danger-emphasis">{{ __('Tidak') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $grade->minApprovalGrade?->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @can('view', $grade)
                                                <a href="{{ route('admin.grades.show', $grade) }}"
                                                    class="btn btn-sm btn-outline-secondary border-0 p-1"
                                                    title="{{ __('Lihat') }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            @endcan
                                            @can('update', $grade)
                                                <a href="{{ route('admin.grades.edit', $grade) }}"
                                                    class="btn btn-sm btn-outline-primary border-0 p-1"
                                                    title="{{ __('Kemaskini') }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $grade)
                                                <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}"
                                                    onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam gred :name?', ['name' => $grade->name]) }}');"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1"
                                                        title="{{ __('Padam') }}">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($grades->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3">
                        {{ $grades->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
