@push('custom-css')
{{-- Add any jstree specific CSS if not globally included --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/jstree/themes/default/style.min.css') }}" />
@endpush

<div wire:ignore.self class="modal fade" id="categoryInfoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple">
    <div class="modal-content p-0 p-md-5"> {{-- Adjusted padding for consistency --}}
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">{{ $categoryInfo->name ?? __('Category Information') }}</h3>
          <p class="text-muted">{{ __('Hierarchical view of sub-categories.') }}</p>
        </div>

        {{-- jstree will populate this div --}}
        <div id="categoryTree_{{ $categoryInfo->id ?? 'default' }}">
            {{-- Content will be loaded by jstree via JavaScript --}}
            {{-- Example: Displaying a loading message or a placeholder --}}
            <p>{{ __('Loading tree...') }}</p>
        </div>

      </div>
    </div>
  </div>
</div>

@push('custom-scripts')
{{-- Ensure jQuery and jstree JS are loaded --}}
<script src="{{ asset('assets/vendor/libs/jstree/jstree.min.js') }}"></script>
<script>
  document.addEventListener('livewire:load', function () {
    // Listener for when Livewire loads this component or an event is triggered to show/update the modal
    Livewire.on('showCategoryInfoModal_{{ $categoryInfo->id ?? 'default' }}', categoryData => {
      $('#categoryTree_{{ $categoryInfo->id ?? 'default' }}').jstree(true).destroy(); // Destroy existing tree if any
      $('#categoryTree_{{ $categoryInfo->id ?? 'default' }}').jstree({
        'core': {
          'data': categoryData.treeData, // Expect treeData from Livewire component
          'themes': {
            'name': 'default-dark', // Or your preferred theme
            'responsive': true
          }
        },
        'plugins': ['types'],
        'types': {
          'default': {
            'icon': 'ti ti-folder text-warning'
          },
          'file': {
            'icon': 'ti ti-file text-info'
          }
          // Define more types if needed
        }
      });
      // $('#categoryInfoModal').modal('show'); // Show modal if not already visible - careful with wire:ignore.self
    });

    // Fallback or initial load if data is directly available (less common for dynamic modals)
    // This example assumes data is fetched and passed via Livewire event for dynamic content
    // If $categoryInfo and its subcategories are directly available on initial load,
    // you might need a different trigger or pre-populate `categoryData.treeData`
    // For instance, if your Livewire component prepares `treeJsonPayload`
    @if(isset($treeJsonPayload))
        $('#categoryTree_{{ $categoryInfo->id ?? 'default' }}').jstree({
            'core': {
                'data': {!! $treeJsonPayload !!}, // Make sure this is correctly formatted JSON
                'themes': {
                    'name': 'default-dark',
                    'responsive': true
                }
            },
            'plugins': ['types'],
            'types': {
                'default': { 'icon': 'ti ti-folder text-warning' },
                'file': { 'icon': 'ti ti-file text-info' }
            }
        });
    @endif

  });
</script>
@endpush
