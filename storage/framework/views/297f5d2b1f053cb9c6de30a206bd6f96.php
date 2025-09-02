


<?php
    $configData = \App\Helpers\Helpers::appClasses(); // Use fully qualified namespace for helpers
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
?>


<script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>

<?php if($hasCustomizer): ?>
    
    <script src="<?php echo e(asset('assets/vendor/js/template-customizer.js')); ?>"></script>
<?php endif; ?>


<script src="<?php echo e(asset('assets/js/config.js')); ?>"></script>

<?php if($hasCustomizer): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the template customizer if available
            if (typeof TemplateCustomizer !== 'undefined') {
                window.templateCustomizer = new TemplateCustomizer({
                    cssPath: "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath) . '/'); ?>",
                    themesPath: "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath) . '/'); ?>",
                    defaultShowDropdownOnHover: <?php echo e($configData['showDropdownOnHover'] ?? ($configData['layout'] === 'horizontal' ? true : false) ? 'true' : 'false'); ?>,
                    displayCustomizer: <?php echo e($displayCustomizer ? 'true' : 'false'); ?>,
                    lang: '<?php echo e(app()->getLocale()); ?>',
                    pathResolver: function(path) {
                        var resolvedPaths = {
                            'core.css': "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath . '/core.css')); ?>",
                            'core-dark.css': "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath . '/core-dark.css')); ?>",
                            'theme-default.css': "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath . '/theme-default.css')); ?>",
                            'theme-default-dark.css': "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath . '/theme-default-dark.css')); ?>",
                            'theme-motac.css': "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac.css')); ?>",
                            'theme-motac-dark.css': "<?php echo e(asset('assets/vendor/css' . $rtlSupportPath . '/theme-motac-dark.css')); ?>",
                        };
                        return resolvedPaths[path] || path;
                    },
                    controls: <?php echo json_encode($customizerControls, 15, 512) ?>
                });
            } else {
                console.warn('TemplateCustomizer class not found, customizer will not be initialized.');
            }
        });
    </script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/layouts/sections/layout-scripts-includes.blade.php ENDPATH**/ ?>