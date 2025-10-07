

<?php $__env->startSection('title'); ?>
404 - Page Not Found
<?php $__env->stopSection(); ?>

<?php $__env->startSection('error-content'); ?>
    <h2>404</h2>
    <p>Sorry ! Page Not Found !</p>
    <a href="<?php echo e(route('dashboard')); ?>">Back to Dashboard</a>
    <a href="<?php echo e(route('login')); ?>">Login Again !</a>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('errors.errors_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\hrms-qubify\resources\views/errors/404.blade.php ENDPATH**/ ?>