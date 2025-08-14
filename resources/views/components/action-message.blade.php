{{-- resources/views/components/action-message.blade.php --}}
@props(['on'])

<div {{ $attributes->merge(['class' => 'alert alert-success small py-2']) }} {{-- Added py-2 for consistent small alert padding --}}
    role="alert" x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 3000);  })" {{-- Extended timeout slightly --}}
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style="display: none;">
  <div class="alert-body d-flex align-items-center"> {{-- Flex for icon alignment --}}
    <i class="bi bi-check-circle-fill me-2"></i> {{-- Added Bootstrap Icon for success --}}
    <span>{{ $slot->isEmpty() ? __('Berjaya disimpan.') : $slot }}</span> {{-- Translated default --}}
  </div>
</div>
