
<?php $__env->startSection('title', isset($publication) ? 'Editar Grupos Vulnerables' : 'Registro de Grupos Vulnerables'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-reportes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">
            <?php echo e(isset($publication) ? 'Editar grupos vulnerables' : 'Registro de grupos vulnerables'); ?>

        </h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para <?php echo e(isset($publication) ? 'actualizar' : 'registrar'); ?> la información de grupos vulnerables.</p>

        <!-- Mensajes de error -->
        <?php if(session('error')): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-sm text-red-800 font-lora font-medium"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-red-800 font-lora font-semibold mb-2">Errores de validación:</p>
                        <ul class="list-disc list-inside text-sm text-red-700 font-lora space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cuadro del formulario responsive -->
        <form id="gruposVulnerablesForm" action="<?php echo e(isset($publication) ? route('reportes.grupos-vulnerables.update', $publication) : route('reportes.grupos-vulnerables.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php if(isset($publication)): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>
            
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            
            <!-- Sección 1: Información general -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="document-text-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información general</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tema <span class="text-red-600">*</span></label>
                            <input id="tema" type="text" 
                                   name="tema"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Prevención de accidentes viales"
                                   value="<?php echo e(old('tema', isset($publication) ? $publication->topic : '')); ?>"
                                   required minlength="3" maxlength="146">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de la actividad <span class="text-red-600">*</span></label>
                            <input id="fecha" type="date" 
                                   name="fecha"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   value="<?php echo e(old('fecha', isset($publication) ? $publication->activity_date->format('Y-m-d') : '')); ?>"
                                   required max="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar <span class="text-red-600">*</span></label>
                            <input id="lugar" type="text" 
                                   name="lugar"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Salón de usos múltiples"
                                   value="<?php echo e(old('lugar', isset($report) ? $report->location : '')); ?>"
                                   required minlength="3" maxlength="255">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio <span class="text-red-600">*</span></label>
                            <select id="grupos_municipality_select" name="municipio" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" required>
                                <option value="">Seleccione un municipio</option>
                                <?php
                                    $selectedMunicipio = old('municipio', isset($report) ? $report->municipality_id : '');
                                ?>
                                <?php if($selectedMunicipio): ?>
                                    <?php $m = $municipalities->firstWhere('id', $selectedMunicipio) ?>
                                    <?php if($m): ?>
                                        <option value="<?php echo e($m->id); ?>" selected><?php echo e($m->name); ?></option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                         <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Tipo de actividad <span class="text-red-600">*</span></label>
                            <select id="activity_type_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="activity_type_id" required>
                                <option value="">Seleccione el tipo de actividad</option>
                                <?php
                                    $selectedActivity = old('activity_type_id', isset($report) ? $report->activity_type_id : '');
                                ?>
                                <option value="1" <?php echo e($selectedActivity == '1' ? 'selected' : ''); ?>>Capacitación</option>
                                <option value="2" <?php echo e($selectedActivity == '2' ? 'selected' : ''); ?>>Taller</option>
                                <option value="3" <?php echo e($selectedActivity == '3' ? 'selected' : ''); ?>>Platica de sensibilizacion</option>
                                <option value="4" <?php echo e($selectedActivity == '4' ? 'selected' : ''); ?>>Reunión</option>
                                <option value="5" <?php echo e($selectedActivity == '5' ? 'selected' : ''); ?>>Evento especial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Participantes <span class="text-red-600">*</span></label>
                            <input id="participantes" type="number" 
                                   name="participantes"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 25"
                                   value="<?php echo e(old('participantes', isset($report) ? $report->participants : '')); ?>"
                                   required min="1" max="9999">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Promotor <span class="text-red-600">*</span></label>
                            <input id="promotor" type="text" 
                                   name="promotor"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Departamento de Salud"
                                   value="<?php echo e(old('promotor', isset($report) ? $report->promoter : '')); ?>"
                                   required minlength="3" maxlength="255">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-500 mb-1 font-lora">Jurisdicción</label>
                            <input type="hidden" id="jurisdiction_input_gv" name="jurisdiccion" value="<?php echo e(old('jurisdiccion', isset($report) ? $report->jurisdiction_id : '')); ?>" required>
                            <input id="jurisdiction_display_gv" type="text" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-600 focus:ring-2 focus:ring-gray-300 focus:border-transparent transition-all duration-200 font-lora" value="<?php echo e(isset($report) && $report->jurisdiction ? $report->jurisdiction->name : 'Pendiente (seleccione municipio)'); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Descripción -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="clipboard-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Descripción</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Descripción de la actividad</label>
                        <textarea 
                            id="descripcion"
                            name="descripcion"
                            class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                            rows="4"
                            placeholder="Describa los detalles, contexto, objetivos, resultados, etc. (opcional)"
                            maxlength="5000"
                        ><?php echo e(old('descripcion', isset($publication) ? $publication->description : '')); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Carga de archivos -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="cloud-upload-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Carga de archivos</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="space-y-4">
                    <!-- Archivos existentes (solo en modo edición) -->
                    <?php if(isset($publication) && $publication->files->count() > 0): ?>
                        <div class="mb-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="font-medium mb-3 font-lora text-sm text-[#404041] flex items-center">
                                    <ion-icon name="folder-open-outline" class="text-lg mr-2"></ion-icon>
                                    Archivos actuales (<?php echo e($publication->files->count()); ?>)
                                </p>
                                <?php
                                    // Get file requirements and count files by type
                                    $requirements = \App\Config\ReportFileRequirements::getRequirements('grupos-vulnerables');
                                    $filesByType = [
                                        'pdf' => $publication->files->filter(fn($f) => strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)) === 'pdf'),
                                        'excel' => $publication->files->filter(fn($f) => in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['xlsx', 'xls'])),
                                        'photos' => $publication->files->filter(fn($f) => in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])),
                                    ];
                                ?>
                                <ul class="space-y-2" id="existing-files-list">
                                    <?php $__currentLoopData = $publication->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                            $fileType = \App\Config\ReportFileRequirements::getFileType($file->original_name);

                                            $iconConfig = match($extension) {
                                                'pdf' => ['icon' => 'document-text-outline', 'color' => 'text-white', 'bg' => 'bg-red-500'],
                                                'xlsx', 'xls' => ['icon' => 'document-outline', 'color' => 'text-white', 'bg' => 'bg-green-500'],
                                                'jpg', 'jpeg', 'png' => ['icon' => 'image-outline', 'color' => 'text-white', 'bg' => 'bg-purple-500'],
                                                default => ['icon' => 'document-outline', 'color' => 'text-white', 'bg' => 'bg-gray-500']
                                            };
                                        ?>
                                        <li class="flex items-center justify-between py-2 px-3 rounded-lg border border-gray-200 transition-all duration-200 font-lora bg-white shadow-sm file-item" 
                                            data-file-id="<?php echo e($file->id); ?>" 
                                            data-file-type="<?php echo e($fileType); ?>">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <input type="checkbox" class="file-delete-checkbox mr-2 w-4 h-4 cursor-pointer border border-gray-300 rounded accent-[#611132] focus:ring-2 focus:ring-[#611132] focus:ring-offset-1" onchange="toggleFileStrikethrough(this)">
                                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg <?php echo e($iconConfig['bg']); ?>">
                                                    <ion-icon name="<?php echo e($iconConfig['icon']); ?>" class="<?php echo e($iconConfig['color']); ?> text-xl"></ion-icon>
                                                </div>
                                                <div class="ml-3 flex-1 min-w-0 file-info">
                                                    <p class="text-sm font-medium text-[#404041] truncate"><?php echo e($file->original_name); ?></p>
                                                    <p class="text-xs text-gray-500"><?php echo e(number_format($file->file_size / 1024 / 1024, 2)); ?> MB</p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <div class="text-sm text-gray-600 font-lora mt-6 flex items-center">
                                    <ion-icon name="checkbox-outline" class="mr-2 text-sm"></ion-icon>
                                    Marca los archivos que deseas eliminar/reemplazar
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tres cuadros en una fila horizontal - CON ESTADO ACTUALIZADO -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-4">
                        <!-- (1) Documento PDF -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="document-outline" class="text-blue-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Documento PDF</span>
                                </div>
                                <span id="pdf-status-badge" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    Pendiente
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: PDF (obligatorio)</p>
                        </div>

                        <!-- (2) Hoja de Cálculo -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="stats-chart-outline" class="text-green-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Hoja de Cálculo</span>
                                </div>
                                <span id="excel-status-badge" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    Pendiente
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formato: XLSX (obligatorio)</p>
                        </div>

                        <!-- (3) Fotografías -->
                        <div class="flex-1 p-4 border border-gray-300 rounded-lg bg-white">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <ion-icon name="images-outline" class="text-purple-500 mr-2 text-lg"></ion-icon>
                                    <span class="text-sm font-medium text-[#404041] font-lora">Fotografías</span>
                                </div>
                                <span id="photos-status-badge" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora">
                                    0/4
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-lora">Formatos: JPG, JPEG, PNG (4 fotos obligatorias)</p>
                        </div>
                    </div>

                    <!-- Área de carga de archivos -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-2 font-lora">
                            <?php if(isset($publication)): ?>
                                Agregar nuevos archivos (opcional)
                            <?php else: ?>
                                Subir archivos (selección múltiple) <span class="text-red-600">*</span>
                            <?php endif; ?>
                        </label>
                        
                        <!-- Cuadro punteado para arrastrar y soltar -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#404041] transition-colors duration-200 bg-gray-50">
                            <input type="file" 
                                   id="file-input"
                                   class="hidden"
                                   accept=".pdf,.xlsx,.xls,.jpg,.jpeg,.png"
                                   multiple
                                   onchange="addFiles(this.files)">
                            
                            <div class="cursor-pointer" onclick="document.getElementById('file-input').click()">
                                <ion-icon name="cloud-upload-outline" class="text-4xl text-gray-400 mb-3"></ion-icon>
                                <p class="text-sm font-medium text-[#404041] mb-1 font-lora">
                                    Haga clic o arrastre archivos aquí para subirlos
                                </p>
                                <p class="text-xs text-gray-500 font-lora">
                                    Formatos permitidos: PDF, XLSX, XLS, JPG, JPEG, PNG<br>
                                    <span class="text-xs text-gray-500">Tamaño máximo por archivo: 10 MB</span>
                                </p>
                                <p class="text-xs text-blue-600 font-lora mt-2">
                                    Puede seleccionar múltiples archivos a la vez
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información de archivos seleccionados -->
                        <div id="file-list" class="mt-4 hidden">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="font-medium mb-3 font-lora text-sm text-[#404041] flex items-center">
                                    <ion-icon name="folder-open-outline" class="text-lg mr-2"></ion-icon>
                                    Archivos agregados
                                </p>
                                <ul id="file-names" class="space-y-2"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES -->
            <?php if(isset($publication) || request()->is('reportes/*/*/edit')): ?>
                <?php if (isset($component)) { $__componentOriginal4472fe0a558b38a919fed94c8472a9fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4472fe0a558b38a919fed94c8472a9fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-buttons','data' => ['primaryText' => 'Actualizar registro','secondaryText' => '','tertiaryText' => 'Volver al listado','tertiaryHref' => ''.e(route('reportes.index')).'','primaryType' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-buttons'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['primaryText' => 'Actualizar registro','secondaryText' => '','tertiaryText' => 'Volver al listado','tertiaryHref' => ''.e(route('reportes.index')).'','primaryType' => 'submit']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4472fe0a558b38a919fed94c8472a9fd)): ?>
<?php $attributes = $__attributesOriginal4472fe0a558b38a919fed94c8472a9fd; ?>
<?php unset($__attributesOriginal4472fe0a558b38a919fed94c8472a9fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4472fe0a558b38a919fed94c8472a9fd)): ?>
<?php $component = $__componentOriginal4472fe0a558b38a919fed94c8472a9fd; ?>
<?php unset($__componentOriginal4472fe0a558b38a919fed94c8472a9fd); ?>
<?php endif; ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal4472fe0a558b38a919fed94c8472a9fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4472fe0a558b38a919fed94c8472a9fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-buttons','data' => ['primaryText' => 'Guardar registro','secondaryText' => 'Limpiar formulario','primaryType' => 'submit','secondaryType' => 'button','secondaryOnclick' => 'clearGruposVulnerablesForm()','tertiaryText' => 'Volver al listado','tertiaryHref' => ''.e(route('reportes.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-buttons'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['primaryText' => 'Guardar registro','secondaryText' => 'Limpiar formulario','primaryType' => 'submit','secondaryType' => 'button','secondaryOnclick' => 'clearGruposVulnerablesForm()','tertiaryText' => 'Volver al listado','tertiaryHref' => ''.e(route('reportes.index')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4472fe0a558b38a919fed94c8472a9fd)): ?>
<?php $attributes = $__attributesOriginal4472fe0a558b38a919fed94c8472a9fd; ?>
<?php unset($__attributesOriginal4472fe0a558b38a919fed94c8472a9fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4472fe0a558b38a919fed94c8472a9fd)): ?>
<?php $component = $__componentOriginal4472fe0a558b38a919fed94c8472a9fd; ?>
<?php unset($__componentOriginal4472fe0a558b38a919fed94c8472a9fd); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Input oculto para archivos a eliminar -->
        <?php if(isset($publication)): ?>
            <input type="hidden" id="files-to-delete" name="files_to_delete" value="">
        <?php endif; ?>
        </form>

        <?php if($errors->any()): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fieldMap = {
                    'tema': 'tema',
                    'fecha': 'fecha',
                    'lugar': 'lugar',
                    'activity_type_id': 'activity_type_id',
                    'participantes': 'participantes',
                    'promotor': 'promotor',
                    'descripcion': 'descripcion'
                };

                let firstErrorField = null;

                <?php $__currentLoopData = $errors->keys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $errorMessage = $errors->first($field); ?>
                    if (fieldMap['<?php echo e($field); ?>']) {
                        const element = document.getElementById(fieldMap['<?php echo e($field); ?>']);
                        if (element) {
                            element.setCustomValidity('<?php echo e(addslashes($errorMessage)); ?>');
                            if (!firstErrorField) {
                                firstErrorField = element;
                            }
                            element.addEventListener('input', function() {
                                this.setCustomValidity('');
                            });
                        }
                    }
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                if (firstErrorField) {
                    firstErrorField.reportValidity();
                }
            });
        </script>
        <?php endif; ?>
    </div>

    <!-- Script para manejo de archivos -->
    <script>
        // Array para almacenar todos los archivos seleccionados
        let selectedFiles = [];
        
        // Track de archivos a eliminar
        let filesToDelete = [];
        
        function addFiles(newFiles) {
            // Agregar nuevos archivos al array
            for (let i = 0; i < newFiles.length; i++) {
                const file = newFiles[i];
                // Verificar que el archivo no esté ya en la lista
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    selectedFiles.push(file);
                }
            }
            
            // Actualizar la visualización
            updateFileStatus();
            updateFileCounters();
            
            // Limpiar el input para permitir seleccionar el mismo archivo de nuevo si se elimina
            document.getElementById('file-input').value = '';
        }
        
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileStatus();
            updateFileCounters();
        }
        
        function toggleFileStrikethrough(checkbox) {
            const fileItem = checkbox.closest('.file-item');
            const fileInfo = fileItem.querySelector('.file-info');
            const fileId = fileItem.dataset.fileId;
            
            if (checkbox.checked) {
                fileInfo.classList.add('line-through', 'opacity-50');
                fileItem.classList.add('opacity-75');
            } else {
                fileInfo.classList.remove('line-through', 'opacity-50');
                fileItem.classList.remove('opacity-75');
            }
            
            // Actualizar contador y lista de archivos a eliminar
            updateFileCounters();
            
            // Actualizar el input hidden con archivos a eliminar
            const filesToDeleteIds = [];
            document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked').forEach(checkbox => {
                const fileId = checkbox.closest('.file-item').dataset.fileId;
                if (fileId) filesToDeleteIds.push(fileId);
            });
            document.getElementById('files-to-delete').value = filesToDeleteIds.join(',');
        }
        
        function updateFileCounters() {
            // Contar archivos existentes (no marcados para eliminar)
            let existingPdfCount = 0;
            let existingExcelCount = 0;
            let existingPhotoCount = 0;
            
            // Contar archivos nuevos seleccionados
            let newPdfCount = 0;
            let newExcelCount = 0;
            let newPhotoCount = 0;
            
            // Contar archivos existentes no marcados para eliminar
            document.querySelectorAll('#existing-files-list .file-item').forEach(item => {
                const checkbox = item.querySelector('.file-delete-checkbox');
                const fileType = item.dataset.fileType;
                
                if (!checkbox.checked) { // Solo contar si NO está marcado para eliminar
                    if (fileType === 'pdf') existingPdfCount++;
                    else if (fileType === 'excel') existingExcelCount++;
                    else if (fileType === 'photos') existingPhotoCount++;
                }
            });
            
            // Contar archivos nuevos por tipo
            selectedFiles.forEach(file => {
                const extension = file.name.split('.').pop().toLowerCase();
                if (extension === 'pdf') newPdfCount++;
                else if (extension === 'xlsx' || extension === 'xls') newExcelCount++;
                else if (['jpg', 'jpeg', 'png'].includes(extension)) newPhotoCount++;
            });
            
            // Total = existentes + nuevos
            const totalPdf = existingPdfCount + newPdfCount;
            const totalExcel = existingExcelCount + newExcelCount;
            const totalPhotos = existingPhotoCount + newPhotoCount;
            
            // Actualizar los contadores de los tres cuadros
            updateCounterDisplay('pdf', totalPdf, 1);
            updateCounterDisplay('excel', totalExcel, 1);
            updateCounterDisplay('photos', totalPhotos, 4);
            
            // Cambiar color de archivo para indicar que será eliminado
            document.querySelectorAll('#existing-files-list .file-item').forEach(item => {
                const checkbox = item.querySelector('.file-delete-checkbox');
                if (checkbox.checked) {
                    item.classList.add('opacity-50', 'line-through');
                    item.style.backgroundColor = '#f5f5f5';
                } else {
                    item.classList.remove('opacity-50', 'line-through');
                    item.style.backgroundColor = '#ffffff';
                }
            });
        }
        
        function updateCounterDisplay(type, current, required) {
            const statusBadge = document.getElementById(`${type}-status-badge`);
            
            let statusText = '';
            let badgeClass = '';
            
            if (current >= required) {
                statusText = current === required ? 'Completado' : `${current}/${required}`;
                badgeClass = 'bg-green-100 text-green-800';
            } else if (current > 0) {
                statusText = type === 'photos' ? `${current}/${required}` : 'Incompleto';
                badgeClass = 'bg-yellow-100 text-yellow-800';
            } else {
                statusText = type === 'photos' ? `${current}/${required}` : 'Pendiente';
                badgeClass = 'bg-yellow-100 text-yellow-800';
            }
            
            statusBadge.textContent = statusText;
            statusBadge.className = `text-xs px-2 py-1 rounded font-lora ${badgeClass}`;
        }
        
        function updateFileStatus() {
            const fileList = document.getElementById('file-list');
            const fileNames = document.getElementById('file-names');
            
            let pdfCount = 0;
            let excelCount = 0;
            let photoCount = 0;
            
            // Limpiar lista anterior
            fileNames.innerHTML = '';
            
            // Contar archivos por tipo y mostrar nombres
            selectedFiles.forEach((file, index) => {
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Determinar icono y color según el tipo
                let iconName = 'document-outline';
                let bgColor = 'bg-gray-500';
                let iconColor = 'text-white';
                
                if (extension === 'pdf') {
                    pdfCount++;
                    iconName = 'document-text-outline';
                    bgColor = 'bg-red-500';
                } else if (extension === 'xlsx' || extension === 'xls') {
                    excelCount++;
                    iconName = 'document-outline';
                    bgColor = 'bg-green-500';
                } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                    photoCount++;
                    iconName = 'image-outline';
                    bgColor = 'bg-purple-500';
                }
                
                // Agregar a la lista con botón de eliminar
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center justify-between py-2 px-3 hover:bg-white rounded-lg border border-gray-200 transition-all duration-200 font-lora bg-white shadow-sm';
                listItem.innerHTML = `
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg ${bgColor}">
                            <ion-icon name="${iconName}" class="${iconColor} text-xl"></ion-icon>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#404041] truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="ml-3 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-all duration-200" title="Eliminar archivo">
                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                    </button>
                `;
                fileNames.appendChild(listItem);
            });
            
            // Mostrar/ocultar lista de archivos
            if (selectedFiles.length > 0) {
                fileList.classList.remove('hidden');
            } else {
                fileList.classList.add('hidden');
            }
        }
        
        function clearGruposVulnerablesForm() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                const form = document.getElementById('gruposVulnerablesForm');
                if (form) {
                    form.reset();
                }
                // Limpiar archivos seleccionados
                selectedFiles = [];
                updateFileStatus();
                // Resetear estados de archivos
                document.getElementById('pdf-status-badge').textContent = 'Pendiente';
                document.getElementById('pdf-status-badge').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('excel-status-badge').textContent = 'Pendiente';
                document.getElementById('excel-status-badge').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('photos-status-badge').textContent = '0/4';
                document.getElementById('photos-status-badge').className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-lora';
                document.getElementById('file-list').classList.add('hidden');
                
                // Reinicializar Tom Select para que cargue todos los municipios de nuevo
                const gruposMuni = document.getElementById('grupos_municipality_select');
                if (gruposMuni && gruposMuni.tomselect) {
                    gruposMuni.tomselect.destroy();
                    gruposMuni.tomselect = null;
                    // Limpiar el valor
                    gruposMuni.value = '';
                    // Reinicializar Tom Select
                    const currentJurisdiction = <?php echo json_encode(optional(auth()->user())->jurisdiction_id, 15, 512) ?>;
                    const ts = new TomSelect(gruposMuni, {
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        maxOptions: 20,
                        maxItems: 1,
                        create: false,
                        preload: true,
                        load: function(query, callback) {
                            let url = '/api/municipalities/search?q=' + encodeURIComponent(query);
                            if (currentJurisdiction) {
                                url += '&jurisdiction_id=' + encodeURIComponent(currentJurisdiction);
                            }
                            fetch(url).then(r => r.json()).then(items => callback(items)).catch(() => callback());
                        },
                        onChange: function(value) {
                            const evt = new Event('change');
                            gruposMuni.dispatchEvent(evt);
                        }
                    });
                    try { gruposMuni.style.display = 'none'; } catch (e) {}
                }
                
                // Restaurar la jurisdicción del usuario (es fija y no debe cambiar)
                const jurisdictionDisplay = document.getElementById('jurisdiction_display_gv');
                const hiddenJur = document.getElementById('jurisdiction_input_gv');
                const currentJurisdiction = <?php echo json_encode(optional(auth()->user())->jurisdiction_id, 15, 512) ?>;
                const jurisNames = <?php echo json_encode($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; }), 15, 512) ?>;
                if (currentJurisdiction) {
                    if (hiddenJur) hiddenJur.value = currentJurisdiction;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[currentJurisdiction] || '';
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
            }
        }
        
        // Funcionalidad de arrastrar y soltar
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar contadores en modo edición
            updateFileCounters();
            
            const dropArea = document.querySelector('.border-dashed');
            const fileInput = document.getElementById('file-input');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropArea.classList.add('border-[#404041]', 'bg-blue-50');
            }
            
            function unhighlight() {
                dropArea.classList.remove('border-[#404041]', 'bg-blue-50');
            }
            
            dropArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                addFiles(files);
            }
            
            // Interceptar el envío del formulario para agregar los archivos
            const mainForm = document.querySelector('form[action*="grupos-vulnerables"][method="POST"]:not([id^="delete-file"])');  
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    console.log('Form submit interceptado, action:', this.action);
                    console.log('Form method:', this.method);
                    
                    // Solo validar archivos en modo CREACIÓN (no en modo EDICIÓN)
                    const isEditMode = this.action.includes('/edit') || this.querySelector('input[name="_method"][value="PUT"]');
                    console.log('Is edit mode:', isEditMode);
                    
                    if (!isEditMode && selectedFiles.length === 0) {
                        e.preventDefault();
                        alert('Debe seleccionar al menos un archivo antes de enviar el formulario.');
                        return false;
                    }
                    
                    // Contar archivos por tipo solo si hay archivos seleccionados
                    if (selectedFiles.length > 0) {
                        let pdfCount = 0;
                        let excelCount = 0;
                        let photoCount = 0;
                        
                        selectedFiles.forEach(file => {
                            const extension = file.name.split('.').pop().toLowerCase();
                            if (extension === 'pdf') pdfCount++;
                            else if (extension === 'xlsx' || extension === 'xls') excelCount++;
                            else if (['jpg', 'jpeg', 'png'].includes(extension)) photoCount++;
                        });
                        
                        // Validar solo en modo creación
                        if (!isEditMode) {
                            if (pdfCount < 1) {
                                e.preventDefault();
                                alert('Debe incluir al menos 1 archivo PDF.');
                                return false;
                            }
                            
                            if (excelCount < 1) {
                                e.preventDefault();
                                alert('Debe incluir al menos 1 archivo Excel (XLSX).');
                                return false;
                            }
                            
                            if (photoCount < 4) {
                                e.preventDefault();
                                alert(`Debe incluir 4 fotografías. Actualmente tiene ${photoCount} foto(s).`);
                                return false;
                            }
                        }
                        
                        // Crear un DataTransfer para poder asignar múltiples archivos al input
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dataTransfer.items.add(file);
                        });
                        
                        // Crear un input hidden con todos los archivos
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'file';
                        hiddenInput.name = 'archivos[]';
                        hiddenInput.multiple = true;
                        hiddenInput.files = dataTransfer.files;
                        hiddenInput.style.display = 'none';
                        
                        this.appendChild(hiddenInput);
                    }
                    
                    console.log('Form va a ser enviado normalmente');
                });
            }
        });
    </script>

    
    <?php if(isset($publication) && $publication->files->count() > 0): ?>
        <?php $__currentLoopData = $publication->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <form id="delete-file-<?php echo e($file->id); ?>" method="POST" action="<?php echo e(route('reportes.file.delete', $file)); ?>" class="hidden">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
            </form>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        /* Tom Select styling to match form inputs */
        .ts-wrapper { border: none !important; padding: 0 !important; background: transparent !important; }
        select.tomselect-select {
            position: absolute !important; left: -9999px !important; width: 1px !important;
            height: 1px !important; overflow: hidden !important; opacity: 0 !important;
            pointer-events: none !important; border: 0 !important; margin: 0 !important;
            padding: 0 !important; background: transparent !important;
        }
        .ts-wrapper { display: block; width: 100%; }
        .ts-control {
            border: 1px solid #d1d5db !important; border-radius: 0.5rem !important;
            padding: 8px 12px !important; background: #ffffff !important;
            font-size: 0.875rem; line-height: 1.25rem !important;
            box-shadow: none !important; height: auto !important; min-height: 36px !important;
        }
        .ts-control .item, .ts-control input { padding: 0 !important; margin: 0 !important; }
        .ts-dropdown { border: 1px solid #d1d5db; border-radius: 0.5rem; max-height: 240px; }
        .ts-dropdown .ts-option { padding: 0.5rem 0.75rem; }
        .ts-control::after {
            content: ""; position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%); width: 18px; height: 18px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='1.6'><polyline points='6 9 12 15 18 9'/></svg>");
            background-size: 12px 12px; pointer-events: none;
        }
        input[type="date"] {
            padding: 8px 12px !important; border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important; font-size: 0.875rem;
            min-height: 36px !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map municipality_id -> jurisdiction_id
            const muniToJur = <?php echo json_encode($municipalities->mapWithKeys(function($m){ return [$m->id => $m->jurisdiction_id]; }), 15, 512) ?>;
            const jurisNames = <?php echo json_encode($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; }), 15, 512) ?>;
            // Jurisdicción del usuario (puede ser null)
            const currentJurisdiction = <?php echo json_encode(optional(auth()->user())->jurisdiction_id, 15, 512) ?>;

            const gruposMuni = document.getElementById('grupos_municipality_select');
            const jurisdictionDisplay = document.getElementById('jurisdiction_display_gv');
            const hiddenJur = document.getElementById('jurisdiction_input_gv');

            function setJurisdictionBasedOnMunicipality() {
                const mid = gruposMuni?.value || '';
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (hiddenJur) hiddenJur.value = jid;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[jid] || '';
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
            }

            if (gruposMuni) {
                gruposMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
                setJurisdictionBasedOnMunicipality();
            }

            // Initialize Tom Select for municipality
            function fetchMunicipalities(q) {
                let url = '/api/municipalities/search?q=' + encodeURIComponent(q);
                if (currentJurisdiction) {
                    url += '&jurisdiction_id=' + encodeURIComponent(currentJurisdiction);
                }
                return fetch(url).then(r => r.json());
            }

            if (gruposMuni) {
                const ts = new TomSelect(gruposMuni, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 20,
                    maxItems: 1,
                    create: false,
                    preload: true,
                    load: function(query, callback) {
                        fetchMunicipalities(query).then(items => callback(items)).catch(() => callback());
                    },
                    onChange: function(value) {
                        const evt = new Event('change');
                        gruposMuni.dispatchEvent(evt);
                    }
                });
                try { gruposMuni.style.display = 'none'; } catch (e) {}
            }

            // Si el usuario tiene una jurisdicción asignada, establecerla en el campo oculto y en el display
            if (currentJurisdiction) {
                if (hiddenJur && !hiddenJur.value) hiddenJur.value = currentJurisdiction;
                if (jurisdictionDisplay && (!jurisdictionDisplay.value || jurisdictionDisplay.value.includes('Pendiente'))) {
                    jurisdictionDisplay.value = jurisNames[currentJurisdiction] || jurisdictionDisplay.value;
                }
            }

            // Interceptar el envío del formulario para actualizar files_to_delete
            const mainForm = document.getElementById('gruposVulnerablesForm');
            const isEditMode = <?php echo e(isset($publication) ? 'true' : 'false'); ?>;
            
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    // Actualizar files_to_delete SIEMPRE (tanto en creación como en edición)
                    if (isEditMode && document.getElementById('files-to-delete')) {
                        const filesToDeleteIds = [];
                        document.querySelectorAll('#existing-files-list .file-delete-checkbox:checked').forEach(checkbox => {
                            const fileId = checkbox.closest('.file-item').dataset.fileId;
                            if (fileId) filesToDeleteIds.push(fileId);
                        });
                        document.getElementById('files-to-delete').value = filesToDeleteIds.join(',');
                    }
                });
            }
        });
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/reportes/registro/grupos-vulnerables.blade.php ENDPATH**/ ?>