{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('Senarai Pengguna Sistem'))
=======
@section('title', __('All Users'))
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)

@section('content')
    <div class="container py-4">

<<<<<<< HEAD
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Senarai Pengguna Sistem') }}</h1>
            {{-- Add Create User button if policy allows. Ensure route 'admin.users.create' or similar exists for admin context. --}}
            {{-- For general user list, creation might not be applicable here. Settings panel handles user creation. --}}
            {{-- @can('create', App\Models\User::class)
            <a href="{{ route('settings.users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-circle-fill me-1"></i> {{ __('Tambah Pengguna Baru') }}
            </a>
            @endcan --}}
        </div>

        @include('partials.session-messages') {{-- Assuming a partial for session messages --}}

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h2 class="h5 card-title fw-semibold mb-0">{{ __('Pengguna Berdaftar') }}</h2>
            </div>
            @if ($users->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-people-fill fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Pengguna Ditemui') }}</h5>
                    <p class="small">{{ __('Sistem tidak mempunyai sebarang pengguna berdaftar pada masa ini.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Nama Penuh') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('E-mel MOTAC') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Jabatan') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium">{{ __('Gred') }}</th>
                                <th scope="col" class="py-2 px-3 small text-uppercase text-muted fw-medium text-end">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="py-2 px-3 small text-dark">
                                        {{ $user->full_name ?? $user->name }}
                                        @if ($user->hasRole('Admin')) {{-- Example check --}}
                                            <span class="badge bg-primary rounded-pill ms-1 align-middle" style="font-size: 0.7em;">{{__('Admin')}}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 small text-muted">{{ $user->motac_email ?? '-' }}</td>
                                    <td class="py-2 px-3 small text-muted">{{ optional($user->department)->name ?? '-' }}</td>
                                    <td class="py-2 px-3 small text-muted">{{ optional($user->grade)->name ?? '-' }}</td>
                                    <td class="text-end py-2 px-3">
                                        {{-- Assuming 'users.show' is a general user profile view route --}}
                                        <a href="{{ route('users.show', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                                            title="{{__('Lihat Profil')}}">
                                            <i class="bi bi-eye-fill"></i>
                                            {{-- <span class="ms-1 d-none d-sm-inline">{{ __('Lihat') }}</span> --}}
                                        </a>
                                        {{-- Edit/Delete buttons are typically part of admin user management (Settings panel) --}}
                                        {{-- @can('update', $user)
                                        <a href="{{ route('settings.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary ms-1" title="{{__('Edit Pengguna (Admin)')}}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $user)
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-1" title="{{__('Padam Pengguna (Admin)')}}"
                                                onclick="confirmDelete('{{ route('settings.users.destroy', $user->id) }}')"> {{-- Assuming a JS confirmDelete function --}}
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        @endcan --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
=======
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('All Users') }}</h1>
            {{-- Add Create User button if applicable --}}
            {{-- @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Create User') }}
            </a>
        @endcan --}}
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($users->isEmpty())
                    <div class="alert alert-info" role="alert">
                        {{ __('No users found.') }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Name') }}
                                    </th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Department') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Grade') }}
                                    </th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium text-end">
                                        {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="align-middle">{{ $user->full_name }}</td>
                                        <td class="align-middle">{{ $user->department->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $user->grade->name ?? '-' }}</td>
                                        <td class="text-end align-middle">
                                            <a href="{{ route('users.show', $user->id) }}"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                                <i class="bi bi-eye me-1"></i> {{ __('View') }}
                                            </a>
                                            {{-- Add Edit/Delete buttons if applicable --}}
                                            {{-- @can('update', $user)
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary ms-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
        </div>

        @if ($users->hasPages())
            <div class="mt-4 d-flex justify-content-center">
<<<<<<< HEAD
                {{ $users->links() }} {{-- Ensure pagination is Bootstrap 5 styled --}}
=======
                {{ $users->links() }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
            </div>
        @endif
    </div>
@endsection
<<<<<<< HEAD

{{-- @push('scripts')
<script>
    function confirmDelete(deleteUrl) {
        if (confirm("{{ __('Adakah anda pasti untuk memadam pengguna ini? Tindakan ini tidak boleh diundur.') }}")) {
            // Create a form and submit it for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush --}}
=======
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
