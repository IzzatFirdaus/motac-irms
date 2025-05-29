<div class="card">
  <h5 class="card-header">
    {{ $title }}
  </h5>
  <div class="card-body">
    @if(isset($description))
    <p class="card-text text-muted">
      {{ $description }}
    </p>
    @endif
    {{ $content }}
  </div>
</div>
