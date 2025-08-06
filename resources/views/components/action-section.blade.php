{{-- resources/views/components/action-section.blade.php --}}
{{-- Card component styled according to MOTAC design language --}}
<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0 fw-semibold">{{ $title }}</h5>
    </div>
    <div class="card-body">
        @if (isset($description))
            <p class="card-text text-muted small mb-3">{{ $description }}</p>
        @endif
        {{ $content }}
    </div>
</div>
