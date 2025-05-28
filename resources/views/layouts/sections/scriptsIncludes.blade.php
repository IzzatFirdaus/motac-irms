{{-- resources/views/layouts/sections/scriptsIncludes.blade.php --}}
{{-- Includes essential early JavaScript files like helpers, config, and optionally the template customizer. --}}
{{-- System Design: Phase 2 (Initial JavaScript Configurations), "The Big Picture" --}}

@php
    // $configData is globally available from commonMaster.blade.php
    $hasCustomizer = $configData['hasCustomizer'] ?? false; // Defaulted to false in Helpers.php for MOTAC
    $displayCustomizer = $configData['displayCustomizer'] ?? false; // Defaulted to false
    $rtlSupportPath = $configData['rtlSupport'] ?? ''; // '/rtl' or ''

    // Define the default customizer controls array explicitly
    $defaultCustomizerControls = ['rtl', 'style', 'themes', 'layoutType', 'showDropdownOnHover', 'layoutNavbarFixed', 'layoutFooterFixed', 'menuFixed', 'menuCollapsed'];
    // Use the default if 'customizerControls' is not set in $configData
    $customizerControls = $configData['customizerControls'] ?? $defaultCustomizerControls;
@endphp

{{-- Core theme helper script --}}
<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

{{-- Template Customizer JS - conditionally included --}}
{{-- Design Language: Simplicity over Decoration - customizer often hidden for internal tools. --}}
{{-- However, if light/dark/rtl toggle persistence is handled by it, it might still be needed without the UI panel. --}}
@if ($hasCustomizer)
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
@endif

{{-- Application config JS (sets global JS variables like assetsPath, baseUrl, etc.) --}}
<script src="{{ asset('assets/js/config.js') }}"></script>

{{-- Initialize TemplateCustomizer if it's enabled and meant to be displayed or used programmatically --}}
@if ($hasCustomizer) {{-- This entire block is conditional --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof TemplateCustomizer !== 'undefined') {
        window.templateCustomizer = new TemplateCustomizer({
          cssPath: "{{ asset('assets/vendor/css' . $rtlSupportPath) . '/' }}", // Base path for CSS
          themesPath: "{{ asset('assets/vendor/css' . $rtlSupportPath) . '/' }}", // Base path for themes CSS
          // defaultShowDropdownOnHover should be sourced from $configData
          defaultShowDropdownOnHover: {{ ($configData['showDropdownOnHover'] ?? ($configData['layout'] === 'horizontal' ? true : false)) ? 'true' : 'false' }},
          displayCustomizer: {{ $displayCustomizer ? 'true' : 'false' }}, // Controls visibility of the customizer UI panel
          lang: '{{ app()->getLocale() }}', // For customizer UI translations
          pathResolver: function(path) {
            // This resolver is used by template-customizer.js to load different theme CSS files.
            // Paths MUST match how they are structured in public/assets/vendor/css/
            // Using asset() directly as styles.blade.php does, NOT mix().
            var resolvedPaths = {
              // Core stylesheets (light and dark)
              'core.css':               "{{ asset('assets/vendor/css' . $rtlSupportPath . '/core.css') }}",
              'core-dark.css':          "{{ asset('assets/vendor/css' . $rtlSupportPath . '/core-dark.css') }}",

              // Theme stylesheets (light and dark)
              // Ensure these theme names and file structures exist. 'theme-motac' is the target.
              'theme-default.css':      "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-default.css') }}",
              'theme-default-dark.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-default-dark.css') }}",

              'theme-motac.css':        "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac.css') }}", // MOTAC Theme
              'theme-motac-dark.css':   "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac-dark.css') }}", // MOTAC Dark Theme

              // Example other themes if supported and present:
              // 'theme-bordered.css': '{{ asset("assets/vendor/css" . $rtlSupportPath . "/theme-bordered.css") }}',
              // 'theme-bordered-dark.css': '{{ asset("assets/vendor/css" . $rtlSupportPath . "/theme-bordered-dark.css") }}',
              // 'theme-semi-dark.css': '{{ asset("assets/vendor/css" . $rtlSupportPath . "/theme-semi-dark.css") }}',
              // 'theme-semi-dark-dark.css': '{{ asset("assets/vendor/css" . $rtlSupportPath . "/theme-semi-dark-dark.css") }}',
            };
            return resolvedPaths[path] || path; // Fallback to original path if not found
          },
          controls: @json($customizerControls) // Now using the pre-defined $customizerControls variable
        });
      } else {
        console.warn('TemplateCustomizer class not found, customizer will not be initialized.');
      }
    });
  </script>
@endif
