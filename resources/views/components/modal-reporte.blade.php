<!-- resources/views/components/modal-alcoholimetria.blade.php -->
<div id="modalAlcoholimetria" class="fixed inset-0 bg-gray-900 bg-opacity-40 flex items-center justify-center z-50 hidden transition-opacity duration-200">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[95vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 border border-gray-200">

        <!-- HEADER -->
        <div class=" px-6 py-4 border-b border-gray-300 flex-shrink-0">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-xl font-lora font-bold text-[#404041]">Operativo de Alcoholimetría</h2>
                        <div class="inline-block bg-[#9D2449] text-white px-3 py-1 rounded-lg text-xs font-semibold font-lora border-l-4 border-[#470202]">
                            Alcoholimetría
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 font-lora" id="modalFechaPublicacion">sábado, 14 de octubre de 2023</p>
                </div>
                <button id="cerrarModalAlcoholimetria" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div class=" px-6 py-2 border-b border-gray-300 flex-shrink-0">
            <!-- INFORMACIÓN ADICIONAL COMPACTA -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-0">
                <div class="w-full md:w-auto">
                    <p class="text-gray-600 text-sm font-lora text-center md:text-left" id="modalFechaActividad">Fecha de la actividad: 8 de noviembre de 2023</p>
                </div>
                <div class="w-full md:w-auto">
                    <p class="text-sm text-gray-600 font-lora text-center md:text-right">Subido por: <span id="modalUsuarioAlcohol" class="font-semibold">Roberto Sánchez Jiménez</span></p>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="flex flex-col h-[calc(95vh-145px)]">
            <div class="p-6 overflow-y-auto flex-1">
                <!-- RESULTADOS DEL OPERATIVO -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Resultados del Operativo</h4>
                    
                    <!-- Estadísticas principales - Más compactas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                        <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i class="fas fa-map-marker-alt text-[#404041] text-lg"></i>
                                <div class="text-2xl font-bold text-[#404041] font-lora" id="puntosRevision">5</div>
                            </div>
                            <p class="text-xs text-gray-700 font-lora">Puntos de revisión instalados</p>
                        </div>

                        <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i class="fas fa-user-times text-[#404041] text-lg"></i>
                                <div class="text-2xl font-bold text-[#404041] font-lora" id="conductoresNoAptos">12</div>
                            </div>
                            <p class="text-xs text-gray-700 font-lora">Conductores no aptos</p>
                        </div>
                        
                        <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i class="fas fa-vial text-[#404041] text-lg"></i>
                                <div class="text-2xl font-bold text-[#404041] font-lora" id="pruebasRealizadas">20</div>
                            </div>
                            <p class="text-xs text-gray-700 font-lora">Pruebas realizadas</p>
                        </div>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA DESPUÉS DE RESULTADOS DEL OPERATIVO -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- CONDUCTORES NO APTOS - NUEVA ESTRUCTURA -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Conductores no aptos</h4>
                    
                    <!-- Por género -->
                    <div class="mb-6">
                        <h5 class="font-semibold text-[#404041] mb-3 text-md font-lora">Por género</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-white rounded-lg p-3 border border-[#404041] h-20 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-female text-[#404041] text-lg"></i>
                                    <span class="text-sm text-gray-700 font-semibold font-lora">Mujeres</span>
                                </div>
                                <div class="text-2xl font-bold text-[#404041] font-lora" id="mujeresNoAptas">5</div>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-[#404041] h-20 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-male text-[#404041] text-lg"></i>
                                    <span class="text-sm text-gray-700 font-semibold font-lora">Hombres</span>
                                </div>
                                <div class="text-2xl font-bold text-[#404041] font-lora" id="hombresNoAptos">6</div>
                            </div>
                        </div>
                    </div>

                    <!-- Por tipo de vehículo -->
                    <div>
                        <h5 class="font-semibold text-[#404041] mb-3 text-md font-lora">Por tipo de vehículo</h5>
                        
                        <!-- Primera fila de 3 cuadros -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <i class="fas fa-car text-[#404041] text-lg"></i>
                                    <div class="text-xl font-bold text-[#404041] font-lora" id="automovilesNoAptos">5</div>
                                </div>
                                <p class="text-xs text-gray-700 font-lora">Automóviles y camionetas</p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <i class="fas fa-motorcycle text-[#404041] text-lg"></i>
                                    <div class="text-xl font-bold text-[#404041] font-lora" id="motocicletasNoAptas">2</div>
                                </div>
                                <p class="text-xs text-gray-700 font-lora">Motocicletas</p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <i class="fas fa-bus text-[#404041] text-lg"></i>
                                    <div class="text-xl font-bold text-[#404041] font-lora" id="transporteColectivoNoApto">0</div>
                                </div>
                                <p class="text-xs text-gray-700 font-lora">Transporte público colectivo</p>
                            </div>
                        </div>
                        
                        <!-- Segunda fila de 3 cuadros -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <i class="fas fa-taxi text-[#404041] text-lg"></i>
                                    <div class="text-xl font-bold text-[#404041] font-lora" id="transporteIndividualNoApto">1</div>
                                </div>
                                <p class="text-xs text-gray-700 font-lora">Transporte público individual</p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <i class="fas fa-truck text-[#404041] text-lg"></i>
                                    <div class="text-xl font-bold text-[#404041] font-lora" id="transporteCargaNoApto">1</div>
                                </div>
                                <p class="text-xs text-gray-700 font-lora">Transporte de carga</p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <i class="fas fa-ambulance text-[#404041] text-lg"></i>
                                    <div class="text-xl font-bold text-[#404041] font-lora" id="emergenciaNoApto">0</div>
                                </div>
                                <p class="text-xs text-gray-700 font-lora">Vehículos de emergencia</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA DESPUÉS DE CONDUCTORES NO APTOS -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- DESCRIPCIÓN - CORREGIDA CON ANCHO COMPLETO -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Descripción</h4>
                    <div class="bg-white rounded-lg border border-[#404041] p-4">
                        <p class="text-gray-700 leading-relaxed max-h-48 overflow-y-auto font-lora w-full px-2 text-left" id="modalDescripcionAlcohol">
                            Operativo de alcoholimetría realizado durante el fin de semana en puntos estratégicos de la ciudad. Se realizaron pruebas a conductores con el objetivo de garantizar la seguridad vial y prevenir accidentes relacionados con el consumo de alcohol.
                        </p>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA DESPUÉS DE DESCRIPCIÓN -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- ARCHIVOS ADJUNTOS - NUEVO DISEÑO MEJORADO CON BORDES CORREGIDOS -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-[#404041] text-lg font-lora">Archivos Adjuntos</h4>
                        <!-- Botón Descargar Todos los Archivos - MÁS COMPACTO -->
                        <button class="descargar-todos-archivos px-3 py-1.5 bg-[#611132] text-white rounded-lg text-xs font-medium hover:bg-[#4a0e26] transition-all duration-300 font-lora whitespace-nowrap flex items-center justify-center gap-1">
                            <i class="fas fa-download text-xs"></i>
                            Descargar Todos
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="modalArchivosAlcohol">
                        <!-- Los archivos se insertarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA DESPUÉS DE ARCHIVOS ADJUNTOS -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- COMENTARIOS CON BORDES CORREGIDOS -->
                <div>
                    <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Comentarios</h4>
                    
                    <!-- Lista de comentarios -->
                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4" id="modalComentariosAlcohol">
                        <!-- Los comentarios se insertarán dinámicamente aquí -->
                    </div>

                    <!-- LÍNEA SEPARADORA ANTES DEL FORMULARIO - MEJOR BALANCE -->
                    <div class="h-px bg-gray-300 my-4"></div>

                    <!-- FORMULARIO PARA NUEVO COMENTARIO - CORREGIDO -->
                    <div class="flex gap-3 items-start w-full">
                        <!-- Campo de texto -->
                        <div class="flex-1">
                            <textarea 
                                id="nuevoComentario"
                                placeholder="Escribe tu comentario..."
                                class="w-full border border-[#404041] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent resize-none min-h-[42px] max-h-[120px]"
                                rows="1"
                            ></textarea>
                        </div>
                        
                        <!-- Botón de enviar - PERFECTAMENTE ALINEADO -->
                        <button 
                            id="enviarComentario"
                            class="px-4 bg-[#611132] text-white text-xs font-semibold rounded-lg hover:bg-[#4a0e26] transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed h-[42px] whitespace-nowrap mt-0"
                            disabled
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            Enviar
                        </button>
                    </div>
                </div>
            </div>

            <!-- FOOTER DEL MODAL - CRUD COMPLETO (BOTONES MÁS DELGADOS) -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-300 flex justify-end gap-3 flex-shrink-0">
                <!-- Botón Eliminar - Para eliminar el reporte (MÁS DELGADO) -->
                <button class="eliminar-reporte px-4 py-2 border border-[#AB1A1A] text-[#AB1A1A] rounded-lg text-sm font-medium hover:bg-[#AB1A1A] hover:text-white transition-all duration-300 font-lora whitespace-nowrap">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
                
                <!-- Botón Editar - Para correcciones (MÁS DELGADO) -->
                <button class="editar-reporte px-4 py-2 border border-[#C08400] text-[#C08400] rounded-lg text-sm font-medium hover:bg-[#C08400] hover:text-white transition-all duration-300 font-lora whitespace-nowrap">
                    <i class="fas fa-edit mr-2"></i>Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script para el modal de Alcoholimetría CON HORA MILITAR EN COMENTARIOS Y CRUD COMPLETO -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAlcoholimetria');
    const cerrarModal = document.getElementById('cerrarModalAlcoholimetria');
    const modalContent = modal.querySelector('div > div');
    const botonesVerDetalle = document.querySelectorAll('.ver-detalle-alcohol');

    // Función para abrir el modal
    function abrirModal(datos) {
        // Llenar información básica
        document.getElementById('modalFechaPublicacion').textContent = datos.fechaPublicacion;
        document.getElementById('modalFechaActividad').textContent = `Fecha de la actividad: ${datos.fechaOperativo}`;
        document.getElementById('modalUsuarioAlcohol').textContent = datos.usuario;
        document.getElementById('modalDescripcionAlcohol').textContent = datos.descripcion;

        // Llenar datos específicos de alcoholimetría
        document.getElementById('puntosRevision').textContent = datos.puntosRevision || '5';
        document.getElementById('conductoresNoAptos').textContent = datos.conductoresNoAptos || '12';
        document.getElementById('pruebasRealizadas').textContent = datos.pruebasRealizadas || '20';
        document.getElementById('mujeresNoAptas').textContent = datos.mujeresNoAptas || '5';
        document.getElementById('hombresNoAptos').textContent = datos.hombresNoAptos || '6';
        document.getElementById('automovilesNoAptos').textContent = datos.automovilesNoAptos || '5';
        document.getElementById('motocicletasNoAptas').textContent = datos.motocicletasNoAptas || '2';
        document.getElementById('transporteColectivoNoApto').textContent = datos.transporteColectivoNoApto || '0';
        document.getElementById('transporteIndividualNoApto').textContent = datos.transporteIndividualNoApto || '1';
        document.getElementById('transporteCargaNoApto').textContent = datos.transporteCargaNoApto || '1';
        document.getElementById('emergenciaNoApto').textContent = datos.emergenciaNoApto || '0';

        // Llenar archivos adjuntos - CON COLORES ESTÁNDAR RECONOCIBLES Y BORDES CORREGIDOS
        const archivosContainer = document.getElementById('modalArchivosAlcohol');
        archivosContainer.innerHTML = '';
        if (datos.archivos && datos.archivos.length > 0) {
            datos.archivos.forEach(archivo => {
                const extension = archivo.split('.').pop().toLowerCase();
                const { icono, color, colorHover } = obtenerEstiloArchivo(extension);
                
                archivosContainer.innerHTML += `
                    <div class="bg-white rounded-xl border border-[#404041] overflow-hidden transition-all duration-300 hover:shadow-lg group cursor-pointer">
                        <!-- Parte superior con color que abarca todo el ancho -->
                        <div class="${color} h-20 flex items-center justify-center transition-colors duration-300 group-hover:${colorHover}">
                            <i class="${icono} text-3xl text-white"></i>
                        </div>
                        
                        <!-- Información del archivo -->
                        <div class="p-4">
                            <p class="text-sm font-semibold text-[#404041] font-lora truncate mb-1" title="${archivo}">
                                ${archivo}
                            </p>
                            <p class="text-xs text-gray-500 font-lora mb-3">
                                ${extension.toUpperCase()} • ${obtenerTamañoAleatorio()}
                            </p>
                            
                            <!-- Botón de descarga individual -->
                            <button class="w-full px-3 py-2 bg-[#404041] text-white text-xs font-semibold rounded-lg hover:bg-[#2a2a2a] transition-all duration-200 flex items-center justify-center gap-2 descargar-archivo-individual" data-archivo="${archivo}">
                                <i class="fas fa-download text-xs"></i>
                                Descargar
                            </button>
                        </div>
                    </div>
                `;
            });
        }

        // Llenar comentarios - BORDES CORREGIDOS A #404041
        const comentariosContainer = document.getElementById('modalComentariosAlcohol');
        comentariosContainer.innerHTML = '';
        if (datos.comentarios && datos.comentarios.length > 0) {
            datos.comentarios.forEach(comentario => {
                const esAdmin = comentario.usuario.toLowerCase().includes('carlos') || 
                               comentario.usuario.toLowerCase().includes('admin');
                const esUsuarioActual = comentario.usuario.toLowerCase().includes('maría') || 
                                       comentario.usuario.toLowerCase().includes('maria') ||
                                       comentario.usuario.toLowerCase().includes('tú');
                
                // Formatear fecha a DD/MM/AAAA
                const fechaFormateada = formatearFecha(comentario.fecha);
                // Convertir hora a formato militar (24 horas)
                const horaMilitar = convertirHoraMilitar(comentario.hora);
                
                // ETIQUETAS CON BORDES QUE COINCIDEN CON EL COLOR DEL TEXTO (DEL CÓDIGO ORIGINAL)
                let estiloAdmin = 'border border-[#9D2449] text-[#9D2449] bg-transparent px-2 py-0.5 rounded text-xs font-medium font-bold';
                let estiloUsuario = 'border border-gray-400 text-gray-600 bg-transparent px-2 py-0.5 rounded text-xs font-medium font-bold';
                
                comentariosContainer.innerHTML += `
                    <div class="bg-white border border-[#404041] rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <div class="font-semibold text-[#404041] font-lora flex items-center gap-2">
                                    ${comentario.usuario}
                                    ${esAdmin ? `<span class="${estiloAdmin}">Admin</span>` : ''}
                                    ${esUsuarioActual ? `<span class="${estiloUsuario}">Tú</span>` : ''}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 font-lora whitespace-nowrap">
                                ${fechaFormateada}
                            </div>
                        </div>
                        
                        <p class="text-gray-700 text-sm break-words font-lora mb-2">${comentario.mensaje}</p>
                        
                        <!-- Hora militar y estado de lectura (solo para mensajes del usuario actual) -->
                        <div class="flex justify-end items-center">
                            <div class="text-xs text-gray-400 flex items-center gap-1">
                                ${esUsuarioActual ? 
                                    (comentario.leido ? 
                                        `${horaMilitar} <i class="fas fa-check-double text-green-500 text-xs ml-1"></i>` : 
                                        `${horaMilitar} <i class="fas fa-check text-gray-400 text-xs ml-1"></i>`) : 
                                    `${horaMilitar}`
                                }
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            comentariosContainer.innerHTML = `
                <div class="text-center py-8 text-gray-500 font-lora">
                    <i class="fas fa-comments text-3xl mb-3 text-gray-300"></i>
                    <p class="text-sm">No hay comentarios aún</p>
                    <p class="text-xs mt-1">Sé el primero en comentar</p>
                </div>
            `;
        }

        // Mostrar el modal con animación
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 50);

        // Inicializar funcionalidad del formulario de comentarios
        setTimeout(inicializarFormularioComentarios, 100);
        
        // Inicializar funcionalidades CRUD
        setTimeout(inicializarFuncionalidadesCRUD, 100);
    }

    // Función para inicializar funcionalidades CRUD
    function inicializarFuncionalidadesCRUD() {
        // Botón Editar (Amarillo)
        const botonEditar = document.querySelector('.editar-reporte');
        if (botonEditar) {
            botonEditar.addEventListener('click', function() {
                // Aquí iría la lógica para editar el reporte
                console.log('Editando reporte de alcoholimetría...');
                
                // Simular funcionalidad de edición
                alert('Funcionalidad de edición: Se abriría un formulario para modificar los datos del reporte.');
                
                // En una implementación real, esto podría:
                // 1. Abrir un modal de edición
                // 2. Cargar los datos actuales en un formulario
                // 3. Permitir la modificación
                // 4. Enviar los cambios al servidor
            });
        }

        // Botón Eliminar (Rojo)
        const botonEliminar = document.querySelector('.eliminar-reporte');
        if (botonEliminar) {
            botonEliminar.addEventListener('click', function() {
                // Confirmación antes de eliminar
                const confirmar = confirm('¿Estás seguro de que deseas eliminar este reporte de alcoholimetría? Esta acción no se puede deshacer.');
                
                if (confirmar) {
                    // Aquí iría la lógica para eliminar el reporte
                    console.log('Eliminando reporte de alcoholimetría...');
                    
                    // Simular eliminación exitosa
                    alert('Reporte eliminado correctamente.');
                    
                    // En una implementación real, esto podría:
                    // 1. Enviar solicitud DELETE al servidor
                    // 2. Cerrar el modal
                    // 3. Actualizar la lista de reportes
                    // 4. Mostrar mensaje de éxito
                    
                    // Cerrar el modal después de eliminar
                    cerrarModalFunc();
                }
            });
        }

        // Botón Descargar Todos los Archivos
        const botonDescargarTodos = document.querySelector('.descargar-todos-archivos');
        if (botonDescargarTodos) {
            botonDescargarTodos.addEventListener('click', function() {
                // Obtener la lista de archivos del modal
                const archivosContainer = document.getElementById('modalArchivosAlcohol');
                const archivos = archivosContainer.querySelectorAll('.bg-white');
                
                if (archivos.length === 0) {
                    alert('No hay archivos adjuntos para descargar.');
                    return;
                }
                
                console.log('Iniciando descarga de todos los archivos...');
                
                // Simular descarga de archivos
                alert(`Descargando ${archivos.length} archivo(s) adjuntos en un ZIP...`);
                
                // En una implementación real, esto podría:
                // 1. Crear un ZIP con todos los archivos
                // 2. Descargar el ZIP automáticamente
                
                // Ejemplo de cómo podría funcionar:
                const nombresArchivos = [];
                archivos.forEach((archivo) => {
                    const nombreArchivo = archivo.querySelector('.font-semibold').textContent;
                    nombresArchivos.push(nombreArchivo);
                });
                
                console.log('Archivos a descargar:', nombresArchivos);
                
                // Simular proceso de descarga
                setTimeout(() => {
                    alert(`Se ha descargado un archivo ZIP con ${archivos.length} archivos adjuntos.`);
                }, 1000);
            });
        }

        // Botones de descarga individual de archivos
        const botonesDescargarIndividual = document.querySelectorAll('.descargar-archivo-individual');
        botonesDescargarIndividual.forEach(boton => {
            boton.addEventListener('click', function() {
                const nombreArchivo = this.getAttribute('data-archivo');
                console.log(`Descargando archivo individual: ${nombreArchivo}`);
                
                // Simular descarga individual
                alert(`Descargando archivo: ${nombreArchivo}`);
                
                // En una implementación real, esto podría:
                // 1. Hacer una solicitud al servidor para el archivo específico
                // 2. Descargar el archivo directamente
            });
        });
    }

    // Función para formatear fecha a DD/MM/AAAA
    function formatearFecha(fecha) {
        if (!fecha) return '';
        
        // Si ya está en formato DD/MM/AAAA, devolverlo tal cual
        if (fecha.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
            return fecha;
        }
        
        // Convertir de formato MM/DD/YYYY a DD/MM/YYYY
        const partes = fecha.split('/');
        if (partes.length === 3) {
            return `${partes[1]}/${partes[0]}/${partes[2]}`;
        }
        
        return fecha;
    }

    // Función para convertir hora a formato militar (24 horas)
    function convertirHoraMilitar(hora) {
        if (!hora) return '18:20'; // Hora por defecto
        
        // Si ya está en formato 24 horas, devolverlo tal cual
        if (hora.match(/^\d{1,2}:\d{2}$/)) {
            return hora;
        }
        
        // Convertir de formato 12 horas a 24 horas
        const match = hora.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
        if (match) {
            let horas = parseInt(match[1]);
            const minutos = match[2];
            const periodo = match[3].toUpperCase();
            
            if (periodo === 'PM' && horas < 12) {
                horas += 12;
            } else if (periodo === 'AM' && horas === 12) {
                horas = 0;
            }
            
            return `${horas.toString().padStart(2, '0')}:${minutos}`;
        }
        
        // Si no se puede convertir, usar hora por defecto
        return '18:20';
    }

    // Función para cerrar el modal
    function cerrarModalFunc() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Event listeners
    botonesVerDetalle.forEach(boton => {
        boton.addEventListener('click', function() {
            const datos = {
                fechaPublicacion: this.getAttribute('data-fecha-actividad'),
                fechaOperativo: this.getAttribute('data-fecha-operativo'),
                usuario: this.getAttribute('data-usuario'),
                descripcion: this.getAttribute('data-descripcion'),
                puntosRevision: this.getAttribute('data-puntos-revision'),
                conductoresNoAptos: this.getAttribute('data-conductores-no-aptos'),
                pruebasRealizadas: this.getAttribute('data-pruebas-realizadas'),
                mujeresNoAptas: this.getAttribute('data-mujeres-no-aptas'),
                hombresNoAptos: this.getAttribute('data-hombres-no-aptos'),
                automovilesNoAptos: this.getAttribute('data-automoviles-no-aptos'),
                motocicletasNoAptas: this.getAttribute('data-motocicletas-no-aptas'),
                transporteColectivoNoApto: this.getAttribute('data-transporte-colectivo-no-apto'),
                transporteIndividualNoApto: this.getAttribute('data-transporte-individual-no-apto'),
                transporteCargaNoApto: this.getAttribute('data-transporte-carga-no-apto'),
                emergenciaNoApto: this.getAttribute('data-emergencia-no-apto'),
                archivos: JSON.parse(this.getAttribute('data-archivos') || '[]'),
                comentarios: JSON.parse(this.getAttribute('data-comentarios') || `[
                    {
                        "usuario": "Carlos Rodríguez",
                        "fecha": "17/10/2023",
                        "hora": "09:15 AM", 
                        "mensaje": "Excelente trabajo en el operativo. Los números son alentadores.",
                        "leido": true
                    },
                    {
                        "usuario": "María González", 
                        "fecha": "18/10/2023",
                        "hora": "18:20",
                        "mensaje": "¿Podemos extender el operativo al próximo fin de semana?",
                        "leido": false
                    }
                ]`)
            };
            abrirModal(datos);
        });
    });

    cerrarModal.addEventListener('click', cerrarModalFunc);

    // Cerrar modal al hacer clic fuera del contenido
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalFunc();
        }
    });

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarModalFunc();
        }
    });

    // Función para inicializar el formulario de comentarios
    function inicializarFormularioComentarios() {
        const textarea = document.getElementById('nuevoComentario');
        const botonEnviar = document.getElementById('enviarComentario');
        
        if (textarea && botonEnviar) {
            // Habilitar/deshabilitar botón
            textarea.addEventListener('input', function() {
                const longitud = this.value.length;
                botonEnviar.disabled = longitud === 0 || longitud > 500;
                
                // Autoajustar altura del textarea
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Enviar comentario
            botonEnviar.addEventListener('click', function() {
                const mensaje = textarea.value.trim();
                if (mensaje && mensaje.length <= 500) {
                    // Aquí iría la lógica para enviar el comentario al servidor
                    console.log('Enviando comentario:', mensaje);
                    
                    // Simular envío exitoso
                    alert('Comentario enviado correctamente');
                    textarea.value = '';
                    textarea.style.height = 'auto';
                    botonEnviar.disabled = true;
                    
                    // Recargar los comentarios (en una implementación real)
                    // abrirModal(datosActuales);
                }
            });
            
            // Enviar con Enter (pero permitir newline con Shift+Enter)
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!botonEnviar.disabled) {
                        botonEnviar.click();
                    }
                }
            });
        }
    }

    // Función auxiliar para obtener estilos de archivo - COLORES ESTÁNDAR RECONOCIBLES
    function obtenerEstiloArchivo(extension) {
        const estilos = {
            'pdf': {
                icono: 'fas fa-file-pdf',
                color: 'bg-red-500', // Rojo estándar para PDF
                colorHover: 'bg-red-600'
            },
            'xlsx': {
                icono: 'fas fa-file-excel',
                color: 'bg-green-500', // Verde estándar para Excel
                colorHover: 'bg-green-600'
            },
            'jpg': {
                icono: 'fas fa-file-image',
                color: 'bg-purple-500', // Púrpura para imágenes
                colorHover: 'bg-purple-600'
            },
            'jpeg': {
                icono: 'fas fa-file-image',
                color: 'bg-purple-500', // Púrpura para imágenes
                colorHover: 'bg-purple-600'
            },
            'png': {
                icono: 'fas fa-file-image',
                color: 'bg-purple-500', // Púrpura para imágenes
                colorHover: 'bg-purple-600'
            },
            'doc': {
                icono: 'fas fa-file-word',
                color: 'bg-blue-500', // Azul para Word
                colorHover: 'bg-blue-600'
            },
            'docx': {
                icono: 'fas fa-file-word',
                color: 'bg-blue-500', // Azul para Word
                colorHover: 'bg-blue-600'
            },
            'zip': {
                icono: 'fas fa-file-archive',
                color: 'bg-yellow-500', // Amarillo para archivos comprimidos
                colorHover: 'bg-yellow-600'
            },
            'default': {
                icono: 'fas fa-file',
                color: 'bg-gray-500', // Gris para otros archivos
                colorHover: 'bg-gray-600'
            }
        };
        
        return estilos[extension] || estilos.default;
    }

    function obtenerTamañoAleatorio() {
        const tamanios = ['2.1 MB', '1.5 MB', '3.2 MB', '856 KB', '4.7 MB'];
        return tamanios[Math.floor(Math.random() * tamanios.length)];
    }
});
</script>

<!-- Estilos adicionales para mejorar la apariencia de los botones CRUD -->
<style>
    /* Estilos para los botones del CRUD - MÁS DELGADOS COMO EN CENTRO DE CONTROL */
    .editar-reporte {
        border-width: 1px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .eliminar-reporte {
        border-width: 1px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .descargar-todos-archivos {
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    /* Efectos hover mejorados */
    .editar-reporte:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(192, 132, 0, 0.2);
    }
    
    .eliminar-reporte:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(171, 26, 26, 0.2);
    }
    
    .descargar-todos-archivos:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(97, 17, 50, 0.2);
    }
</style>