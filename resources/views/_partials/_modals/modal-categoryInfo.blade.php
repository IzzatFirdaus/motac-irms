@push('custom-css')
    {{-- Add any jstree specific CSS if not globally included --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jstree/themes/default/style.min.css') }}" />
@endpush

<div wire:ignore.self class="modal fade" id="categoryInfoModal" tabindex="-1" aria-labelledby="categoryInfoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-0">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryInfoModalLabel">
                    <i class="bi bi-diagram-3-fill me-2"></i>
                    {{ $categoryInfo->name ?? __('Informasi Kategori') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('Tutup') }}"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <p class="text-muted text-center mb-3">{{ __('Paparan hierarki sub-kategori.') }}</p>

                <div id="categoryTree_{{ $categoryInfo->id ?? 'default' }}" class="border rounded p-3 bg-light-subtle">
                    <p class="text-center text-muted fst-italic">{{ __('Memuatkan struktur pokok...') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>{{ __('Tutup') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
    <script src="{{ asset('assets/vendor/libs/jstree/jstree.min.js') }}"></script>
    <script>
        // This script initializes jstree on the modal using Livewire events for dynamic tree rendering
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('showCategoryInfoModal_{{ $categoryInfo->id ?? 'default' }}', categoryData => {
                const treeElement = $('#categoryTree_{{ $categoryInfo->id ?? 'default' }}');
                if (treeElement.jstree(true)) {
                    treeElement.jstree(true).destroy();
                }
                treeElement.jstree({
                    'core': {
                        'data': categoryData.treeData,
                        'themes': {
                            'name': 'default',
                            'responsive': true,
                            'dots': true,
                            'icons': true
                        }
                    },
                    'plugins': ['types'],
                    'types': {
                        'default': {
                            'icon': 'bi bi-folder text-warning'
                        },
                        'file': {
                            'icon': 'bi bi-file-earmark text-info'
                        }
                    }
                });
            });

            // Fallback or initial load if treeJsonPayload is set
            @if (isset($treeJsonPayload) && isset($categoryInfo))
                $('#categoryTree_{{ $categoryInfo->id }}').jstree({
                    'core': {
                        'data': {!! $treeJsonPayload !!},
                        'themes': {
                            'name': 'default',
                            'responsive': true,
                            'dots': true,
                            'icons': true
                        }
                    },
                    'plugins': ['types'],
                    'types': {
                        'default': {
                            'icon': 'bi bi-folder text-warning'
                        },
                        'file': {
                            'icon': 'bi bi-file-earmark text-info'
                        }
                    }
                });
            @endif
        });
    </script>
@endpush
