{{-- User Admin Index: resources/views/livewire/settings/users/index.blade.php (or similar path) --}}
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

    {{-- Session Messages --}}
    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" class="mb-4" :dismissible="true"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4" :dismissible="true"/>
    @endif

    {{-- Filters and Search --}}
    <x-card class="mb-4"> {{-- Assuming x-card is a Blade component --}}
        <div class="row g-3">
            <div class="col-lg-6 col-md-12">
                <label for="userSearchAdmin" class="form-label">{{ __('Carian (Nama, Emel, No. KP, Jabatan)') }}</label>
                <input wire:model.live.debounce.300ms="search" type="text" id="userSearchAdmin"
                    placeholder="{{ __('Masukkan kata kunci...') }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-lg-3 col-md-6"> {{-- Adjusted column for better layout --}}
                <label for="userRoleFilterAdmin" class="form-label">{{ __('Saring Ikut Peranan') }}</label>
                <select wire:model.live="filterRole" id="userRoleFilterAdmin" class="form-select form-select-sm">
                    <option value="">{{ __('Semua Peranan') }}</option>
                    @foreach ($rolesForFilter as $roleNameValue => $roleNameDisplay)
                        <option value="{{ $roleNameValue }}">{{ $roleNameDisplay }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6"> {{-- Added Status Filter for consistency with table --}}
                <label for="userStatusFilterAdmin" class="form-label">{{ __('Saring Ikut Status') }}</label>
                <select wire:model.live="filterStatus" id="userStatusFilterAdmin" class="form-select form-select-sm">
                    <option value="">{{ __('Semua Status') }}</option>
                    <option value="{{ \App\Models\User::STATUS_ACTIVE }}">{{ __('Aktif') }}</option>
                    <option value="{{ \App\Models\User::STATUS_INACTIVE }}">{{ __('Tidak Aktif') }}</option>
                    {{-- Add other statuses if any --}}
                </select>
            </div>
        </div>
    </x-card>

    {{-- Users Table --}}
    <x-card>
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
                     {{-- Corrected Loading Indicator Structure --}}
                     <tr wire:loading.class.delay="table-loading-row" class="transition-opacity">
                        <td colspan="7" class="p-0" style="border:none;">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-label="Loading...">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
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
                                    <span class="badge bg-secondary me-1 fw-normal">{{ $role->name }}</span>
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
                                <a href="{{ route('settings.users.show', $user->id) }}" wire:navigate class="btn btn-sm btn-icon btn-outline-info border-0 me-1" title="{{ __('Lihat Profil') }}">
                                    <i class="ti ti-eye fs-6 lh-1"></i>
                                </a>
                                @endcan
                                @can('update', $user)
                                <a href="{{ route('settings.users.edit', $user->id) }}" wire:navigate class="btn btn-sm btn-icon btn-outline-primary border-0 me-1" title="{{ __('Kemaskini') }}"> {{-- Changed color for distinction --}}
                                    <i class="ti ti-pencil fs-6 lh-1"></i>
                                </a>
                                @endcan
                                @if(Auth::user()->id !== $user->id) {{-- Prevent self-deletion --}}
                                    @can('delete', $user)
                                    <button wire:click="confirmUserDeletion({{ $user->id }})" type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam') }}">
                                        <i class="ti ti-trash fs-6 lh-1"></i>
                                    </button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-5 text-center">
                               <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="ti ti-users-off fs-1 mb-2 text-secondary"></i>
                                    {{ __('Tiada rekod pengguna ditemui berdasarkan tapisan semasa.') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($usersList->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                {{ $usersList->links() }}
            </div>
        @endif
    </x-card>

    {{-- Note: Modals for create/edit are typically handled by separate routes/full-page Livewire components (e.g., CreateUserPage.php, EditUserPage.php)
         or inline modals within this component if preferred.
         A delete confirmation modal (e.g., <x-modal-confirm wire:model.defer="showingDeleteConfirmationModal">) should be part of this Livewire component's view.
    --}}
    {{-- Example for a delete confirmation modal (you'd need to create this Blade component)
    @if($userToDelete)
        <x-modal-confirm
            event-to-dispatch-on-confirm="deleteConfirmedUser"
            event-to-dispatch-on-cancel="cancelUserDeletion"
            modal-title="{{__('Padam Pengguna')}}"
            modal-description="{{__('Anda pasti ingin memadam pengguna')}} {{ $userToDelete->name }}? {{__('Tindakan ini tidak boleh dibatalkan.')}}"
            confirm-button-text="{{__('Ya, Padam')}}"
            cancel-button-text="{{__('Batal')}}"
            wire-model-is-visible="confirmingUserDeletion"
        />
    @endif
    --}}
</div>
