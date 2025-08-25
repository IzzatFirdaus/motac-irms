{{--
    resources/views/components/button.blade.php

    Primary button component with consistent MOTAC styling.
    Default submit button with uppercase text styling.

    Usage:
    <x-button>{{ __('Save') }}</x-button>
    <x-button type="button" class="btn-lg">{{ __('Large Button') }}</x-button>

    Dependencies: Bootstrap 5
--}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary text-uppercase']) }}>
  {{ $slot }}
</button>
