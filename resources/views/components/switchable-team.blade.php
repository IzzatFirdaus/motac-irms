{{-- resources/views/components/switchable-team.blade.php --}}
@props(['team', 'component' => 'dropdown-link'])

{{--
    IMPORTANT: The team switching functionality is currently NON-OPERATIONAL.
    The required route 'current-team.update' is not defined in 'routes/web.php'.
    To enable team switching:
    1. Ensure Jetstream Teams or equivalent custom team functionality is fully implemented.
    2. Define the 'current-team.update' route and its controller logic.
    3. Uncomment the form submission logic below (the x-on:click.prevent attribute on the dynamic component).
    If team switching is not a required feature for the MOTAC system, this component might be unused or can be removed.
--}}

<form method="POST" action="{{-- route('current-team.update') --}}" {{-- Action commented out as route is not defined --}} id="switch-team-form-{{ $team->id }}">
    @method('PUT')
    @csrf

    <input type="hidden" name="team_id" value="{{ $team->id }}">

    <x-dynamic-component :component="$component" href="#"
        {{-- x-on:click.prevent="document.getElementById('switch-team-form-{{ $team->id }}').submit()" --}} {{-- Click submission commented out --}}
        onclick="alert('Fungsi penukaran pasukan tidak aktif pada masa ini. Sila hubungi pentadbir sistem.'); return false;" {{-- Provide user feedback --}}
        title="{{ __('Fungsi penukaran pasukan tidak aktif') }}"
        >
        <div class="d-flex align-items-center">
            @if (Auth::check() && Auth::user()->isCurrentTeam($team))
                <i class="bi bi-check-circle-fill me-2 text-success" style="font-size: 1.1rem;"></i>
            @else
                <span style="width: calc(1.1rem + 0.5rem); display: inline-block;"></span> {{-- Placeholder for alignment --}}
            @endif

            <div class="text-truncate">{{ $team->name }}</div>
        </div>
    </x-dynamic-component>
</form>
