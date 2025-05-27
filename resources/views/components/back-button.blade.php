@props(['route', 'text' => 'Kembali', 'icon' => 'ti-arrow-left']) {{-- Using Tabler Icon class --}}

<a href="{{ $route }}" {{ $attributes->merge(['class' => 'btn btn-secondary inline-flex items-center text-sm']) }}>
    <i class="ti {{ $icon }} {{ $text ? 'mr-1.5 -ml-0.5' : '' }} h-4 w-4"></i>
    @if($text)
        <span>{{ $text }}</span>
    @endif
</a>
