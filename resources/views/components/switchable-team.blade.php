{{-- resources/views/components/switchable-team.blade.php --}}
@props(['team', 'component' => 'dropdown-link']) {{-- dropdown-link is a Bootstrap component, good. --}}

<form method="POST" action="{{ route('current-team.update') }}" id="switch-team-form-{{ $team->id }}">
    @method('PUT')
    @csrf

    <input type="hidden" name="team_id" value="{{ $team->id }}">

    <x-dynamic-component :component="$component" href="#"
        x-on:click.prevent="document.getElementById('switch-team-form-{{ $team->id }}').submit()">
        <div class="d-flex align-items-center">
            @if (Auth::check() && Auth::user()->isCurrentTeam($team))
                {{-- Replaced inline SVG with Bootstrap Icon --}}
                <i class="bi bi-check-circle-fill me-2 text-success" style="font-size: 1.1rem;"></i>
            @else
                {{-- Placeholder for alignment if no icon, or a different icon for non-current teams --}}
                <span style="width: 1.1rem; margin-right: 0.5rem;"></span> {{-- Adjust width to match icon --}}
            @endif

            <div class="text-truncate">{{ $team->name }}</div>
        </div>
    </x-dynamic-component>
</form>
