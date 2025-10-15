<!-- resources/views/components/modal-alcoholimetria.blade.php -->
<div id="modalAlcoholimetria" class="fixed inset-0 bg-gray-900 bg-opacity-40 flex items-center justify-center z-50 hidden transition-opacity duration-200">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 max-h-[95vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 border border-gray-200">
        <!-- HEADER -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-300 flex justify-between items-center flex-shrink-0">
            <div>
                <h2 class="text-xl font-lora font-bold text-[#404041]">Detalles del Reporte - Alcoholimetría</h2>
                <p class="text-sm text-gray-600 mt-1">Información completa del operativo de alcoholimetría</p>
            </div>
            <button id="cerrarModalAlcoholimetria" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- CONTENIDO -->
        <div class="flex flex-col h-[calc(95vh-120px)]">
            <div class="p-6 overflow-y-auto flex-1">
                <!-- INFORMACIÓN BÁSICA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Columna 1: Información básica -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                            <h3 class="text-lg font-semibold text-[#404041] mb-3 font-lora" id="modalTituloAlcohol">Título del reporte</h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-tag text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">Tipo de reporte</p>
                                        <p class="text-sm font-semibold text-[#404041] truncate" id="modalTipoAlcohol">Alcoholimetría</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar-day text-green-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">Fecha de la actividad</p>
                                        <p class="text-sm font-semibold text-[#404041] truncate" id="modalFechaActividad">sábado, 14 de octubre de 2023</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-purple-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">Subido por</p>
                                        <p class="text-sm font-semibold text-[#404041] truncate" id="modalUsuarioAlcohol">Usuario</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar-alt text-orange-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">Fecha de publicación</p>
                                        <p class="text-sm font-semibold text-[#404041] truncate" id="modalFechaPublicacion">lunes, 16 de octubre de 2023</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2: Estado -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                            <h4 class="font-semibold text-[#404041] mb-2 text-sm">Estado</h4>
                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold" id="modalEstadoAlcohol">Completado</span>
                        </div>
                    </div>
                </div>

                <!-- DATOS ESPECÍFICOS DE ALCOHOLIMETRÍA -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Datos del Operativo</h4>
                    
                    <!-- Estadísticas principales -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600">Puntos de revisión</p>
                                    <p class="text-lg font-bold text-blue-800" id="puntosRevision">8</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-times text-red-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-red-600">Conductores no aptos</p>
                                    <p class="text-lg font-bold text-red-800" id="conductoresNoAptos">12</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-vial text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-green-600">Pruebas realizadas</p>
                                    <p class="text-lg font-bold text-green-800" id="pruebasRealizadas">150</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conductores no aptos por género y vehículo -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Por género -->
                        <div class="bg-white rounded-lg p-4 border border-gray-300">
                            <h5 class="font-semibold text-[#404041] mb-3 text-sm">Conductores no aptos - Por género</h5>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-pink-500 rounded-full"></div>
                                        <span class="text-sm text-gray-700">Mujeres</span>
                                    </div>
                                    <span class="font-semibold text-gray-900" id="mujeresNoAptas">3</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm text-gray-700">Hombres</span>
                                    </div>
                                    <span class="font-semibold text-gray-900" id="hombresNoAptos">9</span>
                                </div>
                            </div>
                        </div>

                        <!-- Por tipo de vehículo -->
                        <div class="bg-white rounded-lg p-4 border border-gray-300">
                            <h5 class="font-semibold text-[#404041] mb-3 text-sm">Conductores no aptos - Por tipo de vehículo</h5>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">Automóviles y camionetas</span>
                                    <span class="font-semibold text-gray-900" id="automovilesNoAptos">5</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">Motocicletas</span>
                                    <span class="font-semibold text-gray-900" id="motocicletasNoAptas">4</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">Transporte público colectivo</span>
                                    <span class="font-semibold text-gray-900" id="transporteColectivoNoApto">1</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">Transporte público individual</span>
                                    <span class="font-semibold text-gray-900" id="transporteIndividualNoApto">2</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">Transporte de carga</span>
                                    <span class="font-semibold text-gray-900" id="transporteCargaNoApto">0</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">Vehículos de emergencia</span>
                                    <span class="font-semibold text-gray-900" id="emergenciaNoApto">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Descripción</h4>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-gray-700 leading-relaxed max-h-48 overflow-y-auto" id="modalDescripcionAlcohol">
                            Descripción del operativo aparecerá aquí...
                        </p>
                    </div>
                </div>

                <!-- ARCHIVOS ADJUNTOS -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Archivos Adjuntos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="modalArchivosAlcohol">
                        <!-- Los archivos se insertarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- COMENTARIOS -->
                <div>
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Comentarios</h4>
                    <div class="space-y-3 max-h-64 overflow-y-auto" id="modalComentariosAlcohol">
                        <!-- Los comentarios se insertarán dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <!-- FOOTER DEL MODAL - COMPLETO Y BIEN ESTRUCTURADO -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-300 flex justify-end gap-3 flex-shrink-0">
                <button class="px-4 py-2 border border-[#404041] text-[#404041] rounded-lg text-sm font-semibold hover:bg-gray-100 transition-all duration-300 font-lora whitespace-nowrap">
                    <i class="fas fa-edit mr-2"></i>Editar
                </button>
                <button class="px-4 py-2 bg-[#611132] text-white rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora whitespace-nowrap">
                    <i class="fas fa-download mr-2"></i>Descargar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script para el modal de Alcoholimetría - COMPLETAMENTE INDEPENDIENTE -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAlcoholimetria');
    const cerrarModal = document.getElementById('cerrarModalAlcoholimetria');
    const modalContent = modal.querySelector('div > div');
    const botonesVerDetalle = document.querySelectorAll('.ver-detalle-alcohol');

    // Función para abrir el modal
    function abrirModal(datos) {
        // Llenar información básica
        document.getElementById('modalTituloAlcohol').textContent = datos.titulo;
        document.getElementById('modalTipoAlcohol').textContent = datos.tipo;
        document.getElementById('modalFechaActividad').textContent = datos.fechaActividad;
        document.getElementById('modalFechaPublicacion').textContent = datos.fechaPublicacion;
        document.getElementById('modalUsuarioAlcohol').textContent = datos.usuario;
        document.getElementById('modalDescripcionAlcohol').textContent = datos.descripcion;

        // Actualizar estado
        const estadoElement = document.getElementById('modalEstadoAlcohol');
        estadoElement.textContent = datos.estado;
        estadoElement.className = 'inline-block px-2 py-1 rounded text-xs font-semibold ';
        
        if (datos.estado === 'Completado' || datos.estado === 'Finalizado') {
            estadoElement.classList.add('bg-green-100', 'text-green-800');
        } else if (datos.estado === 'En progreso') {
            estadoElement.classList.add('bg-yellow-100', 'text-yellow-800');
        } else if (datos.estado === 'En revisión') {
            estadoElement.classList.add('bg-blue-100', 'text-blue-800');
        } else {
            estadoElement.classList.add('bg-gray-100', 'text-gray-800');
        }

        // Llenar datos específicos de alcoholimetría
        document.getElementById('puntosRevision').textContent = datos.puntosRevision || '0';
        document.getElementById('conductoresNoAptos').textContent = datos.conductoresNoAptos || '0';
        document.getElementById('pruebasRealizadas').textContent = datos.pruebasRealizadas || '0';
        document.getElementById('mujeresNoAptas').textContent = datos.mujeresNoAptas || '0';
        document.getElementById('hombresNoAptos').textContent = datos.hombresNoAptos || '0';
        document.getElementById('automovilesNoAptos').textContent = datos.automovilesNoAptos || '0';
        document.getElementById('motocicletasNoAptas').textContent = datos.motocicletasNoAptas || '0';
        document.getElementById('transporteColectivoNoApto').textContent = datos.transporteColectivoNoApto || '0';
        document.getElementById('transporteIndividualNoApto').textContent = datos.transporteIndividualNoApto || '0';
        document.getElementById('transporteCargaNoApto').textContent = datos.transporteCargaNoApto || '0';
        document.getElementById('emergenciaNoApto').textContent = datos.emergenciaNoApto || '0';

        // Llenar archivos adjuntos
        const archivosContainer = document.getElementById('modalArchivosAlcohol');
        archivosContainer.innerHTML = '';
        if (datos.archivos && datos.archivos.length > 0) {
            datos.archivos.forEach(archivo => {
                const extension = archivo.split('.').pop();
                const icono = obtenerIconoArchivo(extension);
                archivosContainer.innerHTML += `
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                        <div class="w-10 h-10 bg-[#BC955C] rounded-lg flex items-center justify-center text-white flex-shrink-0">
                            <i class="${icono} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#404041] truncate">${archivo}</p>
                            <p class="text-xs text-gray-500">${extension.toUpperCase()} • ${obtenerTamañoAleatorio()}</p>
                        </div>
                        <button class="text-gray-400 hover:text-[#611132] transition-colors flex-shrink-0">
                            <i class="fas fa-download text-sm"></i>
                        </button>
                    </div>
                `;
            });
        }

        // Llenar comentarios
        const comentariosContainer = document.getElementById('modalComentariosAlcohol');
        comentariosContainer.innerHTML = '';
        if (datos.comentarios && datos.comentarios.length > 0) {
            datos.comentarios.forEach(comentario => {
                comentariosContainer.innerHTML += `
                    <div class="bg-white border border-gray-300 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-semibold text-[#404041]">${comentario.usuario}</div>
                            <div class="text-xs text-gray-500 whitespace-nowrap">${comentario.fecha}</div>
                        </div>
                        <p class="text-gray-700 text-sm break-words">${comentario.mensaje}</p>
                    </div>
                `;
            });
        } else {
            comentariosContainer.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-comment-slash text-2xl mb-2"></i>
                    <p>No hay comentarios aún</p>
                </div>
            `;
        }

        // Mostrar el modal con animación
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 50);
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
                titulo: this.getAttribute('data-titulo'),
                tipo: this.getAttribute('data-tipo'),
                fechaActividad: this.getAttribute('data-fecha-actividad'),
                fechaPublicacion: this.getAttribute('data-fecha-publicacion'),
                usuario: this.getAttribute('data-usuario'),
                estado: this.getAttribute('data-estado'),
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
                comentarios: JSON.parse(this.getAttribute('data-comentarios') || '[]')
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

    // Funciones auxiliares
    function obtenerIconoArchivo(extension) {
        const iconos = {
            'pdf': 'fas fa-file-pdf',
            'xlsx': 'fas fa-file-excel',
            'xls': 'fas fa-file-excel',
            'doc': 'fas fa-file-word',
            'docx': 'fas fa-file-word',
            'jpg': 'fas fa-file-image',
            'png': 'fas fa-file-image',
            'zip': 'fas fa-file-archive'
        };
        return iconos[extension] || 'fas fa-file';
    }

    function obtenerTamañoAleatorio() {
        const tamanios = ['2.1 MB', '1.5 MB', '3.2 MB', '856 KB', '4.7 MB'];
        return tamanios[Math.floor(Math.random() * tamanios.length)];
    }
});
</script>