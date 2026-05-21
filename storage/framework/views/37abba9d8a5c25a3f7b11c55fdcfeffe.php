
<?php $__env->startSection('title', 'Mi Perfil'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-usuario', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Mi Perfil</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Consulta tu información personal y datos de cuenta.</p>

        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- COLUMNA IZQUIERDA - FICHA DE USUARIO -->
            <div class="lg:w-80">
                <div class="border border-[#404041] rounded-lg p-6 bg-white">
                    <!-- FOTO DE PERFIL -->
                    <div class="flex flex-col items-center mb-6">
                        <div class="relative group">
                            <?php if(auth()->user()->profile_photo_path): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->profile_photo_path)); ?>" alt="Foto de perfil" class="w-24 h-24 rounded-full object-cover border-4 border-[#611132]">
                            <?php else: ?>
                                <img src="<?php echo e(asset('images/default_pfp.svg.png')); ?>" alt="Avatar predeterminado" class="w-24 h-24 rounded-full object-cover border-4 border-[#611132]">
                            <?php endif; ?>
                            
                            <!-- Overlay hover con opciones -->
                            <div class="absolute inset-0 rounded-full bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="flex gap-2">
                                    <button type="button" id="uploadPhotoBtn" class="bg-white text-[#611132] p-2 rounded-full hover:bg-gray-100 transition" title="Cambiar foto">
                                        <i class="fas fa-camera text-sm"></i>
                                    </button>
                                    <?php if(auth()->user()->profile_photo_path): ?>
                                        <button type="button" id="deletePhotoBtn" class="bg-white text-red-600 p-2 rounded-full hover:bg-gray-100 transition" title="Eliminar foto">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Input file hidden -->
                        <input type="file" id="photoInput" accept="image/*" style="display: none;">
                        
                        <h2 class="text-lg font-lora font-bold text-[#404041] text-center mt-4"><?php echo e($fullName ?? auth()->user()->name); ?></h2>
                        <p class="text-sm text-gray-600 font-lora text-center"><?php echo e(auth()->user()->position->name ?? 'Sin cargo'); ?></p>
                    </div>

                    <!-- INFORMACIÓN RÁPIDA -->
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-[#611132] text-sm"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-lora">Correo</p>
                                <p class="text-sm text-[#404041] font-lora"><?php echo e(auth()->user()->email); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <i class="fas fa-briefcase text-[#611132] text-sm"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-lora">Cargo</p>
                                <p class="text-sm text-[#404041] font-lora"><?php echo e(auth()->user()->position->name ?? 'Sin cargo'); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-[#611132] text-sm"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-lora">Jurisdicción</p>
                                <p class="text-sm text-[#404041] font-lora"><?php echo e(auth()->user()->jurisdiction->name ?? 'Sin jurisdicción'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- BOTÓN CERRAR SESIÓN -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2">
                                <i class="fas fa-sign-out-alt text-xs"></i>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA - INFORMACIÓN DETALLADA -->
            <div class="flex-1">
                <div class="border border-[#404041] rounded-lg bg-white">
                    
                    <!-- ENCABEZADO -->
                    <div class="border-b border-[#404041] p-4">
                        <h3 class="text-lg font-lora font-bold text-[#404041]">Información Personal</h3>
                        <p class="text-sm text-gray-600 font-lora mt-1">Datos personales y información de tu cuenta</p>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información Básica -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información Básica</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Usuario</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e(auth()->user()->username ?? explode('@', auth()->user()->email)[0]); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Nombre Completo</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e($fullName ?? auth()->user()->name); ?></p>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información de Contacto</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Correo Electrónico</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e(auth()->user()->email); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Teléfono</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e(auth()->user()->phone ?? 'No especificado'); ?></p>
                                </div>
                            </div>

                            <!-- Información Laboral -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información Laboral</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Cargo</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e(auth()->user()->position->name ?? 'Sin cargo'); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Jurisdicción</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e(auth()->user()->jurisdiction->name ?? 'Sin jurisdicción'); ?></p>
                                </div>
                            </div>

                            <!-- Información de Cuenta -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información de Cuenta</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Fecha de Alta</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border"><?php echo e(auth()->user()->created_at->format('d/m/Y')); ?></p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs text-gray-600 font-lora mb-1">Estado de la Cuenta</label>
                                        <div class="flex items-center gap-2">
                                            <?php if(auth()->user()->status ?? true): ?>
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                <span class="text-sm text-green-600 font-lora">Activo</span>
                                            <?php else: ?>
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                <span class="text-sm text-red-600 font-lora">Inactivo</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs text-gray-600 font-lora mb-1">Rol en el Sistema</label>
                                        <?php
                                            $roleName = auth()->user()->role->name ?? 'Usuario';
                                            $roleLower = strtolower($roleName);
                                            if (in_array($roleLower, ['administrador', 'admin'])) {
                                                $roleClasses = 'bg-[#762f2d] text-white font-bold';
                                            } elseif (in_array($roleLower, ['coordinador'])) {
                                                $roleClasses = 'bg-[#4f772d] text-white font-bold';
                                            } elseif (in_array($roleLower, ['operador'])) {
                                                $roleClasses = 'bg-[#2d4f76] text-white font-bold';
                                            } elseif ($roleLower === 'invitado') {
                                                $roleClasses = 'bg-gray-600 text-white font-bold';
                                            } elseif (in_array($roleLower, ['usuario', 'user'])) {
                                                $roleClasses = 'bg-blue-700 text-white font-bold';
                                            } else {
                                                $roleClasses = 'bg-slate-600 text-white font-bold';
                                            }
                                        ?>
                                        <span class="inline-block <?php echo e($roleClasses); ?> text-xs font-semibold px-3 py-1 rounded-lg"><?php echo e($roleName); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NOTA INFORMATIVA ACTUALIZADA -->
                        <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-300">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-gray-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-[#404041] font-lora font-semibold">¿Necesitas ayuda?</p>
                                    <p class="text-xs text-gray-600 font-lora mt-1">
                                        Para cualquier modificación en tu información, contacta al administrador del sistema:<br>
                                        <strong>carlos.rodriguez@tamaulipas.gob.mx</strong> | <strong>+52 834 318 6300</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
            const deletePhotoBtn = document.getElementById('deletePhotoBtn');
            const photoInput = document.getElementById('photoInput');

            // Abrir input de archivo al hacer click en el botón
            if (uploadPhotoBtn) {
                uploadPhotoBtn.addEventListener('click', function() {
                    photoInput.click();
                });
            }

            // Manejar la selección del archivo
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Validar tamaño (máx 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. Máximo 5MB.');
                        return;
                    }

                    // Subir foto
                    const formData = new FormData();
                    formData.append('profile_photo', file);
                    formData.append('_token', '<?php echo e(csrf_token()); ?>');

                    const uploadBtn = uploadPhotoBtn;
                    uploadBtn.disabled = true;
                    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';

                    fetch('<?php echo e(route("usuario.upload-photo")); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recargar la página para mostrar la nueva foto
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al subir la foto');
                    })
                    .finally(() => {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = '<i class="fas fa-camera text-sm"></i>';
                    });
                });
            }

            // Eliminar foto de perfil
            if (deletePhotoBtn) {
                deletePhotoBtn.addEventListener('click', function() {
                    if (!confirm('¿Está seguro de que desea eliminar su foto de perfil?')) {
                        return;
                    }

                    const deleteBtn = deletePhotoBtn;
                    deleteBtn.disabled = true;

                    fetch('<?php echo e(route("usuario.delete-photo")); ?>', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la foto');
                    })
                    .finally(() => {
                        deleteBtn.disabled = false;
                    });
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views\usuarios\miperfil.blade.php ENDPATH**/ ?>