{{-- resources/views/components/back-button.blade.php --}}
@props([
    'route',
    'text' => __('Kembali'),
    'icon' => 'bi-arrow-left' // Changed default to Bootstrap Icon
])

<a href="{{ $route }}" {{ $attributes->merge(['class' => 'btn btn-outline-secondary d-inline-flex align-items-center']) }}> {{-- Suggesting btn-outline-secondary for a common "back" style --}}
    {{-- Using Bootstrap Icon class --}}
    <i class="bi {{ $icon }} @if($text) me-1 @endif"></i>
    @if($text)
        <span>{{ $text }}</span>
    @endif
</a>
