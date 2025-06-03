{{-- resources/views/components/input.blade.php --}}
@props(['disabled' => false, 'type' => 'text']) {{-- Added type prop with default --}}

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control form-control-sm']) !!}> {{-- Added form-control-sm for consistency if desired --}}
