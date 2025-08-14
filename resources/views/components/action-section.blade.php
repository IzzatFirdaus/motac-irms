{{-- resources/views/components/action-section.blade.php --}}
{{-- This component should inherit MOTAC card styling from the global theme. --}}
{{-- Ensure .card, .card-header, .card-body are styled according to Design Language Documentation --}}
<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}> {{-- Added shadow-sm and mb-4 for consistency --}}
    <div class="card-header bg-light py-3"> {{-- Example: bg-light for header, can be themed --}}
        <h5 class="card-title mb-0 fw-semibold">{{ $title }}</h5>
    </div>
    <div class="card-body">
        @if (isset($description))
            <p class="card-text text-muted small mb-3">{{ $description }}</p> {{-- Added small and mb-3 --}}
        @endif
        {{ $content }}
    </div>
</div>
