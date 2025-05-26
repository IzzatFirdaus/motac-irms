{{-- resources/views/livewire/resource-management/my-applications/email/index.blade.php --}}
<div>
    @section('title', __('Status Permohonan Emel/ID Saya'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">{{ __('Senarai Permohonan Emel/ID Saya') }}</h1>
        <a href="{{ route('resource-management.application-forms.email.create') }}"
            class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
            <i class="ti ti-file-plus {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
            {{ __('Mohon Emel/ID Baru') }}
        </a>
    </div>

    {{-- Alerts --}}
    {{-- Ensure this include points to your Bootstrap-styled alert component --}}
    @include('layouts.sections.components.alert-general-bootstrap')

    {{-- Filters and Search --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="searchTerm" class="form-label">{{ __('Carian (ID, Emel Dicadang, Tujuan)') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" id="searchTerm"
                        placeholder="{{ __('Masukkan kata kunci...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label for="filterStatus" class="form-label">{{ __('Tapis mengikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatus" class="form-select form-select-sm">
                        @foreach ($this->statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Email Applications Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('ID Permohonan') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Emel/ID Dicadang') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Tujuan/Catatan') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Tarikh Mohon') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Status') }}</th>
                        <th scope="col" class="text-end small text-uppercase text-muted fw-medium px-3 py-2">
                            {{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="6" class="p-0">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($this->applications as $application)
                        <tr wire:key="email-app-{{ $application->id }}">
                            <td class="px-3 py-2 align-middle small text-dark fw-medium">#{{ $application->id }}</td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ $application->proposed_email ?: ($application->group_email ?: __('N/A')) }}</td>
                            <td class="px-3 py-2 align-middle small text-muted"
                                style="max-width: 300px; white-space: normal;">
                                {{ Str::limit($application->purpose ?? $application->application_reason_notes, 70) }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ $application->created_at->translatedFormat('d M Y, h:i A') }}</td>
                            <td class="px-3 py-2 align-middle small">
                                <span
                                    class="badge rounded-pill {{ Helpers::getBootstrapStatusColorClass($application->status) }}">
                                    {{ $application->status_translated }}
                                </span>
                            </td>
                            <td class="px-3 py-2 align-middle text-end">
                                <a href="{{ route('resource-management.my-applications.email-applications.show', $application->id) }}"
                                    class="btn btn-sm btn-outline-primary border-0 p-1"
                                    title="{{ __('Lihat Detail') }}">
                                    <i class="ti ti-eye fs-6"></i>
                                </a>
                                @if ($application->status === \App\Models\EmailApplication::STATUS_DRAFT)
                                    @can('update', $application)
                                        {{-- Assuming edit route is a Livewire component or a standard controller action --}}
                                        <a href="{{ route('resource-management.application-forms.email.edit', $application->id) }}"
                                            {{-- Example edit route --}} class="btn btn-sm btn-outline-secondary border-0 p-1 ms-1"
                                            title="{{ __('Kemaskini Draf') }}">
                                            <i class="ti ti-pencil fs-6"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $application)
                                        <button
                                            wire:click="$dispatch('open-delete-modal', { id: {{ $application->id }}, modelClass: 'App\\Models\\EmailApplication', itemDescription: 'Permohonan Emel/ID #{{ $application->id }}' })"
                                            type="button" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1"
                                            title="{{ __('Padam Draf') }}">
                                            <i class="ti ti-trash fs-6"></i>
                                        </button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="ti ti-mail-off fs-1 mb-2 text-secondary"></i>
                                    {{ __('Tiada rekod permohonan emel/ID ditemui.') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($this->applications->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $this->applications->links() }} {{-- Ensure Bootstrap pagination views are used --}}
        </div>
    @endif

    {{-- Placeholder for a global delete confirmation modal. Ensure it's Bootstrap-styled. --}}
    {{--
    <livewire:components.confirmation-modal
        event-to-open="open-delete-modal"
        event-on-confirm="delete-item" {{-- Define this method in your Livewire PHP --}}
    modal-title="{{ __('Sahkan Pemadaman') }}"
    modal-description="{{ __('Adakah anda pasti ingin memadam item ini? Tindakan ini tidak boleh diundur.') }}"
    confirm-button-text="{{ __('Ya, Padam') }}"
    confirm-button-class="btn-danger"
    wire-key="global-delete-confirmation-modal"
    />
    --}}
</div>
