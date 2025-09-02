
<?php
    // Apply page-specific configuration if provided
    if (isset($pageConfigs)) {
        \App\Helpers\Helpers::updatePageConfig($pageConfigs);
    }

    // Main layout and theme configuration from helper
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout toggles and CSS class configuration
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);
    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    // CSS class helpers for layout states
    $navbarDetached = !empty($configData['navbarDetached']) ? 'navbar-detached' : '';
    $menuFixed = !empty($configData['menuFixed']) ? 'layout-menu-fixed' : '';
    $menuCollapsed = !empty($configData['menuCollapsed']) ? 'layout-menu-collapsed' : '';
    $navbarFixed = !empty($configData['navbarFixed']) ? 'layout-navbar-fixed' : '';
    $footerFixed = !empty($configData['footerFixed']) ? 'layout-footer-fixed' : '';
    $menuHover = !empty($configData['showDropdownOnHover']) ? 'layout-menu-hover' : '';
    $templateName = \Illuminate\Support\Str::slug(config('variables.templateName', 'MOTAC IRMS'), '-');

    // Theme style for initial load (light/dark)
    $currentThemeStyle = $configData['myStyle'] ?? 'light';

    // The $menuData variable is expected to be available from MenuServiceProvider
    $currentUserRole = Auth::check() ? Auth::user()?->getRoleNames()->first() : null;
?>


<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
    class="<?php echo e($currentThemeStyle); ?>-style layout-navbar-fixed <?php echo e($navbarFixed); ?> <?php echo e($menuFixed); ?> <?php echo e($footerFixed); ?> <?php echo e($menuCollapsed); ?> <?php echo e($menuHover); ?>"
    dir="<?php echo e($configData['textDirection'] ?? 'ltr'); ?>"
    data-theme="<?php echo e($configData['myTheme'] ?? 'theme-default'); ?>"
    data-bs-theme="<?php echo e($currentThemeStyle); ?>"
    data-assets-path="<?php echo e(asset('assets/') . '/'); ?>"
    data-base-url="<?php echo e(url('/')); ?>"
    data-framework="laravel"
    data-template="<?php echo e($templateName); ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    
    <title><?php echo $__env->yieldContent('title', __('Halaman Utama')); ?> | <?php echo e(config('variables.templateName', 'MOTAC IRMS')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', config('variables.templateDescription', 'Sistem Pengurusan Sumber Bersepadu MOTAC')); ?>" />
    <meta name="keywords" content="<?php echo $__env->yieldContent('keywords', config('variables.templateKeyword', 'motac, bpm, sistem dalaman')); ?>" />
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon/favicon-motac.ico')); ?>" />

    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/core.css')); ?>" class="<?php echo e($configData['hasCustomizer'] ?? false ? 'template-customizer-core-css' : ''); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/' . ($configData['myTheme'] ?? 'theme-default') . '.css')); ?>" class="<?php echo e($configData['hasCustomizer'] ?? false ? 'template-customizer-theme-css' : ''); ?>" />

    
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/node-waves/node-waves.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/toastr/toastr.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/animate-css/animate.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/sweetalert2/sweetalert2.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/spinkit/spinkit.css')); ?>" />

    
    <?php echo $__env->yieldContent('page-css'); ?>

    
    <script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>
    
    <?php echo $__env->make('layouts.sections.layout-scripts-includes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    
    <?php echo $__env->yieldPushContent('page-style'); ?>
    <style>
        :root { --myds-font-body: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; --myds-font-heading: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; }
        body { font-family: var(--myds-font-body); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--myds-font-heading); }
        .visually-hidden-focusable:not(:focus):not(:active) { position:absolute!important; height:1px;width:1px; overflow:hidden; clip:rect(1px,1px,1px,1px); white-space:nowrap; }
        .visually-hidden-focusable:focus { position: static !important; height: auto; width: auto; padding: 6px 10px; background: #111827; color: #fff; border-radius: 6px; }
        :focus-visible { outline: 3px solid #2563EB; outline-offset: 2px; }
    </style>
</head>

<body>
    <a href="#main-content" class="visually-hidden-focusable"><?php echo e(__('Langkau ke Kandungan Utama')); ?></a>
    
    <div class="layout-wrapper layout-content-navbar <?php echo e($isNavbar ? '' : 'layout-without-navbar'); ?>">
        <div class="layout-container">
            <?php if($isMenu): ?>
                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sections.menu.vertical-menu', [
                    'menuData' => $menuData ?? null,
                    'role' => $currentUserRole,
                    'configData' => $configData,
                ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3528977496-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            <?php endif; ?>

            <div class="layout-page">
                <?php if($isNavbar): ?>
                    
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sections.navbar.navbar', [
                        'containerNav' => $containerNav,
                        'navbarDetachedClass' => $navbarDetached,
                    ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3528977496-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?>

                <div class="content-wrapper">
                    <?php if($isFlex): ?>
                        
                        <main id="main-content" class="<?php echo e($container); ?> d-flex align-items-stretch flex-grow-1 p-0" role="main" aria-label="<?php echo e(__('Kandungan Utama')); ?>">
                    <?php else: ?>
                        
                        <main id="main-content" class="<?php echo e($container); ?> flex-grow-1 container-p-y" role="main" aria-label="<?php echo e(__('Kandungan Utama')); ?>">
                    <?php endif; ?>

                        
                        <?php echo $__env->make('_partials._alerts.alert-general', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        
                        <?php if(isset($slot)): ?>
                            <?php echo e($slot); ?>

                        <?php else: ?>
                            <?php echo $__env->yieldContent('content'); ?>
                        <?php endif; ?>
                    </main>

                    <?php if($isFooter): ?>
                        
                        <?php echo $__env->make('layouts.sections.footer.footer-section', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <?php if($isMenu): ?>
            
            <div class="layout-overlay layout-menu-toggle"></div>
        <?php endif; ?>
        <div class="drag-target"></div>
    </div>

    
    <?php echo $__env->make('layouts.sections.layout-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


    
    <?php echo $__env->yieldPushContent('page-script'); ?>

    
    <script>
        /**
         * MOTAC IRMS - Unified Theme Switcher Logic
         * Allows user to toggle between light/dark theme and notifies Livewire navbar
         */
        (function () {
          'use strict';
          const themeStorageKey = 'theme-preference';

          // Get saved theme or detect system preference
          const getThemePreference = () => {
            const preference = localStorage.getItem(themeStorageKey);
            if (preference) {
              return preference;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
          };

          // Apply theme and persist to localStorage
          const setTheme = (theme) => {
            localStorage.setItem(themeStorageKey, theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
          };

          // Expose global toggle function for theme switching (e.g., from navbar)
          window.toggleAppTheme = () => {
            const currentTheme = getThemePreference();
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);

            // Notify Livewire components (e.g., Navbar) of theme change
            if (window.Livewire) {
                window.Livewire.dispatch('themeHasChanged', { theme: newTheme });
            }
          };

          // Set theme at page load to prevent flickering
          setTheme(getThemePreference());
        })();
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/layouts/app.blade.php ENDPATH**/ ?>