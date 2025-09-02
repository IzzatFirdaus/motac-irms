 

<?php $__env->startSection('title', __('dashboard.user_title')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark fw-bold">
                <?php echo e(__('dashboard.welcome_user', ['userName' => Auth::user()?->name ?? __('User')])); ?>

            </h1>
        </div>

        <div class="row">
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="motac-card shadow-sm h-100">
                    <div class="motac-card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-headset fs-1 text-info"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2"><?php echo e(__('dashboard.create_helpdesk_ticket_title')); ?></h5>
                        <p class="card-text small text-muted mb-3"><?php echo e(__('dashboard.create_helpdesk_ticket_text')); ?></p>
                        <div class="mt-auto w-100">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\HelpdeskTicket::class)): ?>
                                <a href="<?php echo e(route('helpdesk.create')); ?>"
                                   class="motac-btn-primary mt-auto btn-sm w-100"><?php echo e(__('dashboard.create_new_ticket')); ?></a>
                            <?php else: ?>
                                <button type="button" class="motac-btn-outline mt-auto btn-sm w-100" disabled>
                                    <?php echo e(__('dashboard.no_permission')); ?>

                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="motac-card shadow-sm h-100">
                    <div class="motac-card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-ticket-detailed-fill fs-1 text-primary"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2"><?php echo e(__('dashboard.view_my_tickets_title')); ?></h5>
                        <p class="card-text small text-muted mb-3"><?php echo e(__('dashboard.view_my_tickets_text')); ?></p>
                        <div class="mt-auto w-100">
                                     <a href="<?php echo e(route('helpdesk.index')); ?>"
                                         class="motac-btn-primary mt-auto btn-sm w-100"><?php echo e(__('dashboard.view_all_my_tickets')); ?></a>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="motac-card shadow-sm h-100">
                    <div class="motac-card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-handbag-fill fs-1 text-success"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2"><?php echo e(__('dashboard.apply_ict_loan_title')); ?></h5>
                        <p class="card-text small text-muted mb-3"><?php echo e(__('dashboard.apply_ict_loan_text')); ?></p>
                        <div class="mt-auto w-100">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\LoanApplication::class)): ?>
                                <a href="<?php echo e(route('loan-applications.create')); ?>"
                                   class="motac-btn-primary mt-auto btn-sm w-100"><?php echo e(__('dashboard.apply_new_loan')); ?></a>
                            <?php else: ?>
                                <button type="button" class="motac-btn-outline mt-auto btn-sm w-100" disabled>
                                    <?php echo e(__('dashboard.no_permission')); ?>

                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="motac-card shadow-sm h-100">
                    <div class="motac-card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-journal-check fs-1 text-warning"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2"><?php echo e(__('dashboard.view_my_loan_applications_title')); ?></h5>
                        <p class="card-text small text-muted mb-3"><?php echo e(__('dashboard.view_my_loan_applications_text')); ?></p>
                        <div class="mt-auto w-100">
                                     <a href="<?php echo e(route('loan-applications.index')); ?>"
                                         class="motac-btn-primary mt-auto btn-sm w-100"><?php echo e(__('dashboard.view_my_loan_applications')); ?></a>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="motac-card shadow-sm h-100">
                    <div class="motac-card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                        <div class="mb-3"><i class="bi bi-bell-fill fs-1 text-warning"></i></div>
                        <h5 class="card-title h6 fw-semibold mb-2"><?php echo e(__('dashboard.notifications_title')); ?></h5>
                        <p class="card-text small text-muted mb-3"><?php echo e(__('dashboard.notifications_text')); ?></p>
                        <a href="<?php echo e(route('notifications.index')); ?>"
                            class="motac-btn-primary mt-auto btn-sm w-100"><?php echo e(__('dashboard.view_all_notifications')); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="motac-card shadow-sm">
                    <div class="motac-card-header py-3 d-flex align-items-center"><i class="bi bi-journals me-2 text-primary"></i>
                        <h6 class="card-title mb-0 fw-bold"><?php echo e(__('dashboard.your_recent_activity_summary')); ?></h6>
                    </div>
                    <div class="motac-card-body">
                        <p class="text-center text-muted small py-4 my-4"><i
                                class="bi bi-info-circle-fill fs-2 d-block mb-2"></i><?php echo e(__('dashboard.your_recent_activity_text')); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/dashboard/user-dashboard.blade.php ENDPATH**/ ?>