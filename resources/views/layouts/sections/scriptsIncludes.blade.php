<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

@if ($configData['hasCustomizer'] ?? false)
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
@endif

<script src="{{ asset('assets/js/config.js') }}"></script>

@if ($configData['hasCustomizer'] ?? false)
  <script>
    window.templateCustomizer = new TemplateCustomizer({
      cssPath: '',
      themesPath: '',
      // Assuming 'true' is the default for showDropdownOnHover if not set, based on your custom.php
      defaultShowDropdownOnHover: {{ ($configData['showDropdownOnHover'] ?? true) ? 'true' : 'false' }},
      // Assuming 'false' is the default for displayCustomizer if not set, based on your custom.php
      displayCustomizer: {{ ($configData['displayCustomizer'] ?? false) ? 'true' : 'false' }},
      lang: '{{ app()->getLocale() }}',
      pathResolver: function(path) {
        var resolvedPaths = {
          // Core stylesheets
          @foreach (['core'] as $name)
            '{{ $name }}.css': '{{ asset(mix("assets/vendor/css" . ($configData['rtlSupport'] ?? '') . "/{$name}.css")) }}',
            '{{ $name }}-dark.css': '{{ asset(mix("assets/vendor/css" . ($configData['rtlSupport'] ?? '') . "/{$name}-dark.css")) }}',
          @endforeach

          // Themes
          @foreach (['default', 'bordered', 'semi-dark'] as $name)
            'theme-{{ $name }}.css': '{{ asset(mix("assets/vendor/css" . ($configData['rtlSupport'] ?? '') . "/theme-{$name}.css")) }}',
            'theme-{{ $name }}-dark.css':
            '{{ asset(mix("assets/vendor/css" . ($configData['rtlSupport'] ?? '') . "/theme-{$name}-dark.css")) }}',
          @endforeach
        }
        return resolvedPaths[path] || path;
      },
      'controls': <?php echo json_encode($configData['customizerControls'] ?? []); ?>,
    });
  </script>
@endif
