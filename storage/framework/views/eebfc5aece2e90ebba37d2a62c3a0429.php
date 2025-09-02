

<aside id="layout-menu" class="myds-vertical-menu" aria-label="Navigasi Sistem">
    <div class="myds-sidebar-header">
        <span class="myds-sidebar-logo">
            <img src="<?php echo e(asset($configData['appLogo'] ?? 'assets/img/logo/motac-logo.svg')); ?>"
                alt="<?php echo e(__('Logo Aplikasi')); ?>" height="32">
        </span>
        <span class="myds-sidebar-title heading-small fw-semibold"><?php echo e(__($configData['templateName'] ?? 'MOTAC IRMS')); ?></span>
        
    </div>

    <?php
        $menuObj = isset($filteredMenu)
            ? (is_array($filteredMenu) ? (object) $filteredMenu : $filteredMenu)
            : (object) ['menu' => []];
        $menuItems = (isset($menuObj->menu) && is_array($menuObj->menu)) ? $menuObj->menu : [];
    ?>
    <ul class="myds-sidebar-menu">
        
        <!--[if BLOCK]><![endif]--><?php if(count($menuItems)): ?>
            
            <?php echo $__env->make('layouts.sections.menu.submenu-partial', [
                'menuItems' => $menuItems,
                'configData' => $configData,
                'currentRouteName' => $currentRouteName,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            
            <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->guest()): ?>
            <li class="myds-menu-item">
                <a href="<?php echo e(route('login')); ?>" class="myds-menu-link">
                    <i class="myds-menu-icon bi bi-box-arrow-in-right"></i>
                    <div class="myds-menu-label heading-xsmall"><?php echo e(__('Sila log masuk untuk akses sistem dalaman')); ?></div>
                </a>
            </li>
            <?php else: ?>
            <li class="myds-menu-item">
                <a href="javascript:void(0);" class="myds-menu-link">
                    <i class="myds-menu-icon bi bi-alert-circle"></i>
                    <div class="myds-menu-label heading-xsmall"><?php echo e(__('Tiada data menu tersedia.')); ?></div>
                </a>
            </li>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </ul>
    
</aside>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/sections/menu/vertical-menu.blade.php ENDPATH**/ ?>