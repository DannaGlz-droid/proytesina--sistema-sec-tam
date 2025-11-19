<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
        <ul class="inline-flex items-center space-x-2">
            
            <?php if($paginator->onFirstPage()): ?>
                <li>
                    <span class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-[#404041] text-gray-400 opacity-50 cursor-not-allowed" aria-hidden="true">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-[#404041] text-gray-700 hover:bg-[#404041] hover:text-white transition font-lora" aria-label="Anterior">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <li><span class="px-3 py-2 text-gray-500"><?php echo e($element); ?></span></li>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <li>
                                <span aria-current="page" class="px-3 py-2 rounded-md bg-[#404041] text-white font-lora"><?php echo e($page); ?></span>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?php echo e($url); ?>" class="px-3 py-2 rounded-md bg-white border border-[#404041] text-gray-700 hover:bg-[#404041] hover:text-white transition font-lora"><?php echo e($page); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <li>
                    <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-[#404041] text-gray-700 hover:bg-[#404041] hover:text-white transition font-lora" aria-label="Siguiente">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <span class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-[#404041] text-gray-400 opacity-50 cursor-not-allowed" aria-hidden="true">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/vendor/pagination/custom.blade.php ENDPATH**/ ?>