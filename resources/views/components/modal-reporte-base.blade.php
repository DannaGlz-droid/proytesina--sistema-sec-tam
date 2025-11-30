@props(['tipo', 'titulo', 'colorBadge', 'colorBorder', 'modalId'])

<div id="{{ $modalId }}" class="fixed inset-0 bg-gray-900 bg-opacity-40 flex items-center justify-center z-50 hidden transition-opacity duration-200">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[95vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 border border-gray-200">

        <!-- HEADER -->
        <div class="px-6 py-4 border-b border-gray-300 flex-shrink-0">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-xl font-lora font-bold text-[#404041] modal-titulo" title="{{ $titulo }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; word-break:break-word; overflow-wrap:break-word;">{{ $titulo }}</h2>
                        <div class="inline-block {{ $colorBadge }} text-white px-3 py-1 rounded-lg text-xs font-semibold font-lora border-l-4 {{ $colorBorder }}">
                            {{ ucwords(str_replace('_', ' ', $tipo)) }}
                        </div>
                    </div>
                    <!-- Mostrar la fecha de la actividad directamente bajo el título -->
                    <p class="text-sm text-gray-600 font-lora modal-fecha-actividad">sábado, 14 de octubre de 2023</p>
                </div>
                <button class="modal-cerrar text-gray-500 hover:text-gray-700 transition-colors duration-200 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="px-6 py-2 border-b border-gray-300 flex-shrink-0">
            <div class="flex flex-col md:flex-row justify-between items-center gap-0">
                <div class="w-full md:w-auto">
                    <p class="text-sm text-gray-600 font-lora text-center md:text-left">Subido por: <span class="modal-usuario font-semibold">Usuario</span></p>
                </div>
                <div class="w-full md:w-auto">
                    <p class="text-sm text-gray-600 font-lora text-center md:text-right">Fecha de publicación: <span class="modal-fecha-publicacion font-semibold">Usuario</span></p>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="flex flex-col h-[calc(95vh-145px)]">
            <div class="p-6 overflow-y-auto flex-1">
                
                <!-- SECCIÓN ESPECÍFICA DEL TIPO DE REPORTE -->
                {{ $slot }}
                
                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Descripción</h4>
                    <div class="bg-white rounded-lg border border-[#404041] p-4">
                        <p class="text-gray-700 leading-relaxed max-h-48 overflow-y-auto font-lora w-full px-2 text-left modal-descripcion">
                            Descripción del reporte...
                        </p>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- ESTADO DE APROBACIÓN (visible para todos) -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Estado de Aprobación</h4>
                    <div class="bg-white rounded-lg border border-[#404041] p-4">
                        <div class="flex items-center gap-3 modal-status-container">
                            <!-- Se llenará dinámicamente con JavaScript -->
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-lg border border-yellow-300">
                                    <i class="fas fa-clock mr-1"></i>Pendiente de revisión
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- ARCHIVOS ADJUNTOS -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-[#404041] text-lg font-lora">Archivos Adjuntos</h4>
                        <button class="descargar-todos-archivos px-3 py-1.5 bg-[#611132] text-white rounded-lg text-xs font-medium hover:bg-[#4a0e26] transition-all duration-300 font-lora whitespace-nowrap flex items-center gap-1">
                            <i class="fas fa-download text-xs"></i>
                            Descargar Todos
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 modal-archivos">
                        <!-- Los archivos se insertarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- COMENTARIOS -->
                <div>
                    <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Comentarios</h4>
                    
                    <!-- Lista de comentarios -->
                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4 modal-comentarios">
                        <!-- Los comentarios se insertarán dinámicamente aquí -->
                    </div>

                    <!-- FORMULARIO PARA NUEVO COMENTARIO -->
                    <!-- Admin/Coordinador siempre pueden comentar, Operador solo en sus propias publicaciones -->
                    <div class="comentario-form-container" style="display: none;">
                        <div class="flex gap-3 items-start w-full">
                            <div class="flex-1">
                                <textarea 
                                    class="nuevo-comentario w-full border border-[#404041] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent resize-none min-h-[42px] max-h-[120px]"
                                    rows="1"
                                    placeholder="Escribe tu comentario..."
                                ></textarea>
                            </div>
                            <button 
                                class="enviar-comentario px-4 bg-[#611132] text-white text-xs font-semibold rounded-lg hover:bg-[#4a0e26] transition-all duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed h-[42px] whitespace-nowrap mt-0"
                                disabled
                            >
                                <i class="fas fa-paper-plane text-xs"></i>
                                Enviar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-300 flex items-center">
                <!-- Botones de aprobación/rechazo (solo Admin y Coordinador) -->
                <div class="approval-buttons-container ml-auto flex gap-3" style="display: none;">
                    <button class="aprobar-reporte px-6 py-2.5 bg-[#75A84E] text-white rounded-lg text-sm font-semibold hover:bg-[#5a8339] transition-all duration-300 font-lora whitespace-nowrap shadow-md hover:shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i>Aprobar
                    </button>
                    <button class="rechazar-reporte px-6 py-2.5 bg-[#9D2449] text-white rounded-lg text-sm font-semibold hover:bg-[#7a1c38] transition-all duration-300 font-lora whitespace-nowrap shadow-md hover:shadow-lg">
                        <i class="fas fa-times-circle mr-2"></i>Rechazar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>