{{-- resources/views/_partials/_alerts/alert-general.blade.php --}}
{{-- Displays global session-based flash messages and validation errors using the x-alert component. --}}
@php
    $alertMessage = null;
    $alertLevel = 'info'; // Default level
    $alertTitle = null;

    // Determine alert details from session flash messages
    if (session()->has('success')) {
        $alertMessage = session('success');
        $alertLevel = 'success';
        // $alertTitle = __('Berjaya!');
    } elseif (session()->has('error')) {
        $alertMessage = session('error');
        $alertLevel = 'danger';
        // $alertTitle = __('Ralat!');
    } elseif (session()->has('warning')) {
        $alertMessage = session('warning');
        $alertLevel = 'warning';
        // $alertTitle = __('Amaran!');
    } elseif (session()->has('info')) {
        $alertMessage = session('info');
        $alertLevel = 'info';
        // $alertTitle = __('Makluman');
    } elseif (session()->has('message')) {
        $sessionMessage = session('message');
        if (is_array($sessionMessage) && isset($sessionMessage['content'])) {
            $alertMessage = $sessionMessage['content'];
            $alertLevel = $sessionMessage['level'] ?? 'info';
            $alertTitle = $sessionMessage['title'] ?? null;
        } elseif (is_array($sessionMessage) && isset($sessionMessage['notifications']['generic_error'])) {
            $alertMessage = $sessionMessage['notifications']['generic_error'];
        } else {
            // Fallback: Convert the array to a JSON string for debugging.
            $alertMessage = 'An unexpected message format was received: ' . json_encode($sessionMessage);
            // Log::warning('Alert message received as array', ['message_content' => $sessionMessage]);
        }
    }
@endphp

{{-- Display Session-Based Flash Message using x-alert --}}
@if ($alertMessage)
    <x-alert :type="$alertLevel"
             @if($alertTitle) title="{{ $alertTitle }}" @endif
             :message="__($alertMessage)"
             dismissible="true" />
@endif

{{-- Display Laravel Validation Errors using x-alert --}}
@if ($errors->any())
    <x-alert type="danger"
             :title="__('Amaran! Sila semak ralat input berikut:')"
             dismissible="true">
        <ul class="list-unstyled mb-0 small ps-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
