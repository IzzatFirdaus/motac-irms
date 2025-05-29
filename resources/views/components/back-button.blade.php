@props(['route', 'text' => __('Kembali'), 'icon' => 'ti-arrow-left'])

<a href="{{ $route }}" {{ $attributes->merge(['class' => 'btn btn-secondary d-inline-flex align-items-center']) }}>
    <i class="ti {{ $icon }} @if($text) me-1 @endif"></i>
    @if($text)
        <span>{{ $text }}</span>
    @endif
</a>
