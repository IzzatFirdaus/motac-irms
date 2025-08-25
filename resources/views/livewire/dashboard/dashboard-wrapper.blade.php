<div>
    {{--
        Dashboard Role-Based Wrapper
        Checks the current user's role (provided as $isAdmin, $isApprover, etc.).
        Loads the relevant dashboard Livewire component.
        If user has no special role, shows a friendly message.
    --}}
    @if ($isAdmin)
        @livewire('dashboard.admin-dashboard')
    @elseif ($isApprover)
        @livewire('dashboard.approver-dashboard')
    @elseif ($isBpm)
        @livewire('dashboard.bpm-dashboard')
    @elseif ($isItAdmin)
        @livewire('dashboard.it-admin-dashboard')
    @else
        <div class="alert alert-info">{{ __('dashboard.welcome') }}, {{ $displayUserName }}!</div>
    @endif
</div>
