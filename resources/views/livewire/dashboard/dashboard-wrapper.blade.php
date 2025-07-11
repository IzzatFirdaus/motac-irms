{{-- resources/views/livewire/dashboard/admin-dashboard-wrapper.blade.php --}}
<div>
    {{-- This component checks the user's role and loads the appropriate dashboard view. --}}
    @if ($isAdmin)
        @livewire('dashboard.admin-dashboard')
    @elseif ($isApprover)
        @livewire('dashboard.approver-dashboard')
    @elseif ($isBpm)
        @livewire('dashboard.bpm-dashboard')
    @elseif ($isItAdmin)
        @livewire('dashboard.it-admin-dashboard')
    @else
        {{-- Fallback for any other privileged user type --}}
        <div class="alert alert-info">{{ __('dashboard.welcome') }}, {{ $displayUserName }}!</div>
    @endif
</div>
