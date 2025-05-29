@props(['id' => 'navbarDropdown', 'align' => 'end']) {{-- Added align prop for Bootstrap 5 (start, end) --}}

<li class="nav-item dropdown">
  <a id="{{ $id }}" {!! $attributes->merge(['class' => 'nav-link dropdown-toggle']) !!} href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    {{ $trigger }}
  </a>

  <div class="dropdown-menu dropdown-menu-{{ $align }} {{ $attributes->get('menu-class', '') }}" aria-labelledby="{{ $id }}">
    {{ $content }}
  </div>
</li>
