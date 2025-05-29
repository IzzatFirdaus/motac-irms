@props(['team', 'component' => 'dropdown-link'])

<form method="POST" action="{{ route('current-team.update') }}" id="switch-team-form-{{ $team->id }}">
  @method('PUT')
  @csrf

  <input type="hidden" name="team_id" value="{{ $team->id }}">

  <x-dynamic-component :component="$component" href="#"
    x-on:click.prevent="document.getElementById('switch-team-form-{{ $team->id }}').submit()"> {{-- Using Alpine for click if component supports it, or original onclick --}}
    <div class="d-flex align-items-center">
      @if (Auth::check() && Auth::user()->isCurrentTeam($team))
        <svg class="me-2 text-success" width="18" height="18" fill="none" stroke-linecap="round" stroke-linejoin="round" {{-- Adjusted size slightly --}}
          stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      @endif

      <div class="text-truncate">{{ $team->name }}</div> {{-- Added text-truncate in case team names are long --}}
    </div>
  </x-dynamic-component>
</form>
