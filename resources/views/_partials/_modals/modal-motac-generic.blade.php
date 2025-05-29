{{-- resources/views/_partials/_modals/modal-motac-generic.blade.php --}}
{{--
    Generic Reusable Bootstrap 5 Modal for MOTAC System

    Props to pass or set:
    - modalId: (string) Unique ID for the modal (e.g., 'confirmDeleteModal'). Required.
    - modalTitle: (string) The title for the modal header (will be translated). Required.
    - modalIcon: (string, optional) Tabler Icon class for an icon next to the title (e.g., 'ti-alert-triangle').
    - modalSize: (string, optional) Bootstrap modal size class (e.g., 'modal-sm', 'modal-lg', 'modal-xl'). Default is medium.
    - modalFormId: (string, optional) ID of the form this modal's primary button should submit.
    - modalSubmitButtonText: (string, optional) Text for the primary submission button (translatable). Default: 'Simpan'.
    - hideFooter: (bool, optional) Set to true to hide the default footer. Default: false.
    - livewireSubmitAction: (string, optional) Livewire action to call on primary button click if not using a form.
--}}

<div wire:ignore.self class="modal fade" id="{{ $modalId }}" tabindex="-1"
     aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog {{ $modalSize ?? '' }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="{{ $modalId }}Label">
                    @if(isset($modalIcon) && !empty($modalIcon))
                        <span class="me-2"><i class="ti {{ $modalIcon }} ti-sm"></i></span>
                    @endif
                    {{ __($modalTitle) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
            </div>
            <div class="modal-body">
                {{-- Slot for dynamic modal content --}}
                {{ $slot }}
            </div>
            @if(!isset($hideFooter) || !$hideFooter)
            <div class="modal-footer">
                @isset($modalFooterContent)
                    {{ $modalFooterContent }} {{-- Allows for completely custom footer buttons --}}
                @else
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    @if(isset($modalFormId) && !empty($modalFormId))
                        <button type="submit" form="{{ $modalFormId }}" class="btn btn-primary">{{ __($modalSubmitButtonText ?? 'Simpan') }}</button>
                    @elseif(isset($livewireSubmitAction) && !empty($livewireSubmitAction))
                        <button type="button" wire:click="{{ $livewireSubmitAction }}" class="btn btn-primary" wire:loading.attr="disabled" wire:target="{{ $livewireSubmitAction }}">
                            <span wire:loading.remove wire:target="{{ $livewireSubmitAction }}">{{ __($modalSubmitButtonText ?? 'Teruskan') }}</span>
                            <span wire:loading wire:target="{{ $livewireSubmitAction }}">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
