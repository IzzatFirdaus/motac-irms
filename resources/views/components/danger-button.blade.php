{{--
    resources/views/components/danger-button.blade.php

    Danger/destructive action button with warning icon and styling.
    Used for delete operations and other dangerous actions.

    Usage:
    <x-danger-button>{{ __('Delete') }}</x-danger-button>
    <x-danger-button type="submit">{{ __('Remove User') }}</x-danger-button>

    Dependencies: Bootstrap 5, Bootstrap Icons
--}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-danger text-uppercase']) }}>
  <i class="bi bi-exclamation-triangle-fill me-1"></i>
  {{ $slot }}
</button>
