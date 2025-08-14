{{-- resources/views/components/dropdown-link.blade.php --}}
{{-- This will correctly render a Bootstrap dropdown-item and inherit MOTAC link styling --}}
<a {{ $attributes->merge(['class' => 'dropdown-item']) }}>{{ $slot }}</a>
