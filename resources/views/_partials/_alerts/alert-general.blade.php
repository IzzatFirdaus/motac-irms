{{-- resources/views/_partials/_alerts/alert-general.blade.php --}}
{{-- Displays global session-based flash messages and validation errors. --}}
{{-- System Design: Section 6.3 Reusable Blade Components, Design Language: Clear Instructions & Actionable Feedback --}}
@php
    $alertMessage = null;
    $alertLevel = 'info'; // Default level for generic messages
    $alertIcon = 'ti-info-circle'; // Default icon

    // Check for standard Laravel session flash messages
    if (session()->has('success')) {
        $alertMessage = session('success');
        $alertLevel = 'success';
    } elseif (session()->has('error')) {
        $alertMessage = session('error');
        $alertLevel = 'danger';
    } elseif (session()->has('warning')) {
        $alertMessage = session('warning');
        $alertLevel = 'warning';
    } elseif (session()->has('info')) {
        $alertMessage = session('info');
        $alertLevel = 'info';
    } elseif (session()->has('message')) {
        // For structured messages: ['level' => 'success', 'content' => 'My message']
        $sessionMessage = session('message');
        if (is_array($sessionMessage) && isset($sessionMessage['content'])) {
            $alertMessage = $sessionMessage['content'];
            $alertLevel = $sessionMessage['level'] ?? 'info';
        } elseif (is_string($sessionMessage)) {
            $alertMessage = $sessionMessage; // Treat as simple info message
        }
    }

    // Determine icon based on the final alert level
    // Design Language: Iconography (Simple, Meaningful, Consistent)
    $alertIcon = match (strtolower($alertLevel)) {
        'success' => 'ti-circle-check', // Changed for more positive visual
        'danger' => 'ti-alert-triangle', // Changed for distinct error/danger
        'warning' => 'ti-alert-hexagon',   // Changed for distinct warning
        default => 'ti-info-circle',    // Default for 'info'
    };
@endphp

@if ($alertMessage)
  <div class="alert alert-{{ \App\Helpers\Helpers::getBootstrapAlertClass($alertLevel) }} alert-dismissible d-flex align-items-center fade show" role="alert">
    <span class="alert-icon alert-icon-lg me-2">
      <i class="ti {{ $alertIcon }} ti-sm"></i>
    </span>
    <div class="d-flex flex-column ps-1">
      {{-- Assuming $alertMessage might be a translation key or direct text --}}
      <p class="mb-0 alert-message-content">{{ __($alertMessage) }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
  </div>
@endif

{{-- Display Laravel Validation Errors, if any --}}
{{-- System Design: Validation error messages automatically translated (lang/my/validation.php) --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <span class="alert-icon alert-icon-lg me-2"><i class="ti ti-alert-octagon ti-sm"></i></span> {{-- More prominent error icon --}}
            <div class="d-flex flex-column ps-1">
                <h5 class="alert-heading mb-2">{{ __('Amaran! Sila semak ralat input berikut:') }}</h5>
                <ul class="list-unstyled mb-0"> {{-- Using list-unstyled for cleaner look in alerts --}}
                    @foreach ($errors->all() as $error)
                        <li><small>{{ $error }}</small></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
    </div>
@endif
