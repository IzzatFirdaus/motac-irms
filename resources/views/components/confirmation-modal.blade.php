@props(['id' => null, 'maxWidth' => null, 'title', 'content', 'footer'])

{{-- Assuming x-modal is a base Bootstrap modal component like modal-motac-generic.blade.php --}}
{{-- This component provides the modal-content part --}}
<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
  {{-- The modal-dialog part should be handled by the x-modal component itself --}}
  {{-- This content populates the .modal-content div --}}
  <div class="modal-header">
    @if(isset($title))
    <h4 class="modal-title">{{ $title }}</h4>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
  </div>
  <div class="modal-body">
    {{ $content }}
  </div>
  @if(isset($footer))
  <div class="modal-footer">
    {{ $footer }}
  </div>
  @endif
</x-modal>
