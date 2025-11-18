<?php $__env->startSection('title', config('app.name', 'تطبيق لغة الإشارة')); ?>

<?php $__env->startSection('content'); ?>
    <div class="guest-wrapper" style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 0;">
        <div class="guest-card" style="width:100%;max-width:420px;background:var(--surface);padding:20px;border-radius:12px;box-shadow:var(--shadow);">
            <div style="text-align:center;margin-bottom:12px">
                <img src="/frontend/logo.svg" alt="logo" style="width:64px;height:64px;margin:0 auto;display:block" />
            </div>
            <?php echo e($slot); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\a_i_s_l_project\si\resources\views/layouts/guest.blade.php ENDPATH**/ ?>