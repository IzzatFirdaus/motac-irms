{{-- resources/views/components/card.blade.php --}}
@props([
    'title' => null,
    'headerClass' => 'bg-light py-3',
    'titleTag' => 'h5',
    'titleClass' => 'card-title mb-0 fw-semibold',
    'bodyClass' => '',
    'footer' => null,
    'footerClass' => 'bg-light py-3 text-end',
])

<div {{ $attributes->merge(['class' => 'card shadow-sm mb-3']) }}>
    @if ($title || $attributes->has('header'))
        <div class="card-header {{ $headerClass }}">
            @if ($title)
                <{{ $titleTag }} class="{{ $titleClass }}">{{ $title }}</{{ $titleTag }}>
            @endif
            {{ $attributes->get('header') ?? '' }}
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    @if ($footer)
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
