@props([
    'title' => null,
    'headerClass' => '', // Custom classes for card-header
    'titleTag' => 'h5',  // HTML tag for the title, e.g., h5, h3
    'titleClass' => '', // Custom classes for card-title
    'bodyClass' => '',   // Custom classes for card-body
    'footer' => null     // Slot for card-footer content
])

<div {{ $attributes->merge(['class' => 'card shadow-sm mb-3']) }}> {{-- Standard Bootstrap card with shadow and margin --}}
    @if ($title || $attributes->has('header'))
        <div class="card-header {{ $headerClass }}">
            @if ($title)
                <{{ $titleTag }} class="card-title {{ $titleClass }}">{{ $title }}</{{ $titleTag }}>
            @endif
            {{ $attributes->get('header') ?? '' }} {{-- Allows passing additional header content --}}
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    @if ($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
