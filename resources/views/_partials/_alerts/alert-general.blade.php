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
        // Title can be sourced from your x-alert component's defaults or set explicitly here
        // For simplicity, we'll let x-alert handle default titles based on type if not set here
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
            $alertTitle = $sessionMessage['title'] ?? null; // Use title from session if provided
        } elseif (is_string($sessionMessage)) {
            $alertMessage = $sessionMessage;
            // Default title for generic string message can be set or let x-alert decide
        }
    }

    // IMPORTANT: Ensure $alertMessage is always a string for __() helper
    // This block handles cases where an array (like the full approvals.php content from debug)
    // is unexpectedly flashed to the session.
    if (is_array($alertMessage)) {
        // Attempt to extract a meaningful string from the array
        // Prioritize specific messages that were seen in debug context
        if (isset($alertMessage['no_tasks']) && is_string($alertMessage['no_tasks'])) {
            $alertMessage = $alertMessage['no_tasks'];
        } elseif (isset($alertMessage['loading_text']) && is_string($alertMessage['loading_text'])) {
            $alertMessage = $alertMessage['loading_text'];
        } elseif (isset($alertMessage['title']) && is_string($alertMessage['title'])) {
            // Check if it's the 'title' from approvals.php, which is 'Papan Pemuka Kelulusan'
            // and use it if it seems appropriate as a general message.
            $alertMessage = $alertMessage['title'];
        } elseif (isset($alertMessage['notifications']['generic_error']) && is_string($alertMessage['notifications']['generic_error'])) {
            // A more specific fallback if it's an error notification
            $alertMessage = $alertMessage['notifications']['generic_error'];
        } else {
            // Fallback: Convert the array to a JSON string.
            // This prevents the TypeError and helps you see what array was passed.
            $alertMessage = 'An unexpected message format was received: ' . json_encode($alertMessage);
            // You might also want to log this in your application for further investigation
            // Log::warning('Alert message received as array', ['message_content' => $alertMessage]);
        }
    }
@endphp

{{-- Display Session-Based Flash Message using x-alert --}}
@if ($alertMessage)
    <x-alert :type="$alertLevel"
             @if($alertTitle) title="{{ $alertTitle }}" @endif
             :message="__($alertMessage)" {{-- $alertMessage is now guaranteed to be a string --}}
             dismissible="true" />
@endif

{{-- Display Laravel Validation Errors using x-alert --}}
@if ($errors->any())
    <x-alert type="danger"
             :title="__('Amaran! Sila semak ralat input berikut:')"
             dismissible="true">
        {{-- The default icon for 'danger' will be used from x-alert component --}}
        {{-- Error messages are passed into the default slot of the x-alert component --}}
        <ul class="list-unstyled mb-0 small ps-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
