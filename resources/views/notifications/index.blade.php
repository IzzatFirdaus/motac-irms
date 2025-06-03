{{-- resources/views/notifications/index.blade.php --}}

@extends('layouts.app') {{-- Assuming this is your main Bootstrap 5 layout --}}

@section('title', __('Pusat Pemberitahuan'))

@push('page-style')
    <style>
        .notification-item a {
            text-decoration: none;
        }

        .notification-item.unread strong,
        /* Target strong tag within unread items if message has it */
        .notification-item.unread .text-primary,
        /* Target specific classes within unread */
        .notification-item.unread .text-dark {
            /* Ensure default link color is bold if no other class */
            font-weight: 600;
            /* Semibold for unread emphasis */
        }

        .notification-item.unread a:not([class*="text-"]),
        /* For links without specific text color */
        .notification-item.unread a.text-dark {
            /* If using default dark text for link */
            color: var(--bs-body-color) !important;
            /* Ensure it's not overly muted */
        }

        .notification-item.read {
            background-color: var(--bs-light);
            /* Subtle background for read items */
        }

        .notification-item.read a,
        .notification-item.read small,
        .notification-item.read div {
            /* General text within read items */
            color: var(--bs-secondary-text) !important;
            /* Muted color for read items */
            font-weight: normal;
        }

        .notification-item.read strong {
            /* Ensure strong tags in read items are not bold if text is muted */
            font-weight: normal;
        }

        .notification-item:hover {
            background-color: var(--bs-tertiary-bg);
            /* Subtle hover for better UX */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4"> {{-- Added py-4 for consistent padding --}}

        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Pusat Pemberitahuan') }}</h1>
            </div>
            @if ($notifications->whereNull('read_at')->isNotEmpty() && $notifications->isNotEmpty())
                <div class="col-auto">
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="bi bi-check2-all me-1"></i>{{-- Bootstrap Icon --}}
                            {{ __('Tandakan Semua Telah Dibaca') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3"> {{-- Added bg-light --}}
                <h6 class="m-0 fw-semibold text-primary d-flex align-items-center">
                    <i class="bi bi-bell-fill me-2"></i>{{-- Bootstrap Icon --}}
                    {{ __('Senarai Pemberitahuan Anda') }}
                </h6>
            </div>
            <div class="card-body p-0">
                @if ($notifications->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-bell-slash-fill fs-1 d-block mb-2"></i> {{-- Bootstrap Icon for empty state --}}
                        {{ __('Tiada pemberitahuan baharu atau lama ditemui.') }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <li class="list-group-item notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                                <a href="{{ $notification->data['url'] ?? route('notifications.show', $notification->id) }}"
                                    {{-- Fallback to a show route --}}
                                    class="d-flex justify-content-between align-items-center w-100 py-2 px-1 {{ $notification->read_at ? '' : 'text-dark' }}"
                                    wire:click.prevent="markAsRead('{{ $notification->id }}')" {{-- Example: Mark as read on click with Livewire --}}>
                                    <div class="flex-grow-1 me-3">
                                        {{-- Message rendering. Ensure $notification->data['message'] is safe. --}}
                                        <span class="fw-medium"> {!! $notification->data['title'] ?? ($notification->data['subject'] ?? __('Notifikasi Baru')) !!} </span>
                                        <small class="d-block text-muted">
                                            {!! Str::limit($notification->data['message'] ?? __('Butiran tidak tersedia.'), 150) !!}
                                        </small>
                                    </div>
                                    <small class="text-muted flex-shrink-0 text-end" style="min-width: 120px;">
                                        {{ $notification->created_at->locale(app()->getLocale())->diffForHumans() }}
                                        @if (!$notification->read_at)
                                            <span class="badge bg-primary rounded-pill ms-2 animate-pulse">Baru</span>
                                        @endif
                                    </small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($notifications->hasPages())
                <div class="card-footer bg-light border-top-0 py-3"> {{-- Added bg-light and border-top-0 --}}
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

{{-- @push('page-script') --}}
{{--
    <script>
        // Livewire might handle interactions. If pure Blade, you might add JS here.
        // Example: Marking notification as read via AJAX if not using Livewire for that.
    </script>
    --}}
{{-- @endpush --}}
