@extends('layouts.principal')
@section('title', 'Actualizar Usuario')
@section('content')

    @include('components.header')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Actualizar datos de usuario</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Modifique los campos necesarios y guarde los cambios.</p>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            
            <!-- Sección 1: Información del usuario -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del usuario</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Nombre(s) *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: María Elena"
                                   value="{{ old('nombres', $usuario->nombres ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Apellido paterno *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: García"
                                   value="{{ old('apellido_paterno', $usuario->apellido_paterno ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Correo electrónico *</label>
                            <input type="email" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: usuario@ejemplo.com"
                                   value="{{ old('email', $usuario->email ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Apellido materno *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: López"
                                   value="{{ old('apellido_materno', $usuario->apellido_materno ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Teléfono *</label>
                            <input type="tel" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: 8123456789"
                                   value="{{ old('telefono', $usuario->telefono ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Información laboral -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="business-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información laboral</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Cargo *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" name="cargo">
                                <option value="">Seleccione un cargo</option>
                                <option value="administrador" {{ old('cargo', $usuario->cargo ?? '') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                <option value="coordinador" {{ old('cargo', $usuario->cargo ?? '') == 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                                <option value="capturista" {{ old('cargo', $usuario->cargo ?? '') == 'capturista' ? 'selected' : '' }}>Capturista</option>
                                <option value="consultor" {{ old('cargo', $usuario->cargo ?? '') == 'consultor' ? 'selected' : '' }}>Consultor</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Jurisdicción *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" name="jurisdiccion">
                                <option value="">Seleccione una jurisdicción</option>
                                <option value="centro" {{ old('jurisdiccion', $usuario->jurisdiccion ?? '') == 'centro' ? 'selected' : '' }}>Jurisdicción Centro</option>
                                <option value="norte" {{ old('jurisdiccion', $usuario->jurisdiccion ?? '') == 'norte' ? 'selected' : '' }}>Jurisdicción Norte</option>
                                <option value="sur" {{ old('jurisdiccion', $usuario->jurisdiccion ?? '') == 'sur' ? 'selected' : '' }}>Jurisdicción Sur</option>
                                <option value="este" {{ old('jurisdiccion', $usuario->jurisdiccion ?? '') == 'este' ? 'selected' : '' }}>Jurisdicción Este</option>
                                <option value="oeste" {{ old('jurisdiccion', $usuario->jurisdiccion ?? '') == 'oeste' ? 'selected' : '' }}>Jurisdicción Oeste</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Configuración de cuenta -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="settings-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Configuración de cuenta</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Usuario *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                   placeholder="Ej: mgarcia"
                                   value="{{ old('username', $usuario->username ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Rol *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" name="rol">
                                <option value="">Seleccione un rol</option>
                                <option value="admin" {{ old('rol', $usuario->rol ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="editor" {{ old('rol', $usuario->rol ?? '') == 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="visor" {{ old('rol', $usuario->rol ?? '') == 'visor' ? 'selected' : '' }}>Visor</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Botones responsive -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                <button type="button" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 text-xs lg:text-sm border border-[#404041] text-[#404041] font-medium rounded-lg hover:bg-[#404041] hover:text-white transition-all duration-200">
                    Volver al listado
                </button>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 text-xs lg:text-sm bg-[#404041] text-white font-medium rounded-lg hover:bg-[#2a2a2a] transition-all duration-200">
                    Actualizar registro
                </button>
            </div>
        </div>
    </div>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection