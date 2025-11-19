
<?php $__env->startSection('title', 'Datos de Defunciones'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-estadisticas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Datos de Defunciones</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    En esta sección puede cargar archivos (Excel o CSV), aplicar filtros y consultar los registros en la tabla.
                </p>
            </div>

            <a href="<?php echo e(route('statistic.create')); ?>" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-plus text-xs"></i>
                Reg. Def.
            </a>
           
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Columna Izquierda - Filtros -->
            <div class="lg:w-80">
                <!-- SECCIÓN CARGAR ARCHIVO -->
                <div class="border border-[#404041] rounded-lg p-4 bg-white mb-6">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-3">
                        <h3 class="font-semibold text-[#404041] text-lg font-lora">Cargar Archivo</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Área de Drag & Drop -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-xs text-gray-600 font-lora mb-1">Arrastre un archivo aquí</p>
                            <p class="text-xs text-gray-500 font-lora">(Excel/CSV)</p>
                        </div>
                        
                        <!-- Información de tamaño -->
                        <p class="text-xs text-gray-500 text-center font-lora">Tamaño máximo: 10MB</p>
                        
                        <!-- Botón Seleccionar Archivo -->
                        <button id="selectFileBtn" type="button" class="w-full bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2 shadow-sm" aria-controls="fileInput" aria-label="Seleccionar archivo">
                            <i class="fas fa-folder-open text-xs"></i>
                            Seleccionar archivo
                        </button>
                        <input id="fileInput" type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" />
                        
                        
                    </div>
                </div>

                <!-- COMPONENTE DE FILTROS -->
                <?php if (isset($component)) { $__componentOriginal23105fab6c390d1966189f69b007b578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23105fab6c390d1966189f69b007b578 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros.defunciones','data' => ['jurisdictions' => $jurisdictions,'municipalities' => $municipalities,'causes' => $causes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros.defunciones'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jurisdictions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jurisdictions),'municipalities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($municipalities),'causes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($causes)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23105fab6c390d1966189f69b007b578)): ?>
<?php $attributes = $__attributesOriginal23105fab6c390d1966189f69b007b578; ?>
<?php unset($__attributesOriginal23105fab6c390d1966189f69b007b578); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23105fab6c390d1966189f69b007b578)): ?>
<?php $component = $__componentOriginal23105fab6c390d1966189f69b007b578; ?>
<?php unset($__componentOriginal23105fab6c390d1966189f69b007b578); ?>
<?php endif; ?>
            </div>

            <!-- Columna Derecha - Tabla -->
            <div class="flex-1">
                <?php if (isset($component)) { $__componentOriginala59db8256e6aabe430c247ae425c7265 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala59db8256e6aabe430c247ae425c7265 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-controls','data' => ['items' => $deaths,'action' => route('statistic.data'),'perPageOptions' => [25,50,100,250],'sortOptions' => [
                        // Show newest-first id as default, and oldest-first id second for clarity
                        'id_desc' => 'ID: Recientes',
                        'id_asc' => 'ID: Antiguos',
                        'death_date_desc' => 'Fecha Def.: Recientes',
                        'death_date_asc' => 'Fecha Def.: Antiguos',
                        'age_asc' => 'Edad: Menor a mayor',
                        'age_desc' => 'Edad: Mayor a menor',
                        'name_asc' => 'Nombre: A–Z',
                        'name_desc' => 'Nombre: Z–A',
                    ],'defaultSort' => 'id_desc']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-controls'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($deaths),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('statistic.data')),'perPageOptions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([25,50,100,250]),'sortOptions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        // Show newest-first id as default, and oldest-first id second for clarity
                        'id_desc' => 'ID: Recientes',
                        'id_asc' => 'ID: Antiguos',
                        'death_date_desc' => 'Fecha Def.: Recientes',
                        'death_date_asc' => 'Fecha Def.: Antiguos',
                        'age_asc' => 'Edad: Menor a mayor',
                        'age_desc' => 'Edad: Mayor a menor',
                        'name_asc' => 'Nombre: A–Z',
                        'name_desc' => 'Nombre: Z–A',
                    ]),'default-sort' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('id_desc')]); ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Folio</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Paterno</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Materno</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Edad</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Sexo</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fecha Def.</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio Res.</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Municipio Def.</th>
                                        <th scope="col" class="px-3 py-3 font-lora whitespace-nowrap text-xs">Jurisdicción (residencia)</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Causa</th>
                                        <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs text-right w-24">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($deaths) && $deaths->isNotEmpty()): ?>
                                        <?php $__currentLoopData = $deaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $death): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="border-b hover:bg-gray-50 <?php echo e($loop->even ? 'bg-gray-50' : 'bg-white'); ?>">
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($death)->gov_folio ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e($death->name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e($death->first_last_name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e($death->second_last_name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e($death->pretty_age ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e($death->sex ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e($death->death_date ? $death->death_date->format('d/m/Y') : '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($death->residenceMunicipality)->name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($death->deathMunicipality)->name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($death->jurisdiction)->name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($death->deathLocation)->name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap"><?php echo e(optional($death->deathCause)->name ?? '—'); ?></td>
                                                <td class="px-3 py-3 whitespace-nowrap w-24 text-right">
                                                    <div class="flex items-center justify-end space-x-1">
                                                        <a href="<?php echo e(route('statistic.edit', $death->id)); ?>" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar" aria-label="Editar defunción <?php echo e($death->id); ?>">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                        <form method="POST" action="<?php echo e(route('statistic.destroy', $death->id)); ?>" onsubmit="return confirm('¿Eliminar registro? Esta acción no se puede deshacer.');">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar" aria-label="Eliminar registro <?php echo e($death->id); ?>">
                                                                <i class="fas fa-trash text-xs"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="13" class="px-3 py-4 text-center text-sm text-gray-500">No se encontraron registros.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var selectBtn = document.getElementById('selectFileBtn');
    var fileInput = document.getElementById('fileInput');
    selectBtn && selectBtn.addEventListener('click', function () { fileInput.click(); });

    fileInput && fileInput.addEventListener('change', function (e) {
        var file = e.target.files[0];
        if (!file) return;
        if (file.size > 10 * 1024 * 1024) { alert('El archivo supera el límite de 10MB.'); return; }

        if (!confirm('¿Deseas importar este archivo ahora? Esto procesará y guardará los registros en la base de datos.')) return;

        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '<?php echo e(csrf_token()); ?>');

        var url = '<?php echo e(route("statistic.import")); ?>';
        fetch(url, { method: 'POST', body: fd, headers: {} })
            .then(function (res) {
                // try to parse JSON even on non-2xx so we can show server message
                return res.text().then(function (text) {
                    try { return JSON.parse(text); } catch (e) { return { ok: false, message: text || 'Respuesta no JSON del servidor' }; }
                });
            })
            .then(function (json) {
                if (!json) { alert('Error inesperado'); return; }
                // If server signaled failure, show its message
                if (json.ok === false) {
                    var serverMsg = json.message || (json.error_message ? json.error_message : 'Error en el servidor');
                    alert('Importación fallida:\n' + serverMsg + '\n\nRevisa la consola o el log en el servidor para más detalles.');
                    if (json.errors_file) console.info('Archivo de errores:', json.errors_file);
                    return;
                }

                // Ensure numeric fields exist (defaults to 0)
                var total = typeof json.total !== 'undefined' ? json.total : 0;
                var imported = typeof json.imported !== 'undefined' ? json.imported : 0;
                var failed = typeof json.failed !== 'undefined' ? json.failed : 0;

                var msg = 'Importación finalizada:\nTotal filas: ' + total + '\nImportadas: ' + imported + '\nFallidas: ' + failed;
                if (json.errors_file) msg += '\nArchivo de errores: ' + json.errors_file;
                alert(msg);
                // reload page to show new records
                window.location.reload();
            })
            .catch(function (err) { console.error(err); alert('Error al subir o procesar el archivo. Revisa la consola.'); });
    });
});
</script>
<?php $__env->stopPush(); ?>

 
<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/estadisticas/datos.blade.php ENDPATH**/ ?>