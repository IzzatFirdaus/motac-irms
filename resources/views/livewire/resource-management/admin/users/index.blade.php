{{-- resources/views/livewire/resource-management/admin/users/index.blade.php --}}
<div>
    {{-- If using event-based title updates from the Livewire component, this @section is not needed for browser title. --}}
    {{-- @section('title', __('Pengurusan Pentadbir Pengguna')) --}}

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-people-fill me-2"></i>
            {{ __('Senarai Pengguna Sistem') }}
        </h1>
        @can('create', App\Models\User::class)
            <a href="{{ route('settings.users.create') }}" wire:navigate
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
                <i class="bi bi-person-plus-fill {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Tambah Pengguna Baru') }}
            </a>
        @endcan
    </div>

    {{-- Session Messages --}}
    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" class="mb-4" :dismissible="true"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4" :dismissible="true"/>
    @endif

    {{-- Filters and Search --}}
    <x-card class="mb-4 motac-card"> {{-- Assuming x-card is your component and it can take a class --}}
        <x-slot name="header"> {{-- Assuming x-card has a header slot --}}
            <h5 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i>{{ __('Carian dan Saringan') }}
            </h5>
        </x-slot>

        <div class="card-body motac-card-body"> {{-- Or directly in x-card if it handles padding --}}
            <div class="row g-3">
                <div class="col-lg-6 col-md-12">
                    <label for="userSearchAdmin" class="form-label form-label-sm">{{ __('Carian (Nama, Emel, No. KP, Jabatan)') }}</label>
                    <input wire:model.live.debounce.300ms="search" type="text" id="userSearchAdmin"
                        placeholder="{{ __('Masukkan kata kunci...') }}"
                        class="form-control form-control-sm">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="userRoleFilterAdmin" class="form-label form-label-sm">{{ __('Saring Ikut Peranan') }}</label>
                    <select wire:model.live="filterRole" id="userRoleFilterAdmin" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Peranan') }}</option>
                        @foreach ($rolesForFilter as $roleNameValue => $roleNameDisplay)
                            <option value="{{ $roleNameValue }}">{{ $roleNameDisplay }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="userStatusFilterAdmin" class="form-label form-label-sm">{{ __('Saring Ikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="userStatusFilterAdmin" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="{{ \App\Models\User::STATUS_ACTIVE }}">{{ __('Aktif') }}</option>
                        <option value="{{ \App\Models\User::STATUS_INACTIVE }}">{{ __('Tidak Aktif') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Users Table --}}
    {{-- MODIFIED: Changed from <div class="card motac-card"> to <x-card class="motac-card"> --}}
    <x-card class="motac-card">
        {{-- If your x-card component has a header slot, use it. Otherwise, structure as needed. --}}
        <x-slot name="header">
             <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                    <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Rekod Pengguna') }}
                </h5>
                @if (isset($usersList) && $usersList->total() > 0)
                    <span class="text-muted small">
                        {{ __('Memaparkan :start - :end daripada :total rekod', [
                            'start' => $usersList->firstItem(),
                            'end' => $usersList->lastItem(),
                            'total' => $usersList->total(),
                        ]) }}
                    </span>
                @endif
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama') }} <small class="text-muted">({{ __('Emel Utama') }})</small></th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Emel MOTAC') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. KP') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span class="visually-hidden">{{ __('Tindakan') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                     <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="7" class="p-0 border-0">
                            <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 2px; width: 100%;" role="progressbar" aria-label="Loading...">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($usersList as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="px-3 py-2 small">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle">
                                    </div>
                                    <div>
                                        <span class="text-dark fw-medium">{{ $user->title ? $user->title.' ' : '' }}{{ $user->name }}</span>
                                        <div class="text-muted" style="font-size: 0.85em;">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 small text-muted">{{ $user->motac_email ?? '-'}}</td>
                            <td class="px-3 py-2 small text-muted">{{ $user->identification_number ?? '-'}}</td>
                            <td class="px-3 py-2 small text-muted">{{ $user->department->name ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                @forelse ($user->roles as $role)
                                    <span class="badge text-bg-secondary me-1 fw-normal">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted fst-italic small">{{__('Tiada peranan')}}</span>
                                @endforelse
                            </td>
                            <td class="px-3 py-2 small">
                                <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($user->status) }}">
                                    {{ $user->status === \App\Models\User::STATUS_ACTIVE ? __('Aktif') : __('Tidak Aktif') }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-end">
                                @can('view', $user)
                                <a href="{{ route('settings.users.show', $user->id) }}" wire:navigate class="btn btn-sm btn-icon btn-outline-info border-0 me-1 motac-btn-icon" title="{{ __('Lihat Profil') }}">
                                    <i class="bi bi-eye-fill fs-6 lh-1"></i>
                                </a>
                                @endcan
                                @can('update', $user)
                                <a href="{{ route('settings.users.edit', $user->id) }}" wire:navigate class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                    <i class="bi bi-pencil-fill fs-6 lh-1"></i>
                                </a>
                                @endcan
                                @if(Auth::user()->id !== $user->id)
                                    @can('delete', $user)
                                    <button wire:click="confirmUserDeletion({{ $user->id }})" type="button" class="btn btn-sm btn-icon btn-outline-danger border-0 motac-btn-icon" title="{{ __('Padam') }}">
                                        <i class="bi bi-trash3-fill fs-6 lh-1"></i>
                                    </button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-5 text-center">
                               <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-people fs-1 mb-2 text-secondary"></i>
                                     <p>{{ __('Tiada rekod pengguna ditemui berdasarkan tapisan semasa.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- This @if should ideally wrap the card-footer if it exists --}}
        @if (isset($usersList) && $usersList->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                {{ $usersList->links() }}
            </div>
        @endif
    </x-card> {{-- MODIFIED: Corresponding closing tag for the Users Table card --}}

    {{-- ... (Delete Confirmation Modal - ensure it's correctly implemented if used) ... --}}
</div>
