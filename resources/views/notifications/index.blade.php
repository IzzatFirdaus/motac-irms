{{-- resources/views/notifications/index.blade.php --}}

@extends('layouts.app') {{-- Assuming this is your main Bootstrap 5 layout --}}

<<<<<<< HEAD
@section('title', __('Pusat Pemberitahuan'))

@push('page-style')
=======
@section('title', __('Pusat Pemberitahuan')) {{-- Optional: Set page title --}}

@push('page-style')
    {{-- Add any page-specific styles here if necessary, though prefer global CSS --}}
>>>>>>> b3ca845 (code additions and edits)
    <style>
        .notification-item a {
            text-decoration: none;
        }
<<<<<<< HEAD

        .notification-item.unread strong,
        .notification-item.unread .text-primary,
        .notification-item.unread .text-dark {
            font-weight: 600;
        }

        .notification-item.unread a:not([class*="text-"]),
        .notification-item.unread a.text-dark {
            color: var(--bs-body-color) !important;
        }

        .notification-item.read {
            background-color: var(--bs-light);
        }

        .notification-item.read a,
        .notification-item.read small,
        .notification-item.read div {
            color: var(--bs-secondary-text) !important;
            font-weight: normal;
        }

        .notification-item.read strong {
            font-weight: normal;
        }

        .notification-item:hover {
            background-color: var(--bs-tertiary-bg);
=======
        .notification-item.unread {
            font-weight: bold; /* Example: Make unread notifications bold */
        }
        .notification-item.read {
            /* Example: Style for read notifications, e.g., less emphasis */
            color: #6c757d; /* Bootstrap's $gray-600 */
        }
        .notification-item.read a {
            color: #6c757d; /* Bootstrap's $gray-600 */
        }
        .notification-item.read strong, .notification-item.read .text-primary {
             font-weight: normal; /* Ensure headings inside read items are not overly emphasized */
>>>>>>> b3ca845 (code additions and edits)
        }
    </style>
@endpush

@section('content')
<<<<<<< HEAD
    <div class="container-fluid py-4">

        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Pusat Pemberitahuan') }}</h1>
=======
    <div class="container-fluid"> {{-- Or use .container for a fixed-width layout --}}

        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">{{ __('Pusat Pemberitahuan') }}</h1>
>>>>>>> b3ca845 (code additions and edits)
            </div>
            @if ($notifications->whereNull('read_at')->isNotEmpty() && $notifications->isNotEmpty())
                <div class="col-auto">
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="mb-0">
                        @csrf
<<<<<<< HEAD
                        <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="bi bi-check2-all me-1"></i>
                            {{ __('Tandakan Semua Telah Dibaca') }}
=======
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-checks ti-xs me-1"></i>{{ __('Tandakan Semua Telah Dibaca') }}
>>>>>>> b3ca845 (code additions and edits)
                        </button>
                    </form>
                </div>
            @endif
        </div>

<<<<<<< HEAD
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
=======

        {{-- Display success or error messages from session --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
>>>>>>> b3ca845 (code additions and edits)
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
<<<<<<< HEAD
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 fw-semibold text-primary d-flex align-items-center">
                    <i class="bi bi-bell-fill me-2"></i>
                    {{ __('Senarai Pemberitahuan Anda') }}
                </h6>
            </div>
            <div class="card-body p-0">
                @if ($notifications->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-bell-slash-fill fs-1 d-block mb-2"></i>
                        {{ __('Tiada pemberitahuan baharu atau lama ditemui.') }}
=======
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Senarai Pemberitahuan Anda') }}</h6>
            </div>
            <div class="card-body p-0"> {{-- p-0 to make list-group flush with card edges --}}
                @if ($notifications->isEmpty())
                    <div class="p-4 text-center text-muted">
                        {{ __('Tiada pemberitahuan ditemui.') }}
>>>>>>> b3ca845 (code additions and edits)
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
<<<<<<< HEAD
                            <li class="list-group-item notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                                {{-- CORRECTED: Changed fallback route to javascript:void(0); or # --}}
                                <a href="{{ $notification->data['url'] ?? 'javascript:void(0);' }}"
                                    class="d-flex justify-content-between align-items-center w-100 py-2 px-1 {{ $notification->read_at ? '' : 'text-dark' }}"
                                    wire:click.prevent="markAsRead('{{ $notification->id }}')">
                                    <div class="flex-grow-1 me-3">
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
=======
                            <li class="list-group-item d-flex justify-content-between align-items-center notification-item {{ $notification->read_at ? 'read list-group-item-light' : 'unread' }}">
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="flex-grow-1 me-3 {{ $notification->read_at ? '' : 'text-dark' }}">
                                    <div>
                                        {{-- Display the notification message from the 'data' payload --}}
                                        {!! $notification->data['message'] ?? __('Pemberitahuan Baharu') !!}
                                        {{-- Using {!! !!} if your message might contain simple HTML for styling, otherwise use {{ }} --}}
                                    </div>
                                </a>
                                <small class="text-muted flex-shrink-0">
                                    {{ $notification->created_at->locale(app()->getLocale())->diffForHumans() }}
                                </small>
>>>>>>> b3ca845 (code additions and edits)
                            </li>
                        @endforeach
                    </ul>
                @endif
<<<<<<< HEAD
            </div>

            @if ($notifications->hasPages())
                <div class="card-footer bg-light border-top-0 py-3">
                    {{ $notifications->links() }}
=======
            </div> {{-- End card-body --}}

            @if ($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }} {{-- Bootstrap 5 pagination styling is usually default --}}
>>>>>>> b3ca845 (code additions and edits)
                </div>
            @endif
        </div>

<<<<<<< HEAD
    </div>
@endsection
=======
    </div> {{-- End .container-fluid --}}
@endsection

@push('page-script')
    {{-- Add any page-specific JavaScript here --}}
@endpush
>>>>>>> b3ca845 (code additions and edits)
