{{-- resources/views/livewire/settings/users/users-index.blade.php --}}
@include('_partials._alerts.alert-general')
<div class="container mt-4">
    <div class="row mb-24 align-items-center pb-16 border-bottom">
        <div class="col">
            <h1 class="heading-medium fw-semibold text-black-900 mb-0 d-flex align-items-center">
                <i class="bi bi-people-fill me-2"></i>
                {{ __('Pengurusan Pengguna Sistem') }}
            </h1>
        </div>
        <div class="col text-end">
            @can('create', App\Models\User::class)
                <button wire:click="redirectToCreateUser" class="button variant-primary size-medium d-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Pengguna') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card shadow-sm mb-24 motac-card">
        <div class="card-header bg-light-100 py-16 motac-card-header d-flex align-items-center">
            <i class="bi bi-filter me-2 text-primary-500"></i>
            <h5 class="heading-small fw-semibold text-black-900 mb-0">{{ __('Penapis') }}</h5>
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

    <div class="card shadow-sm mb-24 motac-card">
        <div class="card-header bg-light-100 py-16 motac-card-header d-flex align-items-center">
            <i class="bi bi-list-ul me-2 text-primary-500"></i>
            <h5 class="heading-small fw-semibold text-black-900 mb-0">{{ __('Senarai Pengguna') }}</h5>
        </div>
        <div class="card-body p-0 motac-card-body">
            @if ($usersList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-myds table-hover table-striped align-middle mb-0">
                        <thead class="table-light-100">
                            <tr>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">{{ __('Nama') }}</th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">{{ __('Emel MOTAC') }}</th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">{{ __('No. Kad Pengenalan') }}</th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">{{ __('Jabatan') }}</th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">{{ __('Peranan') }}</th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16">{{ __('Status') }}</th>
                                <th class="heading-xsmall text-muted fw-semibold px-3 py-16 text-end">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usersList as $user)
                                <tr wire:key="user-{{ $user->id }}">
                                    <td class="px-3 py-16">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle" width="40" height="40">
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-black-900">{{ $user->title }} {{ $user->name }}</span>
                                                <div class="heading-xsmall text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-16 heading-xsmall">{{ $user->motac_email ?? '-' }}</td>
                                    <td class="px-3 py-16 heading-xsmall">{{ $user->identification_number ?? '-' }}</td>
                                    <td class="px-3 py-16 heading-xsmall">
                                        {{ $user->department->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-16 heading-xsmall">
                                        @if ($user->roles->isNotEmpty())
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary-500 text-white rounded-pill">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-16">
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success-500' : 'danger-500' }} text-white rounded-pill">
                                            {{ $statusOptions[$user->status] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-16 text-end">
                                        @can('view', $user)
                                            <a href="{{ route('settings.users.show', $user->id) }}" class="button variant-info size-small border-0 me-1 motac-btn-icon" title="{{ __('Lihat') }}">
                                                <i class="bi bi-eye-fill fs-6"></i>
                                            </a>
                                        @endcan
                                        @can('update', $user)
                                            <a href="{{ route('settings.users.edit', $user->id) }}" class="button variant-primary size-small border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill fs-6"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $user)
                                            <button wire:click="confirmUserDeletion({{ $user->id }}, '{{ $user->name }}')" class="button variant-danger size-small border-0 motac-btn-icon" title="{{ __('Padam') }}">
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
                    <div class="card-footer bg-light-100 border-top py-16 motac-card-footer d-flex justify-content-center">
                        <nav aria-label="MYDS Pagination">
                            {{ $usersList->links('vendor.pagination.myds') }}
                        </nav>
                    </div>
                @endif
            @else
                <div class="alert alert-info-500 text-center m-24">
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
