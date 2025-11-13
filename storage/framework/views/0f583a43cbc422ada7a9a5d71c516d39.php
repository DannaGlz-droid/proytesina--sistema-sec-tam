<div class="border border-[#404041] rounded-lg p-4 bg-white <?php echo e($attributes->get('class')); ?>">
    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
        <h3 class="font-semibold text-[#404041] text-lg font-lora"><?php echo e($titulo ?? 'Filtros'); ?></h3>
        <?php echo e($headerActions ?? ''); ?>

    </div>
    <div class="space-y-3">
        <?php echo e($slot); ?>

    </div>
</div><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/filtros/base.blade.php ENDPATH**/ ?>