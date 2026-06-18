@props(['tipo', 'titulo', 'colorBadge', 'colorBorder', 'modalId'])

<style>
    .modal-titulo {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.28;
        max-height: 2.56em;
    }
    
    .comentarios-container {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out;
        border-radius: 0.75rem;
    }
    
    .comentarios-container.expanded {
        max-height: 560px;
        opacity: 1;
    }
    
    .description-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .description-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .description-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    .description-scroll::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
    .description-scroll {
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }

    .publication-detail-modal {
        box-shadow: 0 20px 45px rgba(17, 24, 39, 0.18), 0 2px 8px rgba(17, 24, 39, 0.08);
    }

    .publication-detail-modal div.border-\[\#404041\] {
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        box-shadow: 0 1px 3px rgba(17, 24, 39, 0.08);
    }

    .publication-detail-modal div.border-\[\#404041\]:hover {
        border-color: #e5e7eb !important;
        box-shadow: 0 1px 3px rgba(17, 24, 39, 0.08);
    }

    .publication-detail-modal .datos-generales-grid > div {
        min-height: 132px;
        display: flex;
        flex-direction: column;
    }

    .publication-detail-modal .datos-generales-grid .dato-general-value {
        min-height: 3rem;
        display: flex;
        align-items: flex-start;
        line-height: 1.25;
        word-break: break-word;
    }

    .publication-detail-modal .modal-status-container > div {
        background: #f9fafb;
        border-color: #e5e7eb;
        box-shadow: 0 1px 3px rgba(17, 24, 39, 0.08);
    }

    .publication-detail-modal .comentarios-shell {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 18px rgba(17, 24, 39, 0.08);
    }

    .publication-detail-modal .comentarios-toggle {
        background: transparent;
    }

    .publication-detail-modal .modal-comentarios {
        max-height: 220px;
        padding-right: 0.35rem;
        scrollbar-width: thin;
        scrollbar-color: #c7c7c7 transparent;
    }

    .publication-detail-modal .modal-comentarios::-webkit-scrollbar {
        width: 5px;
    }

    .publication-detail-modal .modal-comentarios::-webkit-scrollbar-track {
        background: transparent;
        margin: 4px 0;
    }

    .publication-detail-modal .modal-comentarios::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 999px;
    }

    .publication-detail-modal .modal-comentarios::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .publication-detail-modal .comentario-item {
        background: #ffffff !important;
        border-color: #e5e7eb !important;
        box-shadow: 0 1px 2px rgba(17, 24, 39, 0.06);
        padding-top: 12px !important;
        padding-bottom: 12px !important;
    }

    .publication-detail-modal .comentario-item:hover {
        border-color: #e5e7eb !important;
        box-shadow: 0 1px 2px rgba(17, 24, 39, 0.06);
    }

    .publication-detail-modal .descargar-todos-archivos,
    .publication-detail-modal .enviar-comentario {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        line-height: 1;
    }

    .publication-detail-modal .descargar-todos-archivos {
        padding: 0.55rem 0.9rem;
        box-shadow: 0 1px 2px rgba(17, 24, 39, 0.12);
        background: #ffffff;
        color: #611132;
        border: 1px solid #611132;
    }

    .publication-detail-modal .descargar-todos-archivos:hover {
        background: #611132;
        color: #ffffff;
        box-shadow: 0 3px 8px rgba(97, 17, 50, 0.18);
        transform: translateY(-1px);
    }

    .publication-detail-modal .enviar-comentario {
        width: 118px;
        padding: 0 0.9rem;
    }
</style>

<div id="{{ $modalId }}" class="fixed inset-0 bg-gray-900 bg-opacity-40 flex items-center justify-center z-[999999] hidden transition-opacity duration-200">
    <div class="publication-detail-modal bg-white rounded-xl max-w-4xl w-full mx-4 max-h-[95vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 border border-gray-200 ring-1 ring-black/5 flex flex-col">

        <!-- HEADER -->
        <div class="px-5 py-2.5 min-h-[58px] border-b border-gray-300 flex-shrink-0 flex items-center">
            <div class="flex justify-between items-center gap-4 w-full">
                <div class="flex-1 overflow-hidden">
                    <h2 class="text-lg font-lora font-bold text-[#404041] modal-titulo" title="{{ $titulo }}">{{ $titulo }}</h2>

                </div>
                <button class="modal-cerrar flex-shrink-0 text-gray-500 hover:text-gray-700 transition-colors duration-200 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- TIPO DE REPORTE FRANJA -->
        <div class="{{ $colorBadge }} h-1.5 flex-shrink-0" aria-label="{{ ucwords(str_replace('_', ' ', $tipo)) }}"></div>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="px-6 py-3 border-b border-gray-300 flex-shrink-0 bg-gray-50">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div class="w-full md:w-auto text-left md:text-right">
                    <p class="text-sm text-gray-600 font-lora text-center md:text-left">Subido por: <span class="modal-usuario font-semibold">Usuario</span></p>
                </div>
                <div class="w-full md:w-auto">
                    <p class="text-sm text-gray-600 font-lora text-center md:text-right">Fecha de publicación: <span class="modal-fecha-publicacion font-semibold">Usuario</span></p>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="flex flex-col min-h-0 flex-1">
            <div class="p-6 overflow-y-auto flex-1">
                
                <div class="hidden">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Datos del reporte</h4>
                    <div class="bg-white rounded-lg p-4 border border-[#404041]">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calendar-alt text-[#404041] text-xl"></i>
                            <div>
                                <h5 class="font-semibold text-[#404041] font-lora">Fecha de la actividad</h5>
                            <p class="text-sm font-semibold text-gray-700 font-lora modal-fecha-actividad">sábado, 14 de octubre de 2023</p>
                        </div>
                        </div>
                    </div>
                </div>
                
                <!-- SECCIÓN ESPECÍFICA DEL TIPO DE REPORTE -->
                {{ $slot }}
                
                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6 descripcion-separator"></div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-6 descripcion-section">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Descripción</h4>
                    <div class="bg-white rounded-lg border border-[#404041] p-4">
                        <p class="text-gray-700 leading-relaxed font-lora w-full px-2 text-left modal-descripcion break-words whitespace-normal">
                            Descripción del reporte...
                        </p>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- ESTADO DEL REPORTE (visible para todos) -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Estado del reporte</h4>
                    <div class="modal-status-container">
                        <!-- Se llenara dinamicamente con JavaScript -->
                        <div class="rounded-lg border border-[#404041] p-3 bg-white">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-700 flex items-center justify-center">
                                    <i class="fas fa-clock text-sm"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-[#404041] font-lora leading-tight">Pendiente de revision</p>
                                    <p class="text-xs text-gray-500 font-lora leading-tight">Esperando validacion</p>
                                </div>
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
                        <button class="descargar-todos-archivos rounded-lg text-xs font-semibold transition-all duration-200 font-lora whitespace-nowrap">
                            <i class="fas fa-download text-xs"></i>
                            Descargar todo
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 modal-archivos">
                        <!-- Los archivos se insertarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-300 mb-6"></div>

                <!-- COMENTARIOS - COLAPSABLE CON DISEÑO CLASSROOM -->
                <div class="comentarios-section comentarios-shell rounded-xl p-3">
                    
                    <!-- Botón para expandir/contraer comentarios -->
                    <button type="button" class="comentarios-toggle w-full text-left px-1 pb-3 transition-all duration-200 flex items-center justify-between" style="display: none;">  
                        <p class="font-semibold text-[#404041] font-lora text-sm leading-tight contador-comentarios">Comentarios (0)</p>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 icono-chevron text-xs" style="transform: rotate(0deg);"></i>
                    </button>
                    
                    <!-- Contenedor colapsable de comentarios -->
                    <div class="comentarios-container">
                        
                        <!-- Lista de comentarios -->
                        <div class="space-y-2 overflow-y-auto mb-3 modal-comentarios">
                            <!-- Los comentarios se insertarán dinámicamente aquí -->
                        </div>

                        <!-- Separador -->
                        <div class="border-t border-gray-200 pt-3"></div>
                        
                        <!-- FORMULARIO PARA NUEVO COMENTARIO -->
                        <div class="comentario-form-container" style="display: none;">
                            <div class="flex gap-2 items-end w-full">
                                <textarea 
                                    class="nuevo-comentario min-w-0 flex-1 border border-gray-300 bg-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 resize-none min-h-[40px] max-h-[100px] font-lora placeholder-gray-400 shadow-sm"
                                    rows="1"
                                    placeholder="Añade un comentario..."
                                ></textarea>
                                <button 
                                    class="enviar-comentario flex-shrink-0 bg-[#611132] text-white text-sm font-semibold rounded-xl hover:bg-[#4a0e26] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                                    disabled
                                    title="Enviar comentario"
                                >
                                    <i class="fas fa-paper-plane text-sm"></i>
                                    <span>Enviar</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mensaje si no hay permisos -->
                        <div class="comentario-no-permisos" style="display: none;">
                            <p class="text-xs text-gray-600 text-center font-lora italic">Solo administradores y coordinadores pueden comentar.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-actions-footer bg-gray-50 px-6 py-3 border-t border-gray-300 flex items-center min-h-[64px] flex-shrink-0">
                <!-- Botones de aprobación/rechazo (solo Admin y Coordinador) -->
                <div class="approval-buttons-container flex flex-wrap justify-end gap-3 w-full" style="display: none;">
                    <button class="rechazar-reporte min-w-[118px] justify-center border border-[#AB1A1A] text-[#AB1A1A] px-4 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-red-50 transition-all duration-300 font-lora whitespace-nowrap inline-flex items-center gap-2">
                        <i class="fas fa-times-circle"></i>Rechazar
                    </button>
                    <button class="aprobar-reporte min-w-[118px] justify-center bg-[#399e3b] text-white px-4 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#2d7e2f] transition-all duration-300 font-lora whitespace-nowrap inline-flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>Aprobar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // El titulo se limita por CSS a dos lineas para mantener estable el modal.
</script>
