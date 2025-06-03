{{-- resources/views/livewire/resource-management/my-applications/email/index.blade.php --}}
<div>
    @section('title', __('Status Permohonan E-mel/ID Saya'))

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-envelope-check-fill me-2"></i>
            {{ __('Senarai Permohonan E-mel/ID Saya') }}
        </h1>
        @can('create', App\Models\EmailApplication::class)
            <a href="{{ route('email-applications.create') }}"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary">
                <i class="bi bi-envelope-plus-fill {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Mohon E-mel/ID Baharu') }}
            </a>
        @endcan
    </div>

    @include('_partials._alerts.alert-general')

    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-filter-circle-fill me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Penapisan & Carian Permohonan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5 col-sm-12">
                    <label for="emailSearchTerm"
                        class="form-label motac-form-label small fw-medium">{{ __('Carian (ID Permohonan, Emel Dicadang, Tujuan)') }}</label>
                    <input wire:model.live.debounce.350ms="searchTerm" type="text" id="emailSearchTerm"
                        placeholder="{{ __('Taip kata kunci carian...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-5 col-sm-12">
                    <label for="emailFilterStatus"
                        class="form-label motac-form-label small fw-medium">{{ __('Tapis mengikut Status Permohonan') }}</label>
                    <select wire:model.live="filterStatus" id="emailFilterStatus" class="form-select form-select-sm">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-12">
                    <button wire:click="resetFilters" wire:loading.attr="disabled"
                        class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline"
                        title="{{ __('Set Semula Penapis') }}">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span wire:loading wire:target="resetFilters" class="spinner-border spinner-border-sm"
                            role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="resetFilters">{{ __('Set Semula') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm motac-card">
        <div
            class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Rekod Permohonan Dikemukakan') }}
            </h5>
            @if ($applications->total() > 0)
                <span class="text-muted small">
                    {{ __('Memaparkan :start - :end daripada :total rekod', [
                        'start' => $applications->firstItem(),
                        'end' => $applications->lastItem(),
                        'total' => $applications->total(),
                    ]) }}
                </span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('ID') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Emel/ID Dicadang') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2"
                            style="width: 30%;">{{ __('Tujuan/Catatan') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Tarikh Mohon') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">
                            {{ __('Status') }}</th>
                        <th scope="col" class="text-center small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="6" class="p-0 border-0">
                            <div wire:loading.flex class="progress bg-transparent rounded-0"
                                style="height: 3px; width: 100%;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: 100%"
                                    aria-label="{{ __('Memuatkan Data Permohonan...') }}"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($applications as $application)
                        <tr wire:key="email-app-row-{{ $application->id }}">
                            <td class="px-3 py-2 align-middle small text-dark fw-medium">#{{ $application->id }}</td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ $application->proposed_email ?: ($application->group_email ?: __('N/A')) }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted"
                                style="min-width: 250px; white-space: normal;">
                                {{ Str::limit($application->application_reason_notes ?? __('Tiada tujuan/catatan dinyatakan.'), 80) }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ \App\Helpers\Helpers::formatDate($application->created_at, 'datetime_format_my') }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-center">
                                <span
                                    class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status ?? 'default', 'email_application') }}">
                                    {{ __($application->status_translated ?? Str::title(str_replace('_', ' ', $application->status))) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 align-middle text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('email-applications.show', $application->id) }}"
                                        class="btn btn-sm btn-outline-info border-0 p-1 motac-btn-icon"
                                        title="{{ __('Lihat Butiran Permohonan') }}">
                                        <i class="bi bi-eye-fill fs-6"></i>
                                    </a>
                                    @if ($application->status === \App\Models\EmailApplication::STATUS_DRAFT)
                                        @can('update', $application)
                                            <a href="{{ route('email-applications.edit', $application->id) }}"
                                                class="btn btn-sm btn-outline-primary border-0 p-1 motac-btn-icon"
                                                title="{{ __('Kemaskini Draf Permohonan') }}">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $application)
                                            <button
                                                wire:click="$dispatch('open-delete-modal', {
                                                    id: {{ $application->id }},
                                                    modelClass: '{{ addslashes(App\Models\EmailApplication::class) }}',
                                                    itemDescription: '{{ __('Permohonan E-mel/ID #') . $application->id }}',
                                                    deleteMethod: 'deleteEmailApplication'
                                                })"
                                                type="button"
                                                class="btn btn-sm btn-outline-danger border-0 p-1 motac-btn-icon"
                                                title="{{ __('Padam Draf Permohonan') }}">
                                                <i class="bi bi-trash3-fill fs-6"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-mailbox-flag fs-1 text-secondary mb-2"></i>
                                    @if (empty($searchTerm) && ($filterStatus === '' || $filterStatus === 'all'))
                                         <p>{{ __('Tiada rekod permohonan e-mel atau ID pengguna ditemui.') }}</p>
                                        @can('create', App\Models\EmailApplication::class)
                                            <p>{{ __('Sila') }} <a
                                                href="{{ route('email-applications.create') }}">{{ __('buat permohonan baharu') }}</a>.</p>
                                        @endcan
                                    @else
                                        <p>{{ __('Tiada rekod permohonan ditemui yang sepadan dengan kriteria carian/penapisan anda.') }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($applications->hasPages())
            <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>

@push('page-script')
    <script>
        // document.addEventListener('livewire:init', () => {
        // });
    </script>
@endpush
