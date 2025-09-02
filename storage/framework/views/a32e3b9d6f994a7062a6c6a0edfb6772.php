

<?php
    $alertMessage = null;
    $alertLevel = 'info'; // Default level
    $alertTitle = null;

    // Determine alert details from session flash messages
    if (session()->has('success')) {
        $alertMessage = session('success');
        $alertLevel = 'success';
        // $alertTitle = __('Berjaya!');
    } elseif (session()->has('error')) {
        $alertMessage = session('error');
        $alertLevel = 'danger';
        // $alertTitle = __('Ralat!');
    } elseif (session()->has('warning')) {
        $alertMessage = session('warning');
        $alertLevel = 'warning';
        // $alertTitle = __('Amaran!');
    } elseif (session()->has('info')) {
        $alertMessage = session('info');
        $alertLevel = 'info';
        // $alertTitle = __('Makluman');
    } elseif (session()->has('message')) {
        $sessionMessage = session('message');
        if (is_array($sessionMessage) && isset($sessionMessage['content'])) {
            $alertMessage = $sessionMessage['content'];
            $alertLevel = $sessionMessage['level'] ?? 'info';
            $alertTitle = $sessionMessage['title'] ?? null;
        } elseif (is_array($sessionMessage) && isset($sessionMessage['notifications']['generic_error'])) {
            $alertMessage = $sessionMessage['notifications']['generic_error'];
        } else {
            // Fallback: Convert the array to a JSON string for debugging.
            $alertMessage = 'An unexpected message format was received: ' . json_encode($sessionMessage);
            // Log::warning('Alert message received as array', ['message_content' => $sessionMessage]);
        }
    }
?>


<!--[if BLOCK]><![endif]--><?php if($alertMessage): ?>
    <x-alert :type="$alertLevel"
             <?php if($alertTitle): ?> title="<?php echo e($alertTitle); ?>" <?php endif; ?>
             :message="__($alertMessage)"
             dismissible="true" />
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->


<!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
    <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'danger','title' => __('Amaran! Sila semak ralat input berikut:'),'dismissible' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Amaran! Sila semak ralat input berikut:')),'dismissible' => 'true']); ?>
        <ul class="list-unstyled mb-0 small ps-0">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </ul>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH C:\laragon\www\motac-irms\resources\views/_partials/_alerts/alert-general.blade.php ENDPATH**/ ?>