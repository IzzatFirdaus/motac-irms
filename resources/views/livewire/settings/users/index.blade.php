@extends('layouts.app')

@section('title', __('Pengurusan Pengguna Sistem'))

@section('content')
    <div class="container-fluid px-lg-4 py-4">

        {{-- Page Header and Create Button --}}
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-people-fill me-2"></i>{{ __('Pengurusan Pengguna Sistem') }}
            </h1>
            {{-- Use a regular anchor tag with wire:navigate for full page navigation --}}
            @can('create', App\Models\User::class)
                <a href="{{ route('settings.users.create') }}" wire:navigate
                    class="btn btn-primary d-inline-flex align-items-center">
                    <i class="fas fa-plus me-1"></i> {{ __('Tambah Pengguna Baru') }}
                </a>
            @endcan
        </div>

        {{-- Display Session Flash Messages using x-alert component --}}
        {{-- Replaces the manual @if (session()->has('message')) and @if (session()->has('error')) blocks --}}
        @if (session('message'))
            <x-alert :type="session('type', 'success')" :message="session('message')" dismissible="true" />
        @elseif (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible="true" />
        @endif

        {{-- Search and Filter Controls --}}
        <div class="card mb-4 shadow-sm motac-card">
            <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>
                <h5 class="mb-0 fw-medium text-dark">{{ __('Carian dan Saringan') }}</h5>
            </div>
            <div class="card-body p-3 motac-card-body">
                <div class="row g-3">
                    <div class="col-lg-6 col-md-12">
                        <label for="userSearchAdmin" class="form-label form-label-sm fw-medium">{{ __('Carian (Nama, Emel, No. KP, Jabatan)') }}</label>
                        <input wire:model.live.debounce.300ms="search" type="text" id="userSearchAdmin"
                            placeholder="{{ __('Masukkan kata kunci...') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="userRoleFilterAdmin" class="form-label form-label-sm fw-medium">{{ __('Saring Ikut Peranan') }}</label>
                        <select wire:model.live="filterRole" id="userRoleFilterAdmin" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Peranan') }}</option>
                            @foreach($rolesForFilter as $roleNameValue => $roleNameDisplay)
                                <option value="{{ $roleNameValue }}">{{ $roleNameDisplay }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="userStatusFilterAdmin" class="form-label form-label-sm fw-medium">{{ __('Saring Ikut Status') }}</label>
                        <select wire:model.live="filterStatus" id="userStatusFilterAdmin" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Status') }}</option>
                            {{-- Assuming User::STATUS_ACTIVE and User::STATUS_INACTIVE are constants --}}
                            <option value="{{ \App\Models\User::STATUS_ACTIVE }}">{{ __('Aktif') }}</option>
                            <option value="{{ \App\Models\User::STATUS_INACTIVE }}">{{ __('Tidak Aktif') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="card shadow-sm motac-card">
            <div class="card-header bg-light py-3 motac-card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                    <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Rekod Pengguna') }}
                </h5>
                @if ($usersList->total() > 0)
                    <span class="text-muted small">
                        {{ __('Memaparkan :start - :end daripada :total rekod', [
                            'start' => $usersList->firstItem(),
                            'end' => $usersList->lastItem(),
                            'total' => $usersList->total(),
                        ]) }}
                    </span>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('name')" role="button">
                                {{ __('Nama') }} <small class="text-muted">({{ __('Emel Utama') }})</small>
                                @if ($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1"></i>
                                @endif
                            </th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Emel MOTAC') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. KP') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('status')" role="button">
                                {{ __('Status') }}
                                @if ($sortField === 'status')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1"></i>
                                @endif
                            </th>
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
                                            <span class="text-dark fw-medium">{{ $user->title ? \App\Models\User::getTitleOptions()[$user->title].' ' : '' }}{{ $user->name }}</span>
                                            <div class="text-muted" style="font-size: 0.85em;">{{ $user->email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $user->motac_email ?? '-'}}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $user->identification_number ?? '-'}}</td>
                                    <td class="px-3 py-2 small text-muted">{{ optional($user->department)->name ?? '-' }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @forelse ($user->roles as $role)
                                            <span class="badge text-bg-secondary me-1 fw-normal">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted fst-italic small">{{__('Tiada peranan')}}</span>
                                        @endforelse
                                    </td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($user->status, 'user_status') }}">
                                            {{ \App\Models\User::getStatusOptions()[$user->status] ?? $user->status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        @can('view', $user)
                                            <a href="{{ route('settings.users.show', $user->id) }}" wire:navigate class="btn btn-sm btn-icon btn-outline-info border-0 me-1" title="{{ __('Lihat Profil') }}">
                                                <i class="bi bi-eye-fill fs-6 lh-1"></i>
                                            </a>
                                        @endcan
                                        @can('update', $user)
                                            <a href="{{ route('settings.users.edit', $user->id) }}" wire:navigate class="btn btn-sm btn-icon btn-outline-primary border-0 me-1" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill fs-6 lh-1"></i>
                                            </a>
                                        @endcan
                                        @if(Auth::user()->id !== $user->id)
                                            @can('delete', $user)
                                                <button wire:click="confirmUserDeletion({{ $user->id }}, '{{ $user->name }}')" type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam') }}">
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

        @if ($usersList->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                {{ $usersList->links() }}
            </div>
        @endif
    </div>
    </div>

    {{-- Universal Delete Confirmation Modal (x-data for Alpine.js) --}}
    {{-- This modal is expected to be a global component or included where needed. --}}
    {{-- It listens for 'open-delete-modal' dispatch and calls the specified deleteMethod. --}}
    <div x-data="{ show: @entangle('showDeleteConfirmationModal').live, itemId: null, itemDescription: '', deleteMethod: '' }"
        @open-delete-modal.window="show = true; itemId = $event.detail.id; itemDescription = $event.detail.itemDescription; deleteMethod = $event.detail.deleteMethod;"
        x-show="show" class="modal fade" tabindex="-1" style="display: {{ $showDeleteConfirmationModal ? 'block' : 'none' }};" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Sahkan Pemadaman') }}</h5>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Adakah anda pasti ingin memadam') }} "<strong><span x-text="itemDescription"></span></strong>"? {{ __('Tindakan ini tidak boleh diundur.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">{{ __('Batal') }}</button>
                    {{-- CORRECTED: The $wire.call() syntax for dynamic method invocation in Livewire --}}
                    <button type="button" class="btn btn-danger" @click="$wire.call(deleteMethod, itemId); show = false;">{{ __('Padam') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Livewire.on('open-delete-modal', params => { // If using a simpler dispatch without explicit @entangle
            //     var modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            //     modal.show();
            // });
            // Livewire.on('close-delete-modal', () => { // If using a simpler dispatch without explicit @entangle
            //     var modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
            //     if (modal) {
            //         modal.hide();
            //     }
            // });
        });
    </script>
    @endpush
@endsection
