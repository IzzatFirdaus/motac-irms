{{-- resources/views/_partials/_alerts/alert-general.blade.php --}}
{{-- Displays global session-based flash messages and validation errors using Bootstrap 5. --}}
@php
    $alertMessage = null;
    $alertLevel = 'info'; // Default level
    $alertIcon = 'ti-info-circle'; // Default icon

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
        $sessionMessage = session('message');
        if (is_array($sessionMessage) && isset($sessionMessage['content'])) {
            $alertMessage = $sessionMessage['content'];
            $alertLevel = $sessionMessage['level'] ?? 'info';
        } elseif (is_string($sessionMessage)) {
            $alertMessage = $sessionMessage;
        }
    }

    // Determine icon based on the final alert level
    $alertIcon = match (strtolower($alertLevel)) {
        'success' => 'ti-circle-check',
        'danger' => 'ti-alert-triangle',
        'warning' => 'ti-alert-hexagon',
        default => 'ti-info-circle',
    };
@endphp

@if ($alertMessage)
  {{-- Uses Helper to get Bootstrap alert class e.g., alert-success, alert-danger --}}
  <div class="alert alert-{{ \App\Helpers\Helpers::getAlertClass($alertLevel) }} alert-dismissible d-flex align-items-center fade show" role="alert">
    <span class="alert-icon alert-icon-lg me-2">
      <i class="ti {{ $alertIcon }} ti-sm"></i> {{-- Tabler Icon --}}
    </span>
    <div class="d-flex flex-column ps-1">
      <p class="mb-0 alert-message-content">{{ __($alertMessage) }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
  </div>
@endif

{{-- Display Laravel Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <span class="alert-icon alert-icon-lg me-2"><i class="ti ti-alert-octagon ti-sm"></i></span>
            <div class="d-flex flex-column ps-1">
                <h5 class="alert-heading mb-2">{{ __('Amaran! Sila semak ralat input berikut:') }}</h5>
                <ul class="list-unstyled mb-0">
                    @foreach ($errors->all() as $error)
                        <li><small>{{ $error }}</small></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
    </div>
@endif
