
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(__('Peralatan Pinjaman ICT Telah Dikeluarkan')); ?></title>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.6; color: #212529; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 25px 35px; border-radius: 0.375rem; border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);}
        h1 { color: #1A202C; margin-top: 0; margin-bottom: 0.75rem; font-size: 22px; }
        .footer { margin-top: 25px; font-size: 0.875em; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; font-size: 0.9em;}
        th, td { padding: 0.5rem 0.5rem; text-align: left; border-bottom: 1px solid #dee2e6;}
        th { background-color: #e9ecef; font-weight: bold; color: #495057;}
        .alert-details { margin-top: 20px; padding: 1rem; border: 1px solid transparent; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-info { color: #004085; background-color: #cfe2ff; border-color: #b6d4fe; }
        .alert-info p { margin-bottom: 0.5rem; }
        .alert-info strong { display: inline-block; min-width: 150px; }
    </style>
</head>
<body>
    <div class="email-container">
        <?php echo $__env->make('emails._partials.email-header', [
            'logoUrl' => secure_asset('assets/img/logo/motac_logo_email.png'),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <h1><?php echo e(__('Notifikasi Pengeluaran Peralatan Pinjaman ICT')); ?></h1>
        <p><?php echo e(__('Salam sejahtera')); ?> <?php echo e($loanApplication->user->name ?? __('Pemohon')); ?>,</p>
        <p><?php echo e(__('Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan')); ?>

            <strong>#<?php echo e($loanApplication->id); ?></strong>.</p>
        <p><?php echo e(__('Sukacita dimaklumkan bahawa peralatan yang diluluskan untuk permohonan anda telah')); ?>

            <strong><?php echo e(__('Dikeluarkan')); ?></strong>.</p>
        <div class="alert-details alert-info">
            <p style="margin-top:0;"><strong><?php echo e(__('Butiran Peralatan yang Dikeluarkan')); ?>:</strong></p>
            <?php if(isset($issueTransactions) && $issueTransactions->isNotEmpty()): ?>
                <table>
                    <thead>
                        <tr>
                            <th><?php echo e(__('Peralatan (Tag ID)')); ?></th>
                            <th><?php echo e(__('Aksesori Dikeluarkan')); ?></th>
                            <th><?php echo e(__('Tarikh Dikeluarkan')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $issueTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $transaction->loanTransactionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loanItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php echo e($loanItem->equipment->asset_type_name ?? 'N/A'); ?> -
                                        <?php echo e($loanItem->equipment->brand ?? 'N/A'); ?>

                                        <?php echo e($loanItem->equipment->model ?? 'N/A'); ?>

                                        (Tag: <?php echo e($loanItem->equipment->tag_id ?? 'N/A'); ?>)
                                    </td>
                                    <td>
                                        <?php
                                            $accessories = json_decode($transaction->accessories_checklist_on_issue, true) ?? [];
                                            echo !empty($accessories)
                                                ? implode(', ', array_keys(array_filter($accessories)))
                                                : '-';
                                        ?>
                                    </td>
                                    <td><?php echo e($transaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><?php echo e(__('Tiada butiran peralatan dikeluarkan direkodkan untuk permohonan ini.')); ?></p>
            <?php endif; ?>
            <p style="margin-top: 1rem; margin-bottom:0;">
                <?php echo e(__('Sila pastikan peralatan dipulangkan pada atau sebelum tarikh jangkaan pulangan:')); ?>

                <strong><?php echo e($loanApplication->loan_end_date?->translatedFormat(config('app.date_format_my', 'd/m/Y')) ?? 'N/A'); ?></strong>.
            </p>
        </div>
        <p><?php echo e(__('Jika anda mempunyai sebarang pertanyaan mengenai peralatan yang dikeluarkan, sila hubungi Bahagian Pengurusan Maklumat (BPM).')); ?></p>
        <p><?php echo e(__('Sekian, terima kasih.')); ?></p>
        <p><?php echo e(__('Yang menjalankan amanah,')); ?><br>
            <?php echo e(__('Pasukan Pentadbir Sistem')); ?><br>
            <?php echo e(__('Bahagian Pengurusan Maklumat (BPM)')); ?><br>
            <?php echo e(__('Kementerian Pelancongan, Seni dan Budaya Malaysia')); ?></p>
        <div class="footer">
            <p><?php echo e(__('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.')); ?></p>
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(__('Kementerian Pelancongan, Seni dan Budaya Malaysia')); ?>. <?php echo e(__('Hak Cipta Terpelihara.')); ?></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/emails/loan-application-issued.blade.php ENDPATH**/ ?>