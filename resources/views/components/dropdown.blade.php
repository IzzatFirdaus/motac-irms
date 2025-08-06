{{-- resources/views/components/dropdown.blade.php --}}
@props(['id' => 'navbarDropdown', 'align' => 'end', 'triggerClass' => 'nav-link dropdown-toggle'])

<li class="nav-item dropdown">
  <a id="{{ $id }}" {!! $attributes->merge(['class' => $triggerClass]) !!} href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    {{ $trigger }}
  </a>

  <div class="dropdown-menu dropdown-menu-{{ $align }} {{ $attributes->get('menu-class', '') }}" aria-labelledby="{{ $id }}">
    {{ $content }}
  </div>
</li>
