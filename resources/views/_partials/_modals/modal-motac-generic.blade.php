{{-- resources/views/_partials/_modals/modal-motac-generic.blade.php --}}
{{-- Generic Reusable Bootstrap 5 Modal for MOTAC System --}}
{{--
    This is a versatile modal component that can be used throughout the MOTAC system.

    Available Props:
    - modalId: unique modal ID (required)
    - modalTitle: modal header/title (translated)
    - modalIcon: Bootstrap icon class (optional)
    - modalSize: modal size class (optional - modal-sm, modal-lg, modal-xl)
    - modalFormId: form ID for submit button (optional)
    - modalSubmitButtonText: primary button text (translatable, optional)
    - hideFooter: hide the footer (optional boolean)
    - livewireSubmitAction: Livewire action for primary button (optional)

    Usage Examples:
    @include('_partials._modals.modal-motac-generic', [
        'modalId' => 'myModal',
        'modalTitle' => 'Edit Record',
        'modalIcon' => 'bi-pencil-square',
        'modalSize' => 'modal-lg'
    ])
--}}

<div wire:ignore.self class="modal fade" id="{{ $modalId }}" tabindex="-1"
     aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog {{ $modalSize ?? '' }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- Modal Header --}}
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="{{ $modalId }}Label">
                    {{-- Display icon if provided --}}
                    @if(isset($modalIcon) && !empty($modalIcon))
                        <span class="me-2"><i class="bi {{ $modalIcon }} fs-5"></i></span>
                    @endif
                    {{ __($modalTitle) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
            </div>

            {{-- Modal Body - Content passed via slot --}}
            <div class="modal-body">
                {{ $slot }}
            </div>

            {{-- Modal Footer - Conditional rendering --}}
            @if(!isset($hideFooter) || !$hideFooter)
            <div class="modal-footer">
                {{-- Custom footer content if provided --}}
                @isset($modalFooterContent)
                    {{ $modalFooterContent }}
                @else
                    {{-- Default footer with Cancel button --}}
                    <button type="button" class="btn btn-outline-secondary motac-btn-outline" data-bs-dismiss="modal">{{ __('Batal') }}</button>

                    {{-- Submit button for form-based modals --}}
                    @if(isset($modalFormId) && !empty($modalFormId))
                        <button type="submit" form="{{ $modalFormId }}" class="btn btn-primary motac-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            {{ __($modalSubmitButtonText ?? 'Simpan') }}
                        </button>
                    {{-- Submit button for Livewire action-based modals --}}
                    @elseif(isset($livewireSubmitAction) && !empty($livewireSubmitAction))
                        <button type="button" wire:click="{{ $livewireSubmitAction }}" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled" wire:target="{{ $livewireSubmitAction }}">
                            {{-- Button content changes based on loading state --}}
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
