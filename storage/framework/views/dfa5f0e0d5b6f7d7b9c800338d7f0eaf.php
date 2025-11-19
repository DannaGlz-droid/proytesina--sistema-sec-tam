
<?php $__env->startSection('title', 'Gestión de Usuarios'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-usuario', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Gestión de Usuarios</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Administre y gestione todos los usuarios del sistema con permisos y roles específicos.
                </p>
            </div>
            
            <!-- BOTÓN CREAR USUARIO -->
                <a href="<?php echo e(route('user.create')); ?>" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto" title="Crear Usuario">
                <i class="fas fa-plus text-xs"></i>
                Crear Usuario
                </a>
            </div>
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-80">
                        <?php if (isset($component)) { $__componentOriginaldfb34dbd9e2a4c0448d6497003ff47d0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfb34dbd9e2a4c0448d6497003ff47d0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.usuarios','data' => ['positions' => $positions,'jurisdictions' => $jurisdictions,'roles' => $roles]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.usuarios'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['positions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($positions),'jurisdictions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jurisdictions),'roles' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roles)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldfb34dbd9e2a4c0448d6497003ff47d0)): ?>
<?php $attributes = $__attributesOriginaldfb34dbd9e2a4c0448d6497003ff47d0; ?>
<?php unset($__attributesOriginaldfb34dbd9e2a4c0448d6497003ff47d0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldfb34dbd9e2a4c0448d6497003ff47d0)): ?>
<?php $component = $__componentOriginaldfb34dbd9e2a4c0448d6497003ff47d0; ?>
<?php unset($__componentOriginaldfb34dbd9e2a4c0448d6497003ff47d0); ?>
<?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <?php if (isset($component)) { $__componentOriginala59db8256e6aabe430c247ae425c7265 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala59db8256e6aabe430c247ae425c7265 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-controls','data' => ['items' => $users]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-controls'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users)]); ?>
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">ID</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Usuario</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Paterno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Materno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Correo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Teléfono</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Cargo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisdicción</th>
                                    <th scope="col" class="px-3 py-3 font-lora whitespace-nowrap text-xs">Fecha Alta</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Rol</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Estado</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Últ. Sesión</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(isset($users) && $users->isNotEmpty()): ?>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="border-b hover:bg-gray-50 <?php echo e($loop->even ? 'bg-gray-50' : 'bg-white'); ?>">
                                            <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap"><?php echo e($user->id); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->username); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->name); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->first_last_name); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->second_last_name); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->email); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->phone); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($user->position)->name ?? '—'); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($user->jurisdiction)->name ?? '—'); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->formatted_registration_date ?? '—'); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <?php
                                                    $roleName = optional($user->role)->name ?? '—';
                                                    $roleLower = strtolower($roleName);
                                                    if (in_array($roleLower, ['administrador', 'admin'])) {
                                                        $roleClasses = 'bg-red-100 text-red-800';
                                                    } elseif (in_array($roleLower, ['usuario', 'user'])) {
                                                        $roleClasses = 'bg-green-100 text-green-800';
                                                    } elseif ($roleLower === 'invitado') {
                                                        $roleClasses = 'bg-gray-100 text-gray-800';
                                                    } elseif ($roleLower === 'operador') {
                                                        $roleClasses = 'bg-blue-100 text-blue-800';
                                                    } else {
                                                        $roleClasses = 'bg-yellow-100 text-yellow-800';
                                                    }
                                                ?>
                                                <span class="<?php echo e($roleClasses); ?> text-xs font-medium px-2 py-0.5 rounded-full"><?php echo e($roleName); ?></span>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <?php $isActive = (bool) $user->is_active; $statusText = $isActive ? 'Activo' : 'Inactivo'; ?>
                                                <div class="flex items-center gap-1" role="status" aria-label="Estado: <?php echo e($statusText); ?>" title="Estado: <?php echo e($statusText); ?>">
                                                    <span class="w-2 h-2 rounded-full <?php echo e($isActive ? 'bg-emerald-500' : 'bg-rose-500'); ?>" aria-hidden="true"></span>
                                                    <span class="text-xs"><?php echo e($statusText); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap"><?php echo e($user->last_session_diff ?? '—'); ?></td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="flex items-center justify-end space-x-1">
                                                    <a href="<?php echo e(route('user.edit', $user->id)); ?>" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar" aria-label="Editar usuario <?php echo e($user->id); ?>">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('user.update-password', $user->id)); ?>" class="w-7 h-7 flex items-center justify-center rounded border border-[#C08400] text-[#C08400] hover:bg-[#C08400] hover:text-white transition-all duration-200" title="Cambiar Contraseña" aria-label="Cambiar contraseña usuario <?php echo e($user->id); ?>">
                                                        <i class="fas fa-key text-xs"></i>
                                                    </a>
                                                    <form method="POST" action="<?php echo e(route('user.destroy', $user->id)); ?>" onsubmit="return confirm('¿Eliminar usuario? Esta acción no se puede deshacer.');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar" aria-label="Eliminar usuario <?php echo e($user->id); ?>">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="14" class="px-3 py-4 text-center text-sm text-gray-500">No se encontraron usuarios.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala59db8256e6aabe430c247ae425c7265)): ?>
<?php $attributes = $__attributesOriginala59db8256e6aabe430c247ae425c7265; ?>
<?php unset($__attributesOriginala59db8256e6aabe430c247ae425c7265); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala59db8256e6aabe430c247ae425c7265)): ?>
<?php $component = $__componentOriginala59db8256e6aabe430c247ae425c7265; ?>
<?php unset($__componentOriginala59db8256e6aabe430c247ae425c7265); ?>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function clearSearch(btn) {
            try {
                const form = btn.closest('form');
                if (!form) return;
                const q = form.querySelector('input[name="q"]');
                if (q) q.value = '';
                form.submit();
            } catch (e) {
                console.error('clearSearch error', e);
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/usuarios/gestion-de-usuarios.blade.php ENDPATH**/ ?>