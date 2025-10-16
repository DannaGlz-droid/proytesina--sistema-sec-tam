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
                                    <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-comment-alt text-sm"></i>
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS -->
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES -->
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS -->
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES -->
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

                    <!-- Reporte 3 - Alcoholimetría (CON BOTÓN ESPECIAL) -->
                    <div class="border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative">
                        <!-- CONTENIDO SUPERIOR - ÁREA FIJA -->
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-gray-600 text-sm font-medium font-lora">jueves, 12 de octubre de 2023</div>
                                <div class="relative">
                                    <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-comment-alt text-sm"></i>
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS -->
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES - CON BOTÓN ESPECIAL PARA ALCOHOLIMETRÍA -->
                        <div class="flex-none">
                            <div class="flex justify-end gap-2">
                                <!-- En el reporte de alcoholimetría, actualiza el botón ver-detalle-alcohol -->
<button class="ver-detalle-alcohol w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white" 
        title="Ver detalles"
        data-fecha-actividad="sábado, 14 de octubre de 2023"
        data-fecha-operativo="8 de noviembre de 2023"
        data-usuario="Roberto Sánchez Jiménez"
        data-descripcion="Operativo de alcoholimetría realizado durante el fin de semana en puntos estratégicos de la ciudad. Se realizaron pruebas a conductores con el objetivo de garantizar la seguridad vial y prevenir accidentes relacionados con el consumo de alcohol."
        data-puntos-revision="5"
        data-conductores-no-aptos="12"
        data-pruebas-realizadas="20"
        data-mujeres-no-aptas="5"
        data-hombres-no-aptos="6"
        data-automoviles-no-aptos="5"
        data-motocicletas-no-aptas="2"
        data-transporte-colectivo-no-apto="0"
        data-transporte-individual-no-apto="1"
        data-transporte-carga-no-apto="1"
        data-emergencia-no-apto="0"
        data-archivos='["reporte_operativo.pdf", "estadisticas.xlsx", "fotos_evidencia.zip"]'
        data-comentarios='[{"usuario": "Carlos Rodríguez", "fecha": "17/10/2023", "mensaje": "Excelente trabajo en el operativo. Los números son alentadores."}, {"usuario": "María González", "fecha": "18/10/2023", "mensaje": "¿Podemos extender el operativo al próximo fin de semana?"}]'>
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- ARCHIVOS ADJUNTOS -->
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
                        
                        <div class="h-[1px] bg-gray-300 my-3"></div>
                        
                        <!-- BOTONES -->
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

        <!-- Paginación -->
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

    <!-- INCLUIR EL COMPONENTE DEL MODAL DE ALCOHOLIMETRÍA -->
    @include('components.modal-reporte')

    <!-- Incluir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection