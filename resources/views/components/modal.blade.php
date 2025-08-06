{{-- resources/views/components/modal.blade.php --}}
@props([
    'id',
    'maxWidth' => null,
    'title' => null,
    'icon' => null,
    'headerClass' => 'modal-header',
    'bodyClass' => 'modal-body',
    'footerClass' => 'modal-footer',
])

@php
$id = $id ?? md5($attributes->wire('model').uniqid());

$modalSizeClass = '';
switch ($maxWidth ?? '') {
    case 'sm': $modalSizeClass = ' modal-sm'; break;
    case 'lg': $modalSizeClass = ' modal-lg'; break;
    case 'xl': $modalSizeClass = ' modal-xl'; break;
    case 'fullscreen': $modalSizeClass = ' modal-fullscreen'; break;
    default: $modalSizeClass = ''; break;
}
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')).defer }"
    x-init="() => {
        let modalElement = document.getElementById('{{ $id }}');
        if (!modalElement) {
            console.error('Modal element #{{ $id }} not found.');
            return;
        }
        let bootstrapModal = bootstrap.Modal.getInstance(modalElement);
        if (!bootstrapModal) {
            bootstrapModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
        }

        $watch('show', value => {
            if (value) {
                bootstrapModal.show();
            } else {
                bootstrapModal.hide();
            }
        });

        modalElement.addEventListener('hidden.bs.modal', () => {
            show = false;
        });
    }"
    wire:ignore.self
    class="modal fade"
    tabindex="-1"
    id="{{ $id }}"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    x-ref="{{ $id }}">

  <div class="modal-dialog{{ $modalSizeClass }} modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      @if ($title || isset($header))
        <div class="{{ $headerClass }}">
          @if (isset($header))
            {{ $header }}
          @else
            <h5 class="modal-title d-flex align-items-center" id="{{ $id }}Label">
                @if($icon)<i class="bi {{ $icon }} me-2 fs-5"></i>@endif
                {{ $title }}
            </h5>
            <button type="button" class="btn-close" @click="show = false" aria-label="{{ __('Tutup') }}"></button>
          @endif
        </div>
      @endif

      <div class="{{ $bodyClass }}">
        {{ $slot }}
      </div>

      @if (isset($footer))
        <div class="{{ $footerClass }}">
          {{ $footer }}
        </div>
      @endif
    </div>
  </div>
</div> 
