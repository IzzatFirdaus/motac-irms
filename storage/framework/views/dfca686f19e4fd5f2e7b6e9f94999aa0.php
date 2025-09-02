
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'headerClass' => 'bg-light py-3',
    'titleTag' => 'h5',
    'titleClass' => 'card-title mb-0 fw-semibold',
    'bodyClass' => '',
    'footer' => null,
    'footerClass' => 'bg-light py-3 text-end',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => null,
    'headerClass' => 'bg-light py-3',
    'titleTag' => 'h5',
    'titleClass' => 'card-title mb-0 fw-semibold',
    'bodyClass' => '',
    'footer' => null,
    'footerClass' => 'bg-light py-3 text-end',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'card shadow-sm mb-3'])); ?>>
    
    <!--[if BLOCK]><![endif]--><?php if($title || $attributes->has('header')): ?>
        <div class="card-header <?php echo e($headerClass); ?>">
            <!--[if BLOCK]><![endif]--><?php if($title): ?>
                <<?php echo e($titleTag); ?> class="<?php echo e($titleClass); ?>"><?php echo e($title); ?></<?php echo e($titleTag); ?>>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php echo e($attributes->get('header') ?? ''); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="card-body <?php echo e($bodyClass); ?>">
        <?php echo e($slot); ?>

    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($footer): ?>
        <div class="card-footer <?php echo e($footerClass); ?>">
            <?php echo e($footer); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/components/card.blade.php ENDPATH**/ ?>