@props(['id', 'maxWidth', 'modal' => false, 'title' => null]) {{-- Added title prop --}}

@php
$id = $id ?? md5($attributes->wire('model').uniqid()); // Ensured more unique ID if generated

$modalSizeClass = '';
switch ($maxWidth ?? '') {
    case 'sm':
        $modalSizeClass = ' modal-sm';
        break;
    // case 'md': // Standard Bootstrap modal, no specific size class needed for md
    //     $modalSizeClass = '';
    //     break;
    case 'lg':
        $modalSizeClass = ' modal-lg';
        break;
    case 'xl':
        $modalSizeClass = ' modal-xl';
        break;
    // '2xl' is not a standard Bootstrap modal size, defaults to standard if not 'sm', 'lg', or 'xl'.
    // If a 'fullscreen' or other custom size is needed, it would require custom CSS or specific Bootstrap classes like 'modal-fullscreen'.
    default:
        $modalSizeClass = ''; // Default to standard Bootstrap modal size
        break;
}
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')).defer }" {{-- Added .defer for better performance with Livewire --}}
    x-init="() => {
        let modalElement = document.getElementById('{{ $id }}');
        let bootstrapModal = new bootstrap.Modal(modalElement);

        $watch('show', value => {
            if (value) {
                bootstrapModal.show();
            } else {
                bootstrapModal.hide();
            }
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            show = false;
        });
    }"
    wire:ignore.self
    class="modal fade"
    tabindex="-1"
    id="{{ $id }}"
    aria-labelledby="{{ $id }}Label" {{-- Added Label suffix for aria --}}
    aria-hidden="true"
    x-ref="{{ $id }}">

  <div class="modal-dialog{{ $modalSizeClass }} modal-dialog-centered modal-dialog-scrollable"> {{-- Added modal-dialog-centered and modal-dialog-scrollable for common use cases --}}
    <div class="modal-content">
      {{-- Optional Modal Header --}}
      @if ($title || isset($header))
        <div class="modal-header">
          @if (isset($header))
            {{ $header }}
          @else
            <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
            <button type="button" class="btn-close" @click="show = false" aria-label="{{ __('Close') }}"></button>
          @endif
        </div>
      @endif

      {{-- Modal Body (Main Slot) --}}
      {{ $slot }}

      {{-- Optional Modal Footer --}}
      @if (isset($footer))
        <div class="modal-footer">
          {{ $footer }}
        </div>
      @endif
    </div>
  </div>
</div>
