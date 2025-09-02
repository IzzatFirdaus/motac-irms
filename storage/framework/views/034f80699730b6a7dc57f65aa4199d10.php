

<!--
  IMPORTANT: Livewire components must have ONE root HTML element.
  This <nav> is the root for this component.
-->
<nav class="myds-navbar-alt <?php echo e($containerNav); ?> <?php echo e($navbarDetachedClass); ?>" id="layout-navbar" aria-label="<?php echo app('translator')->get('common.main_title'); ?>">
    <div class="myds-navbar-left">
        
        <button class="myds-navbar-hamburger" aria-label="<?php echo app('translator')->get('common.toggle_sidebar'); ?>" title="<?php echo app('translator')->get('common.toggle_sidebar'); ?>">
            <i class="bi bi-list"></i>
        </button>
        
        <a href="<?php echo e(url('/')); ?>" class="myds-navbar-brand text-decoration-none">
            <span class="myds-navbar-logo">
                <img src="<?php echo e(asset('assets/img/logo/motac-logo.svg')); ?>" alt="Logo MOTAC">
            </span>
            <span class="heading-xsmall">motac-irms</span>
            <span class="myds-navbar-ministry d-none d-lg-inline">
                <?php echo e(__('Kementerian Pelancongan, Seni dan Budaya Malaysia')); ?>

            </span>
        </a>
    </div>
    
    <div class="myds-navbar-links d-none d-lg-flex">
        
        <a href="<?php echo e(route('loan-applications.my-applications.index')); ?>" class="myds-navbar-link">
            <?php echo e(__('Permohonan Pinjaman Saya')); ?>

        </a>
        
        <?php
            $canSeeReports = false;
            if (Auth::check()) {
                $user = Auth::user();
                $canSeeReports = $user->hasRole('Admin') || $user->hasRole('BPM Staff') || $user->hasRole('IT Admin');
            }
        ?>
        <!--[if BLOCK]><![endif]--><?php if($canSeeReports): ?>
            <a href="<?php echo e(route('reports.index')); ?>" class="myds-navbar-link">
                <?php echo e(__('Laporan')); ?>

            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    <div class="myds-navbar-right">
        
        <!--[if BLOCK]><![endif]--><?php if(count($availableLocales) > 1): ?>
            <div class="myds-navbar-action" tabindex="0">
                <a href="#" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo app('translator')->get('common.language_selector'); ?>" title="<?php echo app('translator')->get('common.language_selector'); ?>">
                    <span class="bi bi-translate"></span>
                </a>
                <div class="myds-navbar-dropdown">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableLocales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeKey => $localeData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('language.swap', ['lang' => $localeKey])); ?>"
                           rel="nofollow"
                           hreflang="<?php echo e($localeKey); ?>"
                           class="d-flex align-items-center"
                           <?php if(app()->getLocale() === $localeKey): ?> aria-current="true" <?php endif; ?>>
                            <span class="flag-icon flag-icon-<?php echo e($localeData['flag_code']); ?>"></span>
                            <?php echo e($localeKey === 'ms' ? 'Bahasa Melayu' : 'English'); ?>

                            <!--[if BLOCK]><![endif]--><?php if(app()->getLocale() === $localeKey): ?>
                                <i class="bi bi-check-lg ms-2 text-success"></i>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
    <div class="myds-navbar-action" tabindex="0" x-data="{
                theme: localStorage.getItem('theme') || 'light',
                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    document.documentElement.setAttribute('data-bs-theme', this.theme);
                }
            }"
            x-init="document.documentElement.setAttribute('data-bs-theme', theme)">
            <button type="button"
                aria-label="<?php echo app('translator')->get('common.toggle_theme'); ?>"
                title="<?php echo app('translator')->get('common.toggle_theme'); ?>"
                @click="toggleTheme()"
                style="background: none; border: none; padding: 7px 10px; font-size: 1.21em; color: var(--myds-navbar-text);">
                <i class="bi bi-moon-stars-fill" x-show="theme === 'light'"></i>
                <i class="bi bi-sun-fill" x-show="theme === 'dark'"></i>
            </button>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sections.navbar.notifications-dropdown');

$__html = app('livewire')->mount($__name, $__params, 'lw-769367331-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
            <?php
                $currentUser = Auth::user();
            ?>
            <div class="myds-navbar-action" tabindex="0">
                <a href="#" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo e(__('User Menu')); ?>">
                    <img src="<?php echo e($currentUser->profile_photo_url); ?>"
                        alt="Avatar <?php echo e($currentUser->name); ?>"
                        class="myds-navbar-avatar">
                </a>
                <div class="myds-navbar-dropdown">
                    <div style="padding: 16px 22px 10px 22px; border-bottom:1px solid #e6e6e6;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <img src="<?php echo e($currentUser->profile_photo_url); ?>"
                                alt="Avatar <?php echo e($currentUser->name); ?>"
                                class="myds-navbar-avatar">
                            <div>
                                <strong><?php echo e($currentUser->name); ?></strong><br>
                                <small class="text-muted" style="font-size: 0.97em;">
                                    <?php echo e(Str::title($currentUser->getRoleNames()->first() ?? __('User'))); ?>

                                </small>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo e(route('profile.show')); ?>"><i class="bi bi-person-circle"></i> <?php echo e(__('Profil Saya')); ?></a>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-settings-admin')): ?>
                        <a href="<?php echo e(route('settings.users.index')); ?>"><i class="bi bi-gear"></i> <?php echo e(__('Tetapan Sistem')); ?></a>
                    <?php endif; ?>
                    <a href="#"><i class="bi bi-question-circle"></i> <?php echo e(__('Bantuan')); ?></a>
                    <div style="border-top:1px solid #e6e6e6;"></div>
                    <form id="logout-form" method="POST" action="<?php echo e(route('logout')); ?>" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                    <a href="<?php echo e(route('logout')); ?>"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> <?php echo e(__('Log Keluar')); ?>

                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="myds-navbar-action" tabindex="0">
                <a class="myds-navbar-link" href="<?php echo e(route('login')); ?>" title="<?php echo app('translator')->get('common.login'); ?>">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="d-none d-md-inline"><?php echo app('translator')->get('common.login'); ?></span>
                </a>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/navbar.css')); ?>">
    
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.navbar-action > a, .navbar-action > button').forEach(function(trigger){
                trigger.addEventListener('click', function(e){
                    e.preventDefault();
                    var parent = trigger.parentNode;
                    document.querySelectorAll('.navbar-action.show').forEach(function(a){
                        if (a !== parent) a.classList.remove('show');
                    });
                    parent.classList.toggle('show');
                });
            });
            document.addEventListener('click', function(e){
                if (!e.target.closest('.navbar-action')) {
                    document.querySelectorAll('.navbar-action.show').forEach(function(a){ a.classList.remove('show'); });
                }
            });
        });
    </script>
</nav>
<?php /**PATH C:\laragon\www\motac-irms\resources\views/livewire/sections/navbar/navbar.blade.php ENDPATH**/ ?>