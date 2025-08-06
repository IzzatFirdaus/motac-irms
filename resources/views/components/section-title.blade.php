{{-- resources/views/components/section-title.blade.php --}}
@props(['title', 'description', 'aside' => null, 'icon' => null])

<div
    {{ $attributes->merge(['class' => 'd-md-flex justify-content-between align-items-start mb-4 pb-2 border-bottom']) }}>
    <div class="mb-3 mb-md-0">
        <h3 class="h4 fw-semibold d-flex align-items-center">
            @if ($icon)
                <i class="bi {{ $icon }} me-2 fs-5"></i>
            @endif
            {{ $title }}
        </h3>
        @if ($description)
            <p class="text-muted mb-0 small">{{ $description }}</p>
        @endif
    </div>

    @if ($aside)
        <div class="ms-md-3 mt-2 mt-md-0">
            {{ $aside }}
        </div>
    @endif
</div>
