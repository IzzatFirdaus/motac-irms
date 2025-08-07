{{-- resources/views/users/user-index.blade.php --}}
{{-- User Index Page: Displays a list of all registered users in the system --}}
@extends('layouts.app')

@section('title', __('Senarai Pengguna Sistem'))

@section('content')
    <div class="container py-4">

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

        @include('partials.session-messages') {{-- Includes flash/session messages --}}

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
                                        @if ($user->hasRole('Admin')) {{-- Show Admin badge if user has Admin role --}}
                                            <span class="badge bg-primary rounded-pill ms-1 align-middle" style="font-size: 0.7em;">{{__('Admin')}}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 small text-muted">{{ $user->motac_email ?? '-' }}</td>
                                    <td class="py-2 px-3 small text-muted">{{ optional($user->department)->name ?? '-' }}</td>
                                    <td class="py-2 px-3 small text-muted">{{ optional($user->grade)->name ?? '-' }}</td>
                                    <td class="text-end py-2 px-3">
                                        {{-- View Profile button --}}
                                        <a href="{{ route('users.show', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                                            title="{{__('Lihat Profil')}}">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        {{-- Edit/Delete only in admin/settings context --}}
                                        {{-- @can('update', $user)
                                        <a href="{{ route('settings.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary ms-1" title="{{__('Edit Pengguna (Admin)')}}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $user)
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-1" title="{{__('Padam Pengguna (Admin)')}}"
                                                onclick="confirmDelete('{{ route('settings.users.destroy', $user->id) }}')">
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
        </div>

        @if ($users->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $users->links() }} {{-- Use Bootstrap 5 pagination styling --}}
            </div>
        @endif
    </div>
@endsection

{{--
@push('scripts')
<script>
    function confirmDelete(deleteUrl) {
        if (confirm("{{ __('Adakah anda pasti untuk memadam pengguna ini? Tindakan ini tidak boleh diundur.') }}")) {
            // Create and submit a form for the delete action
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
@endpush
--}}
