{{-- Backward-compatible wrapper: <x-alert-bootstrap> delegates to <x-alert> --}}
{{-- Accepts the same attributes as <x-alert>, e.g., type, message, class --}}
<x-alert :type="$type ?? 'info'" :message="$message ?? null" {{ $attributes }} />
