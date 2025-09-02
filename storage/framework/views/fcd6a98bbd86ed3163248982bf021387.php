<div>
    <?php $__env->startSection('title', __('Permohonan Pinjaman Untuk Diproses')); ?>

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-card-checklist me-2"></i>
            <?php echo e(__('Permohonan Sedia Untuk Pengeluaran')); ?>

        </h4>
    </div>

    <div class="card motac-card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="searchTerm" class="form-label"><?php echo e(__('Carian')); ?></label>
                    <input type="text" id="searchTerm" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="<?php echo e(__('Cari ID, Tujuan, atau Pemohon...')); ?>">
                </div>
            </div>
        </div>

        
        <div wire:loading.delay.long class="w-100 text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden"><?php echo e(__('Memuatkan...')); ?></span>
            </div>
        </div>

        <div wire:loading.remove>
            <!--[if BLOCK]><![endif]--><?php if($this->outstandingApplications->isEmpty()): ?>
                <div class="text-center p-5">
                    <i class="bi bi-info-circle-fill fs-1 text-info"></i>
                    <p class="mt-3"><?php echo e(__('Tiada permohonan yang menunggu tindakan anda pada masa ini.')); ?></p>
                </div>
            <?php else: ?>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('id')" style="cursor: pointer;" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    <?php echo app('translator')->get('Permohonan #'); ?>
                                    <!--[if BLOCK]><![endif]--><?php if($sortBy === 'id'): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    <?php echo app('translator')->get('Pemohon'); ?>
                                </th>
                                <th wire:click="sortBy('purpose')" style="cursor: pointer;" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    <?php echo app('translator')->get('Tujuan'); ?>
                                    <!--[if BLOCK]><![endif]--><?php if($sortBy === 'purpose'): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </th>
                                <th wire:click="sortBy('updated_at')" style="cursor: pointer;" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    <?php echo app('translator')->get('Tarikh Diluluskan'); ?>
                                    <!--[if BLOCK]><![endif]--><?php if($sortBy === 'updated_at'): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo app('translator')->get('Item Diluluskan'); ?></th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2"><?php echo app('translator')->get('Tindakan'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->outstandingApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr wire:key="app-<?php echo e($application->id); ?>">
                                    <td class="px-3 py-2 small fw-medium">
                                        <a href="<?php echo e(route('loan-applications.show', $application->id)); ?>">#<?php echo e($application->id); ?></a>
                                    </td>
                                    <td class="px-3 py-2 small"><?php echo e($application->user->name); ?></td>
                                    <td class="px-3 py-2 small"><?php echo e(Str::limit($application->purpose, 50)); ?></td>
                                    <td class="px-3 py-2 small"><?php echo e($application->updated_at->translatedFormat('d M Y, g:i A')); ?></td>
                                    <td class="px-3 py-2 small">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $application->loanApplicationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><?php echo e(\App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? $item->equipment_type); ?> (<?php echo e(__('Qty')); ?>: <?php echo e($item->quantity_approved); ?>)</div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="text-center px-3 py-2">
                                        <a href="<?php echo e(route('loan-applications.issue.form', ['loanApplication' => $application->id])); ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-box-arrow-in-up-right me-1"></i> <?php echo app('translator')->get('Proses Pengeluaran'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($this->outstandingApplications->hasPages()): ?>
                    <div class="card-footer d-flex justify-content-between">
                        <div class="small text-muted">
                            <?php echo app('translator')->get('Showing'); ?>
                            <strong><?php echo e($this->outstandingApplications->firstItem()); ?></strong>
                            <?php echo app('translator')->get('to'); ?>
                            <strong><?php echo e($this->outstandingApplications->lastItem()); ?></strong>
                            <?php echo app('translator')->get('of'); ?>
                            <strong><?php echo e($this->outstandingApplications->total()); ?></strong>
                            <?php echo app('translator')->get('results'); ?>
                        </div>
                        <?php echo e($this->outstandingApplications->links()); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/resource-management/admin/bpm/outstanding-loans.blade.php ENDPATH**/ ?>