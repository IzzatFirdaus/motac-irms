


<?php $__env->startPush('page-style'); ?>
    <style>
        #approvalActionBootstrapModal .modal-dialog {
            max-height: 90vh;
        }
        #approvalActionBootstrapModal .modal-body {
            overflow-y: auto;
        }
    </style>
<?php $__env->stopPush(); ?>

<div>
    <?php echo $__env->make('_partials._alerts.alert-general', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-check2-square me-2"></i>
            <?php echo e(__('approvals.table.title')); ?>

        </h4>
    </div>

    <div class="card mb-4 motac-card">
        <div class="card-body motac-card-body">
            
            <form wire:submit.prevent class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterTypeApproval" class="form-label form-label-sm"><?php echo e(__('approvals.filter.by_type')); ?></label>
                    <select wire:model.live="filterType" id="filterTypeApproval" class="form-select form-select-sm">
                        <option value="all"><?php echo e(__('common.all_types')); ?></option>
                        <option value="loan_application"><?php echo e(__('common.loan_application')); ?></option>
                        
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatusApproval" class="form-label form-label-sm"><?php echo e(__('approvals.filter.by_status')); ?></label>
                    <select wire:model.live="filterStatus" id="filterStatusApproval" class="form-select form-select-sm">
                        <option value="all"><?php echo e(__('common.all_statuses')); ?></option>
                        <option value="<?php echo e(App\Models\Approval::STATUS_PENDING); ?>"><?php echo e(__('common.pending')); ?></option>
                        <option value="<?php echo e(App\Models\Approval::STATUS_APPROVED); ?>"><?php echo e(__('common.approved')); ?></option>
                        <option value="<?php echo e(App\Models\Approval::STATUS_REJECTED); ?>"><?php echo e(__('common.rejected')); ?></option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchApprovals" class="form-label form-label-sm"><?php echo e(__('approvals.filter.advanced_search')); ?></label>
                    <input wire:model.live.debounce.300ms="searchTerm" id="searchApprovals" type="text"
                           placeholder="<?php echo e(__('approvals.filter.placeholder')); ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="$set('searchTerm', '')" class="btn btn-sm btn-outline-secondary w-100"><?php echo e(__('common.reset')); ?></button>
                </div>
            </form>
        </div>
    </div>

    
    <div wire:loading.delay.long class="w-100 text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden"><?php echo e(__('common.loading')); ?></span>
        </div>
        <p class="mt-2 fs-5"><?php echo e(__('approvals.loading_text')); ?></p>
    </div>

    
    <div wire:loading.remove>
        <!--[if BLOCK]><![endif]--><?php if($this->approvalTasks->isEmpty()): ?>
            <div class="alert alert-info d-flex align-items-center">
                 <span class="alert-icon me-2"><i class="bi bi-info-circle-fill fs-4"></i></span>
                <?php echo e(__('approvals.no_tasks')); ?>

            </div>
        <?php else: ?>
            <div class="card motac-card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.task_id')); ?></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.application_type')); ?></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.applicant')); ?></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.stage')); ?></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.status')); ?></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.date_received')); ?></th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('approvals.table.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->approvalTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approvalTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $approvable = $approvalTask->approvable; ?>
                                <tr wire:key="approval-task-<?php echo e($approvalTask->id); ?>">
                                    <td class="px-3 py-2 small"><strong>#<?php echo e($approvalTask->id); ?></strong></td>
                                    <td class="px-3 py-2 small">
                                        <!--[if BLOCK]><![endif]--><?php if($approvable instanceof \App\Models\LoanApplication): ?>
                                            <i class="bi bi-laptop text-primary me-1"></i><?php echo e(__('common.loan_application')); ?>

                                        
                                        <?php else: ?>
                                            <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i><?php echo e(__('common.unknown')); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($approvable): ?> - #<?php echo e($approvable->id); ?> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-3 py-2 small"><?php echo e(optional(optional($approvable)->user)->name ?? (optional($approvable)->applicant_name ?? __('common.not_available'))); ?></td>
                                    <td class="px-3 py-2 small"><?php echo e($approvalTask->stage_translated); ?></td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge <?php echo e($approvalTask->status_color_class); ?>"><?php echo e($approvalTask->status_translated); ?></span>
                                    </td>
                                    <td class="px-3 py-2 small"><?php echo e($approvalTask->created_at->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A'))); ?></td>
                                    <td class="text-center px-3 py-2">
                                        <button type="button" class="btn btn-sm btn-primary"
                                                wire:click="openApprovalModal(<?php echo e($approvalTask->id); ?>, '<?php echo e(\App\Models\Approval::STATUS_PENDING); ?>')">
                                            <i class="bi bi-eye me-1"></i> <?php echo e(__('common.view_action')); ?>

                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($this->approvalTasks->hasPages()): ?>
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                        <?php echo e($this->approvalTasks->links()); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div wire:ignore.self class="modal fade" id="approvalActionBootstrapModal" tabindex="-1" aria-labelledby="approvalActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="approvalActionModalLabel"><?php echo e(__('approvals.modal.title')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if($currentApprovalTask): ?>
                        <?php $modalApprovable = $currentApprovalTask->approvable; ?>
                        <h6 class="fw-semibold mb-3"><?php echo e(__('approvals.modal.details')); ?></h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 small text-muted"><?php echo e(__('approvals.modal.application_type')); ?>:</p>
                                <p class="fw-medium text-dark">
                                    <!--[if BLOCK]><![endif]--><?php if($modalApprovable instanceof \App\Models\LoanApplication): ?>
                                        <i class="bi bi-laptop text-primary me-1"></i><?php echo e(__('common.loan_application')); ?>

                                    
                                    <?php else: ?>
                                        <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i><?php echo e(__('common.unknown')); ?>

                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 small text-muted"><?php echo e(__('approvals.modal.applicant')); ?>:</p>
                                <p class="fw-medium text-dark"><?php echo e(optional(optional($modalApprovable)->user)->name ?? (optional($modalApprovable)->applicant_name ?? __('common.not_available'))); ?></p>
                            </div>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($modalApprovable instanceof \App\Models\LoanApplication): ?>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('resource-management.loan-application.loan-application-details', ['loanApplicationId' => $modalApprovable->id]);

$__html = app('livewire')->mount($__name, $__params, ['loan-app-details-'.$modalApprovable->id], $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        
                        <?php else: ?>
                            <div class="alert alert-warning small"><?php echo e(__('approvals.modal.no_details_available')); ?></div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <hr class="my-4">

                        <h6 class="fw-semibold mb-3"><?php echo e(__('approvals.modal.current_approval_stage')); ?></h6>
                        <p class="mb-1 small text-muted"><?php echo e(__('approvals.modal.stage')); ?>: <span class="fw-medium text-dark"><?php echo e($currentApprovalTask->stage_translated); ?></span></p>
                        <p class="mb-1 small text-muted"><?php echo e(__('approvals.modal.status')); ?>: <span class="badge <?php echo e($currentApprovalTask->status_color_class); ?>"><?php echo e($currentApprovalTask->status_translated); ?></span></p>
                        <p class="mb-3 small text-muted"><?php echo e(__('approvals.modal.received_on')); ?>: <span class="fw-medium text-dark"><?php echo e($currentApprovalTask->created_at->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A'))); ?></span></p>

                        <!--[if BLOCK]><![endif]--><?php if($currentApprovalTask->status === App\Models\Approval::STATUS_PENDING): ?>
                            <div class="mb-3">
                                <label for="approvalNotes" class="form-label form-label-sm"><?php echo e(__('approvals.modal.notes')); ?> <span class="text-muted">(<?php echo e(__('common.optional')); ?>)</span></label>
                                <textarea wire:model="approvalNotes" id="approvalNotes" class="form-control form-control-sm" rows="3" placeholder="<?php echo e(__('approvals.modal.notes_placeholder')); ?>"></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" wire:click="processApproval" class="btn btn-success d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="processApproval">
                                    <span wire:loading wire:target="processApproval" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i wire:loading.remove wire:target="processApproval" class="bi bi-check-circle me-1"></i>
                                    <?php echo e(__('common.approve')); ?>

                                </button>
                                <button type="button" wire:click="processApproval" class="btn btn-danger d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="processApproval">
                                    <span wire:loading wire:target="processApproval" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i wire:loading.remove wire:target="processApproval" class="bi bi-x-circle me-1"></i>
                                    <?php echo e(__('common.reject')); ?>

                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info small"><?php echo e(__('approvals.modal.task_already_processed')); ?></div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php else: ?>
                        <div class="text-center py-5">
                            <p class="text-muted"><?php echo e(__('approvals.modal.select_task_to_view')); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/resource-management/approval/approval-dashboard.blade.php ENDPATH**/ ?>