@extends('layouts.principal')
@section('title', 'Reportes')
@section('content')

    @include('components.header')
    @include('components.nav')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Centro de Control</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Monitoreo y administración centralizada de reportes.</p>

        <!-- Contenedor principal -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            
            <!-- PESTAÑAS INTEGRADAS AL CONTENEDOR -->
            <div class="border-b border-gray-300 bg-gray-50 px-4 lg:px-6 pt-4">
                <nav class="flex space-x-1" aria-label="Tabs">
                    <button class="px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Todos los tipos
                    </button>
                    <button class="px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Seguridad Vial
                    </button>
                    <button class="px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Observatorio
                    </button>
                    <button class="px-4 py-2 text-sm font-medium font-lora rounded-t-lg bg-white text-[#404041] border border-b-0 border-gray-300 transition-all duration-200 hover:bg-gray-100">
                        Alcoholimetría
                    </button>
                </nav>
            </div>

            <!-- CONTENIDO INTERIOR DEL CONTENEDOR -->
            <div class="p-4 lg:p-6">
                <!-- FILTROS MEJORADOS - ORDEN UX OPTIMIZADO -->
                <div class="flex flex-col lg:flex-row gap-3 lg:gap-3 items-start lg:items-end mb-6">
                    <!-- Búsqueda por texto - PRIMERO (más importante) -->
                    <div class="flex-1 min-w-0 lg:max-w-[280px]">
                        <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Buscar</label>
                        <div class="relative">
                            <input type="text" placeholder="Títulos, usuarios..." class="w-full border border-[#404041] rounded-lg px-3 py-1.5 pl-9 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                            <!-- ICONO DE BÚSQUEDA CORREGIDO - POSICIÓN EXACTA -->
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Rango de fechas compacto -->
                    <div class="flex-1 min-w-0 lg:max-w-[300px]">
                        <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Rango de fechas</label>
                        <div class="flex gap-2">
                            <input type="date" class="flex-1 border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                            <span class="flex items-center text-gray-500 text-xs">a</span>
                            <input type="date" class="flex-1 border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                        </div>
                    </div>

                    <!-- Ordenar por compacto -->
                    <div class="flex-1 min-w-0 lg:max-w-[180px]">
                        <label class="block text-xs font-semibold text-[#404041] mb-1 font-lora">Ordenar</label>
                        <select class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                            <option value="fecha-desc">Más recientes</option>
                            <option value="fecha-asc">Más antiguos</option>
                            <option value="titulo">Título A-Z</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>

                    <!-- Botones de acción compactos -->
                    <div class="flex gap-2 mt-2 lg:mt-0">
                        <button class="bg-[#611132] text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-filter text-xs"></i>
                            Aplicar
                        </button>
                        <button class="border border-[#404041] text-[#404041] px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-redo text-xs"></i>
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- CONTADOR DE RESULTADOS -->
                <div class="flex justify-between items-center mb-6">
                    <div class="text-xs text-gray-600 font-lora">
                        <span class="font-semibold text-[#404041]">4</span> resultados encontrados
                    </div>
                    <div class="text-xs text-gray-500 font-lora">
                        <!-- Espacio para filtros adicionales si se necesitan -->
                    </div>
                </div>

                <!-- Grid de reportes - 4 columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    <!-- Reporte 1 - Seguridad Vial -->
                    <div class="border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative">
                        <!-- CONTENIDO SUPERIOR - ÁREA FIJA -->
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-gray-600 text-sm font-medium font-lora">sábado, 14 de octubre de 2023</div>
                                <div class="relative">
                                    <!-- ICONO DE COMENTARIO CON PUNTO ROJO MEJORADO -->
                                    <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-comment-alt text-sm"></i>
                                        <!-- PUNTO ROJO PERFECTAMENTE POSICIONADO -->
                                        <div class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-white"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="inline-block bg-[#4C8CC4] text-white px-3 py-1 rounded-lg text-xs font-semibold font-lora mb-3 border-l-4 border-[#13264F]">
                                Seguridad Vial
                            </div>
                            
                            <h3 class="text-lg font-semibold text-[#404041] mb-3 leading-tight font-lora">Capacitación de primeros auxilios</h3>
                            
                            <div class="mb-2">
                                <div class="flex items-center gap-2 text-gray-600 text-sm mb-1 font-lora">
                                    <i class="fas fa-tasks text-[#404041] w-4"></i>
                                    <span>Actividad: Taller</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600 text-sm font-lora">
                                    <i class="fas fa-user text-[#404041] w-4"></i>
                                    <span>Subido por: María González López</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora SUPERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS - MÁS GRANDE -->
                        <div class="flex-none">
                            <div class="bg-gray-50 p-4 rounded-lg border border-[#404041] cursor-pointer transition-all duration-300 hover:bg-gray-100 hover:translate-x-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#BC955C] text-white">
                                        <i class="fas fa-copy text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-[#404041] text-sm font-lora">Archivos adjuntos</div>
                                        <div class="text-gray-500 text-xs font-lora mt-1">2 archivos adjuntos</div>
                                    </div>
                                    <div class="text-gray-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#404041]">
                                        <i class="fas fa-chevron-right text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora INFERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES - ÁREA FIJA -->
                        <div class="flex-none">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white ver-detalle" 
                                        title="Ver detalles"
                                        data-titulo="Capacitación de primeros auxilios"
                                        data-tipo="Seguridad Vial"
                                        data-actividad="Taller"
                                        data-usuario="María González López"
                                        data-fecha="sábado, 14 de octubre de 2023"
                                        data-estado="Completado"
                                        data-descripcion="Capacitación completa en primeros auxilios para el personal de seguridad vial. Se cubrieron técnicas básicas de reanimación, manejo de emergencias y protocolos de seguridad. La sesión incluyó práctica con maniquíes y simulacros de situaciones reales."
                                        data-archivos='["manual_procedimientos.pdf", "lista_asistencia.xlsx"]'
                                        data-comentarios='[{"usuario": "Carlos Rodríguez", "fecha": "15/10/2023", "mensaje": "Excelente capacitación, muy informativa y práctica."}, {"usuario": "Ana Martínez", "fecha": "16/10/2023", "mensaje": "¿Podemos tener una sesión de refuerzo el próximo mes?"}]'>
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#C08400] text-[#C08400] transition-all duration-300 hover:bg-[#C08400] hover:text-white" title="Editar">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#AB1A1A] text-[#AB1A1A] transition-all duration-300 hover:bg-[#AB1A1A] hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte 2 - Observatorio -->
                    <div class="border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative">
                        <!-- CONTENIDO SUPERIOR - ÁREA FIJA -->
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-gray-600 text-sm font-medium font-lora">viernes, 13 de octubre de 2023</div>
                                <div class="relative">
                                    <!-- ICONO DE COMENTARIO SIN PUNTO ROJO -->
                                    <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-comment-alt text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="inline-block bg-[#75A84E] text-white px-3 py-1 rounded-lg text-xs font-semibold font-lora mb-3 border-l-4 border-[#184823]">
                                Observatorio de lesiones
                            </div>
                            
                            <h3 class="text-lg font-semibold text-[#404041] mb-3 leading-tight font-lora">Análisis de lesiones por accidentes</h3>
                            
                            <div class="mb-2">
                                <div class="flex items-center gap-2 text-gray-600 text-sm font-lora">
                                    <i class="fas fa-user text-[#404041] w-4"></i>
                                    <span>Subido por: Carlos Rodríguez</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora SUPERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS - MÁS GRANDE -->
                        <div class="flex-none">
                            <div class="bg-gray-50 p-4 rounded-lg border border-[#404041] cursor-pointer transition-all duration-300 hover:bg-gray-100 hover:translate-x-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#BC955C] text-white">
                                        <i class="fas fa-file text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-[#404041] text-sm font-lora">Archivos adjuntos</div>
                                        <div class="text-gray-500 text-xs font-lora mt-1">1 archivo adjunto</div>
                                    </div>
                                    <div class="text-gray-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#404041]">
                                        <i class="fas fa-chevron-right text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora INFERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES - MISMA POSICIÓN -->
                        <div class="flex-none">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white ver-detalle" 
                                        title="Ver detalles"
                                        data-titulo="Análisis de lesiones por accidentes"
                                        data-tipo="Observatorio de lesiones"
                                        data-actividad="Investigación"
                                        data-usuario="Carlos Rodríguez"
                                        data-fecha="viernes, 13 de octubre de 2023"
                                        data-estado="En revisión"
                                        data-descripcion="Estudio detallado de las lesiones reportadas en accidentes viales durante el último trimestre. Incluye análisis estadístico, patrones de lesiones y recomendaciones para prevención."
                                        data-archivos='["informe_lesiones.pdf"]'
                                        data-comentarios='[{"usuario": "María González", "fecha": "14/10/2023", "mensaje": "Los datos del tercer trimestre muestran una tendencia interesante."}]'>
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#C08400] text-[#C08400] transition-all duration-300 hover:bg-[#C08400] hover:text-white" title="Editar">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#AB1A1A] text-[#AB1A1A] transition-all duration-300 hover:bg-[#AB1A1A] hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte 3 - Alcoholimetría -->
                    <div class="border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative">
                        <!-- CONTENIDO SUPERIOR - ÁREA FIJA -->
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-gray-600 text-sm font-medium font-lora">jueves, 12 de octubre de 2023</div>
                                <div class="relative">
                                    <!-- ICONO DE COMENTARIO CON PUNTO ROJO MEJORADO -->
                                    <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-comment-alt text-sm"></i>
                                        <!-- PUNTO ROJO PERFECTAMENTE POSICIONADO -->
                                        <div class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-white"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="inline-block bg-[#9D2449] text-white px-3 py-1 rounded-lg text-xs font-semibold font-lora mb-3 border-l-4 border-[#470202]">
                                Alcoholimetría
                            </div>
                            
                            <h3 class="text-lg font-semibold text-[#404041] mb-3 leading-tight font-lora">Operativo alcoholimetría fin de semana</h3>
                            
                            <div class="mb-2">
                                <div class="flex items-center gap-2 text-gray-600 text-sm font-lora">
                                    <i class="fas fa-user text-[#404041] w-4"></i>
                                    <span>Subido por: Ana Martínez</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora SUPERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS - MÁS GRANDE -->
                        <div class="flex-none">
                            <div class="bg-gray-50 p-4 rounded-lg border border-[#404041] cursor-pointer transition-all duration-300 hover:bg-gray-100 hover:translate-x-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#BC955C] text-white">
                                        <i class="fas fa-folder-open text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-[#404041] text-sm font-lora">Archivos adjuntos</div>
                                        <div class="text-gray-500 text-xs font-lora mt-1">3 archivos adjuntos</div>
                                    </div>
                                    <div class="text-gray-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#404041]">
                                        <i class="fas fa-chevron-right text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora INFERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES - MISMA POSICIÓN -->
                        <div class="flex-none">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white ver-detalle" 
                                        title="Ver detalles"
                                        data-titulo="Operativo alcoholimetría fin de semana"
                                        data-tipo="Alcoholimetría"
                                        data-actividad="Operativo"
                                        data-usuario="Ana Martínez"
                                        data-fecha="jueves, 12 de octubre de 2023"
                                        data-estado="Finalizado"
                                        data-descripcion="Operativo de alcoholimetría realizado durante el fin de semana en puntos estratégicos de la ciudad. Se realizaron 150 pruebas con resultados positivos en 8 casos. Reporte completo incluye estadísticas y recomendaciones."
                                        data-archivos='["reporte_operativo.pdf", "estadisticas.xlsx", "fotos_evidencia.zip"]'
                                        data-comentarios='[{"usuario": "Roberto Sánchez", "fecha": "13/10/2023", "mensaje": "Buen trabajo en el operativo. Los números son alentadores."}, {"usuario": "Carlos Rodríguez", "fecha": "13/10/2023", "mensaje": "¿Podemos extender el operativo al próximo fin de semana?"}]'>
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#C08400] text-[#C08400] transition-all duration-300 hover:bg-[#C08400] hover:text-white" title="Editar">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#AB1A1A] text-[#AB1A1A] transition-all duration-300 hover:bg-[#AB1A1A] hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte 4 - Seguridad Vial -->
                    <div class="border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative">
                        <!-- CONTENIDO SUPERIOR - ÁREA FIJA -->
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-gray-600 text-sm font-medium font-lora">miércoles, 11 de octubre de 2023</div>
                                <div class="relative">
                                    <!-- ICONO DE COMENTARIO SIN PUNTO ROJO -->
                                    <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-comment-alt text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="inline-block bg-[#4C8CC4] text-white px-3 py-1 rounded-lg text-xs font-semibold font-lora mb-3 border-l-4 border-[#13264F]">
                                Seguridad Vial
                            </div>
                            
                            <h3 class="text-lg font-semibold text-[#404041] mb-3 leading-tight font-lora">Concientización uso cinturón seguridad</h3>
                            
                            <div class="mb-2">
                                <div class="flex items-center gap-2 text-gray-600 text-sm mb-1 font-lora">
                                    <i class="fas fa-tasks text-[#404041] w-4"></i>
                                    <span>Actividad: Campaña</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600 text-sm font-lora">
                                    <i class="fas fa-user text-[#404041] w-4"></i>
                                    <span>Subido por: Roberto Sánchez</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora SUPERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS - MÁS GRANDE -->
                        <div class="flex-none">
                            <div class="bg-gray-50 p-4 rounded-lg border border-[#404041] cursor-pointer transition-all duration-300 hover:bg-gray-100 hover:translate-x-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#BC955C] text-white">
                                        <i class="fas fa-copy text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-[#404041] text-sm font-lora">Archivos adjuntos</div>
                                        <div class="text-gray-500 text-xs font-lora mt-1">4 archivos adjuntos</div>
                                    </div>
                                    <div class="text-gray-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#404041]">
                                        <i class="fas fa-chevron-right text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Línea separadora INFERIOR - gray-300 -->
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES - MISMA POSICIÓN -->
                        <div class="flex-none">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white ver-detalle" 
                                        title="Ver detalles"
                                        data-titulo="Concientización uso cinturón seguridad"
                                        data-tipo="Seguridad Vial"
                                        data-actividad="Campaña"
                                        data-usuario="Roberto Sánchez"
                                        data-fecha="miércoles, 11 de octubre de 2023"
                                        data-estado="En progreso"
                                        data-descripcion="Campaña de concientización sobre el uso correcto del cinturón de seguridad. Incluye material gráfico, folletos informativos y actividades en puntos de alto tráfico. La campaña se desarrollará durante 4 semanas."
                                        data-archivos='["plan_campania.pdf", "material_grafico.zip", "presupuesto.xlsx", "cronograma.docx"]'
                                        data-comentarios='[{"usuario": "Ana Martínez", "fecha": "12/10/2023", "mensaje": "El material gráfico está muy bien diseñado."}, {"usuario": "María González", "fecha": "12/10/2023", "mensaje": "¿Podemos agregar más puntos de distribución?"}]'>
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#C08400] text-[#C08400] transition-all duration-300 hover:bg-[#C08400] hover:text-white" title="Editar">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#AB1A1A] text-[#AB1A1A] transition-all duration-300 hover:bg-[#AB1A1A] hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginación - FUERA del contenedor principal -->
        <div class="flex justify-center gap-2 mt-8">
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg opacity-50 cursor-not-allowed">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button class="px-3 py-2 border border-[#404041] bg-[#404041] text-white rounded-lg font-lora">1</button>
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg transition-all duration-300 hover:bg-[#404041] hover:text-white font-lora">2</button>
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg transition-all duration-300 hover:bg-[#404041] hover:text-white font-lora">3</button>
            <button class="px-3 py-2 border border-[#404041] bg-white rounded-lg transition-all duration-300 hover:bg-[#404041] hover:text-white">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        </div>
    </div>

    <!-- FONDO PERFECTO - NI MUY OSCURO NI MUY CLARO -->
<div id="modalReporte" class="fixed inset-0 bg-gray-900 bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
            <!-- HEADER DEL MODAL -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-300 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-lora font-bold text-[#404041]">Detalles del Reporte</h2>
                    <p class="text-sm text-gray-600 mt-1">Información completa del reporte seleccionado</p>
                </div>
                <button id="cerrarModal" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- CONTENIDO DEL MODAL -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <!-- INFORMACIÓN PRINCIPAL -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Columna 1: Información básica -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                            <h3 class="text-lg font-semibold text-[#404041] mb-3 font-lora" id="modalTitulo">Título del reporte</h3>
                            
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tag text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tipo de reporte</p>
                                        <p class="text-sm font-semibold text-[#404041]" id="modalTipo">Seguridad Vial</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tasks text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Actividad</p>
                                        <p class="text-sm font-semibold text-[#404041]" id="modalActividad">Taller</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Subido por</p>
                                        <p class="text-sm font-semibold text-[#404041]" id="modalUsuario">Usuario</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2: Fecha y estado -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                            <h4 class="font-semibold text-[#404041] mb-2 text-sm">Fecha de creación</h4>
                            <p class="text-sm text-gray-600" id="modalFecha">sábado, 14 de octubre de 2023</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                            <h4 class="font-semibold text-[#404041] mb-2 text-sm">Estado</h4>
                            <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold" id="modalEstado">Completado</span>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Descripción</h4>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                        <p class="text-gray-700 leading-relaxed" id="modalDescripcion">
                            Descripción detallada del reporte aparecerá aquí...
                        </p>
                    </div>
                </div>

                <!-- ARCHIVOS ADJUNTOS -->
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Archivos Adjuntos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="modalArchivos">
                        <!-- Los archivos se insertarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- COMENTARIOS -->
                <div>
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Comentarios</h4>
                    <div class="space-y-3" id="modalComentarios">
                        <!-- Los comentarios se insertarán dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <!-- FOOTER DEL MODAL -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-300 flex justify-end gap-3">
                <button class="px-4 py-2 border border-[#404041] text-[#404041] rounded-lg text-sm font-semibold hover:bg-gray-100 transition-all duration-300 font-lora">
                    <i class="fas fa-edit mr-2"></i>Editar
                </button>
                <button class="px-4 py-2 bg-[#611132] text-white rounded-lg text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora">
                    <i class="fas fa-download mr-2"></i>Descargar Reporte
                </button>
            </div>
        </div>
    </div>

    <!-- Script para manejar el modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalReporte');
            const cerrarModal = document.getElementById('cerrarModal');
            const modalContent = modal.querySelector('div > div'); // El contenedor interno
            const botonesVerDetalle = document.querySelectorAll('.ver-detalle');

            // Función para abrir el modal
            function abrirModal(datos) {
                // Llenar el modal con los datos
                document.getElementById('modalTitulo').textContent = datos.titulo;
                document.getElementById('modalTipo').textContent = datos.tipo;
                document.getElementById('modalActividad').textContent = datos.actividad;
                document.getElementById('modalUsuario').textContent = datos.usuario;
                document.getElementById('modalFecha').textContent = datos.fecha;
                document.getElementById('modalEstado').textContent = datos.estado;
                document.getElementById('modalDescripcion').textContent = datos.descripcion;

                // Actualizar color del estado
                const estadoElement = document.getElementById('modalEstado');
                estadoElement.className = 'inline-block px-2 py-1 rounded text-xs font-semibold ';
                if (datos.estado === 'Completado') {
                    estadoElement.classList.add('bg-green-100', 'text-green-800');
                } else if (datos.estado === 'En progreso') {
                    estadoElement.classList.add('bg-yellow-100', 'text-yellow-800');
                } else if (datos.estado === 'En revisión') {
                    estadoElement.classList.add('bg-blue-100', 'text-blue-800');
                } else if (datos.estado === 'Finalizado') {
                    estadoElement.classList.add('bg-green-100', 'text-green-800');
                }

                // Llenar archivos adjuntos
                const archivosContainer = document.getElementById('modalArchivos');
                archivosContainer.innerHTML = '';
                datos.archivos.forEach(archivo => {
                    const extension = archivo.split('.').pop();
                    const icono = obtenerIconoArchivo(extension);
                    archivosContainer.innerHTML += `
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                            <div class="w-10 h-10 bg-[#BC955C] rounded-lg flex items-center justify-center text-white">
                                <i class="${icono} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[#404041] truncate">${archivo}</p>
                                <p class="text-xs text-gray-500">${extension.toUpperCase()} • ${obtenerTamañoAleatorio()}</p>
                            </div>
                            <button class="text-gray-400 hover:text-[#611132] transition-colors">
                                <i class="fas fa-download text-sm"></i>
                            </button>
                        </div>
                    `;
                });

                // Llenar comentarios
                const comentariosContainer = document.getElementById('modalComentarios');
                comentariosContainer.innerHTML = '';
                datos.comentarios.forEach(comentario => {
                    comentariosContainer.innerHTML += `
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-semibold text-[#404041]">${comentario.usuario}</div>
                                <div class="text-xs text-gray-500">${comentario.fecha}</div>
                            </div>
                            <p class="text-gray-700 text-sm">${comentario.mensaje}</p>
                        </div>
                    `;
                });

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
                        actividad: this.getAttribute('data-actividad'),
                        usuario: this.getAttribute('data-usuario'),
                        fecha: this.getAttribute('data-fecha'),
                        estado: this.getAttribute('data-estado'),
                        descripcion: this.getAttribute('data-descripcion'),
                        archivos: JSON.parse(this.getAttribute('data-archivos')),
                        comentarios: JSON.parse(this.getAttribute('data-comentarios'))
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

    <!-- Incluir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection