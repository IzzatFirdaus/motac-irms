{{-- resources/views/components/action-message.blade.php --}}
@props(['on'])

<div {{ $attributes->merge(['class' => 'alert alert-success small py-2']) }}
    role="alert"
    x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 3000);  })"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style="display: none;">
  <div class="alert-body d-flex align-items-center">
    <i class="bi bi-check-circle-fill me-2"></i>
    <span>{{ $slot->isEmpty() ? __('Berjaya disimpan.') : $slot }}</span>
  </div>
</div>
