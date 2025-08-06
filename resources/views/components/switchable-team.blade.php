{{-- resources/views/components/switchable-team.blade.php --}}
@props(['team', 'component' => 'dropdown-link'])

<form method="POST" action="{{-- route('current-team.update') --}}" id="switch-team-form-{{ $team->id }}">
    @method('PUT')
    @csrf

    <input type="hidden" name="team_id" value="{{ $team->id }}">

    <x-dynamic-component :component="$component" href="#"
        onclick="alert('Fungsi penukaran pasukan tidak aktif pada masa ini. Sila hubungi pentadbir sistem.'); return false;"
        title="{{ __('Fungsi penukaran pasukan tidak aktif') }}"
        >
        <div class="d-flex align-items-center">
            @if (Auth::check() && Auth::user()->isCurrentTeam($team))
                <i class="bi bi-check-circle-fill me-2 text-success" style="font-size: 1.1rem;"></i>
            @else
                <span style="width: calc(1.1rem + 0.5rem); display: inline-block;"></span>
            @endif

            <div class="text-truncate">{{ $team->name }}</div>
        </div>
    </x-dynamic-component>
</form>
