<div>
    {{-- Page title is now set by ->title() in the component's render method --}}
    {{-- Layout is set by #[Layout('layouts.app')] in the component class --}}

    <div class="container-fluid py-4"> {{-- Or 'container' based on your preference --}}
        {{-- Include your general alert partial for session messages or validation errors if needed --}}
        {{-- @include('_partials._alerts.alert-general') --}}

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
            <h4 class="fw-bold mb-2 mb-sm-0">
                {{ __('Senarai Pengguna Sistem') }}
            </h4>
            @can('create', \App\Models\User::class)
                <button wire:click="redirectToCreateUser" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="ti ti-plus me-1"></i>{{ __('Tambah Pengguna Baru') }}
                </button>
            @endcan
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="userSearchSettings" class="form-label visually-hidden">{{ __('Carian') }}</label>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" id="userSearchSettings"
                               placeholder="{{ __('Cari Nama, Emel, No. KP, Jabatan...') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="roleFilterSettings" class="form-label visually-hidden">{{ __('Tapis Peranan') }}</label>
                        <select wire:model.live="filterRole" class="form-select" id="roleFilterSettings">
                            <option value="">{{ __('Semua Peranan') }}</option>
                            @foreach ($rolesForFilter as $roleValue => $roleName)
                                <option value="{{ $roleValue }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div wire:loading.delay.long class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
            </div>
            <p class="mt-2">{{ __('Memuatkan senarai pengguna...') }}</p>
        </div>

        <div wire:loading.remove>
            @if ($users->isEmpty())
                <x-alert type="info" class="d-flex align-items-center">
                    <span class="alert-icon me-2"><i class="ti ti-users-off ti-md"></i></span>
                    {{ __('Tiada pengguna ditemui yang sepadan dengan carian atau tapisan semasa.') }}
                </x-alert>
            @else
                <div class="card shadow-sm">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium">{{ __('Nama Penuh') }}</th>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium">{{ __('Emel') }}</th>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium">{{ __('No. Kad Pengenalan') }}</th>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium">{{ __('Jabatan') }}</th>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium">{{ __('Peranan') }}</th>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium text-center">{{ __('Status') }}</th>
                                    <th class="px-3 py-2 small text-uppercase text-muted fw-medium text-center">{{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($users as $user)
                                    <tr wire:key="settings-user-{{ $user->id }}">
                                        <td class="px-3 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle">
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium">{{ $user->name }}</span>
                                                    <small class="text-muted">{{ $user->full_name ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                        <td class="px-3 py-2">{{ $user->identification_number ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $user->department->name ?? __('Tidak Ditetapkan') }}</td>
                                        <td class="px-3 py-2">
                                            @forelse ($user->roles as $role)
                                                <span class="badge bg-label-primary me-1">{{ $role->name }}</span>
                                            @empty
                                                <span class="badge bg-label-secondary">{{ __('Tiada Peranan') }}</span>
                                            @endforelse
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            @if($user->status === \App\Models\User::STATUS_ACTIVE)
                                                <span class="badge bg-success">{{ Str::title($user->status) }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ Str::title($user->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary p-0 hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('view', $user)
                                                        {{-- Ensure 'settings.users.show' route exists and points to a ShowUser Livewire component or page --}}
                                                        <a class="dropdown-item" href="{{ route('settings.users.show', $user->id) }}">
                                                            <i class="ti ti-eye me-1"></i> {{ __('Lihat') }}
                                                        </a>
                                                    @endcan
                                                    @can('update', $user)
                                                        <button wire:click="redirectToEditUser({{ $user->id }})" class="dropdown-item">
                                                            <i class="ti ti-pencil me-1"></i> {{ __('Kemaskini') }}
                                                        </button>
                                                    @endcan
                                                    @can('delete', $user)
                                                        <button wire:click="confirmUserDeletion({{ $user->id }})" class="dropdown-item text-danger">
                                                            <i class="ti ti-trash me-1"></i> {{ __('Padam') }}
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($users->hasPages())
                        <div class="card-footer bg-light border-top d-flex justify-content-center py-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
        {{-- If you have a separate modal component for delete confirmation, you can include it here: --}}
        {{-- @livewire('settings.users.delete-confirmation-modal') --}}
        {{-- Or define the modal directly in this file if it's simple enough and controlled by properties in this component --}}
    </div>
</div>
