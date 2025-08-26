{{-- resources/views/livewire/shared/notifications/notifications-list.blade.php --}}
{{--
    Reusable Livewire component for listing notifications for the authenticated user.
    Features:
    - Search/filter notifications
    - Mark notifications as read
    - Paginated with Bootstrap styling
--}}

<div class="container-fluid">
    <div class="motac-card shadow-sm mb-4">
        <div class="motac-card-header d-flex align-items-center py-3">
            <i class="bi bi-bell-fill me-2 text-primary" aria-hidden="true"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Senarai Notifikasi') }}</h5>
        </div>
        <div class="motac-card-body">
            {{-- Search box for filtering notifications --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="notificationSearch" class="form-label small fw-medium">{{ __('Carian Notifikasi') }}</label>
                    <input
                        type="search"
                        id="notificationSearch"
                        wire:model.live.debounce.500ms="search"
                        class="form-control"
                        placeholder="{{ __('Cari mesej notifikasi...') }}"
                    >
                </div>
            </div>

            {{-- Flash message for actions --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
                </div>
            @endif

            {{-- Notifications Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Mesej') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Status') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                            <tr @if (is_null($notification->read_at)) class="table-info" @endif>
                                <td class="px-3 py-2 small">
                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-3 py-2 small">
                                    {{-- Display notification message (customize as needed) --}}
                                    {{ $notification->data['message'] ?? $notification->data['title'] ?? __('Notifikasi baharu') }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if (is_null($notification->read_at))
                                        <span class="motac-badge motac-badge-warning rounded-pill" role="status" aria-label="{{ __('Belum Dibaca') }}">{{ __('Belum Dibaca') }}</span>
                                    @else
                                        <span class="motac-badge motac-badge-success rounded-pill" role="status" aria-label="{{ __('Dibaca') }}">{{ __('Dibaca') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-end">
                                    @if (is_null($notification->read_at))
                                        <button wire:click="markAsRead('{{ $notification->id }}')" class="motac-btn-outline btn-sm" title="{{ __('Tanda sebagai dibaca') }}">
                                            <i class="bi bi-check2-square" aria-hidden="true"></i> {{ __('Dibaca') }}
                                        </button>
                                    @else
                                        <span class="text-muted small">{{ __('Tiada tindakan') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-bell-slash display-5 mb-2"></i><br>
                                    {{ __('Tiada notifikasi dijumpai.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3 d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
