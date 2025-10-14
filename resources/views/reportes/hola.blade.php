@extends('layouts.principal')
@section('title', 'Reportes')
@section('content')

    @include('components.header')
    @include('components.nav')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Reportes</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Lista de reportes registrados en el sistema.</p>

        <!-- Contenedor principal -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-full shadow-md">
            
            <!-- Grid de reportes - 4 columnas -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-5">
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
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white" title="Ver detalles">
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
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white" title="Ver detalles">
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

                <!-- Reporte 3 - Alcolimetría -->
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
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white" title="Ver detalles">
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
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#404041] text-[#404041] transition-all duration-300 hover:bg-[#404041] hover:text-white" title="Ver detalles">
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
    </div>

    <!-- Incluir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection