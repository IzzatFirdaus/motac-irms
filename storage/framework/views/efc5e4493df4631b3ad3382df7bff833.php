<?php $__env->startSection('title', __('Tindakan Kelulusan Diperlukan')); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $approvalTask = $approvalTask ?? $notification->approvalTask;
        $application = $application ?? $approvalTask->approvable;
        $approver = $approver ?? $notification->approver ?? $notifiable;
        $itemTypeDisplayName = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId = $application->id ?? 'N/A';
        $stageName = \App\Models\Approval::getStageDisplayName($approvalTask->stage);
        $applicantName = $application->user?->name ?? __('Pemohon Tidak Dikenali');
        $reviewUrl = $reviewUrl ?? route('approvals.show', $approvalTask->id);
    ?>

    <h4 class="mb-3"><?php echo e(__('Salam :name,', ['name' => $approver->name])); ?></h4>

    <p>
        <?php echo e(__('Satu permohonan memerlukan perhatian dan tindakan anda untuk peringkat kelulusan ":stage".', [
            'stage' => $stageName
        ])); ?>

    </p>

    <div class="card mt-4">
        <div class="card-header">
            <?php echo e(__('Butiran Permohonan')); ?>

        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong><?php echo e(__('Jenis Permohonan')); ?>:</strong> <?php echo e($itemTypeDisplayName); ?></li>
            <li class="list-group-item"><strong><?php echo e(__('ID Permohonan')); ?>:</strong> #<?php echo e($applicationId); ?></li>
            <li class="list-group-item"><strong><?php echo e(__('Pemohon')); ?>:</strong> <?php echo e($applicantName); ?></li>
            <?php if($application->purpose): ?>
                <li class="list-group-item"><strong><?php echo e(__('Tujuan')); ?>:</strong> <?php echo e($application->purpose); ?></li>
            <?php endif; ?>
        </ul>
    </div>

    <p class="mt-4"><?php echo e(__('Sila log masuk ke sistem untuk menyemak butiran permohonan dan mengambil tindakan selanjutnya.')); ?></p>

    <?php if($reviewUrl && $reviewUrl !== '#'): ?>
        <div class="text-center mt-4">
            <a href="<?php echo e($reviewUrl); ?>" class="motac-btn-primary d-inline-block"><?php echo e(__('Lihat Tugasan Kelulusan')); ?></a>
        </div>
    <?php endif; ?>

    <p class="mt-4"><?php echo e(__('Sekian, terima kasih.')); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.email', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\motac-irms\resources\views/emails/application-needs-action.blade.php ENDPATH**/ ?>