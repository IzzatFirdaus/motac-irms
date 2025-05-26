<div>
    @section('title', __('Pengurusan Pentadbir Pengguna'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">{{ __('Senarai Pengguna Sistem') }}</h1>
        @can('create', App\Models\User::class)
            <a href="{{ route('settings.users.create') }}" wire:navigate
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
                <i class="ti ti-user-plus {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Tambah Pengguna Baru') }}
            </a>
        @endcan
    </div>

     @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters and Search --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-lg-6 col-md-12">
                    <label for="userSearchAdmin" class="form-label">{{ __('Carian (Nama, Emel, No. KP, Jabatan)') }}</label>
                    <input wire:model.live.debounce.300ms="search" type="text" id="userSearchAdmin"
                        placeholder="{{ __('Masukkan kata kunci...') }}"
                        class="form-control form-control-sm">
                </div>
                <div class="col-lg-6 col-md-12">
                    <label for="userRoleFilterAdmin" class="form-label">{{ __('Saring Ikut Peranan') }}</label>
                    <select wire:model.live="filterRole" id="userRoleFilterAdmin" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Peranan') }}</option>
                        @foreach ($rolesForFilter as $roleNameValue => $roleNameDisplay)
                            <option value="{{ $roleNameValue }}">{{ $roleNameDisplay }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Emel') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. KP') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span class="visually-hidden">{{ __('Tindakan') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                     <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="7" class="p-0">

                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($usersList as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $user->name }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $user->email }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $user->identification_number ?? '-'}}</td>
                            <td class="px-3 py-2 small text-muted">{{ $user->department->name ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-3 py-2 small">
                                <span class="badge rounded-pill {{ \App\Helpers\Helpers::getBootstrapStatusColorClass($user->status) }}">
                                    {{ $user->status === 'active' ? __('Aktif') : __('Tidak Aktif') }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-end">
                                @can('view', $user)
                                <a href="{{ route('settings.users.show', $user->id) }}" wire:navigate class="btn btn-sm btn-outline-info border-0 p-1" title="{{ __('Lihat Profil') }}">
                                    <i class="ti ti-eye fs-6"></i>
                                </a>
                                @endcan
                                @can('update', $user)
                                <a href="{{ route('settings.users.edit', $user->id) }}" wire:navigate class="btn btn-sm btn-outline-secondary border-0 p-1 ms-1" title="{{ __('Kemaskini') }}">
                                    <i class="ti ti-pencil fs-6"></i>
                                </a>
                                @endcan
                                @can('delete', $user)
                                {{-- This button should ideally trigger a confirmation modal --}}
                                <button wire:click="confirmUserDeletion({{ $user->id }})" type="button" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1" title="{{ __('Padam') }}">
                                    <i class="ti ti-trash fs-6"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-5 text-center">
                               <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="ti ti-users-off fs-1 mb-2 text-secondary"></i>
                                    {{ __('Tiada rekod pengguna ditemui.') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($usersList->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $usersList->links() }}
        </div>
    @endif

    {{-- Note: Modals for create/edit are handled by separate routes/components (CreateUser.php, EditUser.php) --}}
    {{-- A delete confirmation modal could be added here if not using a JavaScript confirm dialog --}}

</div>
