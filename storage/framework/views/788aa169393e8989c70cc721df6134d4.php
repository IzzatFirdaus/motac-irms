
<div>
    <div class="card-body border-bottom">
        <div class="row">
            <div class="col-md-4">
                <label for="searchIssued" class="form-label"><?php echo e(__('Carian')); ?></label>
                <input type="text" id="searchIssued" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="<?php echo e(__('Cari ID, Pemohon, Tag ID...')); ?>">
            </div>
        </div>
    </div>

    <div wire:loading.remove>
        <!--[if BLOCK]><![endif]--><?php if($this->issuedLoans->isEmpty()): ?>
            <div class="text-center p-5">
                <i class="bi bi-info-circle-fill fs-1 text-info"></i>
                <p class="mt-3">
                    <!--[if BLOCK]><![endif]--><?php if(empty($searchTerm)): ?>
                        <?php echo e(__('Tiada peralatan yang sedang dipinjam pada masa ini.')); ?>

                    <?php else: ?>
                        <?php echo e(__('Tiada rekod ditemui untuk carian anda.')); ?>

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('ID Permohonan')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('Pemohon')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" style="min-width: 280px;"><?php echo e(__('Peralatan Dikeluarkan')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('Tarikh Keluar')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('Tarikh Jangka Pulang')); ?></th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2"><?php echo e(__('Status')); ?></th>
                            <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2"><span><?php echo e(__('Tindakan')); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->issuedLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loanApplication): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr wire:key="issued-loan-app-<?php echo e($loanApplication->id); ?>">
                                <td class="px-3 py-2 align-middle small text-dark fw-medium">#<?php echo e($loanApplication->id); ?></td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    <span class="fw-medium text-dark"><?php echo e($loanApplication->user?->name ?? __('Tidak Diketahui')); ?></span>
                                    <span class="d-block" style="font-size: 0.75rem;"><?php echo e($loanApplication->user?->department?->name ?? ''); ?></span>
                                </td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    <ul class="list-unstyled ps-2 mb-0" style="font-size: 0.8rem;">
                                        <?php $__currentLoopData = $loanApplication->loanApplicationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <!--[if BLOCK]><![endif]--><?php if($item->quantity_issued > 0): ?>
                                                <li>
                                                    <i class="bi bi-chevron-right text-secondary me-1" style="font-size: 0.7rem;"></i>
                                                    <?php echo e($item->equipment_type_name); ?> (<?php echo e(__('Qty:')); ?> <?php echo e($item->quantity_issued); ?>)
                                                    <ul class="list-unstyled ps-3 text-body-secondary" style="font-size: 0.75rem;">
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item->loanTransactionItems->filter(fn($ti) => $ti->loanTransaction?->type === \App\Models\LoanTransaction::TYPE_ISSUE); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <!--[if BLOCK]><![endif]--><?php if($transactionItem->equipment): ?>
                                                                <li>
                                                                    <i class="bi bi-arrow-right-short text-info me-1" style="font-size: 0.8rem;"></i>
                                                                    <?php echo e($transactionItem->equipment->tag_id ?? 'N/A'); ?> - <?php echo e($transactionItem->equipment->brand ?? 'N/A'); ?> <?php echo e($transactionItem->equipment->model ?? ''); ?>

                                                                </li>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                    </ul>
                                                </li>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                </td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    <?php echo e($loanApplication->loanTransactions->first()?->transaction_date?->translatedFormat('d M Y') ?? __('N/A')); ?>

                                </td>
                                <td class="px-3 py-2 align-middle small text-muted">
                                    <?php echo e($loanApplication->loan_end_date?->translatedFormat('d M Y') ?? __('N/A')); ?>

                                    <!--[if BLOCK]><![endif]--><?php if($loanApplication->isOverdue()): ?>
                                        <span class="d-block small fw-bold text-danger mt-1"><?php echo e(__('TERTUNGGAK')); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-3 py-2 align-middle small">
                                    <?php if (isset($component)) { $__componentOriginal5f0d1be97abd0782dc99c19402ad40fb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f0d1be97abd0782dc99c19402ad40fb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.resource-status-panel','data' => ['resource' => $loanApplication,'statusAttribute' => 'status']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('resource-status-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['resource' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loanApplication),'statusAttribute' => 'status']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5f0d1be97abd0782dc99c19402ad40fb)): ?>
<?php $attributes = $__attributesOriginal5f0d1be97abd0782dc99c19402ad40fb; ?>
<?php unset($__attributesOriginal5f0d1be97abd0782dc99c19402ad40fb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f0d1be97abd0782dc99c19402ad40fb)): ?>
<?php $component = $__componentOriginal5f0d1be97abd0782dc99c19402ad40fb; ?>
<?php unset($__componentOriginal5f0d1be97abd0782dc99c19402ad40fb); ?>
<?php endif; ?>
                                </td>
                                <td class="px-3 py-2 align-middle text-end">
                                    
                                    <?php
                                        $latestIssueTransaction = $loanApplication->loanTransactions->first();
                                        if ($latestIssueTransaction) {
                                            $latestIssueTransaction->setRelation('loanApplication', $loanApplication);
                                        }
                                    ?>
                                    <!--[if BLOCK]><![endif]--><?php if($latestIssueTransaction): ?>
                                        <a href="<?php echo e(route('loan-transactions.show', $latestIssueTransaction->id)); ?>" class="btn btn-sm btn-outline-info border-0 p-1" title="<?php echo e(__('Lihat Detail Transaksi Keluar')); ?>"><i class="bi bi-file-earmark-text fs-6 lh-1"></i></a>
                                        <!--[if BLOCK]><![endif]--><?php if(!$loanApplication->isClosed()): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('processReturn', $latestIssueTransaction)): ?>
                                                <a href="<?php echo e(route('loan-applications.return', ['loanTransaction' => $latestIssueTransaction->id])); ?>" class="btn btn-sm btn-outline-success border-0 p-1 ms-1" title="<?php echo e(__('Proses Pemulangan')); ?>"><i class="bi bi-arrow-return-left fs-6 lh-1"></i></a>
                                            <?php endif; ?>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php else: ?>
                                        <a href="<?php echo e(route('loan-applications.show', $loanApplication->id)); ?>" class="btn btn-sm btn-outline-primary border-0 p-1" title="<?php echo e(__('Lihat Detail Permohonan')); ?>"><i class="bi bi-eye fs-6 lh-1"></i></a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
            <!--[if BLOCK]><![endif]--><?php if($this->issuedLoans->hasPages()): ?>
                <div class="card-footer bg-light border-top d-flex justify-content-center py-2"><?php echo e($this->issuedLoans->links()); ?></div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/resource-management/admin/bpm/issued-loans.blade.php ENDPATH**/ ?>