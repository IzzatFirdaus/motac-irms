@push('custom-css')
    {{-- Add any jstree specific CSS if not globally included --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jstree/themes/default/style.min.css') }}" />
@endpush

<div wire:ignore.self class="modal fade" id="categoryInfoModal" tabindex="-1" aria-labelledby="categoryInfoModalLabel"
    aria-hidden="true">
    {{-- Removed modal-simple --}}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-0">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryInfoModalLabel">
                    <i class="bi bi-diagram-3-fill me-2"></i> {{-- Icon for hierarchy/info --}}
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
    {{-- Ensure jQuery and jstree JS are loaded, typically in a master layout --}}
    <script src="{{ asset('assets/vendor/libs/jstree/jstree.min.js') }}"></script>
    <script>
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
                            // Theme 'default' is more neutral; 'default-dark' for dark themes.
                            // Consider making this dynamic or ensure your app's theme is dark if using 'default-dark'.
                            'name': 'default',
                            'responsive': true,
                            'dots': true, // Show dots for hierarchy
                            'icons': true // Show default icons
                        }
                    },
                    'plugins': ['types'],
                    'types': {
                        'default': {
                            // Changed ti-folder to bi-folder (Bootstrap Icons)
                            'icon': 'bi bi-folder text-warning'
                        },
                        'file': { // Assuming 'file' type for leaf nodes if used
                            // Changed ti-file to bi-file-earmark (Bootstrap Icons)
                            'icon': 'bi bi-file-earmark text-info'
                        }
                    }
                });
            });

            // Fallback or initial load
            @if (isset($treeJsonPayload) && isset($categoryInfo))
                {{-- Ensure categoryInfo is set for ID --}}
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
