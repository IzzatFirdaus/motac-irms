{{-- resources/views/layouts/sections/scriptsIncludes.blade.php --}}
@php
    $configData = \App\Helpers\Helpers::appClasses(); //Use fully qualified namespace
    $hasCustomizer = $configData['hasCustomizer'] ?? false;
    $displayCustomizer = $configData['displayCustomizer'] ?? false;
    $rtlSupportPath = $configData['rtlSupport'] ?? '';

    $defaultCustomizerControls = [
        'rtl',
        'style',
        'themes',
        'layoutType',
        'showDropdownOnHover',
        'layoutNavbarFixed',
        'layoutFooterFixed',
        'menuFixed',
        'menuCollapsed',
    ];
    $customizerControls = $configData['customizerControls'] ?? $defaultCustomizerControls;
@endphp

<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

@if ($hasCustomizer)
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
@endif

<script src="{{ asset('assets/js/config.js') }}"></script>

@if ($hasCustomizer)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof TemplateCustomizer !== 'undefined') {
                window.templateCustomizer = new TemplateCustomizer({
                    cssPath: "{{ asset('assets/vendor/css' . $rtlSupportPath) . '/' }}",
                    themesPath: "{{ asset('assets/vendor/css' . $rtlSupportPath) . '/' }}",
                    defaultShowDropdownOnHover: {{ $configData['showDropdownOnHover'] ?? ($configData['layout'] === 'horizontal' ? true : false) ? 'true' : 'false' }},
                    displayCustomizer: {{ $displayCustomizer ? 'true' : 'false' }},
                    lang: '{{ app()->getLocale() }}',
                    pathResolver: function(path) {
                        var resolvedPaths = {
                            'core.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/core.css') }}",
                            'core-dark.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/core-dark.css') }}",
                            'theme-default.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-default.css') }}",
                            'theme-default-dark.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-default-dark.css') }}",
                            'theme-motac.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac.css') }}",
                            'theme-motac-dark.css': "{{ asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac-dark.css') }}",
                        };
                        return resolvedPaths[path] || path;
                    },
                    controls: @json($customizerControls)
                });
            } else {
                console.warn('TemplateCustomizer class not found, customizer will not be initialized.');
            }
        });
    </script>
@endif
