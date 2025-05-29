@props(['submit'])

<div class="card mb-3 shadow-sm"> {{-- Added mb-3 and shadow-sm for consistency --}}
  @if(isset($title))
  <h5 class="card-header">
    {{ $title }}
  </h5>
  @endif
  <div class="card-body">
    <form wire:submit.prevent="{{ $submit }}">
      @if(isset($description))
      <p class="card-text text-muted">
        {{ $description }}
      </p>
      @endif

      {{ $form }}

      @if (isset($actions))
        <div class="d-flex justify-content-end pt-3 mt-3 border-top"> {{-- Added spacing and border for actions --}}
          {{ $actions }}
        </div>
      @endif
    </form>
  </div>
</div>
