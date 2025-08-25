{{-- resources/views/livewire/sections/navbar/notifications-dropdown.blade.php --}}
{{--
    Notifications Dropdown for Navbar
    Expects:
      - $unreadNotifications (collection of notifications)
      - $unreadCount (integer)
--}}

<div class="myds-nav-item myds-dropdown-notifications myds-navbar-dropdown dropdown me-3 me-xl-1">
    <a class="myds-nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
        data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ __('Notifications') }}">
        <i class="bi bi-bell-fill fs-4"></i>
        @if ($unreadCount > 0)
            <span class="myds-badge bg-danger rounded-pill myds-badge-notifications">{{ $unreadCount }}</span>
        @endif
    </a>
    <ul class="myds-dropdown-menu dropdown-menu-end py-0">
        <li class="myds-dropdown-menu-header border-bottom">
            <div class="myds-dropdown-header d-flex align-items-center py-3">
                <h5 class="heading-xsmall mb-0 me-auto">{{ __('Notifications') }}</h5>
                @if ($unreadCount > 0)
                    <a href="javascript:void(0)" wire:click="markAllAsRead" class="myds-dropdown-notifications-all text-body"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Mark all as read') }}"><i class="bi bi-envelope-open-fill"></i></a>
                @endif
            </div>
        </li>
        <li class="myds-dropdown-notifications-list scrollable-container">
            <ul class="myds-list-group list-group-flush">
                @forelse($unreadNotifications as $notification)
                    <li class="myds-list-group-item list-group-item-action myds-dropdown-notifications-item"
                        wire:click="markAsRead('{{ $notification->id }}')" style="cursor: pointer;">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="myds-avatar">
                                    <span class="myds-avatar-initial rounded-circle bg-label-info"><i
                                            class="{{ $notification->data['icon'] ?? 'bi bi-info-circle-fill' }}"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="heading-xsmall mb-1">{{ $notification->data['subject'] ?? __('New Notification') }}</h6>
                                <p class="mb-0">{{ $notification->data['message'] ?? __('No message content.') }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="myds-list-group-item">
                        <div class="d-flex justify-content-center align-items-center">
                            <p class="mb-0 py-4 text-muted">{{ __('No new notifications.') }}</p>
                        </div>
                    </li>
                @endforelse
            </ul>
        </li>
        <li class="myds-dropdown-menu-footer border-top">
            {{-- This link correctly points to the route defined in web.php --}}
            <a href="{{ route('notifications.index') }}" class="myds-dropdown-item d-flex justify-content-center p-3">
                {{ __('View All Notifications') }}
            </a>
        </li>
    </ul>
</div>
