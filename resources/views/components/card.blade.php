{{--
    resources/views/components/card.blade.php

    Flexible card component with optional header, body, and footer sections.
    Provides consistent styling across the MOTAC application.

    Props:
    - $title: string - Card title (optional)
    - $headerClass: string - Header CSS classes (default: 'bg-light py-3')
    - $titleTag: string - HTML tag for title (default: 'h5')
    - $titleClass: string - Title CSS classes
    - $bodyClass: string - Body CSS classes (optional)
    - $footer: string - Footer content (optional)
    - $footerClass: string - Footer CSS classes

    Slots:
    - $slot: Main card body content
    - header: Custom header content (via attributes)

    Usage:
    <x-card title="User Information">
        <p>Card content goes here</p>
    </x-card>

    <x-card :footer="__('Last updated: Today')">
        <x-slot name="title">Custom Title</x-slot>
        Content here
    </x-card>

    Dependencies: Bootstrap 5
--}}
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
    {{-- Card Header (if title or custom header provided) --}}
    @if ($title || $attributes->has('header'))
        <div class="card-header {{ $headerClass }}">
            @if ($title)
                <{{ $titleTag }} class="{{ $titleClass }}">{{ $title }}</{{ $titleTag }}>
            @endif
            {{ $attributes->get('header') ?? '' }}
        </div>
    @endif

    {{-- Card Body --}}
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    {{-- Card Footer (if provided) --}}
    @if ($footer)
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
