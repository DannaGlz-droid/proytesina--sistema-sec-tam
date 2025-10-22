@extends('layouts.principal')
@section('title', 'Actualizar Usuario')
@section('content')

    @include('components.header-admin')
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: María Elena"
                                   value="{{ old('nombres', $usuario->nombres ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: García"
                                   value="{{ old('apellido_paterno', $usuario->apellido_paterno ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Correo electrónico *</label>
                            <input type="email" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: usuario@ejemplo.com"
                                   value="{{ old('email', $usuario->email ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: López"
                                   value="{{ old('apellido_materno', $usuario->apellido_materno ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Teléfono *</label>
                            <input type="tel" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Cargo *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="cargo">
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="jurisdiccion">
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Usuario *</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: mgarcia"
                                   value="{{ old('username', $usuario->username ?? '') }}">
                        </div>
                        
                        <!-- Switch para estado activo/inactivo -->
                        <div class="pt-2">
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-3 font-lora">Estado de la cuenta</label>
                            <div class="flex items-center">
                                <label class="switch">
                                    <input type="checkbox" 
                                           name="activo"
                                           value="1"
                                           {{ (old('activo', $usuario->activo ?? 1) == 1) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <span class="ml-3 text-sm font-medium text-[#404041] font-lora" id="toggleLabel">
                                    {{ (old('activo', $usuario->activo ?? 1) == 1) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 font-lora">
                                <i class="fas fa-info-circle text-[#611132] mr-1"></i>
                                Cuando este inactivo, el usuario no podra iniciar sesión en el sistema.
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Rol *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="rol">
                                <option value="">Seleccione un rol</option>
                                <option value="admin" {{ old('rol', $usuario->rol ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="editor" {{ old('rol', $usuario->rol ?? '') == 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="visor" {{ old('rol', $usuario->rol ?? '') == 'visor' ? 'selected' : '' }}>Visor</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de advertencia para usuarios inactivos -->
            <div id="advertenciaInactivo" class="mb-6 p-4 border border-yellow-300 bg-yellow-50 rounded-lg {{ (old('activo', $usuario->activo ?? 1) == 1) ? 'hidden' : '' }}">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-lg mt-0.5 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800 text-sm font-lora">Usuario inactivo</h3>
                        <p class="text-yellow-700 text-xs mt-1 font-lora">
                            Este usuario está marcado como inactivo. No podrá iniciar sesión en el sistema hasta que sea reactivado.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES PERSONALIZADO -->
            <!-- USAR COMPONENTE DE BOTONES ESTANDARIZADO -->
            <x-form-buttons 
                primaryText="Actualizar registro"
                secondaryText="Volver al listado"
                primaryType="submit"
                secondaryType="button"
            />
        </div>
    </div>

    <!-- Estilos para el switch -->
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #10B981;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>

    <!-- Script para el switch de estado activo/inactivo -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleCheckbox = document.querySelector('input[name="activo"]');
            const toggleLabel = document.getElementById('toggleLabel');
            const advertenciaInactivo = document.getElementById('advertenciaInactivo');
            
            toggleCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel.textContent = 'Activo';
                    advertenciaInactivo.classList.add('hidden');
                } else {
                    toggleLabel.textContent = 'Inactivo';
                    advertenciaInactivo.classList.remove('hidden');
                }
            });
        });
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <!-- Incluir Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection