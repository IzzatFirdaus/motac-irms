{{-- scriptsIncludes.blade.php --}}
<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

@if ($configData['hasCustomizer'])
  {{-- Design Document: User-selectable Dark Mode is required.
       The customizer might be limited to only Light/Dark mode toggle for simplicity in an internal system.
       This depends on the final configuration of $configData['customizerControls'].
  --}}
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
@endif

<script src="{{ asset('assets/js/config.js') }}"></script>

@if ($configData['hasCustomizer'])
  <script>
    window.templateCustomizer = new TemplateCustomizer({
      cssPath: '',
      themesPath: '',
      defaultShowDropdownOnHover: {{$configData['showDropdownOnHover']}}, // true/false (for horizontal layout only)
      displayCustomizer: {{$configData['displayCustomizer']}},
      lang: '{{ app()->getLocale() }}', // Correctly passes current locale
      pathResolver: function(path) {
        var resolvedPaths = {
          // Core stylesheets
          @foreach (['core'] as $name)
            '{{ $name }}.css': '{{ asset(mix("assets/vendor/css{$configData['rtlSupport']}/{$name}.css")) }}',
            '{{ $name }}-dark.css': '{{ asset(mix("assets/vendor/css{$configData['rtlSupport']}/{$name}-dark.css")) }}',
          @endforeach

          // Themes
          @foreach (['default', 'bordered', 'semi-dark'] as $name)
            'theme-{{ $name }}.css': '{{ asset(mix("assets/vendor/css{$configData['rtlSupport']}/theme-{$name}.css")) }}',
            'theme-{{ $name }}-dark.css':
            '{{ asset(mix("assets/vendor/css{$configData['rtlSupport']}/theme-{$name}-dark.css")) }}',
          @endforeach
        }
        return resolvedPaths[path] || path;
      },
      'controls': <?php echo json_encode($configData['customizerControls']); ?>, // Controls what's shown in the customizer
    });
  </script>
@endif
//{{--}}
//{{-- resources/views/layouts/sections/scriptsIncludes.blade.php --}}
//{{--@php
//    $configData = \App\Helpers\Helpers::appClasses(); // Use fully qualified namespace
//    $hasCustomizer = $configData['hasCustomizer'] ?? false;
//    $displayCustomizer = $configData['displayCustomizer'] ?? false;
//    $rtlSupportPath = $configData['rtlSupport'] ?? '';

//    $defaultCustomizerControls = ['rtl', 'style', 'themes', 'layoutType', 'showDropdownOnHover', 'layoutNavbarFixed', 'layoutFooterFixed', 'menuFixed', 'menuCollapsed'];
//    $customizerControls = $configData['customizerControls'] ?? $defaultCustomizerControls;
//@endphp

//<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

//@if ($hasCustomizer)
//  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
//@endif

//<script src="{{ asset('assets/js/config.js') }}"></script>

//@if ($hasCustomizer)
//  <script>
//    document.addEventListener('DOMContentLoaded', function() {
//      if (typeof TemplateCustomizer !== 'undefined') {
//        window.templateCustomizer = new TemplateCustomizer({
//          cssPath: "{{ asset('assets/vendor/css' . $rtlSupportPath) . '/' }}",
//          themesPath: "{{ asset('assets/vendor/css' . $rtlSupportPath) . '/' }}",
//          defaultShowDropdownOnHover: {{ ($configData['showDropdownOnHover'] ?? ($configData['layout'] === 'horizontal' ? true : false)) ? 'true' : 'false' }},
//          displayCustomizer: {{ $displayCustomizer ? 'true' : 'false' }},
//          lang: '{{ app()->getLocale() }}',
//          pathResolver: function(path) {
//            var resolvedPaths = {
//              'core.css':               "{{ asset('assets/vendor/css' . $rtlSupportPath . '/core.css') }}",
//              'core-dark.css':          "{{ asset('assets/vendor/css' . $rtlSupportPath . '/core-dark.css') }}",
//              'theme-default.css':      "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-default.css') }}",
//             'theme-default-dark.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-default-dark.css') }}",
//              'theme-motac.css':        "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac.css') }}",
//              'theme-motac-dark.css':   "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac-dark.css') }}",
//            };
//            return resolvedPaths[path] || path;
//          },
//          controls: @json($customizerControls)
//        });
//      } else {
//        console.warn('TemplateCustomizer class not found, customizer will not be initialized.');
//      }
//    });
//  </script>
//@endif
