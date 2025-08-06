{{-- resources/views/livewire/sections/navbar/notifications-dropdown.blade.php --}}
{{--
    Notifications Dropdown for Navbar
    Expects:
      - $unreadNotifications (collection of notifications)
      - $unreadCount (integer)
--}}

<div class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
        data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('Notifications') }}">
        <i class="bi bi-bell-fill fs-4"></i>
        @if ($unreadCount > 0)
            <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadCount }}</span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end py-0">
        <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">{{ __('Notifications') }}</h5>
                @if ($unreadCount > 0)
                    <a href="javascript:void(0)" wire:click="markAllAsRead" class="dropdown-notifications-all text-body"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Mark all as read') }}"><i class="bi bi-envelope-open-fill"></i></a>
                @endif
            </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
                @forelse($unreadNotifications as $notification)
                    <li class="list-group-item list-group-item-action dropdown-notifications-item"
                        wire:click="markAsRead('{{ $notification->id }}')" style="cursor: pointer;">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-info"><i
                                            class="{{ $notification->data['icon'] ?? 'bi bi-info-circle-fill' }}"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $notification->data['subject'] ?? __('New Notification') }}</h6>
                                <p class="mb-0">{{ $notification->data['message'] ?? __('No message content.') }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">
                        <div class="d-flex justify-content-center align-items-center">
                            <p class="mb-0 py-4 text-muted">{{ __('No new notifications.') }}</p>
                        </div>
                    </li>
                @endforelse
            </ul>
        </li>
        <li class="dropdown-menu-footer border-top">
            {{-- This link correctly points to the route defined in web.php --}}
            <a href="{{ route('notifications.index') }}" class="dropdown-item d-flex justify-content-center p-3">
                {{ __('View All Notifications') }}
            </a>
        </li>
    </ul>
</div>
