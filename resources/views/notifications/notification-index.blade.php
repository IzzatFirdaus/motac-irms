{{-- resources/views/notifications/notification-index.blade.php --}}
{{-- Main Notifications Center Page --}}

@extends('layouts.app') {{-- Assuming this is your main Bootstrap 5 layout --}}

@section('title', __('Pusat Pemberitahuan'))

@push('page-style')
    <style>
        .notification-item a {
            text-decoration: none;
        }

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
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-dark fw-bold">{{ __('Pusat Pemberitahuan') }}</h1>
            </div>
            @if ($notifications->whereNull('read_at')->isNotEmpty() && $notifications->isNotEmpty())
                <div class="col-auto">
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="bi bi-check2-all me-1"></i>
                            {{ __('Tandakan Semua Telah Dibaca') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Flash messages for success or error --}}
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
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <li class="list-group-item notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                                {{-- Each notification is a clickable item --}}
                                <a href="{{ $notification->data['url'] ?? 'javascript:void(0);' }}"
                                    class="d-flex justify-content-between align-items-center w-100 py-2 px-1 {{ $notification->read_at ? '' : 'text-dark' }}"
                                    wire:click.prevent="markAsRead('{{ $notification->id }}')">
                                    <div class="flex-grow-1 me-3">
                                        <span class="fw-medium">
                                            {!! $notification->data['title'] ?? ($notification->data['subject'] ?? __('Notifikasi Baru')) !!}
                                        </span>
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
                <div class="card-footer bg-light border-top-0 py-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
