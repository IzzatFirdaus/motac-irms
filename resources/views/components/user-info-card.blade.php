{{--
    resources/views/components/user-info-card.blade.php

    A reusable component to display a user's information in a card format.
    Props:
    - $user: User object containing profile information.
    - $showActions: (optional) Boolean to show action buttons (edit, etc.)

    Usage:
    <x-user-info-card :user="$user" />
    <x-user-info-card :user="$user" :showActions="true" />
--}}
@props(['user', 'showActions' => false])

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex align-items-center">
        <div class="me-3">
            {{-- User avatar, fallback to default if not available --}}
            <img src="{{ $user->profile_photo_url ?? asset('assets/img/avatars/default-avatar.png') }}"
                 alt="{{ $user->name }} avatar"
                 class="rounded-circle shadow"
                 style="width: 64px; height: 64px; object-fit: cover;">
        </div>
        <div class="flex-grow-1">
            <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
            <div class="mb-1">
                @if($user->email)
                    <i class="bi bi-envelope me-1"></i>
                    <span class="text-muted">{{ $user->email }}</span>
                @endif
            </div>
            <div class="mb-1">
                @if($user->department)
                    <i class="bi bi-building me-1"></i>
                    <span>{{ $user->department->name }}</span>
                @endif
            </div>
            <div class="mb-1">
                @if($user->position)
                    <i class="bi bi-person-badge me-1"></i>
                    <span>{{ $user->position->name }}{{ $user->grade ? ' ('.$user->grade->name.')' : '' }}</span>
                @endif
            </div>
            <div>
                {{-- Include user status badge component --}}
                <x-user-status-badge :status="$user->status" />
            </div>
        </div>
        @if($showActions)
            <div class="ms-3 d-flex flex-column">
                {{-- Example edit button, can be customized for your system --}}
                <a href="{{ route('settings.users.edit', ['user' => $user->id]) }}" class="btn btn-outline-primary btn-sm mb-2">
                    <i class="bi bi-pencil-square me-1"></i>{{ __('Edit') }}
                </a>
                {{-- Other actions can go here --}}
            </div>
        @endif
    </div>
</div>
