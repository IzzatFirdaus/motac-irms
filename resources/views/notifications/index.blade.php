{{-- resources/views/notifications/index.blade.php --}}

@extends('layouts.app') {{-- Assuming this is your main Bootstrap 5 layout --}}

@section('title', __('Pusat Pemberitahuan')) {{-- Optional: Set page title --}}

@push('page-style')
    {{-- Add any page-specific styles here if necessary, though prefer global CSS --}}
    <style>
        .notification-item a {
            text-decoration: none;
        }
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
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid"> {{-- Or use .container for a fixed-width layout --}}

        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">{{ __('Pusat Pemberitahuan') }}</h1>
            </div>
            @if ($notifications->whereNull('read_at')->isNotEmpty() && $notifications->isNotEmpty())
                <div class="col-auto">
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-checks ti-xs me-1"></i>{{ __('Tandakan Semua Telah Dibaca') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>


        {{-- Display success or error messages from session --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
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
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div> {{-- End card-body --}}

            @if ($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }} {{-- Bootstrap 5 pagination styling is usually default --}}
                </div>
            @endif
        </div>

    </div> {{-- End .container-fluid --}}
@endsection

@push('page-script')
    {{-- Add any page-specific JavaScript here --}}
@endpush
