{{-- resources/views/components/input.blade.php --}}
@props(['disabled' => false, 'type' => 'text'])

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control form-control-sm']) !!}>
