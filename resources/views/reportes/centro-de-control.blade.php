@extends('layouts.principal')
@section('title', 'Nuevo Registro')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Nuevo registro de defunción</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para registrar una defunción en el sistema.</p>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            
            <!-- Sección 1: Información del fallecido -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del fallecido</h2>
                    <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Juan Diego">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Nava">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad *</label>
                            <input type="number" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 34">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Reyes">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una opción</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Ubicación -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="location-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Ubicación</h2>
                    <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione un municipio</option>
                                <option value="monterrey">Monterrey</option>
                                <option value="sanpedro">San Pedro Garza García</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione un municipio</option>
                                <option value="monterrey">Monterrey</option>
                                <option value="sanpedro">San Pedro Garza García</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Se asignará automáticamente"
                                   readonly>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar específico *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Carretera Monterrey-Saltillo, km 12">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Información de la defunción -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="medical-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información de la defunción</h2>
                    <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una causa</option>
                                <option value="enfermedad">Enfermedad cardiovascular</option>
                                <option value="accidente">Accidente</option>
                                <option value="cancer">Cáncer</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción *</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

            <!-- Botones responsive - ESTILOS CONSISTENTES CON "APLICAR" Y "LIMPIAR" -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                <!-- Botón Limpiar formulario - Mismo estilo que "Limpiar" -->
                <button class="border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center justify-center gap-1 whitespace-nowrap">
                    <i class="fas fa-redo text-xs"></i>
                    Limpiar formulario
                </button>
                
                <!-- Botón Guardar registro - Mismo estilo que "Aplicar" -->
                <button class="bg-[#611132] text-white px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-1 whitespace-nowrap">
                    <i class="fas fa-save text-xs"></i>
                    Guardar registro
                </button>
            </div>
        </div>
    </div>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection