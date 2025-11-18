<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'tipo' => '',
    'titulo' => '',
    'fecha' => '',
    'usuario' => '',
    'usuario_full' => '',
    'descripcion' => '',
    'status' => 'publicado',
    'approvedBy' => null,
    'rejectedBy' => null,
    'rejectionReason' => null,
    // archivos: can be an array or JSON string; archivosCount is a simple count fallback
    'archivos' => null,
    'archivosCount' => 0,
    // badge classes (bg and left border) to allow different colors per tipo
    'badgeClass' => 'bg-[#4C8CC4] text-white',
    'badgeBorderClass' => 'border-[#13264F]',
    // show small red dot when the publication has comments (boolean)
    'hasComments' => false,
    'hasUnread' => false,
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
    'tipo' => '',
    'titulo' => '',
    'fecha' => '',
    'usuario' => '',
    'usuario_full' => '',
    'descripcion' => '',
    'status' => 'publicado',
    'approvedBy' => null,
    'rejectedBy' => null,
    'rejectionReason' => null,
    // archivos: can be an array or JSON string; archivosCount is a simple count fallback
    'archivos' => null,
    'archivosCount' => 0,
    // badge classes (bg and left border) to allow different colors per tipo
    'badgeClass' => 'bg-[#4C8CC4] text-white',
    'badgeBorderClass' => 'border-[#13264F]',
    // show small red dot when the publication has comments (boolean)
    'hasComments' => false,
    'hasUnread' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative publication-card'])); ?>>
    <div class="flex-grow">
        <div class="flex justify-between items-start mb-4">
            <div class="text-gray-600 text-sm font-medium font-lora"><?php echo e($fecha); ?></div>
            <div class="flex items-center gap-2">
                
                <?php if($status === 'aprobado'): ?>
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg border border-green-300" title="Aprobado por: <?php echo e($approvedBy); ?>">
                        <i class="fas fa-check-circle mr-1"></i>Aprobado
                    </span>
                <?php elseif($status === 'rechazado'): ?>
                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-lg border border-red-300" title="Rechazado por: <?php echo e($rejectedBy); ?>">
                        <i class="fas fa-times-circle mr-1"></i>Rechazado
                    </span>
                <?php else: ?>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-lg border border-yellow-300">
                        <i class="fas fa-clock mr-1"></i>Pendiente
                    </span>
                <?php endif; ?>
                
                
                <div class="relative">
                    <button type="button" class="open-comments relative w-5 h-5 flex items-center justify-center text-gray-500">
                        <i class="fas fa-comment-alt text-sm"></i>
                        <?php if(($hasUnread ?? false) || ($hasComments && !isset($hasUnread))): ?>
                            <div class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-white"></div>
                        <?php endif; ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="inline-block <?php echo e($badgeClass); ?> px-3 py-1 rounded-lg text-xs font-semibold font-lora mb-3 border-l-4 <?php echo e($badgeBorderClass); ?>">
            <?php echo e($tipo); ?>

        </div>

        <h3 class="text-lg font-semibold text-[#404041] mb-3 leading-tight font-lora"><?php echo e($titulo); ?></h3>

        <div class="min-h-[3rem]">
            <?php if(!empty($descripcion)): ?>
                <div class="flex items-center gap-2 text-gray-600 text-sm mb-2 font-lora">
                    <i class="fas fa-tasks text-[#404041] w-4"></i>
                    <span><?php echo e($descripcion); ?></span>
                </div>
            <?php endif; ?>
            <div class="flex items-center gap-2 text-gray-600 text-sm font-lora">
                <i class="fas fa-user text-[#404041] w-4"></i>
                <div class="min-w-0">
                    <span class="block truncate" title="<?php echo e($usuario_full ?: $usuario); ?>">Subido por: <span class="font-semibold"><?php echo e($usuario); ?></span></span>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="h-[1px] bg-gray-300 my-3"></div>

    
    <?php
        $files = null;
        if ($archivos) {
            if (is_string($archivos)) {
                // try decode
                $decoded = json_decode($archivos, true);
                $files = is_array($decoded) ? $decoded : null;
            } elseif (is_array($archivos)) {
                $files = $archivos;
            }
        }
        $count = $files ? count($files) : (int) $archivosCount;
    ?>

    <?php if($count > 0): ?>
        <div class="flex-none">
            <div class="bg-gray-50 p-4 rounded-lg border border-[#404041] cursor-pointer transition-all duration-300 hover:bg-gray-100 archivos-open">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#BC955C] text-white">
                        <i class="fas fa-copy text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-[#404041] text-sm font-lora">Archivos adjuntos</div>
                        <div class="text-gray-500 text-xs font-lora mt-1"><?php echo e($count); ?> <?php echo e(\Illuminate\Support\Str::plural('archivo', $count)); ?> adjunto<?php echo e($count>1 ? 's' : ''); ?></div>
                    </div>
                    <div class="text-gray-500 transition-all duration-300 group-hover:text-[#404041]">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-[1px] bg-gray-300 my-3"></div>
    <?php endif; ?>

    <div class="flex-none">
        
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/components/publicacion-card.blade.php ENDPATH**/ ?>