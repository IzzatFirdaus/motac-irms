


<div class="container-fluid">
    <div class="motac-card shadow-sm mb-4">
        <div class="motac-card-header d-flex align-items-center py-3">
            <i class="bi bi-bell-fill me-2 text-primary" aria-hidden="true"></i>
            <h5 class="mb-0 fw-medium text-dark"><?php echo e(__('Senarai Notifikasi')); ?></h5>
        </div>
        <div class="motac-card-body">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="notificationSearch" class="form-label small fw-medium"><?php echo e(__('Carian Notifikasi')); ?></label>
                    <input
                        type="search"
                        id="notificationSearch"
                        wire:model.live.debounce.500ms="search"
                        class="form-control"
                        placeholder="<?php echo e(__('Cari mesej notifikasi...')); ?>"
                    >
                </div>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo e(__('Tutup')); ?>"></button>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('Tarikh')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('Mesej')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center"><?php echo e(__('Status')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-end"><?php echo e(__('Tindakan')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr <?php if(is_null($notification->read_at)): ?> class="table-info" <?php endif; ?>>
                                <td class="px-3 py-2 small">
                                    <?php echo e($notification->created_at->format('d/m/Y H:i')); ?>

                                </td>
                                <td class="px-3 py-2 small">
                                    
                                    <?php echo e($notification->data['message'] ?? $notification->data['title'] ?? __('Notifikasi baharu')); ?>

                                </td>
                                <td class="px-3 py-2 text-center">
                                    <!--[if BLOCK]><![endif]--><?php if(is_null($notification->read_at)): ?>
                                        <span class="motac-badge motac-badge-warning rounded-pill" role="status" aria-label="<?php echo e(__('Belum Dibaca')); ?>"><?php echo e(__('Belum Dibaca')); ?></span>
                                    <?php else: ?>
                                        <span class="motac-badge motac-badge-success rounded-pill" role="status" aria-label="<?php echo e(__('Dibaca')); ?>"><?php echo e(__('Dibaca')); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-3 py-2 text-end">
                                    <!--[if BLOCK]><![endif]--><?php if(is_null($notification->read_at)): ?>
                                        <button wire:click="markAsRead('<?php echo e($notification->id); ?>')" class="motac-btn-outline btn-sm" title="<?php echo e(__('Tanda sebagai dibaca')); ?>">
                                            <i class="bi bi-check2-square" aria-hidden="true"></i> <?php echo e(__('Dibaca')); ?>

                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small"><?php echo e(__('Tiada tindakan')); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-bell-slash display-5 mb-2"></i><br>
                                    <?php echo e(__('Tiada notifikasi dijumpai.')); ?>

                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            
            <div class="mt-3 d-flex justify-content-center">
                <?php echo e($notifications->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/shared/notifications/notifications-list.blade.php ENDPATH**/ ?>