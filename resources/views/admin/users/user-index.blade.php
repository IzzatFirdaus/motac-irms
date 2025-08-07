{{-- resources/views/admin/users/user-index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Pengguna Sistem'))

@section('content')
<div class="container-fluid py-4"> {{-- Using container-fluid for potentially wider table display --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Pengguna Sistem') }}</h1>
        @can('create users')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-person-plus-fill me-1"></i> {{ __('Tambah Pengguna Baru') }}
            </a>
        @endcan
    </div>

    {{-- Session flash messages --}}
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

    <div class="card shadow-sm">
         <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
            <h3 class="h5 card-title fw-semibold mb-0">
                {{ __('Pengguna Berdaftar') }}
            </h3>
            {{-- Search/filter form can be added here if needed --}}
        </div>
        @if ($users->isEmpty())
            <div class="card-body">
                <div class="alert alert-info text-center mb-0" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada pengguna sistem ditemui.') }}
                    @if(request('search'))
                        {{ __('Cuba kata kunci carian yang berbeza.') }}
                    @endif
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Emel Login') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium text-center px-3 py-2">{{ __('Status') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Emel MOTAC') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Gred') }}</th>
                            <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-3 py-2 small text-dark fw-medium">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none text-primary-emphasis">
                                        {{ $user->name ?? 'N/A' }}
                                    </a>
                                    @if($user->full_name && $user->full_name !== $user->name)
                                        <div class="text-muted" style="font-size: 0.8em;">({{ $user->full_name }})</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 small text-muted">{{ $user->email ?? 'N/A' }}</td>
                                <td class="px-3 py-2 small text-muted">
                                    @forelse ($user->getRoleNames() as $roleName)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis fw-normal">{{ $roleName }}</span>
                                    @empty
                                        {{ __('Tiada Peranan') }}
                                    @endforelse
                                </td>
                                <td class="px-3 py-2 small text-center">
                                    <span class="badge rounded-pill {{ $user->status === 'active' ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                        {{ __(Str::title($user->status ?? 'N/A')) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 small text-muted">{{ $user->motac_email ?? '-' }}</td>
                                <td class="px-3 py-2 small text-muted">{{ $user->department->name ?? '-' }}</td>
                                <td class="px-3 py-2 small text-muted">{{ $user->grade->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @can('view', $user)
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="{{__('Lihat')}}"><i class="bi bi-eye-fill"></i></a>
                                        @endcan
                                        @can('update', $user)
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary border-0 p-1" title="{{__('Kemaskini')}}"><i class="bi bi-pencil-fill"></i></a>
                                        @endcan
                                        @can('delete', $user)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('{{ __('Adakah anda pasti ingin memadam pengguna :name?', ['name' => $user->name]) }}');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="{{__('Padam')}}"><i class="bi bi-trash3-fill"></i></button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
                <div class="card-footer bg-light border-top-0 py-3">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
