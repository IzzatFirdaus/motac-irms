

<!--[if BLOCK]><![endif]--><?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $menu = (object) $menu;
        $hasSubmenu = isset($menu->submenu) && is_array($menu->submenu) && count($menu->submenu) > 0;
        $isActive = false;

        // Mark item active if current route matches, or prefix matches
        if (isset($menu->routeName) && $currentRouteName === $menu->routeName) {
            $isActive = true;
        } elseif (isset($menu->routeNamePrefix)) {
            foreach (explode(',', $menu->routeNamePrefix) as $prefix) {
                if (str_starts_with($currentRouteName, trim($prefix))) {
                    $isActive = true;
                    break;
                }
            }
        }

        // Determine the menu link (href)
        $menuHref = $menu->url ?? (isset($menu->routeName) && Route::has($menu->routeName) ? route($menu->routeName) : 'javascript:void(0);');
        if ($hasSubmenu) {
            $menuHref = '#';
        }
    ?>

    
    <!--[if BLOCK]><![endif]--><?php if(isset($menu->menuHeader)): ?>
        <li class="menu-header small text-uppercase text-muted fw-bold">
            <span class="menu-header-text"><?php echo e(__($menu->menuHeader)); ?></span>
        </li>
    <?php else: ?>
        
        <li class="menu-item<?php echo e($isActive ? ' active' : ''); ?><?php echo e($hasSubmenu ? ' has-submenu' : ''); ?>">
            <a href="<?php echo e($menuHref); ?>"
                class="menu-link<?php echo e($hasSubmenu ? ' menu-toggle' : ''); ?>"
                <?php if($hasSubmenu): ?> aria-haspopup="true" aria-expanded="<?php echo e($isActive ? 'true' : 'false'); ?>" tabindex="0" <?php endif; ?>>
                <!--[if BLOCK]><![endif]--><?php if(isset($menu->icon)): ?>
                    <i class="menu-icon bi bi-<?php echo e($menu->icon); ?>" aria-hidden="true"></i>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <div><?php echo e(__($menu->name)); ?></div>
                <!--[if BLOCK]><![endif]--><?php if($hasSubmenu): ?>
                    <span class="menu-arrow bi bi-chevron-right" aria-hidden="true"></span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </a>
            
            <!--[if BLOCK]><![endif]--><?php if($hasSubmenu): ?>
                <ul class="menu-sub">
                    <?php echo $__env->make('layouts.sections.menu.submenu-partial', [
                        'menuItems' => $menu->submenu,
                        'configData' => $configData,
                        'currentRouteName' => $currentRouteName,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </ul>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </li>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH C:\laragon\www\motac-irms\resources\views/layouts/sections/menu/submenu-partial.blade.php ENDPATH**/ ?>