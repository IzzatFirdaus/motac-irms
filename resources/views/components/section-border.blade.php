{{--
    resources/views/components/section-border.blade.php

    Simple horizontal border for visually separating sections on larger screens.
    Hidden on xs screens, shown on sm+.

    Usage:
    <x-section-border />
--}}
<div class="d-none d-sm-block">
    <div class="py-4">
        <hr class="border-top">
    </div>
</div>
