<?php $__env->startSection('title', __('Peralatan Pinjaman Telah Dipulangkan')); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $loanApplication = $loanApplication ?? null;
        $loanTransaction = $returnTransaction ?? null;
        $actionUrl = $actionUrl ?? route('loan-applications.show', $loanApplication->id ?? 0);
    ?>

    <h4 class="mb-3"><?php echo e(__('Salam sejahtera, :name,', ['name' => $loanApplication->user->name ?? 'Pemohon'])); ?></h4>

    <p><?php echo e(__('Merujuk kepada permohonan Pinjaman Peralatan ICT anda #:id, sukacita dimaklumkan bahawa peralatan berikut telah berjaya dipulangkan dan direkodkan dalam sistem.', [
        'id' => $loanApplication->id ?? 'N/A'
    ])); ?></p>

    <div class="card mt-4">
        <div class="card-header">
            <?php echo e(__('Butiran Pulangan Peralatan')); ?>

        </div>
        <?php if($loanTransaction && $loanTransaction->loanTransactionItems->isNotEmpty()): ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?php echo e(__('Peralatan (Tag ID)')); ?></th>
                            <th><?php echo e(__('Keadaan Semasa Pulangan')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $loanTransaction->loanTransactionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e($item->equipment->asset_type_label ?? 'N/A'); ?>

                                    (<?php echo e($item->equipment->tag_id ?? 'N/A'); ?>)
                                </td>
                                <td>
                                    <?php echo e($item->condition_on_return_translated ?? 'N/A'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                <p class="mb-2"><strong><?php echo e(__('Diterima Oleh')); ?>:</strong>
                    <?php echo e($loanTransaction->returnAcceptingOfficer->name ?? 'N/A'); ?></p>
                <p class="mb-2"><strong><?php echo e(__('Tarikh Dipulangkan')); ?>:</strong>
                    <?php echo e($loanTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A'); ?>

                </p>
                <?php if($loanTransaction->return_notes): ?>
                    <p class="mb-0"><strong><?php echo e(__('Catatan Pulangan')); ?>:</strong> <?php echo e($loanTransaction->return_notes); ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="card-body">
                <p><?php echo e(__('Tiada butiran item pulangan ditemui untuk transaksi ini.')); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($actionUrl && $actionUrl !== '#'): ?>
        <div class="text-center mt-4">
            <a href="<?php echo e($actionUrl); ?>" class="motac-btn-primary d-inline-block"><?php echo e(__('Lihat Status Permohonan')); ?></a>
        </div>
    <?php endif; ?>

    <p class="mt-4"><?php echo e(__('Sekian, terima kasih.')); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.email', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/emails/equipment-returned.blade.php ENDPATH**/ ?>