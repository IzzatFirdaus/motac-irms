{{-- resources/views/components/checkbox.blade.php --}}
{{-- This will render a Bootstrap form-check-input.
     Ensure it's wrapped in a <div class="form-check"> and has an associated <label> when used. --}}
<input type="checkbox" {!! $attributes->merge(['class' => 'form-check-input']) !!}>
