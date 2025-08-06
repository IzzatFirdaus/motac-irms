{{-- Generic Reusable Bootstrap 5 Modal for MOTAC System --}}
{{--
    Props:
    - modalId: unique modal ID
    - modalTitle: modal header/title (translated)
    - modalIcon: Bootstrap icon class (optional)
    - modalSize: modal size class (optional)
    - modalFormId: form ID for submit button (optional)
    - modalSubmitButtonText: primary button text (translatable, optional)
    - hideFooter: hide the footer (optional)
    - livewireSubmitAction: Livewire action for primary button (optional)
--}}

<div wire:ignore.self class="modal fade" id="{{ $modalId }}" tabindex="-1"
     aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog {{ $modalSize ?? '' }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="{{ $modalId }}Label">
                    @if(isset($modalIcon) && !empty($modalIcon))
                        <span class="me-2"><i class="bi {{ $modalIcon }} fs-5"></i></span>
                    @endif
                    {{ __($modalTitle) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(!isset($hideFooter) || !$hideFooter)
            <div class="modal-footer">
                @isset($modalFooterContent)
                    {{ $modalFooterContent }}
                @else
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    @if(isset($modalFormId) && !empty($modalFormId))
                        <button type="submit" form="{{ $modalFormId }}" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            {{ __($modalSubmitButtonText ?? 'Simpan') }}
                        </button>
                    @elseif(isset($livewireSubmitAction) && !empty($livewireSubmitAction))
                        <button type="button" wire:click="{{ $livewireSubmitAction }}" class="btn btn-primary" wire:loading.attr="disabled" wire:target="{{ $livewireSubmitAction }}">
                            <span wire:loading.remove wire:target="{{ $livewireSubmitAction }}">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ __($modalSubmitButtonText ?? 'Teruskan') }}
                            </span>
                            <span wire:loading wire:target="{{ $livewireSubmitAction }}" class="d-inline-flex align-items-center">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Memproses...') }}
                            </span>
                        </button>
                    @endif
                @endisset
            </div>
            @endif
        </div>
    </div>
</div>
