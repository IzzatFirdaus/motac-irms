


<div class="myds-nav-item myds-dropdown-notifications myds-navbar-dropdown dropdown me-3 me-xl-1">
    <a class="myds-nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
        data-bs-auto-close="outside" aria-expanded="false" aria-label="<?php echo e(__('Notifications')); ?>">
        <i class="bi bi-bell-fill fs-4"></i>
        <!--[if BLOCK]><![endif]--><?php if($unreadCount > 0): ?>
            <span class="myds-badge bg-danger rounded-pill myds-badge-notifications"><?php echo e($unreadCount); ?></span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </a>
    <ul class="myds-dropdown-menu dropdown-menu-end py-0">
        <li class="myds-dropdown-menu-header border-bottom">
            <div class="myds-dropdown-header d-flex align-items-center py-3">
                <h5 class="heading-xsmall mb-0 me-auto"><?php echo e(__('Notifications')); ?></h5>
                <!--[if BLOCK]><![endif]--><?php if($unreadCount > 0): ?>
                    <a href="javascript:void(0)" wire:click="markAllAsRead" class="myds-dropdown-notifications-all text-body"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="<?php echo e(__('Mark all as read')); ?>"><i class="bi bi-envelope-open-fill"></i></a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </li>
        <li class="myds-dropdown-notifications-list scrollable-container">
            <ul class="myds-list-group list-group-flush">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="myds-list-group-item list-group-item-action myds-dropdown-notifications-item"
                        wire:click="markAsRead('<?php echo e($notification->id); ?>')" style="cursor: pointer;">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="myds-avatar">
                                    <span class="myds-avatar-initial rounded-circle bg-label-info"><i
                                            class="<?php echo e($notification->data['icon'] ?? 'bi bi-info-circle-fill'); ?>"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="heading-xsmall mb-1"><?php echo e($notification->data['subject'] ?? __('New Notification')); ?></h6>
                                <p class="mb-0"><?php echo e($notification->data['message'] ?? __('No message content.')); ?></p>
                                <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                            </div>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="myds-list-group-item">
                        <div class="d-flex justify-content-center align-items-center">
                            <p class="mb-0 py-4 text-muted"><?php echo e(__('No new notifications.')); ?></p>
                        </div>
                    </li>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </ul>
        </li>
        <li class="myds-dropdown-menu-footer border-top">
            
            <a href="<?php echo e(route('notifications.index')); ?>" class="myds-dropdown-item d-flex justify-content-center p-3">
                <?php echo e(__('View All Notifications')); ?>

            </a>
        </li>
    </ul>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/sections/navbar/notifications-dropdown.blade.php ENDPATH**/ ?>