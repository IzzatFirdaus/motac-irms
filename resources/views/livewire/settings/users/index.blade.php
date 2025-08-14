{{-- resources/views/livewire/settings/users/index.blade.php --}}
<div class="container mt-4">
    <div class="row mb-3 align-items-center pb-2 border-bottom">
        <div class="col">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-people-fill me-2"></i>
                {{ __('Pengurusan Pengguna Sistem') }}
            </h1>
        </div>
        <div class="col text-end">
            {{-- ADJUSTMENT: This button is now protected by a permission check. --}}
            @can('create', App\Models\User::class)
                <button wire:click="redirectToCreateUser" class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold px-3 py-2 motac-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Pengguna') }}
                </button>
            @endcan
        </div>
    </div>

    @include('_partials._alerts.alert-general')

    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-filter me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Penapis') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <label for="search" class="form-label small fw-medium text-muted">{{ __('Carian') }}</label>
                    <input type="text" wire:model.live.debounce.500ms="search" class="form-control" id="search" placeholder="{{ __('Carian Nama, Emel, No. Kad Pengenalan...') }}">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="filterRole" class="form-label small fw-medium text-muted">{{ __('Peranan') }}</label>
                    <select wire:model.live="filterRole" class="form-select" id="filterRole">
                        <option value="">{{ __('Semua Peranan') }}</option>
                        @foreach($rolesForFilter as $roleName => $roleLabel)
                            <option value="{{ $roleName }}">{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="filterStatus" class="form-label small fw-medium text-muted">{{ __('Status') }}</label>
                    <select wire:model.live="filterStatus" class="form-select" id="filterStatus">
                        <option value="">{{ __('Semua Status') }}</option>
                        @foreach($statusOptions as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-list-ul me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Senarai Pengguna') }}</h5>
        </div>
        <div class="card-body p-0 motac-card-body">
            @if ($usersList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Emel MOTAC') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('No. Kad Pengenalan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usersList as $user)
                                <tr wire:key="user-{{ $user->id }}">
                                    <td class="px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle" width="40" height="40">
                                            </div>
                                            <div>
                                                <span class="fw-medium text-dark">{{ $user->title }} {{ $user->name }}</span>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $user->motac_email ?? '-' }}</td>
                                    <td class="px-3 py-2 small">{{ $user->identification_number ?? '-' }}</td>
                                    <td class="px-3 py-2 small">
                                        {{ $user->department->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 small">
                                        @if ($user->roles->isNotEmpty())
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}-subtle text-{{ $user->status === 'active' ? 'success' : 'danger' }}-emphasis rounded-pill">
                                            {{ $statusOptions[$user->status] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        @can('view', $user)
                                            <a href="{{ route('settings.users.show', $user->id) }}" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Lihat') }}">
                                                <i class="bi bi-eye-fill fs-6"></i>
                                            </a>
                                        @endcan
                                        @can('update', $user)
                                            <a href="{{ route('settings.users.edit', $user->id) }}" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill fs-6"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $user)
                                            <button wire:click="confirmUserDeletion({{ $user->id }}, '{{ $user->name }}')" class="btn btn-sm btn-icon btn-outline-danger border-0 motac-btn-icon" title="{{ __('Padam') }}">
                                                <i class="bi bi-trash3-fill fs-6"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($usersList->hasPages())
                    <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                        {{ $usersList->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-info text-center m-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ __('Tiada pengguna ditemui.') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Component --}}
    <div x-data="{ show: false, itemId: null, itemDescription: '', deleteMethod: '', modelClass: '' }"
         x-show="show"
         @open-delete-modal.window="
            show = true;
            itemId = $event.detail.id;
            itemDescription = $event.detail.itemDescription;
            deleteMethod = $event.detail.deleteMethod;
            modelClass = $event.detail.modelClass;
         "
         class="modal fade"
         :class="{ 'show': show }"
         style="display: none;"
         aria-modal="true"
         role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ __('Sahkan Pemadaman') }}
                    </h5>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Adakah anda pasti ingin memadam') }} "<strong><span x-text="itemDescription"></span></strong>"? {{ __('Tindakan ini tidak boleh diundur.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">{{ __('Batal') }}</button>
                    <button type="button" class="btn btn-danger" @click="$wire.call(deleteMethod, itemId); show = false;">{{ __('Padam') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
